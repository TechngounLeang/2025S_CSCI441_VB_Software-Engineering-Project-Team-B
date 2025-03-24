@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Point of Sale (POS) System</h1>

    @if (session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
    @endif

    @if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif

    <form action="{{ route('pos.checkout') }}" method="POST" id="pos-form">
        @csrf
        <div class="row">
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header">Transaction Information</div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="register_id" class="form-label">Select Register</label>
                            <select class="form-control" id="register_id" name="register_id" required>
                                <option value="">Select Register</option>
                                @foreach($registers as $register)
                                <option value="{{ $register->id }}">Register #{{ $register->id }} ({{ $register->name }})</option>
                                @endforeach
                            </select>
                            <div class="form-text">
                                <a href="{{ route('pos.registers') }}">Manage Registers</a>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="customer_name" class="form-label">Customer Name</label>
                            <input type="text" class="form-control" id="customer_name" name="customer_name" required>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">Payment Details</div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="payment_method" class="form-label">Payment Method</label>
                            <select class="form-control" id="payment_method" name="payment_method" required>
                                <option value="">Select Payment Method</option>
                                <option value="cash">Cash</option>
                                <option value="credit_card">Credit Card</option>
                                <option value="debit_card">Debit Card</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">Product Selection</div>
                    <div class="card-body">
                        <div id="products-container">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Price</th>
                                        <th>Quantity</th>
                                        <th>Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($products as $product)
                                    <tr class="product-row" data-product-id="{{ $product->id }}" data-price="{{ $product->price }}">
                                        <td>
                                            {{ $product->name }}
                                            <input type="hidden" name="products[{{ $loop->index }}][id]" value="{{ $product->id }}">
                                        </td>
                                        <td>${{ number_format($product->price, 2) }}</td>
                                        <td>
                                            <input type="number" 
                                                   class="form-control product-quantity" 
                                                   name="products[{{ $loop->index }}][quantity]" 
                                                   min="0" 
                                                   max="{{ $product->stock_quantity }}"
                                                   data-max="{{ $product->stock_quantity }}"
                                                   placeholder="Qty">
                                        </td>
                                        <td class="product-subtotal">$0.00</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="3" class="text-right"><strong>Total:</strong></td>
                                        <td id="total-amount">$0.00</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-center mt-3">
            <button type="submit" class="btn btn-primary btn-lg" id="complete-sale-btn" disabled>
                Complete Sale
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Direct references to key elements
    const productRows = document.querySelectorAll('.product-row');
    const totalAmountElement = document.getElementById('total-amount');
    const completeSaleButton = document.getElementById('complete-sale-btn');
    
    // Simple function to update all calculations
    function updateCalculations() {
        let total = 0;
        let hasProducts = false;
        
        // Process each product row
        productRows.forEach(row => {
            const quantityInput = row.querySelector('.product-quantity');
            const subtotalElement = row.querySelector('.product-subtotal');
            const price = parseFloat(row.dataset.price);
            const quantity = parseInt(quantityInput.value) || 0;
            
            // Calculate and display subtotal
            const subtotal = price * quantity;
            subtotalElement.textContent = '$' + subtotal.toFixed(2);
            
            // Add to total
            total += subtotal;
            
            // Check if any products are selected
            if (quantity > 0) {
                hasProducts = true;
            }
        });
        
        // Update total display
        totalAmountElement.textContent = '$' + total.toFixed(2);
        
        // Enable or disable complete button based on having products
        completeSaleButton.disabled = !hasProducts;
    }
    
    // Add event listeners to quantity inputs
    productRows.forEach(row => {
        const quantityInput = row.querySelector('.product-quantity');
        quantityInput.addEventListener('change', updateCalculations);
        quantityInput.addEventListener('input', updateCalculations);
        quantityInput.addEventListener('keyup', updateCalculations);
    });
    
    // Initial calculation
    updateCalculations();
});
</script>
@endpush
@endsection