@extends('master')

@section('title', 'All Bookings')

@section("content")

        <div class="breadcrumbs">
            <div class="breadcrumbs-inner">
                <div class="row m-0">
                    <div class="col-sm-4">
                        <div class="page-header float-left">
                            <div class="page-title">
                                <h1>Bookings</h1>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-8">
                        <div class="page-header float-right">
                            <div class="page-title">
                                <ol class="breadcrumb text-right">
                                    <li><a href="#">Dashboard</a></li>
                                    <li class="active">Bookings</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="content">
            <div class="animated fadeIn">
                <div class="row">

                    <div class="col-md-12">
                        <div class="card">

                            <div class="card-header">
                                <strong class="card-title">Bookings Table</strong>
                            </div>

                            <div class="card-body">
                                <form method="GET" action="{{ route('admin.bookings.index') }}" class="mb-4">
                                    <div class="row">

                                        {{-- Search --}}
                                        <div class="col-md-3">
                                            <input type="text" name="search" class="form-control"
                                                   placeholder="Search name or email"
                                                   value="{{ request('search') }}">
                                        </div>

                                        {{-- Status --}}
                                        <div class="col-md-2">
                                            <select name="status" class="form-control">
                                                <option value="">All Status</option>
                                                <option value="confirmed" {{ request('status')=='confirmed' ? 'selected' : '' }}>Confirmed</option>
                                                <option value="pending" {{ request('status')=='pending' ? 'selected' : '' }}>Pending</option>
                                                <option value="canceled" {{ request('status')=='canceled' ? 'selected' : '' }}>Canceled</option>
                                            </select>
                                        </div>

                                        {{-- Payment --}}
                                        <div class="col-md-2">
                                            <select name="payment_status" class="form-control">
                                                <option value="">All Payments</option>
                                                <option value="paid" {{ request('payment_status')=='paid' ? 'selected' : '' }}>Paid</option>
                                                <option value="pending" {{ request('payment_status')=='pending' ? 'selected' : '' }}>Pending</option>
                                                <option value="failed" {{ request('payment_status')=='failed' ? 'selected' : '' }}>Failed</option>
                                            </select>
                                        </div>

                                        {{-- Date From --}}
                                        <div class="col-md-2">
                                            <input type="date" name="date_from" class="form-control"
                                                   value="{{ request('date_from') }}">
                                        </div>

                                        {{-- Date To --}}
                                        <div class="col-md-2">
                                            <input type="date" name="date_to" class="form-control"
                                                   value="{{ request('date_to') }}">
                                        </div>

                                        {{-- Submit --}}
                                        <div class="col-md-1">
                                            <button class="btn btn-primary w-100">Go</button>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-2">
                                            <a href="{{ route('admin.bookings.index') }}" class="btn btn-secondary">Reset</a>
                                        </div>
                                    </div>
                                </form>

                                <table id="bootstrap-data-table" class="table table-striped table-bordered">

                                    <thead>
                                    <tr>
                                        <th>Number</th>
                                        <th>Trainee</th>
                                        <th>Trainer</th>
                                        <th>Package</th>
                                        <th>Status</th>
                                        <th>Payment</th>
                                        <th>Sessions</th>
                                        <th>Amount</th>
                                        <th>
                                            <a href="{{ route('admin.bookings.index', array_merge(request()->query(), [
                                                    'sort_by' => 'created_at',
                                                    'sort_dir' => request('sort_dir') == 'asc' ? 'desc' : 'asc'
                                                ])) }}">
                                                Created At
                                            </a>
                                        </th>
                                        <th>Actions</th>
                                    </tr>
                                    </thead>

                                    <tbody>
                                    @forelse($bookings as $booking)
                                        <tr>

                                            {{-- number --}}
                                            <td>{{ $loop->iteration + ($bookings->firstItem() - 1) }}</td>

                                            {{-- Trainee --}}
                                            <td>
                                                {{ $booking->user->name ?? '-' }} <br>
                                                <small>{{ $booking->user->email ?? '' }}</small>
                                            </td>

                                            {{-- Trainer --}}
                                            <td>
                                                {{ $booking->trainer->user->name ?? '-' }}
                                            </td>

                                            {{-- Package --}}
                                            <td>
                                                {{ $booking->trainerPackage->package->title ?? '-' }} <br>
                                                <small>{{ $booking->trainerPackage->price ?? 0 }} EGP</small>
                                            </td>

                                            {{-- Status --}}
                                            <td>
                                            <span class="badge
                                                @if($booking->status == 'confirmed') badge-success
                                                @elseif($booking->status == 'pending') badge-warning
                                                @elseif($booking->status == 'canceled') badge-danger
                                                @else badge-secondary
                                                @endif">
                                                {{ $booking->status }}
                                            </span>
                                            </td>

                                            {{-- Payment --}}
                                            <td>
                                            <span class="badge
                                                @if($booking->payment_status == 'paid') badge-success
                                                @elseif($booking->payment_status == 'pending') badge-warning
                                                @elseif($booking->payment_status == 'failed') badge-danger
                                                @else badge-secondary
                                                @endif">
                                                {{ $booking->payment_status }}
                                            </span>
                                            </td>

                                            {{-- Sessions --}}
                                            <td>
                                                {{ $booking->sessions->count() }}
                                            </td>

                                            {{-- Amount --}}
                                            <td>
                                                {{ $booking->payment->amount ?? 0 }} EGP
                                            </td>

                                            {{-- Created --}}
                                            <td>
                                                {{ $booking->created_at }}
                                            </td>

                                            {{-- Actions --}}
                                            <td>
                                                <a href="{{ route('admin.bookings.show', $booking->id) }}"
                                                   class="btn btn-sm btn-info">
                                                    View
                                                </a>
                                            </td>

                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="10" class="text-center">
                                                No bookings found
                                            </td>
                                        </tr>
                                    @endforelse
                                    </tbody>

                                </table>

                                <div class="mt-3">
                                    {{ $bookings->appends(request()->query())->links() }}
                                </div>

                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>


@endsection
