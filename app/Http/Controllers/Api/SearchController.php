<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Trainer;
use App\Models\Specialization;
use App\Models\SearchHistory;
use App\Models\User;
use App\Models\TraineeSession;

class SearchController extends Controller
{
    public function index(){
        $user_id = auth()->id();

//        
        $search_histories=SearchHistory::where('user_id',$user_id)->get();

        return response()->json([
            'data' => $search_histories
        ]);
    }
    public function search( Request $request){
        $valdiate = $request->validate([
            'search_value' => 'required',
        ]);

          $user_id = auth()->user()->id;

        

        $dataSearch = User::query()->where('name', 'like',"%{$request->search_value}%")
                    ->where('role', "trainer")
                    ->with(['trainerProfile.specializations','trainerProfile.availability' ,'trainerProfile.trainerPackages.package','trainerProfile.sessions'])
                    ->limit('10')->get();

       if ($dataSearch->isEmpty()){
            $dataSearch = Specialization::query()->where('name', 'like',"%{$request->search_value}%")
                    ->with(['trainers.user','trainers.availability' ,'trainers.trainerPackages.package','trainers.sessions'])
                    ->limit('10')->get();
        };

        
        $dataSearchSave = [
            'user_id' => $user_id,
            'search_text' => $request->search_value,
        ];

        
        SearchHistory::create($dataSearchSave);
        
        
        return response()->json([
            'status' => true,
            'data' => $dataSearch
        ]);
    }


    public function searchFilter(Request $request)
{
    $request->validate([
        'durationId' => 'nullable|integer|in:1,2,3,4,5',
        'specializationId' => 'nullable|integer|exists:specializations,id',
    ]);

    $durationRanges = [
        2 => ['min' => 10, 'max' => 20],
        3 => ['min' => 20, 'max' => 30],
        4 => ['min' => 30, 'max' => 45],
        5 => ['min' => 45, 'max' => 999999],
    ];

    $query = Trainer::query()
        ->select('trainers.*')
        ->with([
            'user:id,name,email,profile_image',
            'specializations:id,name',
            'availability',
        ]);

    // Join with trainer_specializations if specialization filter is applied
    if ($request->filled('specializationId')) {
        $query->join('trainer_specializations', 'trainers.id', '=', 'trainer_specializations.trainer_id')
              ->where('trainer_specializations.specialization_id', $request->specializationId);
    }

    // Join with trainee_sessions if duration filter is applied
    if ($request->filled('durationId') && $request->durationId != 1) {
        $range = $durationRanges[$request->durationId];
        
        $query->join('trainee_sessions', 'trainers.id', '=', 'trainee_sessions.trainer_id')
              ->whereRaw('TIMESTAMPDIFF(MINUTE, trainee_sessions.session_start, trainee_sessions.session_end) BETWEEN ? AND ?', 
                  [$range['min'], $range['max']]);
    }

    // Remove duplicates if multiple joins
    $query->distinct();

    $trainers = $query->get();

    return response()->json([
        'status' => 'success',
        'total' => $trainers->count(),
        'data' => $trainers
    ]);
}
}
