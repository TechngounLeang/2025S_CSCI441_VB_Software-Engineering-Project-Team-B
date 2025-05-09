<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Product;
use App\Models\Register;
use App\Models\Order;
use App\Models\Sale;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class POSTests extends TestCase
{
    use DatabaseTransactions;
    
    protected $cashier;
    protected $manager;
    protected $register;
    protected $product;
    
    protected function setUp(): void
    {
        parent::setUp();
        
        // Find or create a cashier user
        $this->cashier = User::where('role', 'cashier')->first();
        if (!$this->cashier) {
            $this->cashier = User::create([
                'name' => 'Test Cashier',
                'email' => 'testcashier@example.com',
                'password' => bcrypt('password123'),
                'role' => 'cashier'
            ]);
        }
        
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
        
        // Create a test product if needed
        if (!Product::where('name', 'POS Test Product')->exists()) {
            $this->product = Product::create([
                'name' => 'POS Test Product',
                'description' => 'Product for POS tests',
                'price' => 7.99,
                'stock_quantity' => 20
            ]);
        } else {
            $this->product = Product::where('name', 'POS Test Product')->first();
        }
        
        // Create a test register if needed
        if (!Register::where('name', 'Test Register')->exists()) {
            $this->register = Register::create([
                'name' => 'Test Register',
                'status' => 'closed',
                'cash_balance' => 0,
                'transaction_count' => 0
            ]);
        } else {
            $this->register = Register::where('name', 'Test Register')->first();
        }
    }
    
    protected function tearDown(): void
    {
        // Make sure the register is closed after tests
        if ($this->register && $this->register->status === 'open') {
            $this->register->update([
                'status' => 'closed',
                'cash_balance' => 0,
                'transaction_count' => 0
            ]);
        }
        
        parent::tearDown();
    }

    /**
     * Test POS interface loading
     */
    #[Test]
    public function test_pos_interface_loads()
    {
        $response = $this->actingAs($this->cashier)
                         ->get('/pos');
        
        $response->assertStatus(200);
        $response->assertViewIs('pos.index');
        $response->assertViewHas(['products', 'registers']);
    }
    
    /**
     * Test registers management page
     */
    #[Test]
    public function test_registers_management_page()
    {
        $response = $this->actingAs($this->manager)
                         ->get('/registers');
        
        $response->assertStatus(200);
        $response->assertViewIs('pos.registers');
        $response->assertViewHas('registers');
    }
    
    /**
     * Test POS checkout process
     */
    #[Test]
    public function test_pos_checkout()
    {
        // Make sure register is open
        if ($this->register->status !== 'open') {
            $this->register->update([
                'status' => 'open',
                'cash_balance' => 100.00,
                'opened_at' => now(),
                'opened_by' => $this->manager->id
            ]);
        }
        
        // Initial product stock
        $initialStock = $this->product->stock_quantity;
        
        // Checkout data
        $checkoutData = [
            'products' => [
                [
                    'id' => $this->product->id,
                    'quantity' => 2
                ]
            ],
            'customer_name' => 'POS Test Customer',
            'payment_method' => 'cash',
            'register_id' => $this->register->id
        ];
        
        // Process the checkout
        $response = $this->actingAs($this->cashier)
                         ->post('/pos/checkout', $checkoutData);
        
        // Should be redirected
        $response->assertStatus(302);
        $response->assertRedirect('/pos');
        
        // Check that product stock was reduced
        $this->product->refresh();
        $this->assertEquals($initialStock - 2, $this->product->stock_quantity);
        
        // Check that an order was created
        $order = Order::where('customer_name', 'POS Test Customer')
                      ->latest()
                      ->first();
                      
        $this->assertNotNull($order);
        $this->assertEquals('completed', $order->status);
        
        // Check that a sale record was created
        $sale = Sale::latest()->first();
        $this->assertNotNull($sale);
        $this->assertEquals($this->register->id, $sale->register_id);
        
        // Check that register cash balance was updated
        $this->register->refresh();
        $this->assertTrue($this->register->cash_balance > 100.00);
        $this->assertEquals(1, $this->register->transaction_count);
        
        // Clean up
        if ($order) {
            $order->delete();
        }
        if ($sale) {
            $sale->delete();
        }
    }
}