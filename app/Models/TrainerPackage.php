<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrainerPackage extends Model
{
    use HasFactory;

    protected $fillable = [
        'trainer_id',
        'package_id',
        'price',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'price'     => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    // ─── Relations ───────────────────────────────────────────────────────────────

    public function trainer()
    {
        return $this->belongsTo(Trainer::class);
    }

    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'trainer_package_id');
    }
}
