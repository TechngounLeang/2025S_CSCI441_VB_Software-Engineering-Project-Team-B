<?php
namespace App\Http\Controllers;
use App\Models\Sale;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SalesController extends Controller
{
    public function index(Request $request)
    {
        // Get date range from request or use defaults
        $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date')) : Carbon::now()->subDays(30);
        $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date')) : Carbon::now();
        
        // Today's sales
        $today = Carbon::today();
        $startOfWeek = Carbon::now()->startOfWeek();
        $startOfMonth = Carbon::now()->startOfMonth();
        
        $todaySales = Sale::whereDate('sale_date', $today)
            ->sum('total_price');
            
        // This week's sales
        $weeklySales = Sale::whereBetween('sale_date', [$startOfWeek, Carbon::now()])
            ->sum('total_price');
            
        // This month's sales
        $monthlySales = Sale::whereBetween('sale_date', [$startOfMonth, Carbon::now()])
            ->sum('total_price');
            
        // Get payment method data 
        $paymentMethodSales = Sale::selectRaw('payment_method, SUM(total_price) as total')
            ->whereBetween('sale_date', [$startDate, $endDate])
            ->groupBy('payment_method')
            ->get();
            
        // Daily sales for chart using the selected date range
        $dailySales = Sale::selectRaw('DATE(sale_date) as date, SUM(total_price) as total')
            ->whereBetween('sale_date', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        
        // Initialize arrays for chart data
        $dates = [];
        $salesData = [];
        
        // Extract data for chart
        foreach ($dailySales as $sale) {
            $dates[] = Carbon::parse($sale->date)->format('M d, Y');
            $salesData[] = $sale->total;
        }
        
        // Add trending items data for the selected date range
        $trendingItems = DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->select('products.name as product_name', DB::raw('SUM(order_items.quantity) as quantity_sold'))
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->groupBy('products.id', 'products.name')
            ->orderBy('quantity_sold', 'desc')
            ->limit(5)
            ->get();

        $trendingItemsCount = $trendingItems->count();

        $orderBreakdown = DB::table('orders')
            ->leftJoin('order_items', 'orders.id', '=', 'order_items.order_id')
            ->selectRaw('DATE(orders.created_at) as date, COUNT(DISTINCT orders.id) as order_count, COALESCE(SUM(order_items.quantity), 0) as items_sold, SUM(orders.total_amount) as revenue')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->groupBy(DB::raw('DATE(orders.created_at)'))
            ->orderBy('date')
            ->get();

        $orderBreakdownCount = $orderBreakdown->count();

        // Prepare data for charts to be used in JavaScript
        $paymentLabels = $paymentMethodSales->pluck('payment_method')->map(function ($method) {
            return $method ?: 'Unknown';
        })->toArray();
        
        $paymentData = $paymentMethodSales->pluck('total')->toArray();

        return view('sales.index', compact(
            'todaySales', 
            'weeklySales', 
            'monthlySales', 
            'paymentMethodSales',
            'dates',
            'salesData',
            'trendingItems',
            'startDate',
            'endDate',
            'orderBreakdown',
            'orderBreakdownCount',
            'paymentLabels',
            'paymentData'
        ));
    }
    
    public function report(Request $request)
    {
        $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date')) : Carbon::now()->subDays(30);
        $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date')) : Carbon::now();
        
        // Get sales within date range
        $sales = Sale::whereBetween('sale_date', [$startDate, $endDate])
            ->orderBy('sale_date', 'desc')
            ->paginate(15);
            
        // Calculate totals
        $totalSales = Sale::whereBetween('sale_date', [$startDate, $endDate])->sum('total_price');
        
        // Get payment method data from Orders table
        $paymentMethodSales = Order::selectRaw('payment_method, SUM(total_amount) as total')
            ->whereBetween('order_date', [$startDate, $endDate])
            ->groupBy('payment_method')
            ->get();
        
        // Daily sales for chart
        $dailySales = Sale::selectRaw('DATE(sale_date) as date, SUM(total_price) as total')
            ->whereBetween('sale_date', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        
        // Format data for chart
        $dates = $dailySales->pluck('date')->map(function($date) {
            return Carbon::parse($date)->format('M d, Y');
        })->toArray();
        $salesData = $dailySales->pluck('total')->toArray();
        
        return view('sales.report', compact(
            'sales', 
            'startDate', 
            'endDate', 
            'totalSales',
            'paymentMethodSales',
            'dates',
            'salesData'
        ));
    }
    
    public function export(Request $request)
    {
        // Process timeframe selection
        $timeframe = $request->input('timeframe', 'this_month');
        $startDate = null;
        $endDate = Carbon::now();
            
        switch ($timeframe) {
            case 'today':
                $startDate = Carbon::today();
                break;
            case 'yesterday':
                $startDate = Carbon::yesterday();
                $endDate = Carbon::yesterday()->endOfDay();
                break;
            case 'this_week':
                $startDate = Carbon::now()->startOfWeek();
                break;
            case 'last_week':
                $startDate = Carbon::now()->subWeek()->startOfWeek();
                $endDate = Carbon::now()->subWeek()->endOfWeek();
                break;
            case 'this_month':
                $startDate = Carbon::now()->startOfMonth();
                break;
            case 'last_month':
                $startDate = Carbon::now()->subMonth()->startOfMonth();
                $endDate = Carbon::now()->subMonth()->endOfMonth();
                break;
            case 'custom':
                $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date')) : Carbon::now()->subDays(30);
                $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date')) : Carbon::now();
                break;
        }
            
        // Get sales data with related order information
        $sales = Sale::whereBetween('sale_date', [$startDate, $endDate])
            ->with('order')
            ->get();

        // Generate CSV
        $filename = 'sales_report_' . $startDate->format('Y-m-d') . '_to_' . $endDate->format('Y-m-d') . '.csv';
            
        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];
            
        $callback = function() use ($sales, $startDate, $endDate) {
            $file = fopen('php://output', 'w');
                    
            // CSV headers
            fputcsv($file, [
                'Sale ID',
                'Sale Date',
                'Total Price',
                'Payment Method',
                'Items Sold',
                'Register ID',
                'Tax Amount',
                'Discount Amount'
            ]);
            
            // Get all order IDs from sales
            $orderIds = $sales->pluck('order_id')->filter()->toArray();
            
            // Use EXACTLY the same query structure as orderBreakdown
            $orderItemsData = DB::table('orders')
                ->leftJoin('order_items', 'orders.id', '=', 'order_items.order_id')
                ->selectRaw('orders.id, COALESCE(SUM(order_items.quantity), 0) as items_sold')
                ->whereIn('orders.id', $orderIds)
                ->groupBy('orders.id')
                ->get();
                
            // Create a lookup array with order_id as key and items_sold as value
            $itemsSoldByOrder = [];
            foreach ($orderItemsData as $item) {
                $itemsSoldByOrder[$item->id] = $item->items_sold;
            }
                    
            // Add sales data with proper relationships
            foreach ($sales as $sale) {
                // Get items sold from the pre-calculated collection
                $itemsSold = isset($sale->order_id) && isset($itemsSoldByOrder[$sale->order_id]) 
                    ? $itemsSoldByOrder[$sale->order_id] 
                    : 0;
                            
                fputcsv($file, [
                    $sale->id,
                    $sale->sale_date,
                    $sale->total_price,
                    $sale->payment_method ?? 'N/A',
                    $itemsSold,
                    $sale->register_id ?? 'N/A',
                    $sale->tax_amount ?? 0,
                    $sale->discount_amount ?? 0
                ]);
            }
                    
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
}