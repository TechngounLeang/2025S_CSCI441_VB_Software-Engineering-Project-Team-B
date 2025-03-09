@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Order History</h1>

    @if ($orders->isEmpty())
        <p>No orders found.</p>
    @else
        <table class="table mt-3">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Customer Name</th>
                    <th>Total Amount</th>
                    <th>Status</th>
                    <th>Payment Method</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($orders as $order)
                    <tr>
                        <td>{{ $order->id }}</td>
                        <td>{{ $order->customer_name }}</td>
                        <td>${{ number_format($order->total_amount, 2) }}</td>
                        <td>{{ $order->status }}</td>
                        <td>{{ ucfirst($order->payment_method) }}</td>
                        <td>
                            <!-- You can add edit or show actions if needed -->
                            <a href="#" class="btn btn-info">View</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection
