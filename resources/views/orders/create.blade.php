<!-- Written & debugged by: Tech Ngoun Leang, Ratanakvesal Thong, Longwei Ngor, Samady Sok
Tested by: Tech Ngoun Leang-->
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1>Create a New Order</h1>
        </div>
        <div class="col-md-4 text-right">
            <a href="{{ route('orders.index') }}" class="btn btn-secondary">Back to Orders</a>
        </div>
    </div>

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">Order Information</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('orders.store') }}" method="POST">
                @csrf

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="customer_name" class="form-label">Customer Name</label>
                        <input type="text" class="form-control @error('customer_name') is-invalid @enderror" 
                            id="customer_name" name="customer_name" value="{{ old('customer_name') }}" required>
                        @error('customer_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="customer_email" class="form-label">Customer Email</label>
                        <input type="email" class="form-control @error('customer_email') is-invalid @enderror" 
                            id="customer_email" name="customer_email" value="{{ old('customer_email') }}" required>
                        @error('customer_email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6">
                        <label for="payment_method" class="form-label">Payment Method</label>
                        <select class="form-control @error('payment_method') is-invalid @enderror" 
                            id="payment_method" name="payment_method" required>
                            <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>Cash</option>
                            <option value="credit_card" {{ old('payment_method') == 'credit_card' ? 'selected' : '' }}>Credit Card</option>
                            <option value="bank_transfer" {{ old('payment_method') == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                            <option value="paypal" {{ old('payment_method') == 'paypal' ? 'selected' : '' }}>PayPal</option>
                        </select>
                        @error('payment_method')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Products</h5>
                    </div>
                    <div class="card-body">
                        @if($products->isEmpty())
                            <div class="alert alert-warning">
                                No products available. Please add products first.
                            </div>
                        @else
                            <div id="products-container" class="row">
                                @foreach($products as $index => $product)
                                    <div class="col-md-6 mb-3">
                                        <div class="card h-100">
                                            <div class="card-body">
                                                <h6 class="card-title">{{ $product->name }}</h6>
                                                <p class="card-text text-muted mb-2">${{ number_format($product->price, 2) }}</p>
                                                <div class="form-group mb-0">
                                                    <label for="product_{{ $product->id }}" class="form-label">Quantity</label>
                                                    <input type="number" class="form-control product-quantity @error('products.'.$index.'.quantity') is-invalid @enderror" 
                                                        id="product_{{ $product->id }}" 
                                                        name="products[{{ $index }}][quantity]" 
                                                        min="0" 
                                                        value="{{ old('products.'.$index.'.quantity', 0) }}">
                                                    <input type="hidden" name="products[{{ $index }}][id]" value="{{ $product->id }}">
                                                    @error('products.'.$index.'.quantity')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            @error('products')
                                <div class="alert alert-danger mt-3">{{ $message }}</div>
                            @enderror
                        @endif
                    </div>
                </div>

                <div class="text-right">
                    <button type="submit" class="btn btn-primary" {{ $products->isEmpty() ? 'disabled' : '' }}>Create Order</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const quantityInputs = document.querySelectorAll('.product-quantity');
        
        // Ensure at least one product has a quantity greater than 0
        document.querySelector('form').addEventListener('submit', function(e) {
            let hasProducts = false;
            
            quantityInputs.forEach(input => {
                if (parseInt(input.value) > 0) {
                    hasProducts = true;
                }
            });
            
            if (!hasProducts) {
                e.preventDefault();
                alert('Please add at least one product to the order.');
            }
        });
    });
</script>
@endpush
@endsection
