<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PackageResource;
use App\Http\Resources\TrainerPackageResource;
use App\Models\Package;
use App\Models\TrainerPackage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PackageController extends Controller
{
    public function index()
    {
        $packages = Package::all();

        return PackageResource::collection($packages);
    }

    public function show(Package $package): PackageResource
    {
        return new PackageResource($package);
    }

    public function trainers(Package $package)
    {
        $trainerPackages = TrainerPackage::with(['trainer.user', 'trainer.specializations', 'package'])
            ->where('package_id', $package->id)
            ->where('is_active', true)
            ->get();

        return TrainerPackageResource::collection($trainerPackages);
    }
}
