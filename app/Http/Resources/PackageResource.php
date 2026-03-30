<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Nette\Utils\Random;

class PackageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $features= [
                'progress_tracking' => $this->progress_tracking,
                'nutrition_plan'    => $this->nutrition_plan,
                'priority_booking'  => $this->priority_booking,
                'full_access'       => $this->full_access,
            ];

        return [
            'id'               => $this->id,
            'title'            => $this->title,
            'description'      => $this->description,
            'sessions'         => $this->sessions === 999 ? 'Unlimited' : $this->sessions,
            'duration_days'    => $this->duration_days,
            'features' => collect($features)->filter()->keys()->values(),
            'price' => rand(0,10000),
            'is_recommended' => (bool) rand(0, 1),        // 50% chance true/false
        ];
    }
}
