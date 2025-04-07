<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index()
    {
        // Fetch all orders from the database with pagination
        $orders = Order::with('items.product')
            ->latest() // Sort by most recent orders first
            ->paginate(10); // Add pagination
        return view('orders.index', compact('orders'));
    }

    public function create()
    {
        $products = Product::where('is_active', true)->get(); // Assuming you want only active products
        return view('orders.create', compact('products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'products' => 'required|array|min:1',
            'products.*.id' => 'exists:products,id',
            'products.*.quantity' => 'required|integer|min:1|max:100', // Added max quantity
        ]);

        try {
            // Use a database transaction to ensure data integrity
            return DB::transaction(function () use ($request) {
                // Calculate the total amount for the order
                $totalAmount = 0;
                $orderItems = [];

                foreach ($request->products as $productData) {
                    $product = Product::findOrFail($productData['id']);
                    
                    // Validate stock availability
                    if ($product->stock < $productData['quantity']) {
                        return back()->withErrors([
                            'products' => "Insufficient stock for {$product->name}"
                        ]);
                    }

                    $itemTotal = $product->price * $productData['quantity'];
                    $totalAmount += $itemTotal;

                    $orderItems[] = [
                        'product_id' => $product->id,
                        'quantity' => $productData['quantity'],
                        'unit_price' => $product->price,
                        'total_price' => $itemTotal,
                    ];

                    // Optionally, reduce product stock
                    $product->decrement('stock', $productData['quantity']);
                }

                // Create the order
                $order = Order::create([
                    'customer_name' => $request->customer_name,
                    'customer_email' => $request->customer_email,
                    'total_amount' => $totalAmount,
                    'status' => 'pending',
                    'payment_method' => $request->payment_method ?? 'not_specified', // Optional payment method
                ]);

                // Bulk insert order items for performance
                $order->items()->createMany($orderItems);

                return redirect()->route('orders.index')
                    ->with('success', 'Order created successfully.');
            });
        } catch (\Exception $e) {
            // Log the error
            \Log::error('Order creation failed: ' . $e->getMessage());
            
            return back()->withErrors([
                'error' => 'An error occurred while creating the order. Please try again.'
            ]);
        }
    }

    public function show(Order $order)
    {
        // Fetch the order with its related items and products
        $order->load('items.product');
        return view('orders.show', compact('order'));
    }
}