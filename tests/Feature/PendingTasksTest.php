<?php

namespace Tests\Feature;

use App\Models\DailyNote;
use App\Models\PendingTask;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PendingTasksTest extends TestCase
{
    use RefreshDatabase;

    public function test_author_can_create_move_and_check_off_a_pending_task(): void
    {
        $note = DailyNote::factory()->create(['date' => '2026-10-03']);
        $this->withSession(['note_author_token' => $note->author_token]);

        $this->post(route('pending-tasks.store', $note), [
            'title' => 'Validar entrega', 'description' => 'Revisar em homologação',
            'status' => 'not_started', 'priority' => 'urgent',
            'due_date' => '2026-10-05', 'assignee' => 'Brenda',
        ])->assertRedirect();

        $task = PendingTask::query()->firstOrFail();
        $this->assertSame($note->id, $task->daily_note_id);
        $this->assertSame('urgent', $task->priority);

        $this->patch(route('pending-tasks.move', $task), ['status' => 'approve'])->assertRedirect();
        $this->assertDatabaseHas('pending_tasks', ['id' => $task->id, 'status' => 'approve']);

        $this->post(route('checklist-items.store', $task), ['text' => 'Conferir planilha'])->assertRedirect();
        $item = $task->checklistItems()->firstOrFail();
        $this->patch(route('checklist-items.update', $item), ['is_done' => true])->assertRedirect();
        $this->assertDatabaseHas('checklist_items', ['id' => $item->id, 'is_done' => true]);

        $this->get(route('appointments.index', ['date' => '2026-10-03', 'notes' => $note->id]))
            ->assertSee('Validar entrega')
            ->assertSee('Conferir planilha');
    }

    public function test_other_session_cannot_change_a_card_or_its_checklist(): void
    {
        $task = PendingTask::factory()->create();
        $item = $task->checklistItems()->create(['text' => 'Passo']);

        $this->withSession(['note_author_token' => 'other-author'])
            ->patch(route('pending-tasks.move', $task), ['status' => 'completed'])
            ->assertForbidden();
        $this->patch(route('checklist-items.update', $item), ['is_done' => true])->assertForbidden();

        $this->assertDatabaseHas('pending_tasks', ['id' => $task->id, 'status' => 'not_started']);
        $this->assertDatabaseHas('checklist_items', ['id' => $item->id, 'is_done' => false]);
    }

    public function test_invalid_status_is_rejected(): void
    {
        $note = DailyNote::factory()->create();

        $this->withSession(['note_author_token' => $note->author_token])
            ->post(route('pending-tasks.store', $note), [
                'title' => 'Exemplo', 'status' => 'inventado', 'priority' => 'normal',
            ])->assertSessionHasErrors('status', null, 'tasks');

        $this->assertDatabaseCount('pending_tasks', 0);
    }
}
