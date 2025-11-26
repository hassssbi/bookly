@extends('layouts.app')

@section('title', 'Admin Dashboard | Bookly')
@section('page_title', 'Admin Dashboard')

@section('breadcrumbs')
    <li class="breadcrumb-item active">Dashboard</li>
@endsection

@section('content')
    <div class="row">
        {{-- Users --}}
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3><span class="js-counter" data-target="{{ $totalUsers }}">0</span></h3>
                    <p>Total Users</p>
                </div>
                <div class="icon"><i class="fas fa-users"></i></div>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3><span class="js-counter" data-target="{{ $totalCustomers }}">0</span></h3>
                    <p>Customers</p>
                </div>
                <div class="icon"><i class="fas fa-user"></i></div>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3><span class="js-counter" data-target="{{ $totalSellers }}">0</span></h3>
                    <p>Sellers</p>
                </div>
                <div class="icon"><i class="fas fa-store"></i></div>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3><span class="js-counter" data-target="{{ $pendingSellers }}">0</span></h3>
                    <p>Pending Seller Applications</p>
                </div>
                <div class="icon"><i class="fas fa-clock"></i></div>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- Books --}}
        <div class="col-lg-3 col-6">
            <div class="small-box bg-primary">
                <div class="inner">
                    <h3><span class="js-counter" data-target="{{ $totalBooks }}">0</span></h3>
                    <p>Total Books</p>
                </div>
                <div class="icon"><i class="fas fa-book"></i></div>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3><span class="js-counter" data-target="{{ $activeBooks }}">0</span></h3>
                    <p>Active Books</p>
                </div>
                <div class="icon"><i class="fas fa-check"></i></div>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-secondary">
                <div class="inner">
                    <h3><span class="js-counter" data-target="{{ $outOfStockBooks }}">0</span></h3>
                    <p>Out of Stock</p>
                </div>
                <div class="icon"><i class="fas fa-exclamation-triangle"></i></div>
            </div>
        </div>

        {{-- Orders/Revenue --}}
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3><span class="js-counter" data-target="{{ $paidOrders }}">0</span></h3>
                    <p>Paid Orders</p>
                </div>
                <div class="icon"><i class="fas fa-shopping-cart"></i></div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Orders & Revenue (Last 7 Days)</h3>
                </div>
                <div class="card-body">
                    <canvas id="ordersChart" height="200"></canvas>
                </div>
            </div>
        </div>

        <div class="col-lg-3">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Top Categories by Revenue</h3>
                </div>
                <div class="card-body p-0">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Category</th>
                                <th class="text-right">RM</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($topCategories as $cat)
                                <tr>
                                    <td>{{ $cat->name }}</td>
                                    <td class="text-right">{{ number_format($cat->revenue, 2) }}</td>
                                </tr>
                            @endforeach
                            @if ($topCategories->isEmpty())
                                <tr>
                                    <td colspan="2" class="text-center text-muted">No data</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-3">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Top Sellers by Revenue</h3>
                </div>
                <div class="card-body p-0">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Seller</th>
                                <th class="text-right">RM</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($topSellers as $seller)
                                <tr>
                                    <td>{{ $seller->name }}</td>
                                    <td class="text-right">{{ number_format($seller->revenue, 2) }}</td>
                                </tr>
                            @endforeach
                            @if ($topSellers->isEmpty())
                                <tr>
                                    <td colspan="2" class="text-center text-muted">No data</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        (function() {
            var ctx = document.getElementById('ordersChart').getContext('2d');
            var labels = {!! json_encode($dailyOrders->pluck('date')->map(fn($d) => \Carbon\Carbon::parse($d)->format('d M'))) !!};
            var ordersData = {!! json_encode($dailyOrders->pluck('count')) !!};
            var revenueData = {!! json_encode($dailyOrders->pluck('revenue')) !!};

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                            label: 'Orders',
                            data: ordersData,
                            yAxisID: 'y',
                            backgroundColor: 'rgba(54, 162, 235, 0.5)',
                        },
                        {
                            label: 'Revenue (RM)',
                            data: revenueData,
                            yAxisID: 'y1',
                            type: 'line',
                            fill: false,
                            borderColor: 'rgba(75, 192, 192, 1)',
                        }
                    ]
                },
                options: {
                    scales: {
                        y: {
                            position: 'left',
                            beginAtZero: true
                        },
                        y1: {
                            position: 'right',
                            beginAtZero: true,
                            grid: {
                                drawOnChartArea: false
                            }
                        }
                    }
                }
            });
        })();
    </script>
@endpush
