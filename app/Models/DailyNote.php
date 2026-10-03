<?php

namespace App\Models;

use Database\Factories\DailyNoteFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DailyNote extends Model
{
    /** @use HasFactory<DailyNoteFactory> */
    use HasFactory;

    protected $fillable = ['date', 'body', 'author_token'];

    protected function casts(): array
    {
        return ['date' => 'date'];
    }

    public function pendingTasks(): HasMany
    {
        return $this->hasMany(PendingTask::class);
    }
}
