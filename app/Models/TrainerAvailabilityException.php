<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrainerAvailabilityException extends Model
{
    public $timestamps = false;

    protected $table = 'trainer_availability_exceptions';

    protected $fillable = [
        'trainer_id',
        'date',
        'is_available',
        'start_time',
        'end_time',
        'reason',
    ];

    protected function casts(): array
    {
        return [
            'date'         => 'date',
            'is_available' => 'boolean',
        ];
    }

    // ─── Relations ───────────────────────────────────────────────────────────────

    public function trainer()
    {
        return $this->belongsTo(Trainer::class);
    }
}
