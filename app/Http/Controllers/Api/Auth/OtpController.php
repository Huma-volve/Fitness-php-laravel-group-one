<?php

namespace App\Http\Controllers\Api\Auth;
use App\Models\OtpCode;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

class OtpController extends Controller
{

    public function sendOtp(Request $request)
    {
        $code=123456;
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'error'  => $validator->errors(),
            ], 422);
        }

        OtpCode::where('email', $request->email)->delete();

        OtpCode::create([
            'email'      => $request->email,
            'code'       => $code,
            'expires_at' => now()->addMinutes(10),
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'OTP is ready $code ',
        ], 200);
    }

    public function verifyOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'code'  => 'required|string|size:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'error'  => $validator->errors(),
            ], 422);
        }

        $otp = OtpCode::where('email', $request->email)
                      ->where('code', $request->code)
                      ->where('expires_at', '>=', now())
                      ->first();

        if (!$otp) {
            return response()->json([
                'status'  => false,
                'message' => 'Invalid or expired OTP',
            ], 400);
        }

        $otp->is_used = true;
        $otp->save();

        return response()->json([
            'status'  => true,
            'message' => 'OTP verified successfully',
        ], 200);
    }

    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'    => 'required|email|exists:users,email',
            'code'     => 'required|string|size:6',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $otp = OtpCode::where('email', $request->email)
                      ->where('code', $request->code)
                      ->where('is_used', true)
                      ->first();

        if (!$otp) {
            return response()->json([
                'status'  => false,
                'message' => 'Please verify OTP first',
            ], 400);
        }

        User::where('email', $request->email)
            ->update(['password' => Hash::make($request->password)]);

        OtpCode::where('email', $request->email)->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Password reset successfully',
        ]);
    }
}