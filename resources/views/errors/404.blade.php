@extends('layouts.app')

@section('title', '404 Not Found | Bookly')
@section('page_title', 'Page Not Found')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card text-center">
                <div class="card-body">
                    <h1 class="display-3 text-danger">
                        404
                    </h1>
                    <h3 class="mb-3">
                        <i class="fas fa-search-minus mr-2"></i>
                        We couldn't find what you're looking for.
                    </h3>
                    <p class="text-muted mb-4">
                        The page may have been moved, deleted, or the URL might be incorrect.
                    </p>

                    <div class="d-flex justify-content-center">
                        <a href="{{ url()->previous() }}" class="btn btn-outline-secondary mr-2">
                            <i class="fas fa-arrow-left mr-1"></i> Go Back
                        </a>
                        <a href="{{ route('shop.index') }}" class="btn btn-primary">
                            <i class="fas fa-book-open mr-1"></i> Back to Shop
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
