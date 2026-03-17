<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TraineeSessionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'session_start'  => $this->session_start?->toDateTimeString(),
            'session_end'    => $this->session_end?->toDateTimeString(),
            'session_status' => $this->session_status,
            'notes'          => $this->notes,
            'trainer'        => new TrainerResource($this->whenLoaded('trainer')),
            'client'         => $this->whenLoaded('client', fn () => [
                'id'            => $this->client->id,
                'name'          => $this->client->name,
                'profile_image' => $this->client->profile_image,
            ]),
        ];
    }
}
