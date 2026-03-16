<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    use HasFactory;

    public $timestamps = false;

    const CREATED_AT = 'created_at';
    const UPDATED_AT = null;

    protected $fillable = [
        'title',
        'description',
        'sessions',
        'duration_days',
        'progress_tracking',
        'nutrition_plan',
        'priority_booking',
        'full_access',
    ];

    protected function casts(): array
    {
        return [
            'progress_tracking' => 'boolean',
            'nutrition_plan'    => 'boolean',
            'priority_booking'  => 'boolean',
            'full_access'       => 'boolean',
            'created_at'        => 'datetime',
        ];
    }

    // ─── Relations ───────────────────────────────────────────────────────────────

    public function trainerPackages()
    {
        return $this->hasMany(TrainerPackage::class);
    }

    public function trainers()
    {
        return $this->belongsToMany(Trainer::class, 'trainer_packages')
            ->withPivot('price', 'is_active')
            ->withTimestamps();
    }

    public function bookings()
    {
        return $this->hasManyThrough(Booking::class, TrainerPackage::class, 'package_id', 'trainer_package_id');
    }
}
