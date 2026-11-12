<?php

namespace Tests\Feature;

use App\Models\Note;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class NoteBoardTest extends TestCase
{
    use RefreshDatabase;

    public function test_board_displays_notes_newest_first_and_escapes_user_content(): void
    {
        $olderNote = $this->createNote('Older confession');
        $olderNote->forceFill(['created_at' => now()->subMinute()])->save();
        $newestNote = $this->createNote('Newest confession', '<script>alert(1)</script>');
        $newestNote->update([
            'poster_name' => 'John Doe',
            'is_censored' => true,
        ]);

        $this->get(route('notes.index'))
            ->assertOk()
            ->assertSeeInOrder(['Newest confession', 'Older confession'])
            ->assertSee('J**n D*e')
            ->assertDontSee('John Doe')
            ->assertSee('&lt;script&gt;alert(1)&lt;/script&gt;', false)
            ->assertDontSee('<script>alert(1)</script>', false);
    }

    public function test_board_only_displays_posts_from_the_last_48_hours(): void
    {
        $this->travelTo(now());
        $this->createNote('Recent confession');
        $boundaryNote = $this->createNote('Forty-eight-hour confession');
        $boundaryNote->forceFill(['created_at' => now()->subHours(48)])->save();
        $expiredNote = $this->createNote('Expired confession');
        $expiredNote->forceFill(['created_at' => now()->subHours(48)->subSecond()])->save();

        $this->get(route('notes.index'))
            ->assertOk()
            ->assertSee('Recent confession')
            ->assertSee('Forty-eight-hour confession')
            ->assertDontSee('Expired confession');
    }

    public function test_board_renders_a_floating_auto_dismissing_flash_toast(): void
    {
        $this->followingRedirects()
            ->post(route('notes.store'), [
                'title' => 'A secret thought',
                'content' => 'Something I needed to say.',
                'password' => 'secret-password',
            ])
            ->assertOk()
            ->assertSee('data-toast', false)
            ->assertSee('2700')
            ->assertSee('Your confession has been posted.');
    }

    public function test_long_posts_render_a_see_more_toggle(): void
    {
        $longContent = str_repeat('A longer thought. ', 12);
        $this->createNote('Long confession', $longContent);

        $this->get(route('notes.index'))
            ->assertOk()
            ->assertSee('data-content-toggle', false)
            ->assertSee('See more')
            ->assertSee($longContent);
    }

    public function test_censored_author_names_follow_the_board_masking_rules(): void
    {
        $note = new Note;
        $note->poster_name = 'John Doe';
        $note->is_censored = true;

        $this->assertSame('J**n D*e', $note->display_author);

        $note->poster_name = 'Rhean';

        $this->assertSame('R***n', $note->display_author);

        $note->poster_name = 'Al';

        $this->assertSame('A*', $note->display_author);

        $note->poster_name = 'A';

        $this->assertSame('A', $note->display_author);
    }

    public function test_uncensored_author_name_is_displayed_without_masking(): void
    {
        $note = new Note;
        $note->poster_name = 'Rhean';
        $note->is_censored = false;

        $this->assertSame('Rhean', $note->display_author);
    }

    public function test_create_form_is_available_without_authentication(): void
    {
        $this->get(route('notes.create'))
            ->assertOk()
            ->assertSee('Keep my name private')
            ->assertSee('private post password');
    }

    public function test_valid_submission_creates_anonymous_post_with_hashed_password(): void
    {
        $this->post(route('notes.store'), [
            'title' => 'A secret thought',
            'content' => 'Something I needed to say.',
            'password' => 'secret-password',
        ])->assertRedirect(route('notes.index'));

        $this->assertDatabaseCount('notes', 1);
        $note = Note::query()->firstOrFail();
        $this->assertSame('Anonymous', $note->poster_name);
        $this->assertFalse($note->is_censored);
        $this->assertTrue(Hash::check('secret-password', $note->password));
        $this->assertArrayNotHasKey('password', $note->toArray());
    }

    public function test_submission_requires_title_content_and_password(): void
    {
        $this->post(route('notes.store'), [])
            ->assertSessionHasErrors(['title', 'content', 'password']);

        $this->assertDatabaseCount('notes', 0);
    }

    public function test_edit_form_shows_post_fields_and_password_prompt(): void
    {
        $note = $this->createNote('A thought');

        $this->post(route('notes.edit.authorize', $note), [
            'password' => 'secret-password',
        ]);

        $this->get(route('notes.edit', $note))
            ->assertOk()
            ->assertSee('value="A thought"', false)
            ->assertSee('name="password"', false)
            ->assertSee('Verify your post password');
    }

    public function test_edit_form_is_not_granted_without_password_authorization(): void
    {
        $note = $this->createNote('A protected thought');

        $this->get(route('notes.edit', $note))
            ->assertRedirect(route('notes.index'))
            ->assertSessionHasErrors('password');
    }

    public function test_edit_form_is_granted_after_password_authorization(): void
    {
        $note = $this->createNote('A protected thought');

        $this->post(route('notes.edit.authorize', $note), [
            'password' => 'secret-password',
        ])->assertRedirect(route('notes.edit', $note));

        $this->get(route('notes.edit', $note))
            ->assertOk()
            ->assertSee('A protected thought');
    }

    public function test_edit_form_is_not_granted_when_authorization_password_is_incorrect(): void
    {
        $note = $this->createNote('A protected thought');

        $this->post(route('notes.edit.authorize', $note), [
            'password' => 'wrong-password',
        ])->assertSessionHasErrors('password');

        $this->get(route('notes.edit', $note))
            ->assertRedirect(route('notes.index'));
    }

    public function test_post_is_not_updated_when_edit_password_is_incorrect(): void
    {
        $note = $this->createNote('Original title');

        $this->put(route('notes.update', $note), [
            'poster_name' => 'New Name',
            'title' => 'Changed title',
            'content' => 'Changed content',
            'password' => 'wrong-password',
        ])->assertSessionHasErrors('password');

        $this->assertDatabaseHas('notes', [
            'id' => $note->id,
            'title' => 'Original title',
            'content' => 'A note for the class.',
        ]);
    }

    public function test_post_is_updated_when_edit_password_is_correct(): void
    {
        $note = $this->createNote('Original title');

        $this->put(route('notes.update', $note), [
            'poster_name' => 'Rhean',
            'is_censored' => '1',
            'title' => 'A new beginning',
            'content' => 'I feel better after sharing.',
            'password' => 'secret-password',
        ])->assertRedirect(route('notes.index'));

        $this->assertDatabaseHas('notes', [
            'id' => $note->id,
            'poster_name' => 'Rhean',
            'is_censored' => true,
            'title' => 'A new beginning',
            'content' => 'I feel better after sharing.',
        ]);
    }

    public function test_post_is_not_deleted_when_delete_password_is_incorrect(): void
    {
        $note = $this->createNote('Keep this post');

        $this->delete(route('notes.destroy', $note), [
            'password' => 'wrong-password',
        ])->assertSessionHasErrors('password');

        $this->assertDatabaseHas('notes', ['id' => $note->id]);
    }

    public function test_post_is_deleted_when_delete_password_is_correct(): void
    {
        $note = $this->createNote('Delete this post');

        $this->delete(route('notes.destroy', $note), [
            'password' => 'secret-password',
        ])->assertRedirect(route('notes.index'));

        $this->assertDatabaseMissing('notes', ['id' => $note->id]);
    }

    private function createNote(string $title, string $content = 'A note for the class.'): Note
    {
        return Note::query()->create([
            'poster_name' => 'Alex Student',
            'is_censored' => false,
            'title' => $title,
            'content' => $content,
            'password' => Hash::make('secret-password'),
        ]);
    }
}
