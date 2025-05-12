<?php
// Written & debugged by: Tech Ngoun Leang
// Tested by: Tech Ngoun Leang
namespace Tests\Unit;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UserUnitTests extends TestCase
{
    use DatabaseTransactions;
    
    /**
     * Test user creation
     */
    #[Test]
    public function test_user_creation()
    {
        // Create user data
        $userData = [
            'name' => 'Test User ' . rand(1000, 9999),
            'email' => 'testuser' . rand(1000, 9999) . '@example.com',
            'password' => Hash::make('password123'),
            'role' => 'cashier'
        ];
        
        // Create the user
        $user = User::create($userData);
        
        // Verify user was created
        $this->assertNotNull($user);
        $this->assertEquals($userData['name'], $user->name);
        $this->assertEquals($userData['email'], $user->email);
        $this->assertEquals($userData['role'], $user->role);
        
        // Clean up
        $user->delete();
    }
    
    /**
     * Test admin role check
     */
    #[Test]
    public function test_is_admin_role_check()
    {
        // Create admin user
        $adminUser = User::create([
            'name' => 'Admin Test User',
            'email' => 'admin' . rand(1000, 9999) . '@example.com',
            'password' => Hash::make('password123'),
            'role' => 'admin'
        ]);
        
        // Create non-admin user
        $nonAdminUser = User::create([
            'name' => 'Non-Admin Test User',
            'email' => 'nonadmin' . rand(1000, 9999) . '@example.com',
            'password' => Hash::make('password123'),
            'role' => 'cashier'
        ]);
        
        // Test isAdmin method
        $this->assertTrue($adminUser->isAdmin());
        $this->assertFalse($nonAdminUser->isAdmin());
        
        // Clean up
        $adminUser->delete();
        $nonAdminUser->delete();
    }
    
    /**
     * Test manager role check
     */
    #[Test]
    public function test_is_manager_role_check()
    {
        // Create manager user
        $managerUser = User::create([
            'name' => 'Manager Test User',
            'email' => 'manager' . rand(1000, 9999) . '@example.com',
            'password' => Hash::make('password123'),
            'role' => 'manager'
        ]);
        
        // Create non-manager user
        $nonManagerUser = User::create([
            'name' => 'Non-Manager Test User',
            'email' => 'nonmanager' . rand(1000, 9999) . '@example.com',
            'password' => Hash::make('password123'),
            'role' => 'cashier'
        ]);
        
        // Test isManager method
        $this->assertTrue($managerUser->isManager());
        $this->assertFalse($nonManagerUser->isManager());
        
        // Clean up
        $managerUser->delete();
        $nonManagerUser->delete();
    }
    
    /**
     * Test cashier role check
     */
    #[Test]
    public function test_is_cashier_role_check()
    {
        // Create cashier user
        $cashierUser = User::create([
            'name' => 'Cashier Test User',
            'email' => 'cashier' . rand(1000, 9999) . '@example.com',
            'password' => Hash::make('password123'),
            'role' => 'cashier'
        ]);
        
        // Create non-cashier user
        $nonCashierUser = User::create([
            'name' => 'Non-Cashier Test User',
            'email' => 'noncashier' . rand(1000, 9999) . '@example.com',
            'password' => Hash::make('password123'),
            'role' => 'admin'
        ]);
        
        // Test isCashier method
        $this->assertTrue($cashierUser->isCashier());
        $this->assertFalse($nonCashierUser->isCashier());
        
        // Clean up
        $cashierUser->delete();
        $nonCashierUser->delete();
    }
    
    /**
     * Test manager access check
     */
    #[Test]
    public function test_has_manager_access_check()
    {
        // Create admin user
        $adminUser = User::create([
            'name' => 'Admin Access Test User',
            'email' => 'adminaccess' . rand(1000, 9999) . '@example.com',
            'password' => Hash::make('password123'),
            'role' => 'admin'
        ]);
        
        // Create manager user
        $managerUser = User::create([
            'name' => 'Manager Access Test User',
            'email' => 'manageraccess' . rand(1000, 9999) . '@example.com',
            'password' => Hash::make('password123'),
            'role' => 'manager'
        ]);
        
        // Create cashier user
        $cashierUser = User::create([
            'name' => 'Cashier Access Test User',
            'email' => 'cashieraccess' . rand(1000, 9999) . '@example.com',
            'password' => Hash::make('password123'),
            'role' => 'cashier'
        ]);
        
        // Test hasManagerAccess method
        $this->assertTrue($adminUser->hasManagerAccess());
        $this->assertTrue($managerUser->hasManagerAccess());
        $this->assertFalse($cashierUser->hasManagerAccess());
        
        // Clean up
        $adminUser->delete();
        $managerUser->delete();
        $cashierUser->delete();
    }
    
    /**
     * Test password hashing
     */
    #[Test]
    public function test_password_hashing()
    {
        // Create a user with a plain password
        $plainPassword = 'secret123';
        $user = new User([
            'name' => 'Password Test User',
            'email' => 'passtest' . rand(1000, 9999) . '@example.com',
            'password' => $plainPassword,
            'role' => 'cashier'
        ]);
        
        // Save the user (this should trigger password hashing)
        $user->save();
        
        // Verify password was hashed
        $this->assertNotEquals($plainPassword, $user->password);
        $this->assertTrue(Hash::check($plainPassword, $user->password));
        
        // Clean up
        $user->delete();
    }
}