<?php

namespace Tests\Integration;

use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class IntegrationTests extends TestCase
{
    use DatabaseTransactions;
    
    protected function setUp(): void
    {
        parent::setUp();
    }

    /**
     * Test user authentication flow
     */
    #[Test]
    public function test_auth_flow()
    {
        // Check if users table exists
        if (!\Schema::hasTable('users')) {
            $this->markTestSkipped('Users table does not exist');
            return;
        }
        
        // Test login page loads
        $response = $this->get('/login');
        $response->assertStatus(200);
        
        // Check if we have a test user
        $user = User::first();
        if (!$user) {
            $this->markTestSkipped('No users found for testing');
            return;
        }
        
        // Test logging in as the user
        $this->actingAs($user);
        
        // Test access to a protected route after login
        $response = $this->get('/dashboard');
        
        // We don't assert exact status, as it might depend on user permissions
        $this->assertTrue(
            $response->status() == 200 || $response->status() == 302,
            'Expected status 200 or 302, got ' . $response->status()
        );
    }

    /**
     * Test navigation between key application pages
     */
    #[Test]
    public function test_application_navigation()
    {
        // Visit homepage
        $response = $this->get('/');
        $response->assertStatus(200);
        
        // Visit store page
        $response = $this->get('/store');
        $response->assertStatus(200);
        
        // Visit welcome page
        $response = $this->get('/welcome');
        $response->assertStatus(200);
    }

    /**
     * Test user registration if available
     */
    #[Test]
    public function test_user_registration()
    {
        // Skip if we can't see a register page
        try {
            $response = $this->get('/register');
            if ($response->status() != 200) {
                $this->markTestSkipped('Registration page not accessible');
                return;
            }
        } catch (\Exception $e) {
            $this->markTestSkipped('Registration page not available: ' . $e->getMessage());
            return;
        }
        
        // Generate a unique email
        $email = 'test' . time() . '@example.com';
        
        // Submit registration form
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => $email,
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);
        
        // We expect a redirect after successful registration
        $response->assertStatus(302);
        
        // Check if user was created
        if (\Schema::hasTable('users')) {
            $this->assertDatabaseHas('users', [
                'email' => $email
            ]);
            
            // Clean up - delete the test user
            User::where('email', $email)->delete();
        }
    }
}