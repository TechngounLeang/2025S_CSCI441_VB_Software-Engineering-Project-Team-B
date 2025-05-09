@echo off
echo Fixing test environment...

echo Re-creating testing database...
mysql -u root -p123456789 -e "DROP DATABASE IF EXISTS coffee_db_testing; CREATE DATABASE coffee_db_testing CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

echo Updating TestCase.php...
(
echo ^<?php
echo.
echo namespace Tests;
echo.
echo use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
echo use Illuminate\Foundation\Testing\RefreshDatabase;
echo.
echo abstract class TestCase extends BaseTestCase
echo {
echo     use CreatesApplication;
echo     use RefreshDatabase;
echo     
echo     protected function setUp^(^): void
echo     {
echo         parent::setUp^(^);
echo         
echo         // Run migrations for the test database
echo         $this-^>artisan^('migrate'^);
echo         
echo         // Additional setup if needed
echo     }
echo }
) > tests\TestCase.php

echo Creating Product Unit Test...
mkdir tests\Unit 2>nul
(
echo ^<?php
echo.
echo namespace Tests\Unit;
echo.
echo use App\Models\Product;
echo use App\Models\Category;
echo use Tests\TestCase;
echo.
echo class ProductTest extends TestCase
echo {
echo     /** @test */
echo     public function a_product_has_a_name^(^)
echo     {
echo         $product = Product::factory^(^)-^>create^([
echo             'name' =^> 'Test Product'
echo         ]^);
echo         
echo         $this-^>assertEquals^('Test Product', $product-^>name^);
echo     }
echo     
echo     /** @test */
echo     public function a_product_has_a_price^(^)
echo     {
echo         $product = Product::factory^(^)-^>create^([
echo             'price' =^> 1000
echo         ]^);
echo         
echo         $this-^>assertEquals^(1000, $product-^>price^);
echo     }
echo     
echo     /** @test */
echo     public function a_product_belongs_to_a_category^(^)
echo     {
echo         $category = Category::factory^(^)-^>create^(^);
echo         $product = Product::factory^(^)-^>create^([
echo             'category_id' =^> $category-^>id
echo         ]^);
echo         
echo         $this-^>assertInstanceOf^(Category::class, $product-^>category^);
echo         $this-^>assertEquals^($category-^>id, $product-^>category-^>id^);
echo     }
echo }
) > tests\Unit\ProductTest.php

REM Continue with other file creations...

echo Fix completed. Now run your tests with: php artisan test --testsuite=Unit