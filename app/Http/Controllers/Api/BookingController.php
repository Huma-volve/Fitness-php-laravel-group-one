<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TrainerPackage;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function show_trainer_package(Request $request){
        $request->validate([
            'trainer_id' => 'required|exists:trainer,id',
        ]);

        $packages = TrainerPackage::where('trainer_id' , $request->validated['trainer_id'])->get();

        return response()->json($packages);
    }
    public function show_available_time_for_trainee_package()
    {

    }
}
