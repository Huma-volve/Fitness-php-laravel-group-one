@extends('master')

@section('title', 'Trainer Details')

@section("content")

<div class="container mt-4">
    <div class="row">
        
        <div class="col-lg-8 offset-lg-2">
            <div class="card shadow-lg border-0 rounded">
                
                <div class="card-header bg-primary text-white text-center">
                    
                    @if(!empty($trainer->user->profile_image))
                        <img src="{{ asset('storage/' . $trainer->user->profile_image) }}"
                             width="100" height="100"
                             class="rounded-circle border mb-2">
                    @else
                        <img src="{{ asset('dashboard/images/admin.jpg') }}"
                             width="100" height="100"
                             class="rounded-circle border mb-2">
                    @endif

                    <h3 class="mb-0">{{ $trainer->user->name }}</h3>
                </div>

                <div class="card-body">

                    <div class="mb-4 text-center">
                        <h5 class="text-muted">Bio</h5>
                        <p class="px-3">{{ $trainer->bio ?? 'No bio available' }}</p>
                    </div>

                    <div class="row text-center">
                        <div class="col-md-6 mb-3">
                            <div class="p-3 border rounded bg-light">
                                <h6 class="text-muted">Experience</h6>
                                <h5>{{ $trainer->experience_years }} Years</h5>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <div class="p-3 border rounded bg-light">
                                <h6 class="text-muted">Location</h6>
                                <h5>{{ $trainer->location }}</h5>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="mt-3 text-center">
                        <h5>Specializations</h5>

                        @if($trainer->specializations->count() > 0)
                            @foreach($trainer->specializations as $spec)
                                <span class="badge bg-success p-2 m-1 fs-6">
                                    {{ $spec->name }}
                                </span>
                            @endforeach
                        @else
                            <p class="text-muted">No specializations added</p>
                        @endif
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