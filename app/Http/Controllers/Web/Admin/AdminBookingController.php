<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Web\AdminBookingFilterRequest;
use App\Http\Resources\Admin\AdminBookingResource;
use App\Models\Booking;
use App\Services\AdminBookingService;

class AdminBookingController extends Controller
{
    public function __construct(
        private readonly AdminBookingService $adminBookingService
    ) {}

    public function index(AdminBookingFilterRequest $request)
    {
        $bookings = $this->adminBookingService->getBookings($request);

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


}
