<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Models\TraineeSession;
use App\Models\Trainer;
use App\Models\User;

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


    public function trainers()
    {
        $data = Trainer::with('user')
            ->orderByDesc('rating')
            ->limit(3)
            ->get();

        return $this->successResponse($data);
    }

    public function packages()
    {
        $data = Package::limit(3)->get();
        return $this->successResponse($data);
    }


    private function successResponse($data, $status = true)
    {
        return response()->json([
            'status' => $status,
            'data'   => $data
        ]);
    }
}
