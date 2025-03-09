@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between py-3">
        <h2>Dashboard</h2>
        <button class="btn btn-primary">Export</button>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-body">
                    <h5>Total Order</h5>
                    <p class="h2">21,375</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-body">
                    <h5>New Customer</h5>
                    <p class="h2">1,012</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-body">
                    <h5>Total Sales</h5>
                    <p class="h2">$24,254</p>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <h5>Sales Analytics</h5>
            <div id="sales-analytics" style="height: 300px;"></div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <h5>Trending Coffee</h5>
            <ul class="list-group">
                <li class="list-group-item">Cappuccino</li>
                <li class="list-group-item">Latte</li>
                <li class="list-group-item">Frappuccino</li>
                <li class="list-group-item">Mocha</li>
            </ul>
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-body">
            <h5>Recent Order</h5>
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Item Name</th>
                        <th>Date & Time</th>
                        <th>Table Number</th>
                        <th>Price</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1</td>
                        <td>Cappuccino</td>
                        <td>02/16/2025 10:00 AM</td>
                        <td>5</td>
                        <td>$5.00</td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td>Americano</td>
                        <td>02/16/2025 10:05 AM</td>
                        <td>3</td>
                        <td>$3.00</td>
                    </tr>
                    <!-- Add more rows as needed -->
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Example of initializing a chart for sales analytics
    var ctx = document.getElementById('sales-analytics').getContext('2d');
    var salesChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['10 AM', '11 AM', '12 PM', '1 PM', '2 PM'],
            datasets: [{
                label: 'Sales',
                data: [200, 300, 400, 500, 450],
                borderColor: 'rgba(255, 99, 132, 1)',
                borderWidth: 2,
                fill: false
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>
@endsection
