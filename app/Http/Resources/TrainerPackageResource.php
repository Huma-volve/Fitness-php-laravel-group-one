<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TrainerPackageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'      => $this->id,
            'price'   => $this->price,
            'is_active' => $this->is_active,
            'package' => new PackageResource($this->whenLoaded('package')),
            'trainer' => new TrainerResource($this->whenLoaded('trainer')),
        ];
    }
}
