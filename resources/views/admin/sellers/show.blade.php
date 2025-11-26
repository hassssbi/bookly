@extends('layouts.app')

@section('title', 'Seller Details | Bookly Admin')
@section('page_title', 'Seller Details')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('admin.sellers.index') }}">Sellers</a></li>
    <li class="breadcrumb-item active">Details</li>
@endsection

@section('content')
    @php
        $badge = match ($seller->status) {
            'approved' => 'success',
            'pending' => 'warning',
            'rejected' => 'danger',
            default => 'secondary',
        };
    @endphp
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Seller Profile #{{ $seller->id }} <span
                    class="badge badge-{{ $badge }}">{{ ucfirst($seller->status) }}</span></h3>
        </div>
        <div class="card-body">
            <h5>User Information</h5>
            <p>
                <strong>Name:</strong> {{ $seller->user->name }}<br>
                <strong>Email:</strong> {{ $seller->user->email }}<br>
                <strong>Role:</strong> {{ $seller->user->role }}
            </p>

            <hr>

            <h5>Store Information</h5>
            <p>
                <strong>Store Name:</strong> {{ $seller->store_name }}<br>
                <strong>Phone:</strong> {{ $seller->phone ?? '-' }}<br>
                <strong>Address:</strong><br>
                {{ $seller->address ?? '-' }}
            </p>

            <hr>

            <h5>Status</h5>
            <p>
                <strong>Status:</strong> <span
                    class="badge badge-{{ $badge }}">{{ ucfirst($seller->status) }}</span><br>
                @if ($seller->status === 'rejected')
                    <strong>Rejection Reason:</strong><br>
                    {{ $seller->rejection_reason ?? '-' }}
                @endif

            </p>

            {{-- <form action="{{ route('admin.sellers.updateStatus', $seller) }}" method="POST" class="mt-3">
                @csrf
                @method('PATCH')

                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label>Status</label>
                        <select name="status" class="form-control">
                            <option value="pending" {{ $seller->status === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="approved" {{ $seller->status === 'approved' ? 'selected' : '' }}>Approved
                            </option>
                            <option value="rejected" {{ $seller->status === 'rejected' ? 'selected' : '' }}>Rejected
                            </option>
                        </select>
                    </div>
                    <div class="form-group col-md-8">
                        <label>Rejection Reason (optional)</label>
                        <textarea name="rejection_reason" rows="2" class="form-control">{{ old('rejection_reason', $seller->rejection_reason) }}</textarea>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Save</button>
                <a href="{{ route('admin.sellers.index') }}" class="btn btn-secondary">Back</a>
            </form> --}}
            <a href="{{ route('admin.sellers.index') }}" class="btn btn-secondary">Back</a>

        </div>
    </div>
@endsection
