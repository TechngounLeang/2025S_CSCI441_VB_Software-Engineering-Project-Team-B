<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Models\Category;
use App\Models\Sale;

class ChatbotController extends Controller
{
    /**
     * The OpenAI API key
     */
    protected $apiKey = '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';

    /**
     * The base URL for the OpenAI API
     */
    protected $baseUrl = 'https://api.openai.com/v1/chat/completions';

    /**
     * Process user message and get AI recommendation
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getRecommendation(Request $request)
    {
        // Validate the request
        $validated = $request->validate([
            'message' => 'required|string|max:500',
        ]);

        // Get user message
        $userMessage = $validated['message'];
        
        // Try to find local recommendations first
        $databaseRecommendation = $this->getRecommendationFromDatabase($userMessage);
        
        if ($databaseRecommendation) {
            return response()->json([
                'success' => true,
                'message' => $databaseRecommendation,
                'source' => 'database'
            ]);
        }
        
        // If no database match, use OpenAI
        try {
            // Get product context for OpenAI
            $productContext = $this->getProductContext();
            $salesContext = $this->getSalesContext();
            
            // Add a system message with context about our products and popular items
            $systemContent = "You are a helpful bakery assistant for Cresences Bakery. Your role is to recommend drinks and pastries based on customer preferences. Be conversational, friendly, and knowledgeable. Always tailor recommendations to the customer's preferences or dietary needs if mentioned.\n\n";
            $systemContent .= "AVAILABLE PRODUCTS:\n" . $productContext . "\n\n";
            $systemContent .= "POPULAR ITEMS BASED ON SALES:\n" . $salesContext . "\n\n";
            $systemContent .= "Keep your responses under 150 words, be friendly, and recommend actual products we sell.";
            
            // Make OpenAI API request
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl, [
                'model' => 'gpt-4o',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => $systemContent
                    ],
                    [
                        'role' => 'user',
                        'content' => $userMessage
                    ]
                ],
                'temperature' => 0.7,
                'max_tokens' => 250,
            ]);

            // Check if the request was successful
            if ($response->successful()) {
                $result = $response->json();
                $aiResponse = $result['choices'][0]['message']['content'] ?? 'Sorry, I couldn\'t generate a recommendation at this time.';
                
                return response()->json([
                    'success' => true,
                    'message' => $aiResponse,
                    'source' => 'openai'
                ]);
            } else {
                Log::error('OpenAI API Error', [
                    'status' => $response->status(),
                    'response' => $response->json()
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Sorry, there was an issue generating a recommendation. Please try again later.'
                ], 500);
            }
        } catch (\Exception $e) {
            Log::error('Error in ChatbotController', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred. Please try again later.'
            ], 500);
        }
    }
    
    /**
     * Get recommendation from database based on user message
     *
     * @param string $userMessage
     * @return string|null
     */
    private function getRecommendationFromDatabase($userMessage)
    {
        $userMessage = strtolower($userMessage);
        
        // Check for direct product mentions
        $products = Product::where('stock_quantity', '>', 0)->get();
        $matchedProducts = [];
        
        foreach ($products as $product) {
            $productName = strtolower($product->name);
            if (strpos($userMessage, $productName) !== false) {
                $matchedProducts[] = $product;
            }
        }
        
        // If we have direct matches, recommend those
        if (count($matchedProducts) > 0) {
            $product = $matchedProducts[array_rand($matchedProducts)];
            return $this->formatProductRecommendation($product);
        }
        
        // Check for specific categories or descriptive words
        $keywords = [
            'coffee' => ['coffee', 'caffeine', 'espresso', 'americano', 'latte'],
            'tea' => ['tea', 'chai', 'matcha', 'herbal'],
            'pastry' => ['pastry', 'croissant', 'roll', 'bun', 'danish'],
            'dessert' => ['cake', 'cheesecake', 'sweet', 'dessert', 'tart'],
            'bread' => ['bread', 'sandwich', 'toast', 'baguette', 'loaf'],
            'chocolate' => ['chocolate', 'cocoa', 'choco', 'chocolatey'],
            'fruity' => ['fruit', 'berry', 'fruity', 'apple', 'cherry']
        ];
        
        $matchedCategories = [];
        foreach ($keywords as $category => $terms) {
            foreach ($terms as $term) {
                if (strpos($userMessage, $term) !== false) {
                    $matchedCategories[$category] = isset($matchedCategories[$category]) ? 
                        $matchedCategories[$category] + 1 : 1;
                }
            }
        }
        
        if (!empty($matchedCategories)) {
            // Get top matching category
            arsort($matchedCategories);
            $topCategory = key($matchedCategories);
            
            // Find products that match this category
            $matchingProducts = $this->getProductsByCategory($topCategory);
            
            if ($matchingProducts->isNotEmpty()) {
                // Choose a random product from the matching category
                $product = $matchingProducts->random();
                return $this->formatProductRecommendation($product);
            }
        }
        
        // Check for specific needs
        if (strpos($userMessage, 'popular') !== false || 
            strpos($userMessage, 'best seller') !== false || 
            strpos($userMessage, 'bestseller') !== false ||
            strpos($userMessage, 'recommendation') !== false) {
            
            // Find popular products based on order history
            $popularProducts = $this->getPopularProducts();
            
            if ($popularProducts->isNotEmpty()) {
                $product = $popularProducts->first();
                return "Based on our sales, our " . $product->name . " is one of our most popular items! " .
                    ($product->description ? $product->description . " " : "") . 
                    "It's priced at $" . number_format($product->price, 2) . ". Would you like to try it?";
            }
        }
        
        // No direct match found, return null to use OpenAI
        return null;
    }
    
