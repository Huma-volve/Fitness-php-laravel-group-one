<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TrainerDetailsResource;
use App\Models\Package;
use App\Models\TraineeSession;
use App\Models\Trainer;
use App\Models\User;
use App\Http\Resources\PackageResource;

class LandingController extends Controller
{

    public function stats()
    {
        $data = [
            'trainers' => Trainer::count(),
            'clients'  => User::where('role', 'trainee')->count(),
            'sessions' => TraineeSession::count(),
        ];

        return $this->successResponse($data);
    }


    public function trainers(Trainer $trainer)
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

    public function packages()
    {
        $packages = Package::limit(3)->get();

        return $this->successResponse(PackageResource::collection($packages));
    }


    private function successResponse($data, $status = true)
    {
        return response()->json([
            'status' => $status,
            'data'   => $data
        ]);
    }
}
