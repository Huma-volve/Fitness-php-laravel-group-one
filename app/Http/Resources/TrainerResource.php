<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TrainerResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'               => $this->id,
            'name'             => $this->user->name,
            'profile_image'    =>  "https://i.pravatar.cc/300?img=" . rand(1, 100),
            'bio'              => $this->bio,
            'experience_years' => $this->experience_years,
            'location'         => $this->location,
            'rating'           => $this->rating,
            'total_reviews'    => $this->total_reviews,
            'specializations'  => $this->whenLoaded('specializations', fn () =>
            $this->specializations->pluck('name')
            ),
            'price_per_session' => $this->whenLoaded('trainerPackages', function () {
                $avg = $this->trainerPackages->avg(function ($tp) {
                    $sessionCount = optional($tp->package)->sessions ?: 1;
                    return $tp->price / $sessionCount;
                });
                return $avg ? round($avg, 2) : 0;
            }, 0),
        ];
    }
}
