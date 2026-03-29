<?php

namespace App\Http\Controllers\Api\Trainer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreTrainerPackageRequest;
use App\Http\Requests\Api\UpdateTrainerPackageRequest;
use App\Http\Resources\TrainerPackageResource;
use App\Models\Package;
use App\Models\TrainerPackage;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TrainerPackageController extends Controller
{
    use AuthorizesRequests;

    public function index(): AnonymousResourceCollection
    {
        $this->authorize('viewAny', TrainerPackage::class);

        $trainer = auth()->user()->trainerProfile;

        $packages = TrainerPackage::with(['package', 'bookings.payment'])
            ->where('trainer_id', $trainer->id)
            ->orderByDesc('is_active')
            ->orderBy('created_at')
            ->get();

        return TrainerPackageResource::collection($packages);
    }

    public function show(TrainerPackage $trainerPackage): TrainerPackageResource
    {
        $this->authorize('view', $trainerPackage);

        $trainerPackage->load(['package', 'bookings.payment']);

        return new TrainerPackageResource($trainerPackage);
    }

    /**
     * Body: {
     *   "package_id": 2,
     *   "price": 110.00,
     *   "is_active": true
     * }
     */
    public function store(StoreTrainerPackageRequest $request): JsonResponse
    {
        $this->authorize('create', TrainerPackage::class);

        $trainer = auth()->user()->trainerProfile;

        $trainerPackage = TrainerPackage::create([
            'trainer_id' => $trainer->id,
            'package_id' => $request->package_id,
            'price'      => $request->price,
            'is_active'  => $request->boolean('is_active', true),
        ]);

        return response()->json([
            'message' => 'Package added successfully.',
            'data'    => new TrainerPackageResource(
                $trainerPackage->load('package')
            ),
        ], 201);
    }

    /**
     * Update the price of an existing trainer package.
     */
    public function update(UpdateTrainerPackageRequest $request, TrainerPackage $trainerPackage): JsonResponse
    {
        $this->authorize('update', $trainerPackage);

        $trainerPackage->update([
            'price' => $request->price,
        ]);

        return response()->json([
            'message' => 'Package price updated successfully.',
            'data'    => new TrainerPackageResource(
                $trainerPackage->load('package')
            ),
        ]);
    }

    /**
     * Remove a package from the trainer's offering.
     * Cannot delete a package that has active or confirmed bookings
     */
    public function destroy(TrainerPackage $trainerPackage): JsonResponse
    {
        $this->authorize('delete', $trainerPackage);

        $trainerPackage->delete();

        return response()->json([
            'message' => 'Package removed successfully.',
        ]);
    }


    public function toggle(TrainerPackage $trainerPackage): JsonResponse
    {
        $this->authorize('toggleActive', $trainerPackage);

        $trainerPackage->update([
            'is_active' => ! $trainerPackage->is_active,
        ]);

        $state = $trainerPackage->is_active ? 'activated' : 'deactivated';

        return response()->json([
            'message' => "Package {$state} successfully.",
            'data'    => new TrainerPackageResource(
                $trainerPackage->load('package')
            ),
        ]);
    }

    /**
     * List all base packages the trainer has NOT yet added.
     */
    public function available(): JsonResponse
    {
        $this->authorize('viewAny', TrainerPackage::class);

        $trainer = auth()->user()->trainerProfile;

        $addedPackageIds = TrainerPackage::where('trainer_id', $trainer->id)
            ->pluck('package_id');

        $available = Package::whereNotIn('id', $addedPackageIds)->get();

        return response()->json([
            'data' => $available->map(fn ($p) => [
                'id'            => $p->id,
                'title'         => $p->title,
                'description'   => $p->description,
                'sessions'      => $p->sessions === 999 ? 'Unlimited' : $p->sessions,
                'duration_days' => $p->duration_days,
                'features'      => [
                    'progress_tracking' => $p->progress_tracking,
                    'nutrition_plan'    => $p->nutrition_plan,
                    'priority_booking'  => $p->priority_booking,
                    'full_access'       => $p->full_access,
                ],
            ]),
        ]);
    }
}
