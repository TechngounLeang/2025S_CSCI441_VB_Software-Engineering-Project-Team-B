@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Create a New Order</h1>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('orders.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="customer_name" class="form-label">Customer Name</label>
            <input type="text" class="form-control @error('customer_name') is-invalid @enderror" 
                   id="customer_name" 
                   name="customer_name" 
                   value="{{ old('customer_name') }}" 
                   required>
            @error('customer_name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="customer_email" class="form-label">Customer Email</label>
            <input type="email" class="form-control @error('customer_email') is-invalid @enderror" 
                   id="customer_email" 
                   name="customer_email" 
                   value="{{ old('customer_email') }}" 
                   required>
            @error('customer_email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="payment_method" class="form-label">Payment Method</label>
            <select name="payment_method" id="payment_method" class="form-control">
                <option value="">Select Payment Method</option>
                <option value="credit_card">Credit Card</option>
                <option value="paypal">PayPal</option>
                <option value="bank_transfer">Bank Transfer</option>
            </select>
        </div>

        <h3>Products</h3>
        <div id="products-container">
            @foreach($products as $product)
                <div class="product mb-3">
                    <div class="row">
                        <div class="col-md-6">
                            <label for="product_{{ $product->id }}" class="form-label">
                                {{ $product->name }} 
                                (${{ number_format($product->price, 2) }}, 
                                Stock: {{ $product->stock }})
                            </label>
                        </div>
                        <div class="col-md-6">
                        <input type="number" 
       class="form-control product-quantity" 
       id="product_{{ $product->id }}" 
       name="products[{{ $loop->index }}][quantity]" 
       min="1"   // Changed from 0 to 1
       max="{{ $product->stock }}"
       placeholder="Quantity">
                            <input type="hidden" 
                                   name="products[{{ $loop->index }}][id]" 
                                   value="{{ $product->id }}">
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <button type="submit" class="btn btn-primary" style="display: block; width: 100%;">Create Order</button>
    </form>
</div>
@endsection