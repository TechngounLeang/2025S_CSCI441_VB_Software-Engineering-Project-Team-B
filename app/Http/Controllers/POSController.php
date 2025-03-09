<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class POSController extends Controller
{
    // Show the POS interface
    public function index()
    {
        $products = Product::where('stock_quantity', '>', 0)->get();  // Only show products with available stock
        return view('pos.index', compact('products'));
    }

    // Handle the checkout process
    public function checkout(Request $request)
    {
        $request->validate([
            'products' => 'required|array',
            'products.*.id' => 'exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
            'customer_name' => 'required|string',
            'payment_method' => 'required|string|in:cash,credit_card,debit_card',
        ]);

        try {
            // Use a database transaction to ensure data integrity
            return DB::transaction(function () use ($request) {
                $totalAmount = 0;
                $orderItems = [];

                // Validate stock and prepare order items
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

                    // Optional: Check and trigger reorder if stock is low
                    if ($product->stock_quantity <= $product->reorder_level) {
                        // You could trigger a notification or reorder process here
                        \Log::warning("Product {$product->name} is below reorder level");
                    }
                }

                // Create the order
                $order = Order::create([
                    'customer_name' => $request->customer_name,
                    'total_amount' => $totalAmount,
                    'status' => 'completed',
                    'payment_method' => $request->payment_method,
                ]);

                // Create order items
                $order->items()->createMany($orderItems);

                return redirect()->route('pos.index')
                    ->with('success', 'Sale completed successfully. Total: $' . number_format($totalAmount, 2));
            });
        } catch (\Exception $e) {
            // Rollback the transaction and return with an error
            return redirect()->back()
                ->with('error', $e->getMessage())
                ->withInput();
        }
    }

    // Optional: Method to show recent sales
    public function salesHistory()
    {
        $orders = Order::with('items.product')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        return view('pos.sales-history', compact('orders'));
    }
}