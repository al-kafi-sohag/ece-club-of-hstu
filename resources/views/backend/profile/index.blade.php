@extends('backend.layouts.master', ['page_slug' => 'profile'])

@section('title', 'Profile')

@push('styles')
    <style>
        .profile-image-container {
            position: relative;
        }

        .profile-image-container .profile-image {
            height: 10rem;
            width: 10rem;
            object-fit: cover;
            border-radius: var(--bs-border-radius);
        }

        .profile-image-container i {
            cursor: pointer;
            position: absolute;
            top: 4rem;
            left: 4rem;
            background-color: var(--bs-primary);
            color: var(--bs-text-primary);
            padding: 0.5rem;
            border-radius: 50%;
        }
    </style>
@endpush

@section('content')
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center">
                <div class="">
                    <h5 class="mb-0">Update Profile</h5>
                </div>
            </div>
            @include('backend.includes.form-feedback-all')
        </div>
    </div>
    <div class="container-fluid">
        <div class="card card-outline card-primary">
            <form action="{{ route('backend.profile.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="card-header">
                    <h3 class="card-title">Basic Information</h3>
                    <div class="card-tools">
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12 m-auto">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name">Name</label>
                                        <input type="text" class="form-control mt-2 mb-2" id="name" name="name"
                                            value="{{ $admin->name }}">
                                        @include('backend.includes.form-feedback', ['field' => 'name'])
                                    </div>
                                    <div class="form-group">
                                        <label for="email">Email</label>
                                        <input type="email" class="form-control mt-2 mb-2" id="email" name="email"
                                            value="{{ $admin->email }}">
                                        @include('backend.includes.form-feedback', ['field' => 'email'])
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center justify-content-center">
                                        <div class="form-group">
                                            <label for="image">Profile Image</label>

                                            <input type="file" class="form-control dynamic-image"
                                                id="image"
                                                name="image"
                                                data-src="{{ $admin->profileImageUrl }}"
                                                data-width="300"
                                                data-height="300"
                                                data-maxfilesize="1"
                                                accept="image/jpeg,image/jpg,image/png,image/webp"
                                            >
                                        </div>
                                    </div>
                                    @include('backend.includes.form-feedback', ['field' => 'image'])
                                    <small class="text-muted">Image size should be less than 5MB. Image format should be
                                        JPEG, JPG, PNG</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>

    <div class="container-fluid mt-4">
        <div class="card card-outline card-primary">
            <form action="{{ route('backend.profile.update') }}" method="POST">
                @csrf
                <div class="card-header">
                    <h3 class="card-title">Update Password</h3>

                    <div class="card-tools">
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12 m-auto">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="current_password">Current Password</label>
                                        <input type="password" class="form-control" id="current_password"
                                            name="current_password">
                                        @include('backend.includes.form-feedback', ['field' => 'current_password'])
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="new_password">New Password</label>
                                        <input type="password" class="form-control" id="new_password" name="new_password">
                                        @include('backend.includes.form-feedback', ['field' => 'new_password'])
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="confirm_password">Confirm Password</label>
                                        <input type="password" class="form-control" id="confirm_password"
                                            name="confirm_password">
                                        @include('backend.includes.form-feedback', ['field' => 'confirm_password'])
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
@endpush
