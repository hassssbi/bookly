@extends('layouts.app')

@section('title', 'Server Error | Bookly')
@section('page_title', 'Something went wrong')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card text-center">
                <div class="card-body">
                    <h1 class="display-3 text-danger">
                        500
                    </h1>
                    <h3 class="mb-3">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        Oops, something went wrong on our side.
                    </h3>
                    <p class="text-muted mb-4">
                        We've logged the error. Please try again in a moment,
                        or contact the administrator if the problem persists.
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
