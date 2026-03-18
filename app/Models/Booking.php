<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    public $timestamps = false;

    const CREATED_AT = 'created_at';
    const UPDATED_AT = null;

    protected $fillable = [
        'user_id',
        'trainer_id',
        'trainer_package_id',
        'status',
        'payment_status',
        'expires_at',
        'cancellation_deadline',
        'cancelled_at',
        'cancel_reason',
    ];

    protected function casts(): array
    {
        return [
            'expires_at'            => 'datetime',
            'cancellation_deadline' => 'datetime',
            'cancelled_at'          => 'datetime',
            'created_at'            => 'datetime',
        ];
    }

    // ─── Status Helpers ───────────────────────────────────────────────────────────

    public function isDraft(): bool     { return $this->status === 'draft'; }
    public function isPending(): bool   { return $this->status === 'pending'; }
    public function isConfirmed(): bool { return $this->status === 'confirmed'; }
    public function isCanceled(): bool  { return $this->status === 'canceled'; }

    public function isExpired(): bool
    {
        return $this->isDraft() && $this->expires_at && now()->isAfter($this->expires_at);
    }

    // ─── Relations ───────────────────────────────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function trainer()
    {
        return $this->belongsTo(Trainer::class);
    }

    public function trainerPackage()
    {
        return $this->belongsTo(TrainerPackage::class, 'trainer_package_id');
    }

    public function sessions()
    {
        return $this->hasMany(TraineeSession::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    public function payout()
    {
        return $this->hasOne(TrainerPayout::class);
    }
}
