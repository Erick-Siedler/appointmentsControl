<?php

namespace App\Models;

use Database\Factories\PendingTaskFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PendingTask extends Model
{
    /** @use HasFactory<PendingTaskFactory> */
    use HasFactory;

    public const STATUSES = [
        'not_started' => 'Não iniciado',
        'bre_pending' => 'Pendência Brê',
        'external_blocker' => 'Impedimento externo',
        'deploy_new' => 'Implantar novidade',
        'in_progress' => 'Em andamento',
        'late' => 'Atrasado',
        'completed' => 'Concluído',
        'deploying' => 'Em deploy',
        'approve' => 'Homologar',
        'remember' => 'Não esquecer',
    ];

    public const PRIORITIES = ['low' => 'Baixa', 'normal' => 'Normal', 'high' => 'Alta', 'urgent' => 'Urgente'];

    protected $fillable = ['daily_note_id', 'title', 'description', 'status', 'priority', 'due_date', 'assignee', 'position'];

    protected function casts(): array
    {
        return ['due_date' => 'date'];
    }

    public function dailyNote(): BelongsTo
    {
        return $this->belongsTo(DailyNote::class);
    }

    public function checklistItems(): HasMany
    {
        return $this->hasMany(ChecklistItem::class);
    }
}
