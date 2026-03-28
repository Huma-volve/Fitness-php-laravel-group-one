<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FitnessProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function profile(Request $request)
    {
        $user = $request->user(); // جلب المستخدم المسجل الدخول

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'membership_date' => $user->created_at->format('Y-m-d'),
            'profile_image' => $user->profile_image ? asset('storage/' . $user->profile_image) : null,
            'about_me' => $user->about_me,
            'fitness_goals' => $user->fitness_goals,
            'preferred_training' => $user->preferred_training,
        ]);
    }


    public function update(Request $request)
    {
        $user = $request->user();

        // Validation للبيانات
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'about_me' => 'nullable|string|max:1000',
            'fitness_goals' => 'nullable|string|max:255',
            'preferred_training' => 'nullable|string|max:255',
        ]);

        // تحديث بيانات المستخدم
        $user->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'about_me' => $user->about_me,
                'fitness_goals' => $user->fitness_goals,
                'preferred_training' => $user->preferred_training,
                'profile_image' => $user->profile_image ? asset('storage/' . $user->profile_image) : null,
            ]
        ]);
    }

    public function uploadImage(Request $request)
    {
        $user = $request->user();

        // Validation للصورة
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // حجم الصورة لا يزيد عن 2MB
        ]);

        // حذف الصورة القديمة إذا وجدت
        if ($user->profile_image && Storage::exists($user->profile_image)) {
            Storage::delete($user->profile_image);
        }

        // رفع الصورة الجديدة
        $path = $request->file('image')->store('profiles', 'public');

        // حفظ المسار في قاعدة البيانات
        $user->profile_image = $path;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Profile image uploaded successfully',
            'profile_image' => asset('storage/' . $path)
        ]);
    }
    public function removeImage(Request $request)
    {
        $user = $request->user();

        if ($user->profile_image && Storage::disk('public')->exists($user->profile_image)) {
            Storage::disk('public')->delete($user->profile_image);
            $user->profile_image = null;
            $user->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'Profile image removed successfully',
            'profile_image' => null
        ]);
    }
    public function storeFitnessProfile(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'gender' => 'required|string|in:male,female',
            'age' => 'required|integer|min:10|max:100',
            'height_cm' => 'required|integer|min:100|max:250',
            'weight_kg' => 'required|numeric|min:30|max:300',
            'fitness_goal' => 'required|string|max:255',
            'fitness_level' => 'required|string|max:255',
            'workout_location' => 'required|string|max:255',
            'preferred_training_days' => 'required|integer|min:1|max:7'
        ]);

        $fitnessProfile = FitnessProfile::updateOrCreate(
            ['user_id' => $user->id],
            $validated
        );

        return response()->json([
            'success' => true,
            'message' => 'Fitness profile saved successfully',
            'data' => $fitnessProfile
        ]);
    }
    public function upcomingSessions(Request $request)
    {
        $user = $request->user();

        // جلب الجلسات القادمة للمتدرب فقط
        $sessions = DB::table('trainee_sessions')
            ->where('client_id', $user->id)
            ->where('session_start', '>=', now()) // فقط الجلسات المستقبلية
            ->orderBy('session_start', 'asc')
            ->get([
                'id',
                'booking_id',
                'trainer_id',
                'session_start',
                'session_end',
                'session_status',
                'notes'
            ]);

        return response()->json([
            'success' => true,
            'sessions' => $sessions
        ]);
    }
    public function currentPackages(Request $request)
    {
        $user = $request->user();

        $packages = DB::table('bookings')
            ->join('trainer_packages', 'bookings.trainer_package_id', '=', 'trainer_packages.id')
            ->join('packages', 'trainer_packages.package_id', '=', 'packages.id')
            ->where('bookings.user_id', $user->id)
            ->where('trainer_packages.is_active', true)
            ->get([
                'bookings.id as booking_id',
                'bookings.trainer_id',

                'packages.title',
                'packages.description',
                'packages.sessions',
                'packages.duration_days',
                'trainer_packages.price',
                'trainer_packages.is_active'
            ]);

        return response()->json([
            'success' => true,
            'packages' => $packages
        ]);
    }

    public function progressActivity(Request $request)
    {
        $user = $request->user();

        $completed = DB::table('trainee_sessions')
            ->where('client_id', $user->id)
            ->where('session_status', 'completed')
            ->count();

        $upcoming = DB::table('trainee_sessions')
            ->where('client_id', $user->id)
            ->where('session_start', '>', now())
            ->count();

        $cancelled = DB::table('trainee_sessions')
            ->where('client_id', $user->id)
            ->where('session_status', 'cancelled')
            ->count();

        return response()->json([
            'success' => true,
            'progress' => [
                'completed_sessions' => $completed,
                'upcoming_sessions' => $upcoming,
                'cancelled_sessions' => $cancelled
            ]
        ]);
    }
    public function workoutHistory(Request $request)
    {
        $user = $request->user();

        $history = DB::table('trainee_sessions')
            ->where('client_id', $user->id)
            ->where('session_start', '<', now())
            ->orderBy('session_start', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'history' => $history
        ]);
    }
    public function paymentMethods(Request $request)
    {
        $user = $request->user();

        $methods = DB::table('payments')
            ->join('bookings', 'payments.booking_id', '=', 'bookings.id')
            ->where('bookings.user_id', $user->id)  // ✅ هنا استخدم user_id بدل client_id
            ->get();

        return response()->json([
            'success' => true,
            'payment_methods' => $methods
        ]);
    }
    public function addPaymentMethod(Request $request)
    {
        $user = $request->user();

        $methods = DB::table('payments')
            ->join('bookings', 'payments.booking_id', '=', 'bookings.id')
            ->where('bookings.user_id', $user->id)
            ->select(
                'payments.id',
                'payments.amount',
                'payments.payment_method',
                'payments.payment_status',
                'payments.transaction_id',
                'payments.created_at'
            )
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Card added successfully'
        ]);
    }
}
