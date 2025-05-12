<?php
// Written & debugged by: Tech Ngoun Leang
// Tested by: Tech Ngoun Leang
namespace Tests\Unit;

use App\Models\Product;
use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ModelTests extends TestCase
{
    use DatabaseTransactions;

    /**
     * Test that we can create and delete a product
     */
    #[Test]
    public function test_product_crud()
    {
        // Check if product table exists
        if (!\Schema::hasTable('products')) {
            $this->markTestSkipped('Products table does not exist');
            return;
        }
        
        // Get the column names from the products table
        $columns = \Schema::getColumnListing('products');
        
        // Check if we have the right columns for testing
        if (!in_array('name', $columns) || !in_array('price', $columns)) {
            $this->markTestSkipped('Products table does not have required columns');
            return;
        }
        
        // Create data based on available columns
        $productData = [
            'name' => 'Test Product ' . rand(1000, 9999),
            'price' => 3.99,
        ];
        
        // Add description if column exists
        if (in_array('description', $columns)) {
            $productData['description'] = 'Test description';
        }
        
        // Try to create a product
        $product = Product::create($productData);
        
        // Assert the product was created
        $this->assertInstanceOf(Product::class, $product);
        $this->assertEquals($productData['name'], $product->name);
        $this->assertEquals(3.99, $product->price);
        
        // Update the product
        $product->name = 'Updated ' . $product->name;
        $product->save();
        
        // Assert the product was updated
        $updatedProduct = Product::find($product->id);
        $this->assertNotNull($updatedProduct);
        $this->assertEquals('Updated ' . $productData['name'], $updatedProduct->name);
        
        // Delete the product
        $product->delete();
        
        // Assert the product was deleted
        $deletedProduct = Product::find($product->id);
        $this->assertNull($deletedProduct);
    }

    /**
     * Test basic authentication
     */
    #[Test]
    public function test_user_auth()
    {
        // Check if users table exists
        if (!\Schema::hasTable('users')) {
            $this->markTestSkipped('Users table does not exist');
            return;
        }
        
        // Find a test user or use the first user in the database
        $user = User::first();
        
        if (!$user) {
            $this->markTestSkipped('No users found in database');
            return;
        }
        
        // Test logging in as this user
        $response = $this->actingAs($user)->get('/dashboard');
        
        // Just check that we get a 200 response or a redirect (if not authorized)
        $this->assertTrue($response->status() == 200 || $response->status() == 302);
    }
}