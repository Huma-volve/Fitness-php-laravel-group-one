<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;
class AuthController extends Controller
{
    
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'phone'    => 'nullable|string',
            'role'     => 'in:trainer,trainee',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'errors'  => $validator->errors()
            ], 422);
        }

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'phone'    => $request->phone,
            'role'     => $request->role ?? 'trainee',
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status'  => true,
            'message' => 'Account created successfully',
            'token'   => $token,
            'user'    => $user
        ], 201);
    }

   
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'status'  => false,
                'message' => 'Invalid credentials'
            ], 401);
        }
     

        $user  = Auth::user();
           $user->tokens()->delete();
        $token = $user->createToken('auth_token')->plainTextToken;
        return response()->json([
            'status'  => true,
            'message' => 'Logged in successfully',
            'token'   => $token,
            'user'    => $user
        ]);
    }

    public function logout(Request $request)
    {
      $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Logged out successfully'
        ]);
    }

  
    public function deleteAccount(Request $request)
    {
        $user = $request->user();
        $user->tokens()->delete();
        $user->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Account deleted successfully'
        ]);
    }

    
    public function profile(Request $request)
    {
        return response()->json([
            'status' => true,
            'user'   => $request->user()
        ]);
    }
    public function googleRedirect(){
          $url=Socialite::driver('google')->stateless()->redirect()->getTargetUrl();
     return response()->json([
            'url' => $url
        ], 200);
    }
    public function googleCallback(){
$google_user = Socialite::driver('google')->stateless()->user();

   $user=User::firstOrCreate(['email'=>$google_user->getEmail()],[
    'name'=>$google_user->getName(),
    'email'=>$google_user->getEmail(),
    'password'=>bcrypt(str()->random(10)),
    'role'=>'trainee',
    'status'=>'active'
   ]);
  
   $token=$user->createToken('auth_token')->plainTextToken;

   return response()->json([
        'access_token' => $token,
        'user' => $user
    ]);
    }
}