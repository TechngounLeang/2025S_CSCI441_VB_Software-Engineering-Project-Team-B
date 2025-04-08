<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Register;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class POSController extends Controller
{
    // Show the POS interface with register selection
    // In POSController.php index() method
public function index()
{
    $products = Product::where('stock_quantity', '>', 0)->get();
    
    // Either show all registers:
    $registers = Register::all();
    
    // OR show both open and closed (but indicate status):
    // $registers = Register::orderBy('status', 'desc')->get();
    
    return view('pos.index', compact('products', 'registers'));
}

    // Handle the checkout process
    public function checkout(Request $request)
{
    // First, filter out products with zero quantity
    $filteredProducts = [];
    foreach ($request->products as $key => $product) {
        if (isset($product['quantity']) && (int)$product['quantity'] > 0) {
            $filteredProducts[] = $product;
        }
    }
    
    // Replace products in request with filtered list
    $request->merge(['products' => $filteredProducts]);
    
    // Now validate the filtered products
    $request->validate([
        'products' => 'required|array|min:1',
        'products.*.id' => 'exists:products,id',
        'products.*.quantity' => 'required|integer|min:1',
        'customer_name' => 'required|string',
        'payment_method' => 'required|string|in:cash,credit_card,debit_card',
        'register_id' => 'required|exists:registers,id',
    ]);

    try {
        // Use a database transaction to ensure data integrity
        return DB::transaction(function () use ($request) {
            $register = Register::lockForUpdate()->findOrFail($request->register_id);
            
            // Verify register is open
            if ($register->status !== 'open') {
                throw new \Exception("Register is not open for sales");
            }

            $totalAmount = 0;
            $orderItems = [];

            // Process each product
            foreach ($request->products as $productData) {
                $product = Product::lockForUpdate()->find($productData['id']);
                
                // Check if enough stock is available
                if ($product->stock_quantity < $productData['quantity']) {
                    throw new \Exception("Insufficient stock for product: {$product->name}");
                }

                $itemTotal = $product->price * $productData['quantity'];
                $totalAmount += $itemTotal;

                // Prepare order items and update stock
                $orderItems[] = [
                    'product_id' => $product->id,
                    'quantity' => $productData['quantity'],
                    'unit_price' => $product->price,
                    'total_price' => $itemTotal,
                ];

                // Reduce stock quantity
                $product->decrement('stock_quantity', $productData['quantity']);
            }

            // Create the order
            $order = Order::create([
                'customer_name' => $request->customer_name,
                'total_amount' => $totalAmount,
                'status' => 'completed',
                'payment_method' => $request->payment_method,
                'register_id' => $register->id,
                'cashier_id' => auth()->id(),
            ]);

            // Create order items
            $order->items()->createMany($orderItems);

            // Update register cash balance if payment is cash
            if ($request->payment_method === 'cash') {
                $register->increment('cash_balance', $totalAmount);
            }
            
            // Update register transaction count
            $register->increment('transaction_count');

            return redirect()->route('pos.index')
                ->with('success', 'Sale completed successfully! Total: $' . number_format($totalAmount, 2));
        });
    } catch (\Exception $e) {
        // Rollback the transaction and return with an error
        return redirect()->back()
            ->with('error', 'Error: ' . $e->getMessage())
            ->withInput();
    }
}

    // Method to show recent sales
    public function salesHistory()
    {
        $orders = Order::with(['items.product', 'register'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        return view('pos.sales-history', compact('orders'));
    }
    
    // Method to manage registers
    public function registers()
    {
        $registers = Register::withCount('orders')->get();
        return view('pos.registers', compact('registers'));
    }
    
    // In POSController.php

public function createRegister(Request $request)
{
    $request->validate([
        'register_name' => 'required|string|max:255|unique:registers,name',
        'location' => 'nullable|string|max:255',
        'initial_status' => 'nullable|string|in:open,closed'  // Add option to set initial status
    ]);

    try {
        // Set default status based on request or default to 'closed' if not specified
        $status = $request->initial_status ?? 'closed';
        
        $registerData = [
            'name' => $request->register_name,
            'location' => $request->location,
            'status' => $status,
            'cash_balance' => 0,
            'transaction_count' => 0
        ];
        
        // If opening the register immediately, set additional fields
        if ($status === 'open') {
            $registerData['opened_at'] = now();
            $registerData['opened_by'] = auth()->id();
        }

        $register = Register::create($registerData);

        return redirect()->route('pos.registers')
            ->with('success', "Register created successfully!" . 
                ($status === 'open' ? " Register is now open." : " Register is currently closed."));

    } catch (\Exception $e) {
        return redirect()->back()
            ->with('error', 'Failed to create register: ' . $e->getMessage())
            ->withInput();
    }
}

public function openRegister(Request $request)
{
    $request->validate([
        'register_id' => 'required|exists:registers,id',
        'opening_balance' => 'required|numeric|min:0|max:10000' // Set reasonable max
    ]);

    return DB::transaction(function () use ($request) {
        $register = Register::lockForUpdate()->findOrFail($request->register_id);
        
        if ($register->status !== 'closed') {
            throw new \Exception('Register is not available for opening');
        }

        $register->update([
            'status' => 'open',
            'cash_balance' => $request->opening_balance,
            'opened_at' => now(),
            'opened_by' => auth()->id(),
            'transaction_count' => 0
        ]);

        return redirect()->route('pos.registers')
            ->with('success', "Register #{$register->id} opened successfully");
    });
}

public function closeRegister(Request $request)
{
    $request->validate([
        'register_id' => 'required|exists:registers,id',
        'counted_balance' => 'required|numeric|min:0'
    ]);

    return DB::transaction(function () use ($request) {
        $register = Register::lockForUpdate()->findOrFail($request->register_id);
        
        if ($register->status !== 'open') {
            throw new \Exception('Register is not currently open');
        }

        $difference = $request->counted_balance - $register->cash_balance;

        $register->update([
            'status' => 'closed',
            'counted_balance' => $request->counted_balance,
            'balance_difference' => $difference,
            'closed_at' => now(),
            'closed_by' => auth()->id()
        ]);

        return redirect()->route('pos.registers')->with('success', 
            "Register #{$register->id} closed. " . 
            ($difference == 0 ? 'Perfect balance!' : 
             ($difference > 0 ? "Over by $" . number_format($difference, 2) : 
              "Short by $" . number_format(abs($difference), 2)))
        );
    });
}


public function deleteRegister($id)
{
    $register = Register::findOrFail($id);
    
    // Only allow deletion if register is closed and has no transactions
    if ($register->status !== 'closed') {
        return redirect()->route('pos.registers')->with('error', 'Cannot delete an open register.');
    }
    
    if ($register->orders_count > 0) {
        return redirect()->route('pos.registers')->with('error', 'Cannot delete a register with transaction history.');
    }
    
    $register->delete();
    
    return redirect()->route('pos.registers')->with('success', 'Register deleted successfully!');
}
}