<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                     => $this->id,
            'status'                 => $this->status,
            'payment_status'         => $this->payment_status,
            'cancellation_deadline'  => $this->cancellation_deadline?->toDateTimeString(),
            'cancelled_at'           => $this->cancelled_at?->toDateTimeString(),
            'cancel_reason'          => $this->cancel_reason,
            'created_at'             => $this->created_at?->toDateTimeString(),
            'trainer_package'        => new TrainerPackageResource($this->whenLoaded('trainerPackage')),
            'trainer'                => new TrainerResource($this->whenLoaded('trainer')),
            'sessions'               => TraineeSessionResource::collection($this->whenLoaded('sessions')),
        ];
    }
}
