<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrainerAvailabilities extends Model
{
    use HasFactory;

    public $timestamps = false;
    public $table = 'trainer_availabilities';
    protected $fillable = [
        'trainer_id',
        'date',
        'start_time',
        'end_time',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'datetime',
        ];
    }

    // ─── Relations ───────────────────────────────────────────────────────────────

    public function trainer()
    {
        return $this->belongsTo(Trainer::class);
    }
}
