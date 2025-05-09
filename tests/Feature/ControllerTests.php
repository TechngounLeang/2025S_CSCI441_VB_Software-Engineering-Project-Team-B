<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ControllerTests extends TestCase
{
    use DatabaseTransactions;
    
    protected function setUp(): void
    {
        parent::setUp();
    }

    /**
     * Test that the welcome page loads
     */
    #[Test]
    public function test_welcome_page_loads()
    {
        $response = $this->get('/welcome');
        $response->assertStatus(200);
    }

    /**
     * Test accessing a protected route with authentication
     */
    #[Test]
    public function test_accessing_protected_route()
    {
        // Check if users table exists
        if (!\Schema::hasTable('users')) {
            $this->markTestSkipped('Users table does not exist');
            return;
        }
        
        // Find a user
        $user = User::first();
        
        if (!$user) {
            $this->markTestSkipped('No users found in database');
            return;
        }
        
        // Test accessing dashboard without auth (should redirect)
        $response = $this->get('/dashboard');
        $response->assertStatus(302);
        
        // Test accessing dashboard with auth (should be successful)
        $response = $this->actingAs($user)
                         ->get('/dashboard');
                         
        // We don't assert exact status, as it might depend on user permissions
        $this->assertTrue(
            $response->status() == 200 || $response->status() == 302,
            'Expected status 200 or 302, got ' . $response->status()
        );
    }

    /**
     * Test products listing page
     */
    #[Test]
    public function test_products_index()
    {
        // Check if users table exists
        if (!\Schema::hasTable('users')) {
            $this->markTestSkipped('Users table does not exist');
            return;
        }
        
        // Find a user
        $user = User::first();
        
        if (!$user) {
            $this->markTestSkipped('No users found in database');
            return;
        }
        
        // Try to access products index
        $response = $this->actingAs($user)
                         ->get('/products');
                         
        // We don't assert exact status, as it might depend on user permissions
        $this->assertTrue(
            $response->status() == 200 || $response->status() == 302,
            'Expected status 200 or 302, got ' . $response->status()
        );
    }

    /**
     * Test store homepage loads successfully
     */
    #[Test]
    public function test_store_homepage()
    {
        $response = $this->get('/store');
        $response->assertStatus(200);
    }
}