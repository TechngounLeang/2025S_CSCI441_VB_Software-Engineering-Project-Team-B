<?php
// app/Http/Controllers/SalesController.php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use Illuminate\Http\Request;

class SalesController extends Controller
{
    // app/Http/Controllers/SalesController.php

    public function index()
    {
        $totalSalesToday = Sale::whereDate('sale_date', now()->toDateString())
                            ->sum('total_price');
        $totalSalesThisMonth = Sale::whereMonth('sale_date', now()->month)
                            ->sum('total_price');

        return view('sales.index', compact('totalSalesToday', 'totalSalesThisMonth'));
    }
    public function report()
    {
        $salesByProduct = Sale::select('product_id', \DB::raw('SUM(quantity) as total_quantity'), \DB::raw('SUM(total_price) as total_sales'))
            ->groupBy('product_id')
            ->with('product') // eager load product details
            ->get();

        return view('sales.report', compact('salesByProduct'));
    }

    public function recordSale(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        // Get the product details
        $product = Product::findOrFail($request->product_id);

        // Calculate total price (price * quantity)
        $totalPrice = $product->price * $request->quantity;

        // Reduce the stock quantity
        if ($product->stock_quantity < $request->quantity) {
            return redirect()->back()->with('error', 'Not enough stock.');
        }

        $product->decrement('stock_quantity', $request->quantity);

        // Record the sale
        Sale::create([
            'product_id' => $product->id,
            'quantity' => $request->quantity,
            'total_price' => $totalPrice,
        ]);

        return redirect()->route('sales.index')->with('success', 'Sale recorded successfully.');
    }
    public function show(Product $product)
    {
        $totalSalesForProduct = $product->sales->sum('total_price');
        return view('products.show', compact('product', 'totalSalesForProduct'));
    }

}
