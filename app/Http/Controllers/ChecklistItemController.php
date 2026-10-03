<?php

namespace App\Http\Controllers;

use App\Models\ChecklistItem;
use App\Models\DailyNote;
use App\Models\PendingTask;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ChecklistItemController extends Controller
{
    public function store(Request $request, PendingTask $pendingTask): RedirectResponse
    {
        $dailyNote = $pendingTask->dailyNote;
        $this->authorizeAuthor($request, $dailyNote);
        $validated = $request->validateWithBag('tasks', ['text' => ['required', 'string', 'max:255']]);
        $pendingTask->checklistItems()->create($validated);

        return $this->backToNote($dailyNote);
    }

    public function update(Request $request, ChecklistItem $checklistItem): RedirectResponse
    {
        $dailyNote = $checklistItem->pendingTask->dailyNote;
        $this->authorizeAuthor($request, $dailyNote);
        $validated = $request->validateWithBag('tasks', ['is_done' => ['required', 'boolean']]);
        $checklistItem->update($validated);

        return $this->backToNote($dailyNote);
    }

    public function destroy(Request $request, ChecklistItem $checklistItem): RedirectResponse
    {
        $dailyNote = $checklistItem->pendingTask->dailyNote;
        $this->authorizeAuthor($request, $dailyNote);
        $checklistItem->delete();

        return $this->backToNote($dailyNote);
    }

    private function authorizeAuthor(Request $request, DailyNote $dailyNote): void
    {
        abort_unless($request->session()->get('note_author_token') === $dailyNote->author_token, 403);
    }

    private function backToNote(DailyNote $dailyNote): RedirectResponse
    {
        return redirect()->route('appointments.index', ['date' => $dailyNote->date->toDateString(), 'notes' => $dailyNote->id]);
    }
}
