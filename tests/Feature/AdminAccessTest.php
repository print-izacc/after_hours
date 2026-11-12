<?php

namespace Tests\Feature;

use App\Models\Note;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_sign_in_is_unlinked_from_the_public_board(): void
    {
        $this->get(route('notes.index'))
            ->assertOk()
            ->assertDontSee('Moderation')
            ->assertDontSee('/admin/login');

        $this->get(route('admin.login'))
            ->assertOk()
            ->assertSee('Administrator');
    }

    public function test_guest_is_redirected_to_admin_sign_in_before_opening_moderation(): void
    {
        $this->get(route('admin.notes.index'))
            ->assertRedirect(route('admin.login'));
    }

    public function test_non_admin_cannot_sign_in_or_open_moderation(): void
    {
        $user = User::factory()->create([
            'password' => 'correct-password',
        ]);

        $this->post(route('admin.login.store'), [
            'email' => $user->email,
            'password' => 'correct-password',
        ])->assertSessionHasErrors('email');

        $this->actingAs($user)
            ->get(route('admin.notes.index'))
            ->assertForbidden();
    }

    public function test_administrator_can_sign_in_and_view_all_posts_for_moderation(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
            'password' => 'correct-password',
        ]);
        $note = $this->createNote('A post for moderation');

        $this->post(route('admin.login.store'), [
            'email' => $admin->email,
            'password' => 'correct-password',
        ])->assertRedirect(route('admin.notes.index'));

        $this->get(route('admin.notes.index'))
            ->assertOk()
            ->assertSee('A post for moderation');

        $this->get(route('notes.index'))
            ->assertOk()
            ->assertSee('Moderation');
    }

    public function test_admin_moderation_marks_only_posts_older_than_48_hours_as_expired(): void
    {
        $this->travelTo('2026-11-12 12:00:00');

        $expiredNote = $this->createNote('Expired post');
        $expiredNote->forceFill(['created_at' => now()->subHours(48)->subSecond()])->save();

        $boundaryNote = $this->createNote('Boundary post');
        $boundaryNote->forceFill(['created_at' => now()->subHours(48)])->save();

        $recentNote = $this->createNote('Recent post');

        $response = $this->actingAs(User::factory()->create(['is_admin' => true]))
            ->get(route('admin.notes.index'));

        $response->assertOk()
            ->assertSee('Expired post')
            ->assertSee('Boundary post')
            ->assertSee('Recent post')
            ->assertSee('Expired · hidden from public board')
            ->assertSee('class="confession-card glass-panel is-expired"', false);

        $this->assertSame(
            1,
            substr_count($response->getContent(), 'class="confession-card glass-panel is-expired"'),
        );
    }

    public function test_administrator_can_remove_a_post_without_its_post_password(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $note = $this->createNote('A post to remove');

        $this->actingAs($admin)
            ->delete(route('admin.notes.destroy', $note))
            ->assertRedirect(route('admin.notes.index'));

        $this->assertDatabaseMissing('notes', ['id' => $note->id]);
    }

    public function test_administrator_can_sign_out(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)
            ->post(route('admin.logout'))
            ->assertRedirect(route('admin.login'));

        $this->get(route('admin.notes.index'))
            ->assertRedirect(route('admin.login'));
    }

    public function test_admin_creation_command_saves_a_hashed_password_and_admin_role(): void
    {
        $password = 'test-only-secure-password';

        $this->artisan('admin:create', [
            'name' => 'Board Owner',
            'email' => 'owner@example.test',
        ])
            ->expectsQuestion('Password (minimum 12 characters)', $password)
            ->expectsQuestion('Confirm password', $password)
            ->expectsOutput('Administrator account created.')
            ->assertExitCode(0);

        $admin = User::query()->where('email', 'owner@example.test')->firstOrFail();

        $this->assertTrue($admin->is_admin);
        $this->assertTrue(Hash::check($password, $admin->password));
    }

    private function createNote(string $title): Note
    {
        return Note::query()->create([
            'poster_name' => 'Anonymous',
            'is_censored' => false,
            'title' => $title,
            'content' => 'A note for moderation.',
            'password' => Hash::make('private-post-password'),
        ]);
    }
}
