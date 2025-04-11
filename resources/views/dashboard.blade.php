@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center py-3">
        <h2>Dashboard</h2>
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
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Total Orders</h6>
                            <h3 class="mb-0">21,375</h3>
                            <p class="small text-success mb-0">
                                <i class="fas fa-arrow-up"></i> 12% from last month
                            </p>
                        </div>
                        <div class="bg-light p-3 rounded">
                            <i class="fas fa-shopping-cart text-primary" style="font-size: 24px;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">New Customers</h6>
                            <h3 class="mb-0">1,012</h3>
                            <p class="small text-success mb-0">
                                <i class="fas fa-arrow-up"></i> 5.2% from last month
                            </p>
                        </div>
                        <div class="bg-light p-3 rounded">
                            <i class="fas fa-users text-warning" style="font-size: 24px;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Total Sales</h6>
                            <h3 class="mb-0">$24,254</h3>
                            <p class="small text-success mb-0">
                                <i class="fas fa-arrow-up"></i> 8.7% from last month
                            </p>
                        </div>
                        <div class="bg-light p-3 rounded">
                            <i class="fas fa-dollar-sign text-success" style="font-size: 24px;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Monthly Sales Analytics -->
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Monthly Sales Analytics</h5>
            <div class="btn-group btn-group-sm">
                <button type="button" class="btn btn-outline-secondary active">Daily</button>
                <button type="button" class="btn btn-outline-secondary">Weekly</button>
                <button type="button" class="btn btn-outline-secondary">Monthly</button>
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
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Trending Coffee</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <span class="badge bg-primary rounded-pill me-2">1</span>
                                Cappuccino
                            </div>
                            <span class="badge bg-light text-dark">342 orders</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <span class="badge bg-secondary rounded-pill me-2">2</span>
                                Latte
                            </div>
                            <span class="badge bg-light text-dark">290 orders</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <span class="badge bg-secondary rounded-pill me-2">3</span>
                                Frappuccino
                            </div>
                            <span class="badge bg-light text-dark">265 orders</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <span class="badge bg-secondary rounded-pill me-2">4</span>
                                Mocha
                            </div>
                            <span class="badge bg-light text-dark">211 orders</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <span class="badge bg-secondary rounded-pill me-2">5</span>
                                Espresso
                            </div>
                            <span class="badge bg-light text-dark">187 orders</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        
        <!-- Recent Orders -->
        <div class="col-md-7">
            <div class="card shadow-sm">
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
                                    <th>Item</th>
                                    <th>Date & Time</th>
                                    <th>Table</th>
                                    <th>Price</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td>Cappuccino</td>
                                    <td>{{ \Carbon\Carbon::now()->format('m/d/Y h:i A') }}</td>
                                    <td>5</td>
                                    <td>$5.00</td>
                                    <td><span class="badge bg-success">Completed</span></td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td>Americano</td>
                                    <td>{{ \Carbon\Carbon::now()->subMinutes(15)->format('m/d/Y h:i A') }}</td>
                                    <td>3</td>
                                    <td>$3.00</td>
                                    <td><span class="badge bg-success">Completed</span></td>
                                </tr>
                                <tr>
                                    <td>3</td>
                                    <td>Latte + Croissant</td>
                                    <td>{{ \Carbon\Carbon::now()->subMinutes(25)->format('m/d/Y h:i A') }}</td>
                                    <td>7</td>
                                    <td>$8.50</td>
                                    <td><span class="badge bg-success">Completed</span></td>
                                </tr>
                                <tr>
                                    <td>4</td>
                                    <td>Double Espresso</td>
                                    <td>{{ \Carbon\Carbon::now()->subMinutes(40)->format('m/d/Y h:i A') }}</td>
                                    <td>2</td>
                                    <td>$4.00</td>
                                    <td><span class="badge bg-success">Completed</span></td>
                                </tr>
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
        // Monthly sales data for the chart
        const currentMonth = new Date().getMonth();
        const monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
        const currentMonthName = monthNames[currentMonth];
        
        // Generate days of the current month
        const daysInMonth = new Date(new Date().getFullYear(), currentMonth + 1, 0).getDate();
        const days = Array.from({length: daysInMonth}, (_, i) => i + 1);
        
        // Generate random sales data for each day
        const generateRandomSales = () => {
            return Array.from({length: daysInMonth}, () => Math.floor(Math.random() * 2000) + 1000);
        };
        
        // Create the sales chart
        var ctx = document.getElementById('sales-analytics').getContext('2d');
        var salesChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: days.map(day => `${day} ${currentMonthName}`),
                datasets: [{
                    label: 'Daily Sales ($)',
                    data: generateRandomSales(),
                    borderColor: 'rgba(59, 130, 246, 1)',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    borderWidth: 2,
                    tension: 0.3,
                    fill: true,
                    pointBackgroundColor: 'rgba(59, 130, 246, 1)',
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
                                return `Sales: $${context.parsed.y.toLocaleString()}`;
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
        document.querySelectorAll('.btn-group .btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.btn-group .btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                
                // Update chart data based on selected time period
                // This is just a simulation - in a real app you would fetch data from server
                if (this.textContent === 'Daily') {
                    salesChart.data.labels = days.map(day => `${day} ${currentMonthName}`);
                    salesChart.data.datasets[0].data = generateRandomSales();
                } else if (this.textContent === 'Weekly') {
                    const weeks = ['Week 1', 'Week 2', 'Week 3', 'Week 4', 'Week 5'];
                    salesChart.data.labels = weeks;
                    salesChart.data.datasets[0].data = [
                        Math.floor(Math.random() * 10000) + 5000,
                        Math.floor(Math.random() * 10000) + 5000,
                        Math.floor(Math.random() * 10000) + 5000,
                        Math.floor(Math.random() * 10000) + 5000,
                        Math.floor(Math.random() * 10000) + 5000
                    ];
                } else if (this.textContent === 'Monthly') {
                    const last6Months = [];
                    for (let i = 5; i >= 0; i--) {
                        let d = new Date();
                        d.setMonth(d.getMonth() - i);
                        last6Months.push(monthNames[d.getMonth()]);
                    }
                    salesChart.data.labels = last6Months;
                    salesChart.data.datasets[0].data = [
                        Math.floor(Math.random() * 50000) + 20000,
                        Math.floor(Math.random() * 50000) + 20000,
                        Math.floor(Math.random() * 50000) + 20000,
                        Math.floor(Math.random() * 50000) + 20000,
                        Math.floor(Math.random() * 50000) + 20000,
                        Math.floor(Math.random() * 50000) + 20000
                    ];
                }
                salesChart.update();
            });
        });
    });
</script>
@endsection