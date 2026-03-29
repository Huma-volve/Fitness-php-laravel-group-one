<?php

namespace App\Http\Controllers\Api\Trainer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\GetAvailableSlotsRequest;
use App\Models\Trainer;
use App\Services\TrainerAvailabilityService;
use Illuminate\Http\JsonResponse;

class TrainerAvailabilityController extends Controller
{
    public function __construct(
        private readonly TrainerAvailabilityService $availabilityService
    ) {}

    public function slots(GetAvailableSlotsRequest $request, Trainer $trainer): JsonResponse
    {
        $slots = $this->availabilityService->availableSlots(
            $trainer,
            $request->date
        );

        return response()->json([
            'date'       => $request->date,
            'trainer_id' => $trainer->id,
            'slots'      => $slots->values(),
        ]);
    }

    public function schedule(Trainer $trainer): JsonResponse
    {
        $overview = $this->availabilityService->weeklyOverview($trainer);

        return response()->json([
            'trainer_id' => $trainer->id,
            'schedule'   => $overview['schedule'],
            'exceptions' => $overview['exceptions'],
        ]);
    }
}
