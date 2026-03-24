<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trainer extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'bio',
        'experience_years',
        'location',
        'rating',
        'total_reviews',
    ];

    protected function casts(): array
    {
        return [
            'rating' => 'decimal:2',
        ];
    }

    // ─── Relations ───────────────────────────────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function certifications()
    {
        return $this->hasMany(TrainerCertification::class);
    }

    public function specializations()
    {
        return $this->belongsToMany(Specialization::class, 'trainer_specializations');
    }

    public function availability()
    {
        return $this->hasMany(TrainerAvailabilities::class);
    }
        public function availabilityExceptions()
    {
        return $this->hasMany(TrainerAvailabilityException::class);
    }
      public function activeTrainerPackages()
    {
        return $this->hasMany(TrainerPackage::class)->where('is_active', true);
    }

    public function trainerPackages()
    {
        return $this->hasMany(TrainerPackage::class);
    }

    public function packages()
    {
        return $this->belongsToMany(Package::class, 'trainer_packages')
            ->withPivot('price', 'is_active')
            ->withTimestamps();
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function sessions()
    {
        return $this->hasMany(TraineeSession::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function payouts()
    {
        return $this->hasMany(TrainerPayout::class);
    }

    public function searchHistory()
    {
        return $this->hasMany(SearchHistory::class);
    }
}
