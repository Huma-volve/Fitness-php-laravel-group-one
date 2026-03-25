@extends('master')

@section('title', 'Edit Trainer')

@section('content')

<div class="container mt-4">
    <div class="card">
        <div class="card-header bg-primary">
            <h4>Edit Trainer</h4>
        </div>

        <div class="card-body">

            <form action="{{ route('trainers.update', $trainer->user->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

               
                <input type="text" name="name" value="{{ $trainer->user->name }}" class="form-control mb-2">

             
                <input type="email" name="email" value="{{ $trainer->user->email }}" class="form-control mb-2">

                <input type="text" name="phone" value="{{ $trainer->user->phone }}" class="form-control mb-2">

                <textarea name="bio" class="form-control mb-2">{{ $trainer->bio }}</textarea>

                <input type="number" name="experience_years" value="{{ $trainer->experience_years }}" class="form-control mb-2">

                <input type="text" name="location" value="{{ $trainer->location }}" class="form-control mb-2">

                <input type="text" name="specializations"
                    value="{{ $trainer->specializations->pluck('name')->implode(', ') }}"
                    class="form-control mb-2">

                <input type="file" name="profile_image" class="form-control mb-2">

                <button type="submit" class="btn btn-success">Update</button>

            </form>

        </div>
    </div>
</div>

@endsection