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

        $todayAvailability = optional($this->availability)
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
            'name' => $this->user?->name,
            'location' => $this->location,
            'profile_image' => "https://i.pravatar.cc/300?img=". rand(1,100),
            'bio' => $this->bio,
            'experience_years' => (int) $this->experience_years,
            'rating' => (float) $this->rating,
            'reviews' => $this->reviews->map(function ($reviews){
                return[
                    'id'=>$reviews->id,
                    'user_id'=>$reviews->user_id,
                    'comment'=>$reviews->comment,
                    'rating'=>$reviews->rating,
                    'created_at'=>$reviews->created_at
                ];
            }),
            'is_currently_available' => $isCurrentlyAvailable,

            'specializations' => $this->specializations
                ->pluck('name')
                ->values(),

            'certifications' => $this->certifications->map(function ($certification) {
                return [
                    'id' => $certification->id,
                    'certificate_name' => $certification->certificate_name,
                    'organization' => $certification->organization,
                    'year' => $certification->year,
                    'file_path' => $certification->path,
                ];
            })->values(),

            'availability' => $this->availability->map(function ($slot) {
                return [
                    'id' => $slot->id,
                    'day_of_week' => $slot->day_of_week,
                    'start_time' => $slot->start_time,
                    'end_time' => $slot->end_time,
                    'is_active' => (bool) $slot->is_active,
                ];
            })->values(),

            'availability_exceptions' => $this->availabilityExceptions->map(function ($exception) {
                return [
                    'id' => $exception->id,
                    'date' => $exception->date,
                    'is_available' => (bool) $exception->is_available,
                    'start_time' => $exception->start_time,
                    'end_time' => $exception->end_time,
                    'reason' => $exception->reason,
                ];
            })->values(),

            'packages' => $this->activeTrainerPackages->map(function ($trainerPackage) {
                $features= [
                'progress_tracking' => $trainerPackage->package->progress_tracking,
                'nutrition_plan'    => $trainerPackage->package->nutrition_plan,
                'priority_booking'  => $trainerPackage->package->priority_booking,
                'full_access'       => $trainerPackage->package->full_access,
            ];

                return [
                    'trainer_package_id' => $trainerPackage->id,
                    'package_id' => $trainerPackage->package?->id,
                    'title' => $trainerPackage->package?->title,
                    'description' => $trainerPackage->package?->description,
                    'sessions' => $trainerPackage->package?->sessions,
                    'duration_days' => $trainerPackage->package?->duration_days,
                    'price' => (float) $trainerPackage->price,
                    'features' => collect($features)->filter()->keys()->values(),
                ];
            })->values(),
        ];
    }
}