    /**
     * Format a product recommendation message
     *
     * @param \App\Models\Product $product
     * @return string
     */
    private function formatProductRecommendation($product)
    {
        $message = "I'd recommend our " . $product->name . ". ";
        
        if ($product->description) {
            $message .= $product->description . " ";
        }
        
        $message .= "It's priced at $" . number_format($product->price, 2) . ".";
        
        // Add stock info
        if ($product->stock_quantity <= 5) {
            $message .= " We're running low on this popular item, so I'd recommend trying it today!";
        } elseif ($product->stock_quantity <= 10) {
            $message .= " This is one of our more popular items!";
        }
        
        // Add pairing suggestion if it's a drink
        $category = $product->category ? $product->category->name : null;
        if ($category == 'Drinks' || stripos($product->name, 'coffee') !== false || 
            stripos($product->name, 'tea') !== false || stripos($product->name, 'latte') !== false) {
            $message .= " It pairs perfectly with our pastries, especially our croissants.";
        } 
        
        // Add pairing suggestion if it's a pastry
        if ($category == 'Pastries' || stripos($product->name, 'croissant') !== false || 
            stripos($product->name, 'cake') !== false || stripos($product->name, 'bread') !== false) {
            $message .= " It goes great with our coffee or tea.";
        }
        
        return $message;
    }
    
