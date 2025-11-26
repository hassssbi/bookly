{{-- resources/views/dashboard.blade.php --}}
@extends('layouts.app')

@section('title', 'Dashboard | Bookly')

@section('page_title', 'Dashboard')

@section('breadcrumbs')
    <li class="breadcrumb-item active">Dashboard</li>
@endsection

@section('content')
    <div class="row">
        {{-- Example stats boxes --}}
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $stats['new_orders'] ?? 0 }}</h3>
                    <p>New Orders</p>
                </div>
                <div class="icon">
                    <i class="ion ion-bag"></i>
                </div>
                <a href="#" class="small-box-footer">
                    {{-- <a href="{{ route('orders.index') }}" class="small-box-footer"> --}}
                    More info <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $stats['total_books'] ?? 0 }}</h3>
                    <p>Total Books</p>
                </div>
                <div class="icon">
                    <i class="ion ion-ios-book"></i>
                </div>
                <a href="#" class="small-box-footer">
                    {{-- <a href="{{ auth()->user()->role === 'seller' ? route('seller.books.index') : '#' }}" --}}
                    {{-- class="small-box-footer"> --}}
                    More info <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        {{-- add more boxes as you like --}}
    </div>

    {{-- You can also bring over cards/charts from the original template here --}}
@endsection
