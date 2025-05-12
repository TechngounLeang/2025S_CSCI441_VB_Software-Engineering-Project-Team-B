<!-- Written & debugged by: Tech Ngoun Leang, Ratanakvesal Thong, Longwei Ngor, Samady Sok
Tested by: Tech Ngoun Leang-->
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4>{{ __('app.sales_dashboard') }}</h4>
                </div>
                <div class="card-body">
                    <!-- Summary Cards -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <h5 class="card-title">{{ __("app.todays_sales") }}</h5>
                                    <h2>${{ number_format($todaySales, 2) }}</h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <h5 class="card-title">{{ __('app.weekly_sales') }}</h5>
                                    <h2>${{ number_format($weeklySales, 2) }}</h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <h5 class="card-title">{{ __('app.monthly_sales') }}</h5>
                                    <h2>${{ number_format($monthlySales, 2) }}</h2>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Date Range Filter -->
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5>{{ __('app.filter_sales_trend') }}</h5>
                                </div>
                                <div class="card-body">
                                    <form action="{{ route('sales.index') }}" method="GET" class="row" id="filterForm">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="chart_start_date">{{ __('app.start_date') }}</label>
                                                <input type="date" class="form-control" id="chart_start_date" name="start_date" value="{{ request('start_date', now()->subDays(30)->format('Y-m-d')) }}">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="chart_end_date">{{ __('app.end_date') }}</label>
                                                <input type="date" class="form-control" id="chart_end_date" name="end_date" value="{{ request('end_date', now()->format('Y-m-d')) }}">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>&nbsp;</label>
                                                <button type="submit" class="btn btn-primary d-block w-100">{{ __('app.apply_filter') }}</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sales Chart -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5>{{ __('app.sales_trend') }}</h5>
                                </div>
                                <div class="card-body">
                                    <canvas id="salesChart" height="300"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mb-4">
                        <!-- Payment Methods -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5>{{ __('app.payment_methods') }}</h5>
                                </div>
                                <div class="card-body">
                                    <canvas id="paymentMethodChart" height="300px"></canvas>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Trending Items -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Trending Items</h5>
                                </div>
                                <div class="card-body" style="height: 340px; overflow-y: auto;">
                                    <ul class="list-group list-group-flush">
                                        @foreach ($trendingItems->take(5) as $item)
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                {{ $item->product_name }}
                                                <span class="badge bg-primary rounded-pill">{{ $item->quantity_sold }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Order Breakdown -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5>{{ __('app.order_breakdown') }}</h5>
                                </div>
                                <div class="card-body">
                                    @if($orderBreakdownCount > 0)
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>{{ __('app.date') }}</th>
                                                        <th>{{ __('app.orders') }}</th>
                                                        <th>{{ __('app.items_sold') }}</th>
                                                        <th>{{ __('app.revenue') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($orderBreakdown as $breakdown)
                                                    <tr>
                                                        <td>{{ $breakdown->date }}</td>
                                                        <td>{{ $breakdown->order_count }}</td>
                                                        <td>{{ $breakdown->items_sold ?? 0 }}</td>
                                                        <td>${{ number_format($breakdown->revenue, 2) }}</td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="alert alert-info">
                                            {{ __('app.no_order_data') }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Export Reports -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5>{{ __('app.export_reports') }}</h5>
                                </div>
                                <div class="card-body">
                                    <form action="{{ route('sales.export') }}" method="GET" id="exportForm">
                                        <div class="form-group mb-3">
                                            <label for="timeframe">{{ __('app.select_timeframe') }}</label>
                                            <select name="timeframe" id="timeframe" class="form-control">
                                                <option value="today">{{ __('app.today') }}</option>
                                                <option value="yesterday">{{ __('app.yesterday') }}</option>
                                                <option value="this_week">{{ __('app.this_week') }}</option>
                                                <option value="last_week">{{ __('app.last_week') }}</option>
                                                <option value="this_month">{{ __('app.this_month') }}</option>
                                                <option value="last_month">{{ __('app.last_month') }}</option>
                                                <option value="custom">{{ __('app.custom') }}</option>
                                            </select>
                                        </div>
                                        <div id="customDateRange" style="display: none;" class="mb-3">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label for="start_date">{{ __('app.start_date') }}</label>
                                                    <input type="date" name="start_date" id="start_date" class="form-control">
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="end_date">{{ __('app.end_date') }}</label>
                                                    <input type="date" name="end_date" id="end_date" class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                        <button type="submit" class="btn btn-primary" id="exportButton">{{ __('app.download_csv') }}</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                </div> <!-- card-body -->
            </div> <!-- card -->
        </div> <!-- col -->
    </div> <!-- row -->
</div> <!-- container -->
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize charts
        initializeSalesChart();
        initializePaymentMethodChart();
        
        // Set up timeframe selector
        document.getElementById('timeframe').addEventListener('change', function() {
            const customRangeDiv = document.getElementById('customDateRange');
            if (this.value === 'custom') {
                customRangeDiv.style.display = 'block';
            } else {
                customRangeDiv.style.display = 'none';
            }
        });
        
        // Set up export button
        document.getElementById('exportButton').addEventListener('click', function(e) {
            e.preventDefault();
            const form = document.getElementById('exportForm');
            
            // Make sure we have dates for custom range
            if (form.timeframe.value === 'custom') {
                const startDate = form.start_date.value;
                const endDate = form.end_date.value;
                
                if (!startDate || !endDate) {
                    alert('Please select both start and end dates for custom range');
                    return;
                }
                
                if (new Date(startDate) > new Date(endDate)) {
                    alert('Start date must be before end date');
                    return;
                }
            }
            
            // Submit the form
            form.submit();
        });
    });
    
    function initializeSalesChart() {
        const ctx = document.getElementById('salesChart').getContext('2d');
        
        // Parse data from PHP
        const dates = @json($dates ?? []);
        const salesData = @json($salesData ?? []);
        
        if (!dates.length || !salesData.length) {
            // Display a message if no data
            const noDataMessage = document.createElement('div');
            noDataMessage.className = 'text-center text-muted py-5';
            noDataMessage.textContent = 'No sales data available for the selected period';
            ctx.canvas.parentNode.replaceChild(noDataMessage, ctx.canvas);
            return;
        }
        
        // Format dates for display
        const formattedDates = dates.map(date => {
            const d = new Date(date);
            return d.toLocaleDateString();
        });
        
        const chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: formattedDates,
                datasets: [{
                    label: 'Sales ($)',
                    data: salesData,
                    backgroundColor: 'rgba(0, 123, 255, 0.1)',
                    borderColor: 'rgba(0, 123, 255, 1)',
                    borderWidth: 2,
                    pointBackgroundColor: 'rgba(0, 123, 255, 1)',
                    pointBorderColor: '#fff',
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    tension: 0.3,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                label += '$' + parseFloat(context.raw).toFixed(2);
                                return label;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '$' + value;
                            }
                        }
                    }
                }
            }
        });
    }
    
    function initializePaymentMethodChart() {
        const ctx = document.getElementById('paymentMethodChart').getContext('2d');
        
        // Parse data from PHP
        const paymentMethodData = @json($paymentMethodSales ?? []);
        
        if (!paymentMethodData.length) {
            // Display a message if no data
            const noDataMessage = document.createElement('div');
            noDataMessage.className = 'text-center text-muted py-5';
            noDataMessage.textContent = 'No payment method data available for the selected period';
            ctx.canvas.parentNode.replaceChild(noDataMessage, ctx.canvas);
            return;
        }
        
        // Prepare data for chart
        const labels = paymentMethodData.map(item => item.payment_method || 'Unknown');
        const data = paymentMethodData.map(item => item.total);
        
        // Generate nice colors for each payment method
        const colors = [
            'rgba(255, 99, 132, 0.8)',
            'rgba(54, 162, 235, 0.8)',
            'rgba(255, 206, 86, 0.8)',
            'rgba(75, 192, 192, 0.8)',
            'rgba(153, 102, 255, 0.8)',
            'rgba(255, 159, 64, 0.8)'
        ];
        
        const chart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: data,
                    backgroundColor: colors.slice(0, data.length),
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = '$' + parseFloat(context.raw).toFixed(2);
                                const total = context.chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
                                const percentage = Math.round((context.raw / total) * 100);
                                return `${label}: ${value} (${percentage}%)`;
                            }
                        }
                    },
                    legend: {
                        position: 'right',
                    }
                }
            }
        });
    }
</script>
@endpush