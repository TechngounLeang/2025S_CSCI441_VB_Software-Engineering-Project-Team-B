<!-- Written & debugged by: Tech Ngoun Leang, Ratanakvesal Thong, Longwei Ngor, Samady Sok
Tested by: Tech Ngoun Leang-->
@extends('layouts.app')

@section('content')
<div class="container">
    <h1>{{ $product->name }}</h1>
    <p>{{ $product->description }}</p>
    <p>Price: ${{ number_format($product->price, 2) }}</p>
    <p>Stock: {{ $product->stock_quantity }}</p>

    <h3>Total Sales for this Product: ${{ number_format($totalSalesForProduct, 2) }}</h3>
</div>
@endsection
