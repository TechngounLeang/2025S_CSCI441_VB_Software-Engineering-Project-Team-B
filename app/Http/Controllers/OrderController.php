<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        // Fetch all orders from the database
        $orders = Order::with('items.product')->get();  // Use eager loading to load related order items and products
        return view('orders.index', compact('orders'));
    }
    // Show the form to create a new order
    public function create()
    {
        $products = Product::all();  // Get all available products
        return view('orders.create', compact('products'));
    }

    // Store the newly created order
    public function store(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string',
            'customer_email' => 'required|email',
            'products' => 'required|array',
            'products.*.id' => 'exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
        ]);

        // Calculate the total amount for the order
        $totalAmount = 0;
        foreach ($request->products as $productData) {
            $product = Product::find($productData['id']);
            $totalAmount += $product->price * $productData['quantity'];
        }

        // Create the order
        $order = Order::create([
            'customer_name' => $request->customer_name,
            'customer_email' => $request->customer_email,
            'total_amount' => $totalAmount,
            'status' => 'pending',  // Default status to pending
        ]);

        // Create the order items
        foreach ($request->products as $productData) {
            $product = Product::find($productData['id']);
            $order->items()->create([
                'product_id' => $product->id,
                'quantity' => $productData['quantity'],
                'unit_price' => $product->price,
                'total_price' => $product->price * $productData['quantity'],
            ]);
        }

        return redirect()->route('orders.index')->with('success', 'Order created successfully.');
    }
    public function show(Order $order)
    {
    // Fetch the order with its related items
    $order->load('items.product');
    return view('orders.show', compact('order'));
    }
}

