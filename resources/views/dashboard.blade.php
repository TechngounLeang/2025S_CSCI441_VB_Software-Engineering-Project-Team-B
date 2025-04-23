@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center py-3">
        <h2 class="text-primary">Dashboard</h2>
        <div>
            <button class="btn btn-outline-secondary me-2">
                <i class="fas fa-filter"></i> Filter
            </button>
            <button class="btn btn-primary">
                <i class="fas fa-download"></i> Export
            </button>
        </div>
    </div>

    <!-- Key Metrics Cards -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card h-100 shadow-sm dashboard-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Total Orders</h6>
                            <h3 class="mb-0">{{ number_format($totalOrders) }}</h3>
                            <p class="small {{ $orderGrowth >= 0 ? 'text-success' : 'text-danger' }} mb-0">
                                <i class="fas fa-arrow-{{ $orderGrowth >= 0 ? 'up' : 'down' }}"></i> 
                                {{ abs($orderGrowth) }}% from last month
                            </p>
                        </div>
                        <div class="bg-light p-3 rounded">
                            <i class="fas fa-shopping-cart" style="font-size: 24px; color: #83B6B9;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100 shadow-sm dashboard-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">New Customers</h6>
                            <h3 class="mb-0">{{ number_format($newCustomers) }}</h3>
                            <p class="small {{ $customerGrowth >= 0 ? 'text-success' : 'text-danger' }} mb-0">
                                <i class="fas fa-arrow-{{ $customerGrowth >= 0 ? 'up' : 'down' }}"></i> 
                                {{ abs($customerGrowth) }}% from last month
                            </p>
                        </div>
                        <div class="bg-light p-3 rounded">
                            <i class="fas fa-users" style="font-size: 24px; color: #83B6B9;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100 shadow-sm dashboard-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Total Sales</h6>
                            <h3 class="mb-0">${{ number_format($totalSales, 2) }}</h3>
                            <p class="small {{ $salesGrowth >= 0 ? 'text-success' : 'text-danger' }} mb-0">
                                <i class="fas fa-arrow-{{ $salesGrowth >= 0 ? 'up' : 'down' }}"></i> 
                                {{ abs($salesGrowth) }}% from last month
                            </p>
                        </div>
                        <div class="bg-light p-3 rounded">
                            <i class="fas fa-dollar-sign" style="font-size: 24px; color: #83B6B9;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Monthly Sales Analytics -->
    <div class="card mb-4 shadow-sm dashboard-card">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Monthly Sales Analytics</h5>
            <div class="btn-group btn-group-sm">
                <button type="button" class="btn btn-outline-secondary active" id="btn-daily">Daily</button>
                <button type="button" class="btn btn-outline-secondary" id="btn-weekly">Weekly</button>
                <button type="button" class="btn btn-outline-secondary" id="btn-monthly">Monthly</button>
            </div>
        </div>
        <div class="card-body">
            <div id="sales-analytics" style="height: 300px;"></div>
        </div>
    </div>

    <!-- Trending Coffee and Recent Orders -->
    <div class="row">
        <!-- Trending Coffee -->
        <div class="col-md-5">
            <div class="card shadow-sm dashboard-card">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Trending Coffee</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        @foreach($trendingProducts as $index => $product)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <span class="badge {{ $index === 0 ? 'rounded-pill' : 'bg-secondary rounded-pill' }} me-2" 
                                      style="{{ $index === 0 ? 'background-color: #83B6B9;' : '' }}">{{ $index + 1 }}</span>
                                {{ $product->name }}
                            </div>
                            <span class="badge bg-light text-dark">{{ $product->order_count }} orders</span>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
        
        <!-- Recent Orders -->
        <div class="col-md-7">
            <div class="card shadow-sm dashboard-card">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Recent Orders</h5>
                    <a href="{{ route('orders.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Customer</th>
                                    <th>Items</th>
                                    <th>Date & Time</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentOrders as $order)
                                <tr>
                                    <td>{{ $order->id }}</td>
                                    <td>{{ $order->customer_name }}</td>
                                    <td>
                                        @php
                                            $items = $order->items->map(function($item) {
                                                return $item->product ? $item->product->name : 'Unknown Product';
                                            })->implode(', ');
                                            
                                            // Truncate if too long
                                            if(strlen($items) > 30) {
                                                $items = substr($items, 0, 30) . '...';
                                            }
                                        @endphp
                                        {{ $items }}
                                    </td>
                                    <td>{{ $order->created_at->format('m/d/Y h:i A') }}</td>
                                    <td>${{ number_format($order->total_amount, 2) }}</td>
                                    <td>
                                        <span class="badge" style="background-color: #83B6B9; color: white;">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Prepare data for charts
        const dailySalesData = @json($salesData);
        const weeklySalesData = @json($weeklySalesData);
        const monthlySalesData = @json($monthlySalesData);
        
        // Create the sales chart
        var ctx = document.getElementById('sales-analytics').getContext('2d');
        var salesChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: dailySalesData.map(item => item.date),
                datasets: [{
                    label: 'Daily Sales ($)',
                    data: dailySalesData.map(item => item.total),
                    borderColor: 'rgba(131, 182, 185, 1)',
                    backgroundColor: 'rgba(131, 182, 185, 0.1)',
                    borderWidth: 2,
                    tension: 0.3,
                    fill: true,
                    pointBackgroundColor: 'rgba(131, 182, 185, 1)',
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 10,
                        cornerRadius: 6,
                        callbacks: {
                            label: function(context) {
                                return `Sales: $${parseFloat(context.parsed.y).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            display: true,
                            color: 'rgba(0, 0, 0, 0.05)'
                        },
                        ticks: {
                            callback: function(value) {
                                return '$' + value.toLocaleString();
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            maxTicksLimit: 10
                        }
                    }
                }
            }
        });
        
        // Event handlers for time period buttons
        document.getElementById('btn-daily').addEventListener('click', function() {
            document.querySelectorAll('.btn-group .btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            
            salesChart.data.labels = dailySalesData.map(item => item.date);
            salesChart.data.datasets[0].data = dailySalesData.map(item => item.total);
            salesChart.data.datasets[0].label = 'Daily Sales ($)';
            salesChart.update();
        });
        
        document.getElementById('btn-weekly').addEventListener('click', function() {
            document.querySelectorAll('.btn-group .btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            
            salesChart.data.labels = weeklySalesData.map(item => item.week);
            salesChart.data.datasets[0].data = weeklySalesData.map(item => item.total);
            salesChart.data.datasets[0].label = 'Weekly Sales ($)';
            salesChart.update();
        });
        
        document.getElementById('btn-monthly').addEventListener('click', function() {
            document.querySelectorAll('.btn-group .btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            
            salesChart.data.labels = monthlySalesData.map(item => item.month);
            salesChart.data.datasets[0].data = monthlySalesData.map(item => item.total);
            salesChart.data.datasets[0].label = 'Monthly Sales ($)';
            salesChart.update();
        });
    });
</script>
@endsection