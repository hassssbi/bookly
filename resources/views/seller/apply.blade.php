@extends('layouts.app')

@section('title', 'Apply as Seller | Bookly')
@section('page_title', 'Apply to Become a Seller')

@section('breadcrumbs')
    <li class="breadcrumb-item active">Apply as Seller</li>
@endsection

@section('content')
    @if (session('status'))
        <div class="alert alert-info">{{ session('status') }}</div>
    @endif

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        {{ $profile ? 'Update Your Application' : 'Seller Application' }}
                    </h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('seller.apply.submit') }}" method="POST">
                        @csrf

                        <div class="form-group">
                            <label>Store Name</label>
                            <input type="text" name="store_name"
                                value="{{ old('store_name', $profile->store_name ?? '') }}"
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

                        <button type="submit" class="btn btn-primary">
                            {{ $profile ? 'Resubmit Application' : 'Submit Application' }}
                        </button>

                        <a href="{{ route('dashboard') }}" class="btn btn-secondary">
                            Cancel
                        </a>
                    </form>
                </div>
            </div>
        </div>

        {{-- Status summary --}}
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Application Status</h3>
                </div>
                <div class="card-body">
                    @if (!$profile)
                        <p class="text-muted">
                            You haven’t applied to become a seller yet.
                            Fill in your store details and submit the form.
                        </p>
                    @else
                        @php
                            $badge = match ($profile->status) {
                                'approved' => 'success',
                                'pending' => 'warning',
                                'rejected' => 'danger',
                                default => 'secondary',
                            };
                        @endphp

                        <p>
                            Status:
                            <span class="badge badge-{{ $badge }}">
                                {{ ucfirst($profile->status) }}
                            </span>
                        </p>

                        @if ($profile->status === 'pending')
                            <p class="text-muted mb-0">
                                Your application is under review by the admin.
                            </p>
                        @elseif($profile->status === 'approved')
                            <p class="text-success">
                                Your application has been approved!
                            </p>
                            <p class="mb-0">
                                You now have access to the seller area.
                                Go to <a href="{{ route('seller.profile.show') }}">My Seller Profile</a>.
                            </p>
                        @elseif($profile->status === 'rejected')
                            <p class="text-danger">
                                Your application was rejected.
                            </p>
                            <p>
                                <strong>Reason:</strong><br>
                                {{ $profile->rejection_reason ?: '-' }}
                            </p>
                            <p class="text-muted mb-0">
                                You can update your store details and resubmit your application
                                for another review.
                            </p>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
