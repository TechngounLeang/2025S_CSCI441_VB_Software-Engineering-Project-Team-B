<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;

class OrderController extends Controller
{
    public function index()
    {
        try {
            // Fetch all orders from the database
            $orders = Order::with('items.product')->get();  // Use eager loading to load related order items and products
            return view('orders.index', compact('orders'));
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Failed to load orders: ' . $e->getMessage());
        }
    }

    // Show the form to create a new order
    public function create()
    {
        try {
            $products = Product::all();  // Get all available products
            return view('orders.create', compact('products'));
        } catch (Exception $e) {
            return redirect()->route('orders.index')->with('error', 'Failed to load product data: ' . $e->getMessage());
        }
    }

    // Store the newly created order
    public function store(Request $request)
    {
        try {
            $request->validate([
                'customer_name' => 'required|string',
                'customer_email' => 'required|email',
                'products' => 'required|array',
                'products.*.id' => 'exists:products,id',
                'products.*.quantity' => 'required|integer|min:1',
            ]);

            DB::beginTransaction();

            // Calculate the total amount for the order
            $totalAmount = 0;
            foreach ($request->products as $productData) {
                $product = Product::find($productData['id']);
                if (!$product) {
                    throw new Exception("Product not found");
                }
                $totalAmount += $product->price * $productData['quantity'];
            }

            // Create the order
            $order = Order::create([
                'customer_name' => $request->customer_name,
                'customer_email' => $request->customer_email,
                'total_amount' => $totalAmount,
                'status' => 'pending',  // Default status to pending
                'payment_method' => $request->payment_method ?? 'cash',
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

            DB::commit();
            return redirect()->route('orders.index')->with('success', 'Order created successfully.');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to create order: ' . $e->getMessage())
                             ->withInput();
        }
    }

    public function show(Order $order)
    {
        try {
            // Fetch the order with its related items
            $order->load('items.product');
            return view('orders.show', compact('order'));
        } catch (Exception $e) {
            return redirect()->route('orders.index')->with('error', 'Failed to load order details: ' . $e->getMessage());
        }
    }

    public function edit(Order $order)
    {
        try {
            $order->load('items.product');
            $products = Product::all();
            $statuses = ['pending', 'processing', 'completed', 'cancelled'];
            $paymentMethods = ['cash', 'credit_card', 'bank_transfer', 'paypal'];
            
            return view('orders.edit', compact('order', 'products', 'statuses', 'paymentMethods'));
        } catch (Exception $e) {
            return redirect()->route('orders.index')->with('error', 'Failed to load order for editing: ' . $e->getMessage());
        }
    }

    public function update(Request $request, Order $order)
    {
        try {
            $request->validate([
                'customer_name' => 'required|string',
                'customer_email' => 'email',
                'status' => 'required|in:pending,processing,completed,cancelled',
                'payment_method' => 'required|in:cash,credit_card,bank_transfer,paypal',
            ]);

            DB::beginTransaction();

            $order->update([
                'customer_name' => $request->customer_name,
                'customer_email' => $request->customer_email,
                'status' => $request->status,
                'payment_method' => $request->payment_method,
            ]);

            // If we're also updating order items (optional)
            if ($request->has('items')) {
                // Update order items logic here
                // For simplicity, we'll skip that part in this example
            }

            DB::commit();
            return redirect()->route('orders.show', $order)->with('success', 'Order updated successfully.');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to update order: ' . $e->getMessage())
                             ->withInput();
        }
    }

    public function updateStatus(Request $request, Order $order)
    {
        try {
            $request->validate([
                'status' => 'required|in:pending,processing,completed,cancelled',
            ]);

            $order->update([
                'status' => $request->status,
            ]);

            return redirect()->back()->with('success', 'Order status updated successfully.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Failed to update order status: ' . $e->getMessage());
        }
    }

    public function destroy(Order $order)
    {
        try {
            DB::beginTransaction();
            
            // Delete order items first
            $order->items()->delete();
            
            // Then delete the order
            $order->delete();
            
            DB::commit();
            return redirect()->route('orders.index')->with('success', 'Order deleted successfully.');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->route('orders.index')->with('error', 'Failed to delete order: ' . $e->getMessage());
        }
    }
}