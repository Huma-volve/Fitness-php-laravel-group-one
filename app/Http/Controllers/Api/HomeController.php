<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\TrainerFilterRequest;
use App\Http\Resources\TrainerResource;
use App\Http\Resources\TrainerDetailsResource;

use App\Models\Trainer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class HomeController extends Controller
{
    /**
     * GET /api/trainers
     * Home page: paginated list of trainers with filtering and sorting.
     */
    public function index(TrainerFilterRequest $request): AnonymousResourceCollection
    {
        $query = Trainer::query()
            ->with([
                'user:id,name,profile_image',
                'specializations:id,name',
                'trainerPackages' => fn($q) => $q->where('is_active', true)->select('id', 'trainer_id', 'price'),
            ])
            ->whereHas('user', fn($q) => $q->where('status', 'active'));

        // ── Filters ──────────────────────────────────────────────────────────────

        if ($request->filled('location')) {
            $query->where('location', 'like', '%' . $request->location . '%');
        }

        if ($request->filled('specialization_id')) {
            $query->whereHas('specializations', fn($q) => $q->where('specializations.id', $request->specialization_id));
        }

        if ($request->filled('min_rating')) {
            $query->where('rating', '>=', $request->min_rating);
        }

        if ($request->filled('min_price') || $request->filled('max_price')) {
            $query->whereHas('trainerPackages', function ($q) use ($request) {
                $q->where('is_active', true);
                if ($request->filled('min_price')) {
                    $q->where('price', '>=', $request->min_price);
                }
                if ($request->filled('max_price')) {
                    $q->where('price', '<=', $request->max_price);
                }
            });
        }

        // ── Sorting ───────────────────────────────────────────────────────────────

        $sortDir = $request->input('sort_dir', 'desc');

        match ($request->input('sort_by', 'rating')) {
            'price'            => $query->withMin(['trainerPackages' => fn($q) => $q->where('is_active', true)], 'price')
                ->orderBy('trainer_packages_min_price', $sortDir),
            'experience_years' => $query->orderBy('experience_years', $sortDir),
            default            => $query->orderBy('rating', $sortDir),
        };

        $perPage = $request->input('per_page', 12);

        return TrainerResource::collection($query->paginate($perPage));
    }


    public function showTrainer(Trainer $trainer): JsonResponse
    {
        $trainer->load([
            'user:id,name,profile_image,status',
            'specializations:id,name',
            'certifications',
            'availability',
            'availabilityExceptions',
            'activeTrainerPackages.package',
        ]);

        abort_if(!$trainer->user || $trainer->user->status !== 'active', 404, 'Trainer not found.');

        return response()->json([
            'status' => true,
            'data' => new TrainerDetailsResource($trainer),
        ]);
    }
}
