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

        \App\Models\OtpCode::where('email', $user->email)->delete();
        \App\Models\OtpCode::create([
            'email'      => $user->email,
            'code'       => '123456',
            'expires_at' => now()->addMinutes(10),
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Account created successfully. Please verify your email with the OTP sent (123456).',
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

        $user = Auth::user();

        // if (!$user->email_verified_at) {
        //     return response()->json([
        //         'status'  => false,
        //         'message' => 'Please verify your email first'
        //     ], 403);
        // }

        $user->tokens()->delete();
        $token = $user->createToken('auth_token')->plainTextToken;
        return response()->json([
            'status'  => true,
            'message' => 'Logged in successfully',
            'token'   => $token,
            'user'    => $user,
            'is_complete_the_profile' => $user->fitnessProfile ? false : true,
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

    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'new_password'     => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = $request->user();

        if (! Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'status'  => false,
                'message' => 'Current password is incorrect',
            ], 422);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json([
            'status'  => true,
            'message' => 'Password changed successfully',
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