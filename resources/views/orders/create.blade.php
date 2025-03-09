@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Create a New Order</h1>

    <form action="{{ route('orders.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="customer_name" class="form-label">Customer Name</label>
            <input type="text" class="form-control" id="customer_name" name="customer_name" required>
        </div>

        <div class="mb-3">
            <label for="customer_email" class="form-label">Customer Email</label>
            <input type="email" class="form-control" id="customer_email" name="customer_email" required>
        </div>

        <h3>Products</h3>
        <div id="products-container">
            @foreach($products as $product)
                <div class="product mb-3">
                    <label for="product_{{ $product->id }}" class="form-label">{{ $product->name }}</label>
                    <input type="number" class="form-control product-quantity" id="product_{{ $product->id }}" name="products[{{ $loop->index }}][quantity]" min="1" required>
                    <input type="hidden" name="products[{{ $loop->index }}][id]" value="{{ $product->id }}">
                </div>
            @endforeach
        </div>

        <button type="submit" class="btn btn-primary">Create Order</button>
    </form>
</div>
@endsection
