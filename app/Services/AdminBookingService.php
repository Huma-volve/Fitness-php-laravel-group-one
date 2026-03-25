<?php

namespace App\Services;

use App\Models\Booking;

class AdminBookingService
{
    public function getBookings($request)
    {
        $query = Booking::with([
            'user',
            'trainer.user',
            'trainerPackage.package',
            'sessions',
            'payment',
        ]);
        $query = $this->filterBookings($request, $query);
        $query = $this->searchBookings($request, $query);

        $sortBy  = $request->input('sort_by', 'created_at');
        $sortDir = $request->input('sort_dir', 'desc');
        $query->orderBy($sortBy, $sortDir);

        $bookings = $query->paginate($request->input('per_page', 15));

        return $bookings;
    }

    private function filterBookings($request , $query){
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

        return $query;
    }
    private function searchBookings($request , $query){
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
        return $query;
    }

}
