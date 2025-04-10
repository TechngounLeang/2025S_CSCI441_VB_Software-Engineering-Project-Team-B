@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4>Sales Dashboard</h4>
                </div>
                <div class="card-body">
                    <!-- Summary Cards -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Today's Sales</h5>
                                    <h2>${{ number_format($todaySales, 2) }}</h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Weekly Sales</h5>
                                    <h2>${{ number_format($weeklySales, 2) }}</h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Monthly Sales</h5>
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
                                    <h5>Filter Sales Trend</h5>
                                </div>
                                <div class="card-body">
                                    <form action="{{ route('sales.index') }}" method="GET" class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="chart_start_date">Start Date</label>
                                                <input type="date" class="form-control" id="chart_start_date" name="start_date" value="{{ request('start_date', now()->subDays(30)->format('Y-m-d')) }}">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="chart_end_date">End Date</label>
                                                <input type="date" class="form-control" id="chart_end_date" name="end_date" value="{{ request('end_date', now()->format('Y-m-d')) }}">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>&nbsp;</label>
                                                <button type="submit" class="btn btn-primary d-block w-100">Apply Filter</button>
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
                                    <h5>Sales Trend</h5>
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
                                    <h5>Payment Methods</h5>
                                </div>
                                <div class="card-body">
                                    <canvas id="paymentMethodChart" height="300"></canvas>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Trending Items - Add this section -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Trending Items</h5>
                                </div>
                                <div class="card-body">
                                    @if(isset($trendingItems) && count($trendingItems) > 0)
                                        <ul class="list-group">
                                            @foreach($trendingItems as $item)
                                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                                    {{ $item->product_name }}
                                                    <span class="badge bg-primary rounded-pill text-white">{{ $item->quantity_sold }}</span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @else
                                        <p class="text-muted">No trending items data available.</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    
                    <!-- Order Breakdown -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Order Breakdown</h5>
                                </div>
                                <div class="card-body">
                                    @if($orderBreakdownCount > 0)
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>Date</th>
                                                        <th>Orders</th>
                                                        <th>Items Sold</th>
                                                        <th>Revenue</th>
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
                                            No order data available for the selected date range.
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Export Reports Section -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Export Reports</h5>
                                </div>
                                <div class="card-body">
                                    <form action="{{ route('sales.export') }}" method="GET">
                                        <div class="form-group mb-3">
                                            <label for="timeframe">Select Timeframe</label>
                                            <select name="timeframe" id="timeframe" class="form-control">
                                                <option value="today">Today</option>
                                                <option value="yesterday">Yesterday</option>
                                                <option value="this_week">This Week</option>
                                                <option value="last_week">Last Week</option>
                                                <option value="this_month">This Month</option>
                                                <option value="last_month">Last Month</option>
                                                <option value="custom">Custom Range</option>
                                            </select>
                                        </div>
                                        
                                        <div id="customDateRange" style="display: none;" class="mb-3">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label for="start_date">Start Date</label>
                                                    <input type="date" name="start_date" id="start_date" class="form-control">
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="end_date">End Date</label>
                                                    <input type="date" name="end_date" id="end_date" class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <button type="submit" class="btn btn-primary">Download CSV</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>
<script>
    Chart.register(ChartDataLabels);
    // Sales Chart
    const ctx = document.getElementById('salesChart').getContext('2d');
    const salesChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: {!! json_encode($dates) !!},
            datasets: [{
                label: 'Daily Sales',
                data: {!! json_encode($salesData) !!},
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1,
                tension: 0.1 // Adds slight curve to lines
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        // Add dollar sign to y-axis labels
                        callback: function(value) {
                            return '$' + value;
                        }
                    }
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'Sales: $' + context.raw.toFixed(2);
                        }
                    }
                }
            }
        }
    });
    
    // Payment Method Chart - Handle potential empty data
Chart.register(ChartDataLabels);
const paymentCtx = document.getElementById('paymentMethodChart').getContext('2d');

// Check if payment method data exists
const paymentMethodLabels = {!! json_encode($paymentMethodSales->pluck('payment_method')->toArray()) !!};
const paymentMethodData = {!! json_encode($paymentMethodSales->pluck('total')->toArray()) !!};

if (paymentMethodLabels.length > 0) {
    const paymentData = {
        labels: paymentMethodLabels,
        datasets: [{
            label: 'Sales by Payment Method',
            data: paymentMethodData,
            backgroundColor: [
                'rgba(255, 99, 132, 0.7)',
                'rgba(54, 162, 235, 0.7)',
                'rgba(255, 206, 86, 0.7)',
                'rgba(75, 192, 192, 0.7)'
            ],
            borderColor: [
                'rgba(255, 99, 132, 1)',
                'rgba(54, 162, 235, 1)',
                'rgba(255, 206, 86, 1)',
                'rgba(75, 192, 192, 1)'
            ],
            borderWidth: 1
        }]
    };
    
    const paymentChart = new Chart(paymentCtx, {
        type: 'pie',
        data: paymentData,
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            // Convert to number
                            const value = Number(context.raw) || 0;
                            const dataArr = context.dataset.data;
                            // Convert all values to numbers and sum them
                            const total = dataArr.reduce((sum, val) => sum + Number(val), 0);
                            
                            let percentage = 0;
                            if (total > 0) {
                                percentage = Math.round((value / total) * 100);
                            }
                            return label + ': $' + value.toFixed(2) + ' (' + percentage + '%)';
                        }
                    }
                },  // Added missing comma here
                // Add this section for displaying percentages on the chart
                datalabels: {
                    formatter: (value, ctx) => {
                        // Convert value to a number to ensure proper calculation
                        const numValue = Number(value);
                        const dataArr = ctx.chart.data.datasets[0].data;
                        // Convert all values to numbers and sum them
                        const total = dataArr.reduce((sum, val) => sum + Number(val), 0);
                        
                        let percentage = 0;
                        if (total > 0 && !isNaN(numValue)) {
                            percentage = Math.round((numValue / total) * 100);
                        }
                        return percentage + '%';
                    },
                    color: '#fff',
                    font: {
                        weight: 'bold',
                        size: 12
                    }
                }
            }
        }
    });

    } else {
        // Display a message if no payment method data
        document.getElementById('paymentMethodChart').height = 100;
        paymentCtx.font = '16px Arial';
        paymentCtx.fillStyle = '#666';
        paymentCtx.textAlign = 'center';
        paymentCtx.fillText('No payment method data available', paymentCtx.canvas.width/2, paymentCtx.canvas.height/2);
    }
    
    // Show/hide custom date range
    document.getElementById('timeframe').addEventListener('change', function() {
        if (this.value === 'custom') {
            document.getElementById('customDateRange').style.display = 'block';
        } else {
            document.getElementById('customDateRange').style.display = 'none';
        }
    });
</script>
@endsection