    /**
     * Get products by category keyword
     *
     * @param string $categoryKeyword
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function getProductsByCategory($categoryKeyword)
    {
        switch ($categoryKeyword) {
            case 'coffee':
                return Product::where('name', 'like', '%coffee%')
                    ->orWhere('name', 'like', '%espresso%')
                    ->orWhere('name', 'like', '%latte%')
                    ->orWhere('name', 'like', '%americano%')
                    ->orWhere('description', 'like', '%coffee%')
                    ->where('stock_quantity', '>', 0)
                    ->get();
                
            case 'tea':
                return Product::where('name', 'like', '%tea%')
                    ->orWhere('name', 'like', '%chai%')
                    ->orWhere('name', 'like', '%matcha%')
                    ->orWhere('description', 'like', '%tea%')
                    ->where('stock_quantity', '>', 0)
                    ->get();
                
            case 'pastry':
                return Product::where('name', 'like', '%croissant%')
                    ->orWhere('name', 'like', '%roll%')
                    ->orWhere('name', 'like', '%bun%')
                    ->orWhere('name', 'like', '%danish%')
                    ->orWhere('description', 'like', '%pastry%')
                    ->where('stock_quantity', '>', 0)
                    ->get();
                
            case 'dessert':
                return Product::where('name', 'like', '%cake%')
                    ->orWhere('name', 'like', '%cheesecake%')
                    ->orWhere('name', 'like', '%tart%')
                    ->orWhere('description', 'like', '%sweet%')
                    ->orWhere('description', 'like', '%dessert%')
                    ->where('stock_quantity', '>', 0)
                    ->get();
                
            case 'bread':
                return Product::where('name', 'like', '%bread%')
                    ->orWhere('name', 'like', '%sandwich%')
                    ->orWhere('name', 'like', '%baguette%')
                    ->orWhere('description', 'like', '%bread%')
                    ->where('stock_quantity', '>', 0)
                    ->get();
                
            case 'chocolate':
                return Product::where('name', 'like', '%chocolate%')
                    ->orWhere('description', 'like', '%chocolate%')
                    ->orWhere('description', 'like', '%cocoa%')
                    ->where('stock_quantity', '>', 0)
                    ->get();
                
            case 'fruity':
                return Product::where('name', 'like', '%berry%')
                    ->orWhere('name', 'like', '%fruit%')
                    ->orWhere('name', 'like', '%apple%')
                    ->orWhere('name', 'like', '%cherry%')
                    ->orWhere('description', 'like', '%fruit%')
                    ->orWhere('description', 'like', '%berry%')
                    ->where('stock_quantity', '>', 0)
                    ->get();
                
            default:
                return collect([]);
        }
    }
    
    /**
     * Get the most popular products based on sales/orders
     *
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function getPopularProducts($limit = 5)
    {
        // Check if we can get data from order_items table
        try {
            $popularProducts = DB::table('order_items')
                ->select('product_id', DB::raw('SUM(quantity) as total_ordered'))
                ->groupBy('product_id')
                ->orderBy('total_ordered', 'desc')
                ->limit($limit)
                ->get();
            
            if ($popularProducts->isNotEmpty()) {
                $productIds = $popularProducts->pluck('product_id');
                return Product::whereIn('id', $productIds)
                    ->where('stock_quantity', '>', 0)
                    ->get();
            }
        } catch (\Exception $e) {
            // If there's an error, try another approach
            Log::error('Error getting popular products from order_items: ' . $e->getMessage());
        }
        
        // Default to returning products with highest stock quantity
        return Product::where('stock_quantity', '>', 0)
            ->orderBy('stock_quantity', 'desc')
            ->limit($limit)
            ->get();
    }
    
    /**
     * Get formatted product context for OpenAI
     *
     * @return string
     */
    private function getProductContext()
    {
        $products = Product::with('category')
            ->where('stock_quantity', '>', 0)
            ->get();
        
        $context = "";
        
        // Group products by category
        $productsByCategory = $products->groupBy(function($product) {
            return $product->category ? $product->category->name : 'Other';
        });
        
        foreach ($productsByCategory as $category => $items) {
            $context .= "Category: $category\n";
            
            foreach ($items as $product) {
                $context .= "- {$product->name}: ";
                if ($product->description) {
                    $context .= "{$product->description}. ";
                }
                $context .= "Price: \${$product->price}\n";
            }
            
            $context .= "\n";
        }
        
        return $context;
    }
    
    /**
     * Get sales context with popular items
     *
     * @return string
     */
    private function getSalesContext()
    {
        $popularProducts = $this->getPopularProducts(8);
        
        $context = "Our most popular items are:\n";
        
        foreach ($popularProducts as $product) {
            $context .= "- {$product->name}: ";
            if ($product->description) {
                $context .= "{$product->description}. ";
            }
            $context .= "Price: \${$product->price}\n";
        }
        
        // Add some general sales insights
        try {
            // Most popular payment method
            $popularPayment = DB::table('sales')
                ->select('payment_method', DB::raw('COUNT(*) as count'))
                ->groupBy('payment_method')
                ->orderBy('count', 'desc')
                ->first();
            
            if ($popularPayment) {
                $context .= "\nMost customers prefer to pay with {$popularPayment->payment_method}.\n";
            }
            
            // Most active sales day
            $salesByDay = DB::table('sales')
                ->select(DB::raw('DAYNAME(sale_date) as day'), DB::raw('COUNT(*) as count'))
                ->groupBy('day')
                ->orderBy('count', 'desc')
                ->first();
            
            if ($salesByDay) {
                $context .= "Our busiest day is typically {$salesByDay->day}.\n";
            }
        } catch (\Exception $e) {
            Log::error('Error getting sales context: ' . $e->getMessage());
        }
        
        return $context;
    }
}