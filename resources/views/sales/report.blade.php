@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Sales Report</h1>

    <table class="table">
        <thead>
            <tr>
                <th>Product</th>
                <th>Total Quantity Sold</th>
                <th>Total Sales</th>
            </tr>
        </thead>
        <tbody>
            @foreach($salesByProduct as $sale)
                <tr>
                    <td>{{ $sale->product->name }}</td>
                    <td>{{ $sale->total_quantity }}</td>
                    <td>${{ number_format($sale->total_sales, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
