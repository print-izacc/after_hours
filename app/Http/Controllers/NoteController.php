<?php

namespace App\Http\Controllers;

use App\Models\Note;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class NoteController extends Controller
{
    public function index(): View
    {
        return view('notes.index', [
            'notes' => Note::query()
                ->where('created_at', '>=', now()->subHours(48))
                ->latest()
                ->get(),
        ]);
    }

    public function create(): View
    {
        return view('notes.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->noteRules() + [
            'password' => ['required', 'string', 'min:8', 'max:255'],
        ]);

        Note::create([
            'poster_name' => ($validated['poster_name'] ?? null) ?: 'Anonymous',
            'is_censored' => $validated['is_censored'] ?? false,
            'title' => $validated['title'],
            'content' => $validated['content'],
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('notes.index')->with('success', 'Your confession has been posted.');
    }

    public function authorizeEdit(Request $request, Note $note): RedirectResponse
    {
        $validated = $request->validate([
            'password' => ['required', 'string', 'max:255'],
        ]);

        if (! Hash::check($validated['password'], $note->password)) {
            return back()->withErrors([
                'password' => 'The password is incorrect.',
            ]);
        }

        $request->session()->put(
            'notes.edit_authorized.'.$note->id,
            now()->addMinutes(10)->timestamp,
        );

        return redirect()->route('notes.edit', $note);
    }

    public function edit(Request $request, Note $note): View|RedirectResponse
    {
        $authorizationExpiresAt = $request->session()->get('notes.edit_authorized.'.$note->id);

        if (! is_int($authorizationExpiresAt) || $authorizationExpiresAt < now()->timestamp) {
            $request->session()->forget('notes.edit_authorized.'.$note->id);

            return redirect()->route('notes.index')->withErrors([
                'password' => 'Enter the post password before editing.',
            ]);
        }

        return view('notes.edit', ['note' => $note]);
    }

    public function update(Request $request, Note $note): RedirectResponse
    {
        $validated = $request->validate($this->noteRules() + [
            'password' => ['required', 'string', 'max:255'],
        ]);

        if (! Hash::check($validated['password'], $note->password)) {
            return back()->withErrors([
                'password' => 'The password is incorrect.',
            ]);
        }

        $note->update([
            'poster_name' => ($validated['poster_name'] ?? null) ?: 'Anonymous',
            'is_censored' => $validated['is_censored'] ?? false,
            'title' => $validated['title'],
            'content' => $validated['content'],
        ]);

        return redirect()->route('notes.index')->with('success', 'Your confession has been updated.');
    }

    public function destroy(Request $request, Note $note): RedirectResponse
    {
        $validated = $request->validate([
            'password' => ['required', 'string', 'max:255'],
        ]);

        if (! Hash::check($validated['password'], $note->password)) {
            return back()->withErrors([
                'password' => 'The password is incorrect.',
            ]);
        }

        $note->delete();

        return redirect()->route('notes.index')->with('success', 'The confession has been deleted.');
    }

    /**
     * @return array<string, array<int, string>>
     */
    private function noteRules(): array
    {
        return [
            'poster_name' => ['nullable', 'string', 'max:255'],
            'is_censored' => ['sometimes', 'boolean'],
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
        ];
    }
}
