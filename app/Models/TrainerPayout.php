<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrainerPayout extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'trainer_id',
        'booking_id',
        'trainer_amount',
        'platform_fee',
        'payout_status',
        'payout_date',
    ];

    protected function casts(): array
    {
        return [
            'trainer_amount' => 'decimal:2',
            'platform_fee'   => 'decimal:2',
            'payout_date'    => 'datetime',
        ];
    }

    // ─── Relations ───────────────────────────────────────────────────────────────

    public function trainer()
    {
        return $this->belongsTo(Trainer::class);
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}
