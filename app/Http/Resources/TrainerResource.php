<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TrainerResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $activePackages = $this->trainerPackages
            ->where('is_active', true);

        $startingPrice = $activePackages->min('price');

        return [
            'id' => $this->id,
            'name' => $this->user?->name,
            'location' => $this->location,
            'profile_image' => $this->user?->profile_image
                ? asset('storage/' . $this->user->profile_image)
                : null,
            'bio' => $this->bio,
            'experience_years' => $this->experience_years,
            'rating' => (float) $this->rating,
            'total_reviews' => (int) $this->total_reviews,
            'starting_price' => $startingPrice ? (float) $startingPrice : null,
            'specializations' => $this->specializations->pluck('name')->values(),
        ];
    }
}