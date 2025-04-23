<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\Category;
use App\Models\Sale;
use App\Models\OrderItem;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function index()
    {
        try {
            // Get total orders count
            $totalOrders = Order::count();
            
            // Calculate order growth compared to previous month
            $currentMonth = Carbon::now()->month;
            $lastMonth = Carbon::now()->subMonth()->month;
            
            $currentMonthOrders = Order::whereMonth('created_at', $currentMonth)->count();
            $previousMonthOrders = Order::whereMonth('created_at', $lastMonth)->count();
            $orderGrowth = $previousMonthOrders > 0 
                ? round(($currentMonthOrders - $previousMonthOrders) / $previousMonthOrders * 100, 1) 
                : 0;
            
            // Get new customers (unique customer names in the last month)
            $newCustomers = Order::whereMonth('created_at', $currentMonth)
                ->distinct('customer_name')
                ->count('customer_name');
                
            // Calculate customer growth compared to previous month
            $previousMonthCustomers = Order::whereMonth('created_at', $lastMonth)
                ->distinct('customer_name')
                ->count('customer_name');
            $customerGrowth = $previousMonthCustomers > 0 
                ? round(($newCustomers - $previousMonthCustomers) / $previousMonthCustomers * 100, 1) 
                : 0;
            
            // Get total sales amount
            $totalSales = Sale::sum('total_price') ?? 0;
            
            // Calculate sales growth compared to previous month
            $currentMonthSales = Sale::whereMonth('sale_date', $currentMonth)->sum('total_price') ?? 0;
            $previousMonthSales = Sale::whereMonth('sale_date', $lastMonth)->sum('total_price') ?? 0;
            $salesGrowth = $previousMonthSales > 0 
                ? round(($currentMonthSales - $previousMonthSales) / $previousMonthSales * 100, 1) 
                : 0;
            
            // Get trending products (most ordered products)
            $trendingProducts = DB::table('order_items')
                ->join('products', 'order_items.product_id', '=', 'products.id')
                ->select('products.name', DB::raw('COUNT(order_items.id) as order_count'))
                ->groupBy('products.id', 'products.name')
                ->orderByDesc('order_count')
                ->limit(5)
                ->get();
            
            // Get recent orders with more detailed information
            $recentOrders = Order::select('id', 'customer_name', 'total_amount', 'status', 'created_at')
                ->where('status', 'completed')
                ->orderByDesc('created_at')
                ->limit(4)
                ->get();
                
            // Get items for each recent order
            foreach ($recentOrders as $order) {
                $items = OrderItem::where('order_id', $order->id)
                    ->join('products', 'order_items.product_id', '=', 'products.id')
                    ->select('products.name')
                    ->pluck('name')
                    ->implode(', ');
                    
                $order->items_list = $items;
            }
            
            // Get daily sales data for the current month
            $currentMonthStart = Carbon::now()->startOfMonth()->format('Y-m-d');
            $currentMonthEnd = Carbon::now()->endOfMonth()->format('Y-m-d');
            
            $dailySalesData = Sale::whereBetween('sale_date', [$currentMonthStart, $currentMonthEnd])
                ->select(DB::raw('DATE(sale_date) as date'), DB::raw('SUM(total_price) as total'))
                ->groupBy('date')
                ->orderBy('date')
                ->get();
                
            $salesData = [];
            foreach ($dailySalesData as $data) {
                $salesData[] = [
                    'date' => Carbon::parse($data->date)->format('j F'), // Format: day month
                    'total' => (float) $data->total
                ];
            }
            
            // Generate weekly sales data
            $weeklySalesData = [];
            $weekStart = Carbon::now()->startOfMonth();
            $weekEnd = Carbon::now()->endOfMonth();
            $week = 1;
            
            while ($weekStart->lte($weekEnd)) {
                $weekEndDate = (clone $weekStart)->addDays(6)->min($weekEnd);
                
                $weekTotal = Sale::whereBetween('sale_date', [
                    $weekStart->format('Y-m-d'),
                    $weekEndDate->format('Y-m-d')
                ])->sum('total_price');
                
                $weeklySalesData[] = [
                    'week' => "Week {$week}",
                    'total' => (float) $weekTotal
                ];
                
                $weekStart->addDays(7);
                $week++;
            }
            
            // Generate monthly sales data for the last 6 months
            $monthlySalesData = [];
            
            for ($i = 5; $i >= 0; $i--) {
                $monthDate = Carbon::now()->subMonths($i);
                $monthName = $monthDate->format('F');
                $monthYear = $monthDate->year;
                $monthNum = $monthDate->month;
                
                $monthTotal = Sale::whereYear('sale_date', $monthYear)
                    ->whereMonth('sale_date', $monthNum)
                    ->sum('total_price');
                    
                $monthlySalesData[] = [
                    'month' => $monthName,
                    'total' => (float) $monthTotal
                ];
            }
            
            // Log the data for debugging purposes
            Log::info('Dashboard Data', [
                'salesData' => $salesData,
                'weeklySalesData' => $weeklySalesData,
                'monthlySalesData' => $monthlySalesData
            ]);
            
            return view('dashboard', compact(
                'totalOrders',
                'orderGrowth',
                'newCustomers',
                'customerGrowth',
                'totalSales',
                'salesGrowth',
                'trendingProducts',
                'recentOrders',
                'salesData',
                'weeklySalesData',
                'monthlySalesData'
            ));
            
        } catch (\Exception $e) {
            Log::error('Dashboard error: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            
            // Return a view with default values and error message
            return view('dashboard', [
                'totalOrders' => 0,
                'orderGrowth' => 0,
                'newCustomers' => 0,
                'customerGrowth' => 0,
                'totalSales' => 0,
                'salesGrowth' => 0,
                'trendingProducts' => collect(),
                'recentOrders' => collect(),
                'salesData' => [],
                'weeklySalesData' => [],
                'monthlySalesData' => [],
                'error' => 'An error occurred while loading dashboard data: ' . $e->getMessage()
            ]);
        }
    }
}