<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChecklistItem extends Model
{
    protected $fillable = ['pending_task_id', 'text', 'is_done'];

    protected function casts(): array
    {
        return ['is_done' => 'boolean'];
    }

    public function pendingTask(): BelongsTo
    {
        return $this->belongsTo(PendingTask::class);
    }
}
