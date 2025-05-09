<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use App\Models\OrderItem;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class OrderTests extends TestCase
{
    use DatabaseTransactions;
    
    protected $manager;
    protected $product;
    
    protected function setUp(): void
    {
        parent::setUp();
        
        // Find a manager user for testing
        $this->manager = User::where('role', 'manager')->first();
        
        if (!$this->manager) {
            // Create one if none exists
            $this->manager = User::create([
                'name' => 'Test Manager',
                'email' => 'testmanager@example.com',
                'password' => bcrypt('password123'),
                'role' => 'manager'
            ]);
        }
        
        // Create a test product if needed
        if (!Product::where('name', 'Test Product')->exists()) {
            $this->product = Product::create([
                'name' => 'Test Product',
                'description' => 'Product for order tests',
                'price' => 9.99,
                'stock_quantity' => 20
            ]);
        } else {
            $this->product = Product::where('name', 'Test Product')->first();
        }
    }
    
    protected function tearDown(): void
    {
        // Clean up any test products we created
        if (isset($this->product) && $this->product->name === 'Test Product') {
            // Don't delete if it's not our test product
            Product::where('name', 'Test Product')->delete();
        }
        
        parent::tearDown();
    }

    /**
     * Test order listing page
     */
    #[Test]
    public function test_order_listing()
    {
        $response = $this->actingAs($this->manager)
                         ->get('/orders');
        
        $response->assertStatus(200);
        $response->assertViewIs('orders.index');
    }
    
    /**
     * Test order creation form
     */
    #[Test]
    public function test_order_create_form()
    {
        $response = $this->actingAs($this->manager)
                         ->get('/orders/create');
        
        $response->assertStatus(200);
        $response->assertViewIs('orders.create');
        $response->assertViewHas('products');
    }
    
    /**
     * Test order creation
     */
    #[Test]
    public function test_order_creation()
    {
        $orderData = [
            'customer_name' => 'Test Customer',
            'customer_email' => 'test@example.com',
            'products' => [
                [
                    'id' => $this->product->id,
                    'quantity' => 2
                ]
            ],
            'payment_method' => 'cash'
        ];
        
        $response = $this->actingAs($this->manager)
                         ->post('/orders', $orderData);
        
        // Should be redirected after successful creation
        $response->assertStatus(302);
        $response->assertRedirect('/orders');
        
        // Check that the order was created in the database
        $this->assertDatabaseHas('orders', [
            'customer_name' => 'Test Customer',
            'customer_email' => 'test@example.com'
        ]);
        
        // Get the order we just created
        $order = Order::where('customer_name', 'Test Customer')
                      ->where('customer_email', 'test@example.com')
                      ->latest()
                      ->first();
        
        if ($order) {
            // Check that the order item was created
            $this->assertDatabaseHas('order_items', [
                'order_id' => $order->id,
                'product_id' => $this->product->id,
                'quantity' => 2
            ]);
            
            // Clean up - delete the test order and its items
            OrderItem::where('order_id', $order->id)->delete();
            $order->delete();
        }
    }
    
    /**
     * Test viewing order details
     */
    #[Test]
    public function test_view_order_details()
    {
        // Create a test order
        $order = Order::create([
            'customer_name' => 'View Test Customer',
            'customer_email' => 'viewtest@example.com',
            'total_amount' => 19.98,
            'status' => 'pending',
            'payment_method' => 'cash'
        ]);
        
        // Create an order item
        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $this->product->id,
            'quantity' => 2,
            'unit_price' => 9.99,
            'total_price' => 19.98
        ]);
        
        // Test viewing the order
        $response = $this->actingAs($this->manager)
                         ->get("/orders/{$order->id}");
        
        $response->assertStatus(200);
        $response->assertViewIs('orders.show');
        $response->assertViewHas('order');
        
        // Clean up - delete the test order and its items
        OrderItem::where('order_id', $order->id)->delete();
        $order->delete();
    }
    
    /**
     * Test order status update
     */
    #[Test]
    public function test_order_status_update()
    {
        // Create a test order
        $order = Order::create([
            'customer_name' => 'Status Test Customer',
            'customer_email' => 'statustest@example.com',
            'total_amount' => 9.99,
            'status' => 'pending',
            'payment_method' => 'cash'
        ]);
        
        // Update the order status
        $response = $this->actingAs($this->manager)
                         ->patch("/orders/{$order->id}/update-status", [
                             'status' => 'completed'
                         ]);
        
        // Should be redirected after successful update
        $response->assertStatus(302);
        
        // Check that the order status was updated
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'completed'
        ]);
        
        // Clean up - delete the test order
        $order->delete();
    }
}