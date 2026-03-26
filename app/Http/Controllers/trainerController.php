<?php

namespace App\Http\Controllers;

use App\Models\Specialization;
use App\Models\Trainer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class trainerController extends Controller
{
    public function index(){
        $trainers=User::where('role','=','trainer')->get();
       return view('trainer.trainer',compact('trainers'));
    }
    public function showdetails($id){
        $trainer=Trainer::with(['user','specializations'])->where('user_id','=',$id)->first();
        
       return view('trainer.showdetails',compact('trainer'));
    }
    public function create(){

    return view('trainer.create');

    }
        public function store(Request $request)
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|max:20',
            'password' => 'required|string|min:6|confirmed',
            'profile_image' => 'nullable|image|mimes:jpg,jpeg,png',
        ]);

        $user = new User();
        $user->name  = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->role  = 'trainer';
        $user->password = Hash::make($request->password);

        
        if($request->hasFile('profile_image')){
            $path = $request->file('profile_image')->store('profile_images','public');
            $user->profile_image = $path;

        }

        $user->save();

    $trainer = new Trainer();
    $trainer->user_id = $user->id;
    $trainer->bio = $request->bio;
    $trainer->experience_years = $request->experience_years;
    $trainer->location = $request->location;
    $trainer->save();
        if(!empty($request->specializations)){
            $specializations=explode(',',$request->specializations);
$specializationsid=[];
            foreach($specializations as $specialization){
                $specialization=trim($specialization);
                if(empty($specialization))
                continue;
                $spes=Specialization::firstOrCreate(['name' => strtolower($specialization)]);
$specializationsid[]=$spes->id;
            }
            $trainer->specializations()->sync($specializationsid);
        }

        return redirect()->route('gettrainer')->with('success','trainer added successfully!');
    }
public function edit($id)
{
    $trainer = Trainer::with('user','specializations')
        ->where('user_id', $id)
        ->firstOrFail();

    return view('trainer.edit', compact('trainer'));
}
public function update(Request $request, $id)
{
    $user = User::findOrFail($id);
    $trainer = Trainer::where('user_id', $id)->first();

    $user->name  = $request->name;
    $user->email = $request->email;
    $user->phone = $request->phone;

    if($request->hasFile('profile_image')){
        $path = $request->file('profile_image')->store('profile_images','public');
        $user->profile_image = $path;
    }

    $user->save();

    $trainer->bio = $request->bio;
    $trainer->experience_years = $request->experience_years;
    $trainer->location = $request->location;
    $trainer->save();

    if(!empty($request->specializations)){
        $specializations = explode(',', $request->specializations);
        $ids = [];

        foreach($specializations as $specialization){
            $specialization = trim($specialization);
            if(empty($specialization)) continue;

            $spes = Specialization::firstOrCreate([
                'name' => strtolower($specialization)
            ]);

            $ids[] = $spes->id;
        }

        $trainer->specializations()->sync($ids);
    }

    return redirect()->route('gettrainer')->with('update','Trainer updated successfully!');
}
    public function delete($id){
           $user = User::findOrFail($id);

    if ($user->profile_image && file_exists(public_path('storage/' . $user->profile_image))) {
        unlink(public_path('storage/' . $user->profile_image));
    }
   

    Trainer::where('user_id', $id)->delete();

   
    $user->delete();
                return redirect()->route('gettrainer')->with('delete','trainer delete successfully!');

    }
}