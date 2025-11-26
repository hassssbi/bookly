@extends('layouts.app')

@section('title', 'Seller Dashboard | Bookly')
@section('page_title', 'Seller Dashboard')

@section('breadcrumbs')
    <li class="breadcrumb-item active">Dashboard</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-primary">
                <div class="inner">
                    <h3><span class="js-counter" data-target="{{ $totalBooks }}">0</span></h3>
                    <p>My Books</p>
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
            <div class="small-box bg-info">
                <div class="inner">
                    <h3><span class="js-counter" data-target="{{ $totalUnitsSold }}">0</span></h3>
                    <p>Units Sold (Paid)</p>
                </div>
                <div class="icon"><i class="fas fa-box"></i></div>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>
                        RM <span class="js-counter" data-target="{{ $totalRevenue }}" data-type="money">0</span>
                    </h3>
                    <p>Total Revenue (Paid)</p>
                </div>
                <div class="icon"><i class="fas fa-coins"></i></div>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- Revenue last 7 days --}}
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Revenue (Last 7 Days)</h3>
                </div>
                <div class="card-body">
                    <canvas id="sellerRevenueChart" height="200"></canvas>
                </div>
            </div>
        </div>

        {{-- Top books --}}
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Top Selling Books</h3>
                </div>
                <div class="card-body p-0">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th class="text-right">Units</th>
                                <th class="text-right">Revenue (RM)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($topBooks as $book)
                                <tr>
                                    <td>{{ $book->title }}</td>
                                    <td class="text-right">{{ $book->units }}</td>
                                    <td class="text-right">{{ number_format($book->revenue, 2) }}</td>
                                </tr>
                            @endforeach
                            @if ($topBooks->isEmpty())
                                <tr>
                                    <td colspan="3" class="text-center text-muted">No data</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Recent orders --}}
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Recent Orders Containing Your Books</h3>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover text-nowrap">
                        <thead>
                            <tr>
                                <th>Order #</th>
                                <th>Date</th>
                                <th>Customer</th>
                                <th>Book</th>
                                <th>Qty</th>
                                <th>Line Total (RM)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentOrderItems as $item)
                                <tr>
                                    <td>{{ $item->order->id }}</td>
                                    <td>{{ $item->order->created_at->format('Y-m-d H:i') }}</td>
                                    <td>{{ $item->order->buyer->name ?? 'Unknown' }}</td>
                                    <td>{{ $item->book->title ?? '[Deleted book]' }}</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>{{ number_format($item->subtotal, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted p-3">No recent orders.</td>
                                </tr>
                            @endforelse
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
            var ctx = document.getElementById('sellerRevenueChart').getContext('2d');
            var labels = {!! json_encode($dailyRevenue->pluck('date')->map(fn($d) => \Carbon\Carbon::parse($d)->format('d M'))) !!};
            var revenueData = {!! json_encode($dailyRevenue->pluck('revenue')) !!};

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Revenue (RM)',
                        data: revenueData,
                        fill: false,
                        borderColor: 'rgba(75, 192, 192, 1)',
                        tension: 0.1,
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        })();
    </script>
@endpush
