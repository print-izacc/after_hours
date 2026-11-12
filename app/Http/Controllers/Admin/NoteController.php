<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Note;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class NoteController extends Controller
{
    public function index(): View
    {
        $publicSince = now()->subHours(48);

        return view('admin.notes.index', [
            'notes' => Note::query()->latest()->paginate(25),
            'publicSince' => $publicSince,
        ]);
    }

    public function destroy(Note $note): RedirectResponse
    {
        $note->delete();

        return redirect()
            ->route('admin.notes.index')
            ->with('success', 'The confession has been removed.');
    }
}
