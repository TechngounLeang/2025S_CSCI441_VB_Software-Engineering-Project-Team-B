@extends('layouts.app')

@section('content')
<style>
/* Overlay styles */
.receipt-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.7);
    z-index: 1000;
    display: none;
    justify-content: center;
    align-items: center;
    overflow-y: auto;
    padding: 30px;
}

.receipt-container {
    background-color: white;
    border-radius: 8px;
    width: 100%;
    max-width: 800px;
    max-height: 90vh;
    overflow-y: auto;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
    animation: fadeIn 0.3s ease-out;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-20px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>

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

        <div id="sale-buttons" class="text-center mt-3">
            <button type="button" class="btn btn-primary btn-lg" id="complete-sale-btn" disabled>
                Complete Sale
            </button>
        </div>
    </form>
</div>

<!-- Receipt Overlay -->
<div class="receipt-overlay" id="receipt-overlay">
    <div class="receipt-container">
        <div class="card border-success mb-0">
            <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Purchase Receipt</h4>
                <button type="button" class="btn-close btn-close-white" id="close-receipt" aria-label="Close"></button>
            </div>
            <div class="card-body">
                <div id="receipt-content" class="p-3">
                    <div class="text-center mb-4">
                        <h4>Store Name</h4>
                        <p>123 Main Street, City, State 12345</p>
                        <p>Phone: (123) 456-7890</p>
                        <p class="mb-3">Receipt #<span id="receipt-number"></span></p>
                        <p>Date: <span id="receipt-date"></span></p>
                    </div>
                    
                    <div class="mb-3">
                        <p><strong>Register:</strong> <span id="receipt-register"></span></p>
                        <p><strong>Customer:</strong> <span id="receipt-customer"></span></p>
                        <p><strong>Payment Method:</strong> <span id="receipt-payment"></span></p>
                    </div>
                    
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>Price</th>
                                <th>Qty</th>
                                <th>Amount</th>
                            </tr>
                        </thead>
                        <tbody id="receipt-items">
                            <!-- Items will be added here dynamically -->
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" class="text-end"><strong>Total:</strong></td>
                                <td id="receipt-total"></td>
                            </tr>
                        </tfoot>
                    </table>
                    
                    <div class="text-center mt-4">
                        <p>Thank you for your purchase!</p>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <div class="d-flex justify-content-between">
                    <button type="button" class="btn btn-secondary" id="back-to-sale">Back to Sale</button>
                    <div>
                        <button type="button" class="btn btn-primary me-2" id="print-receipt">Print Receipt</button>
                        <button type="button" class="btn btn-success" id="confirm-sale">Confirm & Submit</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded - setting up POS functionality');
    
    // Direct references to key elements
    const productRows = document.querySelectorAll('.product-row');
    const totalAmountElement = document.getElementById('total-amount');
    const completeSaleButton = document.getElementById('complete-sale-btn');
    const posForm = document.getElementById('pos-form');
    const receiptOverlay = document.getElementById('receipt-overlay');
    
    if (!productRows.length || !totalAmountElement || !completeSaleButton || !posForm || !receiptOverlay) {
        console.error('Required DOM elements not found');
        return;
    }
    
    // Simple function to update all calculations
    function updateCalculations() {
        console.log('Updating calculations...');
        let total = 0;
        let hasProducts = false;
        
        // Process each product row
        productRows.forEach((row, index) => {
            const quantityInput = row.querySelector('.product-quantity');
            const subtotalElement = row.querySelector('.product-subtotal');
            
            if (!quantityInput || !subtotalElement) {
                console.error(`Missing elements in row ${index}`);
                return;
            }
            
            const price = parseFloat(row.dataset.price) || 0;
            const quantity = parseInt(quantityInput.value) || 0;
            console.log(`Row ${index}: price=${price}, quantity=${quantity}`);
            
            // Calculate and display subtotal
            const subtotal = price * quantity;
            subtotalElement.textContent = '$' + subtotal.toFixed(2);
            console.log(`Row ${index}: subtotal=${subtotal}`);
            
            // Add to total
            total += subtotal;
            
            // Check if any products are selected
            if (quantity > 0) {
                hasProducts = true;
            }
        });
        
        // Update total display
        totalAmountElement.textContent = '$' + total.toFixed(2);
        console.log(`Total: ${total}, Has Products: ${hasProducts}`);
        
        // Enable or disable complete button based on having products
        completeSaleButton.disabled = !hasProducts;
    }
    
    // Add event listeners to quantity inputs
    productRows.forEach((row, index) => {
        const quantityInput = row.querySelector('.product-quantity');
        if (quantityInput) {
            console.log(`Adding listeners to row ${index}`);
            quantityInput.addEventListener('change', updateCalculations);
            quantityInput.addEventListener('input', updateCalculations);
        }
    });
    
    // Receipt generation functionality
    completeSaleButton.addEventListener('click', function(e) {
        e.preventDefault();
        
        // Generate receipt
        generateReceipt();
        
        // Show receipt overlay with flex display for centering
        receiptOverlay.style.display = 'flex';
        
        // Disable scrolling on the body
        document.body.style.overflow = 'hidden';
    });
    
    // Back to sale button
    document.getElementById('back-to-sale').addEventListener('click', function() {
        hideReceiptOverlay();
    });
    
    // Close button
    document.getElementById('close-receipt').addEventListener('click', function() {
        hideReceiptOverlay();
    });
    
    // Click outside to close
    receiptOverlay.addEventListener('click', function(e) {
        if (e.target === receiptOverlay) {
            hideReceiptOverlay();
        }
    });
    
    // Function to hide receipt overlay
    function hideReceiptOverlay() {
        receiptOverlay.style.display = 'none';
        document.body.style.overflow = '';
    }
    
    // Function to generate receipt
    function generateReceipt() {
        console.log('Generating receipt...');
        
        // Get form data
        const registerSelect = document.getElementById('register_id');
        const registerText = registerSelect.options[registerSelect.selectedIndex].text;
        const customerName = document.getElementById('customer_name').value;
        const paymentMethodSelect = document.getElementById('payment_method');
        const paymentMethod = paymentMethodSelect.options[paymentMethodSelect.selectedIndex].text;
        
        // Populate receipt header
        document.getElementById('receipt-number').textContent = 'INV-' + Math.floor(Math.random() * 10000);
        document.getElementById('receipt-date').textContent = new Date().toLocaleString();
        document.getElementById('receipt-register').textContent = registerText;
        document.getElementById('receipt-customer').textContent = customerName;
        document.getElementById('receipt-payment').textContent = paymentMethod;
        
        // Clear previous items
        const receiptItemsContainer = document.getElementById('receipt-items');
        receiptItemsContainer.innerHTML = '';
        
        // Add purchased items to receipt
        let totalAmount = 0;
        
        productRows.forEach(row => {
            const quantity = parseInt(row.querySelector('.product-quantity').value) || 0;
            
            if (quantity > 0) {
                const productName = row.querySelector('td:first-child').textContent.trim();
                const price = parseFloat(row.dataset.price);
                const subtotal = price * quantity;
                totalAmount += subtotal;
                
                // Create row for receipt
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${productName}</td>
                    <td>$${price.toFixed(2)}</td>
                    <td>${quantity}</td>
                    <td>$${subtotal.toFixed(2)}</td>
                `;
                receiptItemsContainer.appendChild(tr);
            }
        });
        
        // Update total on receipt
        document.getElementById('receipt-total').textContent = '$' + totalAmount.toFixed(2);
    }
    
    // Print receipt button
    document.getElementById('print-receipt').addEventListener('click', function() {
        const receiptContent = document.getElementById('receipt-content').innerHTML;
        const printWindow = window.open('', '_blank');
        printWindow.document.write(`
            <html>
                <head>
                    <title>Print Receipt</title>
                    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
                    <style>
                        body { font-family: Arial, sans-serif; padding: 20px; }
                    </style>
                </head>
                <body>
                    <div class="container">
                        ${receiptContent}
                    </div>
                </body>
            </html>
        `);
        printWindow.document.close();
        setTimeout(() => {
            printWindow.print();
        }, 500);
    });
    
    // Confirm sale button - submit the form
    document.getElementById('confirm-sale').addEventListener('click', function() {
        hideReceiptOverlay();
        posForm.submit();
    });
    
    // Initial calculation
    console.log('Running initial calculation');
    updateCalculations();
});
</script>
@endpush
@endsection