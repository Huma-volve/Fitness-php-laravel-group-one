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
            'profile_image'    => $this->user->profile_image,
            'bio'              => $this->bio,
            'experience_years' => $this->experience_years,
            'location'         => $this->location,
            'rating'           => $this->rating,
            'total_reviews'    => $this->total_reviews,
            'specializations'  => $this->whenLoaded('specializations', fn () =>
            $this->specializations->pluck('name')
            ),
        ];
    }
}
