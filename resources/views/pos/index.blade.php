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

.product-card {
    transition: all 0.2s ease;
    height: 100%;
}

.product-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.product-card.border-primary {
    border-width: 2px;
}

.card-img-top {
    height: 120px;
    object-fit: cover;
    background-color: #f8f9fa;
}

.order-summary {
    position: sticky;
    top: 1rem;
}

.order-item {
    padding: 8px 0;
    border-bottom: 1px solid #eee;
}

.order-item:last-child {
    border-bottom: none;
}

.product-selection {
    max-height: calc(100vh - 280px);
    overflow-y: auto;
}
</style>

<div class="container">
    <h1>{{ __('app.point_of_sale_system') }}</h1>

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
        <!-- Transaction Info and Payment Details Side by Side -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-header">{{ __('app.transaction_information') }}</div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="register_id" class="form-label">{{ __('app.select_register') }}</label>
                            <select class="form-control" id="register_id" name="register_id" required>
                                <option value="">{{ __('app.select_register') }}</option>
                                @foreach($registers as $register)
                                <option value="{{ $register->id }}">{{ __('app.register') }} #{{ $register->id }} ({{ $register->name }})</option>
                                @endforeach
                            </select>
                            <div class="form-text">
                                <a href="{{ route('pos.registers') }}">{{ __('app.manage_registers') }}</a>
                            </div>
                        </div>
                        
                        <div class="mb-0">
                            <label for="customer_name" class="form-label">{{ __('app.customer_name') }}</label>
                            <input type="text" class="form-control" id="customer_name" name="customer_name" required>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-header">{{ __('app.payment_details') }}</div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="payment_method" class="form-label">{{ __('app.payment_method') }}</label>
                            <select class="form-control" id="payment_method" name="payment_method" required>
                                <option value="">{{ __('app.select_payment_method') }}</option>
                                <option value="cash">{{ __('app.cash') }}</option>
                                <option value="credit_card">{{ __('app.credit_card') }}</option>
                                <option value="debit_card">{{ __('app.debit_card') }}</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Products and Order Summary -->
        <div class="row">
            <!-- Product Selection (70%) -->
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-header">{{ __('app.product_selection') }}</div>
                    <div class="card-body product-selection">
                        <div id="products-container">
                            <div class="row">
                                @foreach($products as $product)
                                <div class="col-md-4 mb-3">
                                    <div class="card product-card" data-product-id="{{ $product->id }}" data-price="{{ $product->price }}">
                                        @if($product->photo_path)
                                            <img src="{{ asset('storage/' . $product->photo_path) }}" class="card-img-top" alt="{{ $product->name }}">
                                        @else
                                            <img src="https://placehold.co/150x100?text={{ urlencode($product->name) }}" class="card-img-top" alt="{{ $product->name }}">
                                        @endif
                                        <div class="card-body p-2">
                                            <h6 class="card-title mb-1">{{ $product->name }}</h6>
                                            <p class="card-text mb-2">${{ number_format($product->price, 2) }}</p>
                                            <div class="input-group input-group-sm">
                                                <input type="number" 
                                                       class="form-control product-quantity" 
                                                       name="products[{{ $loop->index }}][quantity]" 
                                                       min="0" 
                                                       max="{{ $product->stock_quantity }}"
                                                       data-max="{{ $product->stock_quantity }}"
                                                       placeholder="{{ __('app.quantity') }}">
                                                <input type="hidden" name="products[{{ $loop->index }}][id]" value="{{ $product->id }}">
                                            </div>
                                            <div class="product-subtotal text-end mt-1">$0.00</div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Summary (30%) -->
            <div class="col-lg-4">
                <div class="card order-summary mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">{{ __('app.order_summary') }}</h5>
                    </div>
                    <div class="card-body">
                        <div id="order-items" class="mb-3">
                            <!-- Order items will be displayed here dynamically -->
                            <div class="text-center text-muted py-4" id="empty-cart-message">
                                <i class="fas fa-shopping-cart mb-2" style="font-size: 24px;"></i>
                                <p>{{ __('app.no_items_added_yet') }}</p>
                            </div>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>{{ __('app.subtotal') }}</span>
                            <span id="subtotal-amount">$0.00</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>{{ __('app.tax') }}</span>
                            <span id="tax-amount">$0.00</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">{{ __('app.total') }}</h5>
                            <h5 class="mb-0" id="total-amount">$0.00</h5>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="button" class="btn btn-primary btn-lg w-100" id="complete-sale-btn" disabled>
                            {{ __('app.complete_sale') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Receipt Overlay -->
<div class="receipt-overlay" id="receipt-overlay">
    <div class="receipt-container">
        <div class="card border-success mb-0">
            <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                <h4 class="mb-0">{{ __('app.purchase_receipt') }}</h4>
                <button type="button" class="btn-close btn-close-white" id="close-receipt" aria-label="Close"></button>
            </div>
            <div class="card-body">
                <div id="receipt-content" class="p-3">
                    <div class="text-center mb-4">
                        <h4>{{ __('app.store_name') }}</h4>
                        <p>{{ __('app.store_address') }}</p>
                        <p>{{ __('app.store_phone') }}</p>
                        <p class="mb-3">{{ __('app.receipt') }} #<span id="receipt-number"></span></p>
                        <p>{{ __('app.date') }}: <span id="receipt-date"></span></p>
                    </div>
                    
                    <div class="mb-3">
                        <p><strong>{{ __('app.register') }}:</strong> <span id="receipt-register"></span></p>
                        <p><strong>{{ __('app.customer') }}:</strong> <span id="receipt-customer"></span></p>
                        <p><strong>{{ __('app.payment_method') }}:</strong> <span id="receipt-payment"></span></p>
                    </div>
                    
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>{{ __('app.item') }}</th>
                                <th>{{ __('app.price') }}</th>
                                <th>{{ __('app.qty') }}</th>
                                <th>{{ __('app.amount') }}</th>
                            </tr>
                        </thead>
                        <tbody id="receipt-items">
                            <!-- Items will be added here dynamically -->
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" class="text-end"><strong>{{ __('app.total') }}:</strong></td>
                                <td id="receipt-total"></td>
                            </tr>
                        </tfoot>
                    </table>
                    
                    <div class="text-center mt-4">
                        <p>{{ __('app.thank_you_for_purchase') }}</p>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <div class="d-flex justify-content-between">
                    <button type="button" class="btn btn-secondary" id="back-to-sale">{{ __('app.back_to_sale') }}</button>
                    <div>
                        <button type="button" class="btn btn-primary me-2" id="print-receipt">{{ __('app.print_receipt') }}</button>
                        <button type="button" class="btn btn-success" id="confirm-sale">{{ __('app.confirm_and_submit') }}</button>
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
    const totalAmountElement = document.getElementById('total-amount');
    const subtotalAmountElement = document.getElementById('subtotal-amount');
    const taxAmountElement = document.getElementById('tax-amount');
    const orderItemsContainer = document.getElementById('order-items');
    const emptyCartMessage = document.getElementById('empty-cart-message');
    const completeSaleButton = document.getElementById('complete-sale-btn');
    const posForm = document.getElementById('pos-form');
    const receiptOverlay = document.getElementById('receipt-overlay');
    
    // Tax rate
    const TAX_RATE = 0.08; // 8%
    
    // Simple function to update all calculations
    function updateCalculations() {
        console.log('Updating calculations...');
        let subtotal = 0;
        let hasProducts = false;
        let orderItems = [];
        
        // Process each product card
        document.querySelectorAll('.product-card').forEach((card, index) => {
            const quantityInput = card.querySelector('.product-quantity');
            const subtotalElement = card.querySelector('.product-subtotal');
            
            if (!quantityInput || !subtotalElement) {
                console.error(`Missing elements in card ${index}`);
                return;
            }
            
            const productName = card.querySelector('.card-title').textContent.trim();
            const price = parseFloat(card.dataset.price) || 0;
            const quantity = parseInt(quantityInput.value) || 0;
            
            // Calculate and display subtotal
            const itemSubtotal = price * quantity;
            subtotalElement.textContent = '$' + itemSubtotal.toFixed(2);
            
            // Add to subtotal
            subtotal += itemSubtotal;
            
            // Check if any products are selected
            if (quantity > 0) {
                hasProducts = true;
                card.classList.add('border-primary');
                
                // Add to order items
                orderItems.push({
                    name: productName,
                    price: price,
                    quantity: quantity,
                    subtotal: itemSubtotal
                });
            } else {
                card.classList.remove('border-primary');
            }
        });
        
        // Calculate tax and total
        const tax = subtotal * TAX_RATE;
        const total = subtotal + tax;
        
        // Update display
        subtotalAmountElement.textContent = '$' + subtotal.toFixed(2);
        taxAmountElement.textContent = '$' + tax.toFixed(2);
        totalAmountElement.textContent = '$' + total.toFixed(2);
        
        // Enable or disable complete button based on having products
        completeSaleButton.disabled = !hasProducts;
        
        // Update order summary
        updateOrderSummary(orderItems, hasProducts);
    }
    
    // Function to update order summary
    function updateOrderSummary(items, hasProducts) {
        // Clear previous items
        if (emptyCartMessage) {
            emptyCartMessage.style.display = hasProducts ? 'none' : 'block';
        }
        
        // If there are no items with quantity, just return
        if (!hasProducts) {
            orderItemsContainer.innerHTML = `
                <div class="text-center text-muted py-4" id="empty-cart-message">
                    <i class="fas fa-shopping-cart mb-2" style="font-size: 24px;"></i>
                    <p>${window.translations?.app?.no_items_added_yet || 'No items added yet'}</p>
                </div>
            `;
            return;
        }
        
        // Create HTML for order items
        let html = '';
        items.forEach(item => {
            html += `
                <div class="order-item">
                    <div class="d-flex justify-content-between">
                        <span>${item.name} x ${item.quantity}</span>
                        <span>$${item.subtotal.toFixed(2)}</span>
                    </div>
                    <div class="small text-muted">$${item.price.toFixed(2)} each</div>
                </div>
            `;
        });
        
        // Update order items container
        orderItemsContainer.innerHTML = html;
    }
    
    // Add event listeners to quantity inputs
    document.querySelectorAll('.product-card').forEach((card, index) => {
        const quantityInput = card.querySelector('.product-quantity');
        if (quantityInput) {
            console.log(`Adding listeners to card ${index}`);
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
        
        const productCards = document.querySelectorAll('.product-card');
        productCards.forEach(card => {
            const quantity = parseInt(card.querySelector('.product-quantity').value) || 0;
            
            if (quantity > 0) {
                const productName = card.querySelector('.card-title').textContent.trim();
                const price = parseFloat(card.dataset.price);
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
                    <title>${window.translations?.app?.print_receipt || 'Print Receipt'}</title>
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

    // Add this near the beginning of your DOMContentLoaded function
    const registerSelect = document.getElementById('register_id');

    // Load the previously selected register from localStorage
    if (registerSelect) {
        const savedRegisterId = localStorage.getItem('selectedRegisterId');
        if (savedRegisterId) {
            // Find the option with the saved value and select it
            const options = registerSelect.options;
            for (let i = 0; i < options.length; i++) {
                if (options[i].value === savedRegisterId) {
                    registerSelect.selectedIndex = i;
                    break;
                }
            }
        }
        
        // Save the selection when it changes
        registerSelect.addEventListener('change', function() {
            localStorage.setItem('selectedRegisterId', this.value);
        });
    }
});
</script>
@endpush
@endsection