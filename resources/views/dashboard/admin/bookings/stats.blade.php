@extends('master')

@section('title', 'Stats')
@section('content')

    <div class="content">
        <div class="animated fadeIn">

            {{-- Title --}}
            <div class="mb-4">
                <h2>📊 Booking Statistics</h2>
            </div>

            {{-- Top Cards --}}
            <div class="row">

                {{-- Total Bookings --}}
                <div class="col-md-3">
                    <div class="card text-white bg-primary">
                        <div class="card-body">
                            <h5>Total Bookings</h5>
                            <h2>{{ $stats['total'] }}</h2>
                        </div>
                    </div>
                </div>

                {{-- Revenue --}}
                <div class="col-md-3">
                    <div class="card text-white bg-success">
                        <div class="card-body">
                            <h5>Total Revenue</h5>
                            <h2>{{ $stats['total_revenue'] }} EGP</h2>
                        </div>
                    </div>
                </div>

                {{-- Platform Fees --}}
                <div class="col-md-3">
                    <div class="card text-white bg-dark">
                        <div class="card-body">
                            <h5>Platform Fees</h5>
                            <h2>{{ $stats['total_platform_fees'] }} EGP</h2>
                        </div>
                    </div>
                </div>

                {{-- Paid Bookings --}}
                <div class="col-md-3">
                    <div class="card text-white bg-info">
                        <div class="card-body">
                            <h5>Paid Bookings</h5>
                            <h2>{{ $stats['by_payment']['paid'] ?? 0 }}</h2>
                        </div>
                    </div>
                </div>

            </div>

            {{-- Status + Payment Breakdown --}}
            <div class="row mt-4">

                {{-- Booking Status --}}
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <strong>Booking Status</strong>
                        </div>
                        <div class="card-body">

                            @foreach($stats['by_status'] as $status => $count)
                                @php
                                    $percent = ($count / $stats['total']) * 100;
                                @endphp

                                <div class="mb-3">
                                    <strong>{{ ucfirst($status) }} ({{ $count }})</strong>

                                    <div class="progress">
                                        <div class="progress-bar
                                        @if($status == 'confirmed') bg-success
                                        @elseif($status == 'pending') bg-warning
                                        @elseif($status == 'canceled') bg-danger
                                        @else bg-secondary
                                        @endif"
                                             style="width: {{ $percent }}%">
                                            {{ round($percent) }}%
                                        </div>
                                    </div>
                                </div>
                            @endforeach

                        </div>
                    </div>
                </div>

                {{-- Payment Status --}}
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <strong>Payment Status</strong>
                        </div>
                        <div class="card-body">

                            @foreach($stats['by_payment'] as $status => $count)
                                @php
                                    $percent = ($count / $stats['total']) * 100;
                                @endphp

                                <div class="mb-3">
                                    <strong>{{ ucfirst($status) }} ({{ $count }})</strong>

                                    <div class="progress">
                                        <div class="progress-bar
                                        @if($status == 'paid') bg-success
                                        @elseif($status == 'pending') bg-warning
                                        @elseif($status == 'failed') bg-danger
                                        @else bg-secondary
                                        @endif"
                                             style="width: {{ $percent }}%">
                                            {{ round($percent) }}%
                                        </div>
                                    </div>
                                </div>
                            @endforeach

                        </div>
                    </div>
                </div>

            </div>

            {{-- Extra Insights --}}
            <div class="row mt-4">

                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <strong>Insights</strong>
                        </div>
                        <div class="card-body">

                            <p>✅ Confirmed Bookings:
                                <strong>{{ $stats['by_status']['confirmed'] ?? 0 }}</strong>
                            </p>

                            <p>❌ Canceled Bookings:
                                <strong>{{ $stats['by_status']['canceled'] ?? 0 }}</strong>
                            </p>

                            <p>💰 Paid vs Pending:
                                <strong>
                                    {{ $stats['by_payment']['paid'] ?? 0 }} /
                                    {{ $stats['by_payment']['pending'] ?? 0 }}
                                </strong>
                            </p>

                            <p>📈 Conversion Rate:
                                @php
                                    $confirmed = $stats['by_status']['confirmed'] ?? 0;
                                    $total = $stats['total'] ?: 1;
                                    $rate = ($confirmed / $total) * 100;
                                @endphp
                                <strong>{{ round($rate) }}%</strong>
                            </p>

                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>

@endsection
