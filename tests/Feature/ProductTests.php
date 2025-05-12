<?php
// Written & debugged by: Tech Ngoun Leang
// Tested by: Tech Ngoun Leang
namespace Tests\Feature;

use App\Models\Product;
use App\Models\Category;
use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ProductTests extends TestCase
{
    use DatabaseTransactions;
    
    protected $admin;
    
    protected function setUp(): void
    {
        parent::setUp();
        
        // Find an admin user for testing
        $this->admin = User::where('role', 'admin')->first();
        
        if (!$this->admin) {
            // Create one if none exists
            $this->admin = User::create([
                'name' => 'Test Admin',
                'email' => 'testadmin@example.com',
                'password' => bcrypt('password123'),
                'role' => 'admin'
            ]);
        }
    }

    /**
     * Test product listing page
     */
    #[Test]
    public function test_product_listing()
    {
        $response = $this->actingAs($this->admin)
                         ->get('/products');
        
        $response->assertStatus(200);
        $response->assertViewIs('products.index');
        $response->assertViewHas('products');
    }
    
    /**
     * Test product creation form
     */
    #[Test]
    public function test_product_create_form()
    {
        $response = $this->actingAs($this->admin)
                         ->get('/products/create');
        
        $response->assertStatus(200);
        $response->assertViewIs('products.create');
    }
    
    /**
     * Test product creation
     */
    #[Test]
    public function test_product_creation()
    {
        // Check if categories table exists
        if (!\Schema::hasTable('categories')) {
            // Create a category if the table exists but has no records
            $category = Category::first();
            if (!$category && \Schema::hasTable('categories')) {
                $category = Category::create(['name' => 'Test Category']);
            }
            
            $categoryId = $category ? $category->id : null;
        } else {
            $categoryId = null;
        }
        
        $productData = [
            'name' => 'Test Product ' . time(),
            'description' => 'This is a test product description',
            'price' => 19.99,
            'category_id' => $categoryId,
            'stock_quantity' => 10,
        ];
        
        $response = $this->actingAs($this->admin)
                         ->post('/products', $productData);
        
        // Should be redirected after successful creation
        $response->assertStatus(302);
        $response->assertRedirect('/products');
        
        // Check that the product was created in the database
        $this->assertDatabaseHas('products', [
            'name' => $productData['name'],
            'price' => $productData['price']
        ]);
        
        // Clean up - delete the test product
        Product::where('name', $productData['name'])->delete();
    }
    
    /**
     * Test product editing
     */
    #[Test]
    public function test_product_editing()
    {
        // Create a product for testing
        $product = Product::create([
            'name' => 'Edit Test Product',
            'description' => 'This product will be edited',
            'price' => 15.99,
            'stock_quantity' => 5
        ]);
        
        // Test accessing the edit form
        $response = $this->actingAs($this->admin)
                         ->get("/products/{$product->id}/edit");
        
        $response->assertStatus(200);
        $response->assertViewIs('products.edit');
        $response->assertViewHas('product');
        
        // Update the product
        $updatedData = [
            'name' => 'Updated Product Name',
            'description' => 'This product has been updated',
            'price' => 19.99,
            'stock_quantity' => 10
        ];
        
        $response = $this->actingAs($this->admin)
                         ->put("/products/{$product->id}", $updatedData);
        
        // Should be redirected after successful update
        $response->assertStatus(302);
        $response->assertRedirect('/products');
        
        // Check that the product was updated in the database
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => $updatedData['name'],
            'price' => $updatedData['price']
        ]);
        
        // Clean up - delete the test product
        $product->delete();
    }
    
    /**
     * Test product deletion
     */
    #[Test]
    public function test_product_deletion()
    {
        // Create a product for testing
        $product = Product::create([
            'name' => 'Delete Test Product',
            'description' => 'This product will be deleted',
            'price' => 25.99,
            'stock_quantity' => 3
        ]);
        
        // Delete the product
        $response = $this->actingAs($this->admin)
                         ->delete("/products/{$product->id}");
        
        // Should be redirected after successful deletion
        $response->assertStatus(302);
        $response->assertRedirect('/products');
        
        // Check that the product was deleted from the database
        $this->assertDatabaseMissing('products', [
            'id' => $product->id
        ]);
    }
}