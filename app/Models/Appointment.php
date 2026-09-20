<?php

namespace App\Models;

use Database\Factories\AppointmentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Appointment extends Model
{
    /** @use HasFactory<AppointmentFactory> */
    use HasFactory;

    protected $fillable = [
        'date',
        'duration_minutes',
        'project_id',
        'project_task',
        'occurrence',
        'internal_description',
        'entry_type',
        'owner',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'duration_minutes' => 'integer',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function formattedDuration(): string
    {
        return self::formatMinutes($this->duration_minutes);
    }

    public function entryTypeLabel(): string
    {
        return $this->entry_type === 'overtime' ? 'Hora extra' : 'Trabalho';
    }

    public static function formatMinutes(int $minutes): string
    {
        return sprintf('%02d:%02d', intdiv($minutes, 60), $minutes % 60);
    }
}
