<!-- Written & debugged by: Tech Ngoun Leang, Ratanakvesal Thong, Longwei Ngor, Samady Sok
Tested by: Tech Ngoun Leang-->
@extends('layouts.app')

@section('content')
<div class="container">
            <h1>Order #{{ $order->id }} Details</h1>

    <p><strong>Customer Name:</strong> {{ $order->customer_name }}</p>
    <p><strong>Total Amount:</strong> ${{ number_format($order->total_amount, 2) }}</p>
    <p><strong>Status:</strong> {{ $order->status }}</p>
    <p><strong>Payment Method:</strong> {{ ucfirst($order->payment_method) }}</p>

    <h3>Order Items</h3>
    <table class="table">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Quantity</th>
                                    <th>Unit Price</th>
                                    <th>Total Price</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($order->items as $item)
                                    <tr>
                                        <td>{{ $item->product->name }}</td>
                                        <td>{{ $item->quantity }}</td>
                                        <td>${{ number_format($item->unit_price, 2) }}</td>
                                        <td>${{ number_format($item->total_price, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
@endsection
