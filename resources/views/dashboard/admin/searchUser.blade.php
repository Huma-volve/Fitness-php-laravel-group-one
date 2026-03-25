@extends('master')

@section('title', 'user Details')

@section("content")

<div class="container mt-4">
    <div class="row">
        
        <div class="col-lg-8 offset-lg-2">
            <div class="card shadow-lg border-0 rounded">
                
                <div class="card-header bg-primary text-white text-center">
                    
                    
                        <img src="{{ asset('dashboard/images/admin.jpg') }}"
                             width="100" height="100"
                             class="rounded-circle border mb-2">
                    

                    <h3 class="mb-0">{{ $user->name }}</h3>
                </div>

                <div class="card-body">
                     <div class="row text-center">

                    
                        <div class="col-md-12 mb-4 text-center">
                            <h5 class="text-muted">Email</h5>
                            <p class="px-3">{{ $user->email ?? 'No email available' }}</p>
                        </div>
                        @if ($user->trainerProfile)
                            <div class="col-md-12 mb-4 text-center">
                                <h5 class="text-muted">Bio</h5>
                                <p class="px-3">{{ $user->trainerProfile->bio ?? 'No bio available' }}</p>
                            </div>

                        
                            <div class="col-md-6 mb-4 text-center">
                                
                            </div>
                        @endif
                        

                    </div>

                    <div class="row text-center">
                        @if ($user->trainerProfile)
                            <div class="col-md-6 mb-3">
                                <div class="p-3 border rounded bg-light">
                                    <h6 class="text-muted">Experience</h6>
                                    <h5>{{ $user->trainerProfile->experience_years }} Years</h5>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="p-3 border rounded bg-light">
                                    <h6 class="text-muted"> location</h6>
                                    <h5 class="px-3">{{ $user->trainerProfile->location ?? 'No workout_location available' }}</h5>
                                </div>
                            </div>

                        @else
                            <div class="col-md-6 mb-4 text-center">
                                <h5 class="text-muted">Fitness Goal</h5>
                                <p class="px-3">{{ $user->fitnessProfile->fitness_goal ?? 'No fitness_goal available' }}</p>
                            </div>
                            <div class="col-md-6 mb-4 text-center">
                                <h5 class="text-muted">Fitness Level</h5>
                                <p class="px-3">{{ $user->fitnessProfile->fitness_level ?? 'No fitness_level available' }}</p>
                            </div>
                        @endif
                    </div>

                    <hr>
                    
                    <div class="mt-3 text-center">
                        <h4 style="text-align: left; margin-top: 10px; margin-bottom: 10px;">Bookkings</h4>
                        <table class=" table-striped table-bordered" style="width: 100%;">
                            @if($user->role == "trainee")

                                <thead>
                                    <tr>
                                        <th>trainer</th>
                                        <th>trainer package</th>
                                        <th>payment status</th>
                                        <th>payment method	</th>
                                        <th>payment</th>
                                        
                                    </tr>
                                </thead>
                                <tbody>
                                   @if (!$user->bookings->isEmpty())
                                    
                                    
                                    @foreach($user->bookings as $book)
                                        <tr>
                                            <td>{{ $book->Trainer->user->name }}</td>
                                            <td>{{ $book->trainerPackage->package->title}}</td>
                                            <td>{{ $book->Payment->payment_status}}</td>
                                            <td>{{ $book->Payment->payment_method}}</td>
                                            <td>{{ $book->Payment->amount}}</td>
                                        </tr>
                                    @endforeach

                                    @else
                                    <tr>
                                        <td colspan="5"> no bookings</td>
                                    </tr>
                                    @endif
                                </tbody>


                                @else


                                <thead>
                                    <tr>
                                        <th>trainee</th>
                                        <th>trainee package</th>
                                        <th>payment</th>
                                        <th>payment method	</th>
                                        <th>payment status</th>
                                        
                                    </tr>
                                </thead>
                                
                                <tbody>
                                    @foreach($user->trainerProfile->bookings as $book)
                                        <tr>
                                            <td>{{ $book->user->name }}</td>
                                            <td>{{ $book->trainerPackage->package->title}}</td>
                                            <td>{{ $book->Payment->amount}}</td>
                                            <td>{{ $book->Payment->payment_method}}</td>
                                            <td>{{ $book->payment->payment_status}}</td>
                                            
                                        </tr>
                                    @endforeach
                                </tbody>


                                @endif
                        </table>
                    </div>

                </div>

                <div class="card-footer text-center">
                    <a href="{{ url()->previous() }}" class="btn btn-secondary px-4">
                        Back
                    </a>
                </div>

            </div>
        </div>

    </div>
</div>

@endsection