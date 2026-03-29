<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TrainerPackageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $features = [
            'progress_tracking' => $this->package->progress_tracking,
            'nutrition_plan'    => $this->package->nutrition_plan,
            'priority_booking'  => $this->package->priority_booking,
            'full_access'       => $this->package->full_access,
        ];

        return [
            'id'         => $this->id,
            'price'      => $this->price,
            'is_active'  => $this->is_active,
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),

            'package' => $this->whenLoaded('package', fn () => [
                'id'               => $this->package->id,
                'title'            => $this->package->title,
                'description'      => $this->package->description,
                'sessions'         => $this->package->sessions === 999
                    ? 'Unlimited'
                    : $this->package->sessions,
                'duration_days'    => $this->package->duration_days,
                'features'         => collect($features)->filter()->keys()->values(),
            ]),

            // Booking stats (only loaded when requested)
            'stats' => $this->whenLoaded('bookings', fn () => [
                'total_bookings'    => $this->bookings->count(),
                'confirmed_bookings'=> $this->bookings->where('status', 'confirmed')->count(),
                'total_revenue'     => $this->bookings
                    ->where('status', 'confirmed')
                    ->sum(fn ($b) => $b->payment?->amount ?? 0),
            ]),
        ];
    }
}
