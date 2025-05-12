<?php
// Written & debugged by: Tech Ngoun Leang
// Tested by: Tech Ngoun Leang
namespace Tests\Feature;

use App\Models\User;
use App\Models\Sale;
use App\Models\Product;
use App\Models\Order;
use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class SalesTests extends TestCase
{
    use DatabaseTransactions;
    
    protected $manager;
    
    protected function setUp(): void
    {
        parent::setUp();
        
        // Find or create a manager user
        $this->manager = User::where('role', 'manager')->first();
        if (!$this->manager) {
            $this->manager = User::create([
                'name' => 'Test Manager',
                'email' => 'testmanager@example.com',
                'password' => bcrypt('password123'),
                'role' => 'manager'
            ]);
        }
        
        // Create test sales data if none exists
        if (Sale::count() == 0) {
            $this->createTestSalesData();
        }
    }
    
    /**
     * Helper method to create test sales data
     */
    protected function createTestSalesData()
    {
        // Create some products if needed
        if (Product::count() == 0) {
            Product::create([
                'name' => 'Test Coffee',
                'description' => 'Coffee for sales tests',
                'price' => 3.99,
                'stock_quantity' => 50
            ]);
            
            Product::create([
                'name' => 'Test Croissant',
                'description' => 'Croissant for sales tests',
                'price' => 2.99,
                'stock_quantity' => 30
            ]);
        }
        
        // Create some sales records
        $methods = ['cash', 'credit_card', 'debit_card'];
        
        // Create sales for today
        for ($i = 0; $i < 3; $i++) {
            Sale::create([
                'user_id' => $this->manager->id,
                'total_price' => rand(5, 30),
                'tax_amount' => rand(1, 3),
                'discount_amount' => 0,
                'payment_method' => $methods[array_rand($methods)],
                'sale_date' => Carbon::today()->addHours(rand(8, 18))
            ]);
        }
        
        // Create sales for yesterday
        for ($i = 0; $i < 2; $i++) {
            Sale::create([
                'user_id' => $this->manager->id,
                'total_price' => rand(5, 30),
                'tax_amount' => rand(1, 3),
                'discount_amount' => 0,
                'payment_method' => $methods[array_rand($methods)],
                'sale_date' => Carbon::yesterday()->addHours(rand(8, 18))
            ]);
        }
        
        // Create sales for last week
        for ($i = 0; $i < 5; $i++) {
            Sale::create([
                'user_id' => $this->manager->id,
                'total_price' => rand(5, 30),
                'tax_amount' => rand(1, 3),
                'discount_amount' => 0,
                'payment_method' => $methods[array_rand($methods)],
                'sale_date' => Carbon::today()->subDays(rand(3, 7))->addHours(rand(8, 18))
            ]);
        }
    }

    /**
     * Test sales dashboard page
     */
    #[Test]
    public function test_sales_dashboard()
    {
        $response = $this->actingAs($this->manager)
                         ->get('/sales');
        
        $response->assertStatus(200);
        $response->assertViewIs('sales.index');
        
        // Check for key view data
        $response->assertViewHas([
            'todaySales',
            'weeklySales',
            'monthlySales',
            'paymentMethodSales',
            'salesData'
        ]);
    }
    
    /**
     * Test order history page
     */
    #[Test]
    public function test_order_history()
    {
        // Create a test order if none exists
        if (Order::count() == 0) {
            Order::create([
                'customer_name' => 'Test Customer',
                'total_amount' => 15.99,
                'status' => 'completed',
                'payment_method' => 'cash'
            ]);
        }
        
        $response = $this->actingAs($this->manager)
                         ->get('/orders');
        
        $response->assertStatus(200);
        $response->assertViewIs('orders.index');
        $response->assertViewHas('orders');
    }
}