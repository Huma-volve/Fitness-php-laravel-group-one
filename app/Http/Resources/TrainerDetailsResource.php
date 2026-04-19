<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TrainerDetailsResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $now = now();
        $todayDay = strtolower($now->format('l'));
        $currentTime = $now->format('H:i:s');

        // === SAFE NULL CHECKS ===
        $availability = $this->availability ?? collect();   // ← Important

        $todayAvailability = $availability
            ->where('day_of_week', $todayDay)
            ->where('is_active', true)
            ->first();

        $isCurrentlyAvailable = false;

        if ($todayAvailability) {
            $isCurrentlyAvailable =
                $currentTime >= $todayAvailability->start_time &&
                $currentTime <= $todayAvailability->end_time;
        }

        return [
            'id' => $this->id,
            'trainer_id' => $this->id,
            'name' => $this->user?->name ?? $this->name,
            'location' => $this->location,
            'profile_image' => "https://i.pravatar.cc/300?img=" . rand(1, 100),
            'bio' => $this->bio,
            'experience_years' => (int) ($this->experience_years ?? 0),
            'rating' => (float) ($this->rating ?? 0),

            'is_currently_available' => $isCurrentlyAvailable,

            'specializations' => $this->specializations?->pluck('name')->values() ?? [],

            'reviews' => $this->reviews?->map(function ($review) {
                return [
                    'id' => $review->id,
                    'user_id' => $review->user_id,
                    'comment' => $review->comment,
                    'rating' => $review->rating,
                    'created_at' => $review->created_at,
                ];
            }) ?? [],

            'certifications' => $this->certifications?->map(function ($cert) {
                return [
                    'id' => $cert->id,
                    'certificate_name' => $cert->certificate_name,
                    'organization' => $cert->organization,
                    'year' => $cert->year,
                    'file_path' => $cert->path,
                ];
            }) ?? [],

            'availability' => $availability->map(function ($slot) {
                return [
                    'id'          => $slot->id,
                    'day_of_week' => $slot->day_of_week,
                    'start_time'  => $slot->start_time,
                    'end_time'    => $slot->end_time,
                    'is_active'   => (bool) $slot->is_active,
                ];
            })->values(),

            'availability_exceptions' => $this->availabilityExceptions?->map(function ($ex) {
                return [
                    'id' => $ex->id,
                    'date' => $ex->date,
                    'is_available' => (bool) $ex->is_available,
                    'start_time' => $ex->start_time,
                    'end_time' => $ex->end_time,
                    'reason' => $ex->reason,
                ];
            }) ?? [],

            'packages' => $this->activeTrainerPackages?->map(function ($trainerPackage) {   // Check this relation name!
                $features = [
                    'progress_tracking' => $trainerPackage->package->progress_tracking ?? false,
                    'nutrition_plan'    => $trainerPackage->package->nutrition_plan ?? false,
                    'priority_booking'  => $trainerPackage->package->priority_booking ?? false,
                    'full_access'       => $trainerPackage->package->full_access ?? false,
                ];

                return [
                    'trainer_package_id' => $trainerPackage->id,
                    'package_id'         => $trainerPackage->package?->id,
                    'title'              => $trainerPackage->package?->title,
                    'description'        => $trainerPackage->package?->description,
                    'sessions'           => $trainerPackage->package?->sessions,
                    'duration_days'      => $trainerPackage->package?->duration_days,
                    'price'              => (float) ($trainerPackage->price ?? 0),
                    'features'           => collect($features)->filter()->keys()->values(),
                ];
            }) ?? [],
        ];
    }
}
