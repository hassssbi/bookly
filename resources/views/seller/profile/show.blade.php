@extends('layouts.app')

@section('title', 'My Seller Profile | Bookly')
@section('page_title', 'My Seller Profile')

@section('breadcrumbs')
    <li class="breadcrumb-item active">My Seller Profile</li>
@endsection

@section('content')
    @if (session('status'))
        <div class="alert alert-info">{{ session('status') }}</div>
    @endif

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Store Information</h3>
                    <div class="card-tools">
                        <a href="{{ route('seller.profile.edit') }}" class="btn btn-sm btn-primary">
                            Edit Profile
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if (!$profile)
                        <p class="text-muted mb-3">
                            You have not set up your seller profile yet. Please click
                            <strong>Edit Profile</strong> to provide your store details.
                        </p>
                    @else
                        <dl class="row">
                            <dt class="col-sm-4">Store Name</dt>
                            <dd class="col-sm-8">{{ $profile->store_name }}</dd>

                            <dt class="col-sm-4">Phone</dt>
                            <dd class="col-sm-8">{{ $profile->phone ?: '-' }}</dd>

                            <dt class="col-sm-4">Address</dt>
                            <dd class="col-sm-8">
                                {!! nl2br(e($profile->address ?: '-')) !!}
                            </dd>
                        </dl>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-4">
            {{-- Status panel --}}
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Approval Status</h3>
                </div>
                <div class="card-body">
                    @if (!$profile)
                        <p class="text-muted">
                            Status: <span class="badge badge-secondary">Not Submitted</span>
                        </p>
                        <p class="mb-0">
                            Once you submit your seller profile, it will be reviewed by an admin.
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
                            <p class="mb-0 text-muted">
                                Your profile is currently under review. You will be able to create books
                                once it is approved.
                            </p>
                        @elseif($profile->status === 'approved')
                            <p class="mb-0 text-success">
                                Your seller profile is approved. You can now create and manage your books.
                            </p>
                        @elseif($profile->status === 'rejected')
                            <p class="text-danger mb-1">
                                Your seller profile was rejected.
                            </p>
                            <p class="mb-1">
                                <strong>Reason:</strong><br>
                                {{ $profile->rejection_reason ?: '-' }}
                            </p>
                            <p class="mb-0 text-muted">
                                You may update your store details and resubmit. An admin will review again.
                            </p>
                        @endif
                    @endif
                </div>
            </div>

            {{-- Basic user info --}}
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Account Info</h3>
                </div>
                <div class="card-body">
                    <p class="mb-1"><strong>Name:</strong> {{ $user->name }}</p>
                    <p class="mb-1"><strong>Email:</strong> {{ $user->email }}</p>
                    <p class="mb-0"><strong>Role:</strong> {{ ucfirst($user->role) }}</p>
                </div>
            </div>
        </div>
    </div>
@endsection
