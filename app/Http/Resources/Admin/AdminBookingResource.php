<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdminBookingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                    => $this->id,
            'status'                => $this->status,
            'payment_status'        => $this->payment_status,
            'cancellation_deadline' => $this->cancellation_deadline?->toDateTimeString(),
            'cancelled_at'          => $this->cancelled_at?->toDateTimeString(),
            'cancel_reason'         => $this->cancel_reason,
            'created_at'            => $this->created_at?->toDateTimeString(),


            'user' => $this->whenLoaded('user', fn () => [
                'id'            => $this->user->id,
                'name'          => $this->user->name,
                'email'         => $this->user->email,
                'phone'         => $this->user->phone,
                'profile_image' => $this->user->profile_image,
            ]),

            'trainer' => $this->whenLoaded('trainer', fn () => [
                'id'               => $this->trainer->id,
                'name'             => $this->trainer->user->name,
                'email'            => $this->trainer->user->email,
                'profile_image'    => $this->trainer->user->profile_image,
                'experience_years' => $this->trainer->experience_years,
                'rating'           => $this->trainer->rating,
                'location'         => $this->trainer->location,
            ]),


            'package' => $this->whenLoaded('trainerPackage', fn () => [
                'trainer_package_id' => $this->trainerPackage->id,
                'price'              => $this->trainerPackage->price,
                'title'              => $this->trainerPackage->package->title,
                'description'        => $this->trainerPackage->package->description,
                'sessions'           => $this->trainerPackage->package->sessions === 999
                    ? 'Unlimited'
                    : $this->trainerPackage->package->sessions,
                'duration_days'      => $this->trainerPackage->package->duration_days,
                'features'           => [
                    'progress_tracking' => $this->trainerPackage->package->progress_tracking,
                    'nutrition_plan'    => $this->trainerPackage->package->nutrition_plan,
                    'priority_booking'  => $this->trainerPackage->package->priority_booking,
                    'full_access'       => $this->trainerPackage->package->full_access,
                ],
            ]),


            'sessions' => $this->whenLoaded('sessions', fn () =>
            $this->sessions->map(fn ($session) => [
                'id'             => $session->id,
                'session_start'  => $session->session_start?->toDateTimeString(),
                'session_end'    => $session->session_end?->toDateTimeString(),
                'session_status' => $session->session_status,
                'notes'          => $session->notes,
            ])
            ),


            'payment' => $this->whenLoaded('payment', fn () => $this->payment ? [
                'id'                 => $this->payment->id,
                'amount'             => $this->payment->amount,
                'payment_method'     => $this->payment->payment_method,
                'payment_status'     => $this->payment->payment_status,
                'transaction_id'     => $this->payment->transaction_id,
                'gateway_reference'  => $this->payment->gateway_reference,
                'created_at'         => $this->payment->created_at?->toDateTimeString(),
            ] : null),
        ];
    }
}
