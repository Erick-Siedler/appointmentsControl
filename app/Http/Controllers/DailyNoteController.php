<?php

namespace App\Http\Controllers;

use App\Models\DailyNote;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DailyNoteController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validateWithBag('notes', [
            'date' => ['required', 'date_format:Y-m-d'],
            'body' => ['required', 'string', 'max:10000'],
        ]);

        $authorToken = $request->session()->get('note_author_token');

        if ($authorToken === null) {
            $authorToken = (string) Str::uuid();
            $request->session()->put('note_author_token', $authorToken);
        }

        $dailyNote = DailyNote::create([
            'date' => $validated['date'],
            'body' => trim($validated['body']),
            'author_token' => $authorToken,
        ]);

        return redirect()->route('appointments.index', ['date' => $validated['date'], 'notes' => $dailyNote->id])
            ->with('success', 'Anotação adicionada.');
    }

    public function update(Request $request, DailyNote $dailyNote): RedirectResponse
    {
        $this->authorizeAuthor($request, $dailyNote);

        $validated = $request->validateWithBag('notes', ['body' => ['required', 'string', 'max:10000']]);
        $dailyNote->update(['body' => trim($validated['body'])]);

        return redirect()->route('appointments.index', ['date' => $dailyNote->date->toDateString(), 'notes' => $dailyNote->id])
            ->with('success', 'Anotação atualizada.');
    }

    public function destroy(Request $request, DailyNote $dailyNote): RedirectResponse
    {
        $this->authorizeAuthor($request, $dailyNote);

        $date = $dailyNote->date->toDateString();
        $dailyNote->delete();

        return redirect()->route('appointments.index', ['date' => $date])
            ->with('success', 'Anotação excluída.');
    }

    private function authorizeAuthor(Request $request, DailyNote $dailyNote): void
    {
        abort_unless($request->session()->get('note_author_token') === $dailyNote->author_token, 403);
    }
}
