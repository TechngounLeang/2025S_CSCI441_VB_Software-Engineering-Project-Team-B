<!-- Written & debugged by: Tech Ngoun Leang, Ratanakvesal Thong, Longwei Ngor, Samady Sok
Tested by: Tech Ngoun Leang-->
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center py-3">
        <h2 class="text-primary">Dashboard</h2>
        <div>
            <button class="btn btn-outline-secondary me-2" data-toggle="modal" data-target="#filterModal">
                <i class="fas fa-filter"></i> Filter
            </button>
            <button class="btn btn-primary" data-toggle="modal" data-target="#exportModal">
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

    <!-- Monthly Sales Analytics - Fixed Version -->
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
        <!-- Important: Make sure this is a canvas element -->
        <canvas id="sales-analytics" height="300"></canvas>
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
                                <span class="badge {{ $index === 0 ? 'bg-primary rounded-pill' : 'bg-secondary rounded-pill' }} me-2" 
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
                                        @if(isset($order->items_list))
                                            {{ $order->items_list }}
                                        @else
                                            @php
                                                $items = isset($order->items) ? $order->items->map(function($item) {
                                                    return $item->product ? $item->product->name : 'Unknown Product';
                                                })->implode(', ') : '';
                                                
                                                // Truncate if too long
                                                if(strlen($items) > 30) {
                                                    $items = substr($items, 0, 30) . '...';
                                                }
                                            @endphp
                                            {{ $items }}
                                        @endif
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

<!-- Filter Modal -->
<div class="modal fade" id="filterModal" tabindex="-1" role="dialog" aria-labelledby="filterModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="filterModalLabel">Filter Dashboard Data</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('dashboard') }}" method="GET" id="filterForm">
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label for="filter_start_date">Start Date</label>
                        <input type="date" class="form-control" id="filter_start_date" name="start_date" 
                               value="{{ request('start_date', now()->startOfMonth()->format('Y-m-d')) }}">
                    </div>
                    <div class="form-group mb-3">
                        <label for="filter_end_date">End Date</label>
                        <input type="date" class="form-control" id="filter_end_date" name="end_date" 
                               value="{{ request('end_date', now()->format('Y-m-d')) }}">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Apply Filter</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Export Modal -->
<div class="modal fade" id="exportModal" tabindex="-1" role="dialog" aria-labelledby="exportModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exportModalLabel">Export Dashboard Data</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('dashboard.export') }}" method="GET" id="exportForm">
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label for="export_type">Export Type</label>
                        <select class="form-control" id="export_type" name="export_type">
                            <option value="sales">Sales Data</option>
                            <option value="orders">Orders Data</option>
                            <option value="customers">Customer Data</option>
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label for="timeframe">Time Period</label>
                        <select class="form-control" id="timeframe" name="timeframe">
                            <option value="today">Today</option>
                            <option value="yesterday">Yesterday</option>
                            <option value="this_week">This Week</option>
                            <option value="last_week">Last Week</option>
                            <option value="this_month">This Month</option>
                            <option value="last_month">Last Month</option>
                            <option value="custom">Custom Range</option>
                        </select>
                    </div>
                    <div id="customDateRange" style="display: none;">
                        <div class="form-group mb-3">
                            <label for="export_start_date">Start Date</label>
                            <input type="date" class="form-control" id="export_start_date" name="start_date">
                        </div>
                        <div class="form-group mb-3">
                            <label for="export_end_date">End Date</label>
                            <input type="date" class="form-control" id="export_end_date" name="end_date">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Export</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Prepare data for charts
        const dailySalesData = @json($salesData ?? []);
        const weeklySalesData = @json($weeklySalesData ?? []);
        const monthlySalesData = @json($monthlySalesData ?? []);
        
        // Check if the canvas element exists before trying to create a chart
        const chartElement = document.getElementById('sales-analytics');
        if (!chartElement) {
            console.error('Chart element not found');
            return;
        }
        
        // Create the sales chart - make sure we're working with a canvas element
        try {
            var ctx = chartElement.getContext('2d');
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
            const btnDaily = document.getElementById('btn-daily');
            const btnWeekly = document.getElementById('btn-weekly');
            const btnMonthly = document.getElementById('btn-monthly');
            
            if (btnDaily) {
                btnDaily.addEventListener('click', function() {
                    document.querySelectorAll('.btn-group .btn').forEach(b => b.classList.remove('active'));
                    this.classList.add('active');
                    
                    salesChart.data.labels = dailySalesData.map(item => item.date);
                    salesChart.data.datasets[0].data = dailySalesData.map(item => item.total);
                    salesChart.data.datasets[0].label = 'Daily Sales ($)';
                    salesChart.update();
                });
            }
            
            if (btnWeekly) {
                btnWeekly.addEventListener('click', function() {
                    document.querySelectorAll('.btn-group .btn').forEach(b => b.classList.remove('active'));
                    this.classList.add('active');
                    
                    salesChart.data.labels = weeklySalesData.map(item => item.week);
                    salesChart.data.datasets[0].data = weeklySalesData.map(item => item.total);
                    salesChart.data.datasets[0].label = 'Weekly Sales ($)';
                    salesChart.update();
                });
            }
            
            if (btnMonthly) {
                btnMonthly.addEventListener('click', function() {
                    document.querySelectorAll('.btn-group .btn').forEach(b => b.classList.remove('active'));
                    this.classList.add('active');
                    
                    salesChart.data.labels = monthlySalesData.map(item => item.month);
                    salesChart.data.datasets[0].data = monthlySalesData.map(item => item.total);
                    salesChart.data.datasets[0].label = 'Monthly Sales ($)';
                    salesChart.update();
                });
            }
        } catch (error) {
            console.error('Error initializing chart:', error);
            // Display fallback message if chart initialization fails
            chartElement.innerHTML = '<div class="alert alert-warning">Unable to load chart. Please make sure the element is a canvas.</div>';
        }
        
        // Filter form validation
        const filterForm = document.getElementById('filterForm');
        if (filterForm) {
            filterForm.addEventListener('submit', function(e) {
                const startDate = document.getElementById('filter_start_date').value;
                const endDate = document.getElementById('filter_end_date').value;
                
                if (!startDate || !endDate) {
                    e.preventDefault();
                    alert('Please select both start and end dates');
                    return false;
                }
                
                if (new Date(startDate) > new Date(endDate)) {
                    e.preventDefault();
                    alert('Start date must be before end date');
                    return false;
                }
            });
        }
        
        // Export form setup
        const timeframeSelect = document.getElementById('timeframe');
        if (timeframeSelect) {
            timeframeSelect.addEventListener('change', function() {
                const customRangeDiv = document.getElementById('customDateRange');
                if (this.value === 'custom') {
                    customRangeDiv.style.display = 'block';
                } else {
                    customRangeDiv.style.display = 'none';
                }
            });
        }
        
        // Export form validation
        const exportForm = document.getElementById('exportForm');
        if (exportForm) {
            exportForm.addEventListener('submit', function(e) {
                if (timeframeSelect.value === 'custom') {
                    const startDate = document.getElementById('export_start_date').value;
                    const endDate = document.getElementById('export_end_date').value;
                    
                    if (!startDate || !endDate) {
                        e.preventDefault();
                        alert('Please select both start and end dates for custom range');
                        return false;
                    }
                    
                    if (new Date(startDate) > new Date(endDate)) {
                        e.preventDefault();
                        alert('Start date must be before end date');
                        return false;
                    }
                }
            });
        }
    });
</script>
@endpush