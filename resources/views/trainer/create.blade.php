@extends('master')

@section('title', 'Add Trainee')

@section('content')
<div class="container mt-4">
    <div class="row">
        <div class="col-lg-6 offset-lg-3">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4>Add New Trainee</h4>
                </div>
@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
                <div class="card-body">
                  

                    <form action="{{route('trainees.store')}}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-3">
                            <label>Name</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label>Phone</label>
                            <input type="text" name="phone" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label>Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label>Confirm Password</label>
                            <input type="password" name="password_confirmation" class="form-control" required>
                        </div>
                        <div class="mb-3">
    <label>Specializations (comma separated)</label>
    <input type="text" name="specializations" class="form-control" placeholder="e.g. Yoga, Weightlifting">
    <small class="text-muted">Separate multiple specializations with commas</small>
</div>
<div class="mb-3">
    <label>Bio</label>
    <textarea name="bio" class="form-control">{{ old('bio') }}</textarea>
</div>

<div class="mb-3">
    <label>Experience Years</label>
    <input type="number" name="experience_years" class="form-control" value="{{ old('experience_years') }}">
</div>

<div class="mb-3">
    <label>Location</label>
    <input type="text" name="location" class="form-control" value="{{ old('location') }}">
</div>

                        <div class="mb-3">
                            <label>Profile Image</label>
                            <input type="file" name="profile_image" class="form-control">
                        </div>

                        <button type="submit" class="btn btn-primary">Add Trainee</button>
                    </form>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection