@extends('master')

@section('title', 'Booking')

@section('content')

    <div class="breadcrumbs">
        <div class="breadcrumbs-inner">
            <div class="row m-0">
                <div class="col-sm-6">
                    <h1>Booking Details</h1>
                </div>
                <div class="col-sm-6 text-right">
                    <a href="{{ route('admin.bookings.index') }}" class="btn btn-secondary">
                        Back
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="row">

            {{-- Booking Info --}}
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header"><strong>Booking Info</strong></div>
                    <div class="card-body">

                        <p><strong>ID:</strong> #{{ $booking->id }}</p>

                        <p><strong>Status:</strong>
                            <span class="badge
                            @if($booking->status == 'confirmed') badge-success
                            @elseif($booking->status == 'pending') badge-warning
                            @elseif($booking->status == 'canceled') badge-danger
                            @else badge-secondary
                            @endif">
                            {{ $booking->status }}
                        </span>
                        </p>

                        <p><strong>Payment Status:</strong>
                            <span class="badge
                            @if($booking->payment_status == 'paid') badge-success
                            @elseif($booking->payment_status == 'pending') badge-warning
                            @elseif($booking->payment_status == 'failed') badge-danger
                            @else badge-secondary
                            @endif">
                            {{ $booking->payment_status }}
                        </span>
                        </p>

                        <p><strong>Created At:</strong> {{ $booking->created_at }}</p>
                        <p><strong>Cancellation Deadline:</strong> {{ $booking->cancellation_deadline ?? '-' }}</p>
                        <p><strong>Cancelled At:</strong> {{ $booking->cancelled_at ?? '-' }}</p>
                        <p><strong>Cancel Reason:</strong> {{ $booking->cancel_reason ?? '-' }}</p>

                    </div>
                </div>
            </div>

            {{-- Trainee --}}
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header"><strong>Trainee</strong></div>
                    <div class="card-body">

                        <p><strong>Name:</strong> {{ $booking->user->name ?? '-' }}</p>
                        <p><strong>Email:</strong> {{ $booking->user->email ?? '-' }}</p>
                        <p><strong>Phone:</strong> {{ $booking->user->phone ?? '-' }}</p>

                        @if($booking->user && $booking->user->profile_image)
                            <img src="{{ asset($booking->user->profile_image) }}" width="80">
                        @endif

                    </div>
                </div>
            </div>

            {{-- Trainer --}}
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header"><strong>Trainer</strong></div>
                    <div class="card-body">

                        <p><strong>Name:</strong> {{ optional($booking->trainer->user)->name }}</p>
                        <p><strong>Email:</strong> {{ optional($booking->trainer->user)->email }}</p>
                        <p><strong>Experience:</strong> {{ $booking->trainer->experience_years ?? '-' }} years</p>
                        <p><strong>Rating:</strong> {{ $booking->trainer->rating ?? '-' }}</p>
                        <p><strong>Location:</strong> {{ $booking->trainer->location ?? '-' }}</p>

                    </div>
                </div>
            </div>

            {{-- Package --}}
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header"><strong>Package</strong></div>
                    <div class="card-body">

                        <p><strong>Title:</strong> {{ $booking->trainerPackage->package->title ?? '-' }}</p>
                        <p><strong>Price:</strong> {{ $booking->trainerPackage->price ?? 0 }} EGP</p>
                        <p><strong>Description:</strong> {{ $booking->trainerPackage->package->description ?? '-' }}</p>

                        <p><strong>Sessions:</strong>
                            {{ $booking->trainerPackage->package->sessions == 999 ? 'Unlimited' : $booking->trainerPackage->package->sessions }}
                        </p>

                        <p><strong>Duration:</strong> {{ $booking->trainerPackage->package->duration_days ?? '-' }} days</p>

                    </div>
                </div>
            </div>

            {{-- Sessions --}}
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header"><strong>Sessions</strong></div>
                    <div class="card-body">

                        <table class="table table-bordered">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Start</th>
                                <th>End</th>
                                <th>Status</th>
                                <th>Notes</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($booking->sessions as $session)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $session->session_start }}</td>
                                    <td>{{ $session->session_end }}</td>
                                    <td>{{ $session->session_status }}</td>
                                    <td>{{ $session->notes ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">No sessions</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>

                    </div>
                </div>
            </div>

            {{-- Payment --}}
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header"><strong>Payment</strong></div>
                    <div class="card-body">

                        @if($booking->payment)
                            <p><strong>Amount:</strong> {{ $booking->payment->amount }} EGP</p>
                            <p><strong>Method:</strong> {{ $booking->payment->payment_method }}</p>
                            <p><strong>Status:</strong> {{ $booking->payment->payment_status }}</p>
                            <p><strong>Transaction ID:</strong> {{ $booking->payment->transaction_id }}</p>
                            <p><strong>Gateway Ref:</strong> {{ $booking->payment->gateway_reference }}</p>
                            <p><strong>Date:</strong> {{ $booking->payment->created_at }}</p>
                        @else
                            <p>No payment data</p>
                        @endif

                    </div>
                </div>
            </div>

        </div>
    </div>

@endsection
