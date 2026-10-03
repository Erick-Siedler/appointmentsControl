<?php

namespace Tests\Feature;

use App\Models\DailyNote;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DailyNotesTest extends TestCase
{
    use RefreshDatabase;

    public function test_note_is_saved_for_selected_date_with_creation_time(): void
    {
        $this->travelTo(now()->setDate(2026, 10, 3)->setTime(14, 15));

        $this->post(route('daily-notes.store'), ['date' => '2026-10-03', 'body' => 'Ligar para a equipe'])
            ->assertRedirectContains('date=2026-10-03');

        $note = DailyNote::query()->firstOrFail();
        $this->assertSame('2026-10-03', $note->date->toDateString());
        $this->assertSame('2026-10-03 14:15:00', $note->created_at->format('Y-m-d H:i:s'));
        $this->get(route('appointments.index', ['date' => '2026-10-03']))
            ->assertSee('Ligar para a equipe')
            ->assertSee('03/10/2026 às 14:15');
    }

    public function test_only_the_session_that_created_a_note_can_change_or_delete_it(): void
    {
        $note = DailyNote::factory()->create(['author_token' => '20d8f2b8-95a3-4e4c-a409-d7d36aece837']);

        $this->withSession(['note_author_token' => 'other-author'])
            ->put(route('daily-notes.update', $note), ['body' => 'Alterada'])
            ->assertForbidden();
        $this->delete(route('daily-notes.destroy', $note))->assertForbidden();
        $this->assertModelExists($note);

        $this->withSession(['note_author_token' => $note->author_token])
            ->put(route('daily-notes.update', $note), ['body' => 'Alterada'])
            ->assertRedirect();
        $this->assertDatabaseHas('daily_notes', ['id' => $note->id, 'body' => 'Alterada']);

        $this->delete(route('daily-notes.destroy', $note))->assertRedirect();
        $this->assertModelMissing($note);
    }

    public function test_note_requires_a_date_and_body(): void
    {
        $this->post(route('daily-notes.store'), [])
            ->assertSessionHasErrors(['date', 'body'], null, 'notes');

        $this->assertDatabaseCount('daily_notes', 0);
    }
}
