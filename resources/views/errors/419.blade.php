@extends('layouts.app')

@section('title', 'Page Expired | Bookly')
@section('page_title', 'Page Expired')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card text-center">
                <div class="card-body">
                    <h1 class="display-3 text-warning">
                        419
                    </h1>
                    <h3 class="mb-3">
                        <i class="fas fa-hourglass-end mr-2"></i>
                        Your session has expired.
                    </h3>
                    <p class="text-muted mb-4">
                        This usually happens if the page was left open for too long.
                        Please refresh the page and try again.
                    </p>

                    <div class="d-flex justify-content-center">
                        <a href="{{ url()->previous() }}" class="btn btn-outline-secondary mr-2">
                            <i class="fas fa-sync-alt mr-1"></i> Try Again
                        </a>
                        <a href="{{ route('login') }}" class="btn btn-primary">
                            <i class="fas fa-sign-in-alt mr-1"></i> Log In
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
