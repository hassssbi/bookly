@extends('layouts.app')

@section('title', 'Edit Seller Profile | Bookly')
@section('page_title', $profile ? 'Edit Seller Profile' : 'Create Seller Profile')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('seller.profile.show') }}">My Seller Profile</a></li>
    <li class="breadcrumb-item active">{{ $profile ? 'Edit' : 'Create' }}</li>
@endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('seller.profile.store') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label>Store Name</label>
                    <input type="text" name="store_name" value="{{ old('store_name', $profile->store_name ?? '') }}"
                        class="form-control @error('store_name') is-invalid @enderror" required>
                    @error('store_name')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label>Phone (optional)</label>
                    <input type="text" name="phone" value="{{ old('phone', $profile->phone ?? '') }}"
                        class="form-control @error('phone') is-invalid @enderror">
                    @error('phone')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label>Store Address (optional)</label>
                    <textarea name="address" rows="3" class="form-control @error('address') is-invalid @enderror">{{ old('address', $profile->address ?? '') }}</textarea>
                    @error('address')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                @if ($profile)
                    <div class="alert alert-secondary">
                        <strong>Current status:</strong>
                        {{ ucfirst($profile->status) }}<br>
                        <small class="d-block mt-1">
                            Updating your details will not automatically change your status.
                            Admin will review your profile and update the status separately.
                        </small>
                    </div>
                @else
                    <div class="alert alert-info">
                        After submitting your profile, it will be sent for admin approval.
                        You will be able to create books once your store is approved.
                    </div>
                @endif

                <button type="submit" class="btn btn-primary">
                    Save Profile
                </button>
                <a href="{{ route('seller.profile.show') }}" class="btn btn-secondary">
                    Cancel
                </a>
            </form>
        </div>
    </div>
@endsection
