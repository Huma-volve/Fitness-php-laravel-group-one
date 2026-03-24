<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Web\AdminBookingFilterRequest;
use App\Http\Resources\Admin\AdminBookingResource;
use App\Models\Booking;


class AdminBookingController extends Controller
{

    public function index(AdminBookingFilterRequest $request)
    {
        $query = Booking::with([
            'user',
            'trainer.user',
            'trainerPackage.package',
            'sessions',
            'payment',
        ]);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('trainer_id')) {
            $query->where('trainer_id', $request->trainer_id);
        }

        if ($request->filled('package_id')) {
            $query->whereHas('trainerPackage', fn ($q) =>
            $q->where('package_id', $request->package_id)
            );
        }

        if ($request->filled('date_from')) {
            $query->where('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('created_at', '<=', $request->date_to . ' 23:59:59');
        }

        // Search by user name, user email, or trainer name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', fn ($q) =>
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                )->orWhereHas('trainer.user', fn ($q) =>
                $q->where('name', 'like', "%{$search}%")
                );
            });
        }


        $sortBy  = $request->input('sort_by', 'created_at');
        $sortDir = $request->input('sort_dir', 'desc');
        $query->orderBy($sortBy, $sortDir);

        $bookings = $query->paginate($request->input('per_page', 15));

//        return AdminBookingResource::collection($bookings);
        return view('dashboard.admin.bookings.index', compact('bookings'));
    }

    public function show(Booking $booking)
    {
        $booking->load([
            'user',
            'trainer.user',
            'trainerPackage.package',
            'sessions',
            'payment',
        ]);

       $booking = new AdminBookingResource($booking);
       return view('dashboard.admin.bookings.show', compact('booking'));
    }

    public function stats()
    {
        $byStatus = Booking::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        $byPayment = Booking::selectRaw('payment_status, COUNT(*) as count')
            ->groupBy('payment_status')
            ->pluck('count', 'payment_status');

        $revenue = \App\Models\Payment::where('payment_status', 'paid')
            ->selectRaw('SUM(amount) as total_revenue')
            ->value('total_revenue') ?? 0;

        $platformFees = \App\Models\TrainerPayout::selectRaw('SUM(platform_fee) as total_fees')
            ->value('total_fees') ?? 0;

        $stats =[
            'total'               => Booking::count(),
            'by_status'           => $byStatus,
            'by_payment'          => $byPayment,
            'total_revenue'       => round((float) $revenue, 2),
            'total_platform_fees' => round((float) $platformFees, 2),
        ];
        return view('dashboard.admin.bookings.stats', compact('stats'));

    }
}
