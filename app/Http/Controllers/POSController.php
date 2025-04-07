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
    // Log the incoming request for debugging
    \Log::info('Checkout Request:', $request->all());

    // Filter out products with zero quantity
    $filteredProducts = array_filter($request->products ?? [], function($product) {
        return isset($product['quantity']) && intval($product['quantity']) > 0;
    });

    // Validate request with more specific rules
    $validator = Validator::make($request->all(), [
        'register_id' => 'required|exists:registers,id',
        'customer_name' => 'required|string|max:255',
        'payment_method' => 'required|in:cash,credit_card,debit_card',
        'products' => 'required|array|min:1',
        'products.*.id' => 'required|exists:products,id',
        'products.*.quantity' => 'required|integer|min:1',
    ], [
        'products.required' => 'Please select at least one product.',
        'products.*.quantity.min' => 'Product quantity must be at least 1.',
    ]);

    if ($validator->fails()) {
        return redirect()->back()
            ->withErrors($validator)
            ->withInput()
            ->with('error', 'Please check your input and try again.');
    }

    try {
        // Existing transaction logic remains the same
        return DB::transaction(function () use ($request, $filteredProducts) {
            // Your existing checkout implementation
        });
    } catch (\Exception $e) {
        \Log::error('Checkout Error: ' . $e->getMessage());
        return redirect()->back()
            ->with('error', 'Transaction failed: ' . $e->getMessage())
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
            'location' => 'nullable|string|max:255'
        ]);
    
        try {
            $register = Register::create([
                'name' => $request->register_name,
                'location' => $request->location,
                'status' => 'closed',
                'cash_balance' => 0,
                'transaction_count' => 0
            ]);
    
            return redirect()->route('pos.registers')
                ->with('success', 'Register created successfully!');
    
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