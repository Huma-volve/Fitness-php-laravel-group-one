<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;

class BookingStateController extends Controller
{
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
