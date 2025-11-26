@extends('layouts.app')

@section('title', '403 Forbidden | Bookly')
@section('page_title', 'Forbidden')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card text-center">
                <div class="card-body">
                    <h1 class="display-3 text-warning">
                        403
                    </h1>
                    <h3 class="mb-3">
                        <i class="fas fa-ban mr-2"></i>
                        You don't have permission to access this page.
                    </h3>
                    <p class="text-muted mb-4">
                        If you believe this is a mistake, please contact the administrator
                        or try logging in with a different account.
                    </p>

                    <div class="d-flex justify-content-center">
                        <a href="{{ url()->previous() }}" class="btn btn-outline-secondary mr-2">
                            <i class="fas fa-arrow-left mr-1"></i> Go Back
                        </a>
                        <a href="{{ route('dashboard') }}" class="btn btn-primary">
                            <i class="fas fa-home mr-1"></i> Go to Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
