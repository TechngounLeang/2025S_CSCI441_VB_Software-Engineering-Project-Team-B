<?php
// Written & debugged by: Tech Ngoun Leang, Ratanakvesal Thong, Longwei Ngor, Samady Sok
// Tested by: Tech Ngoun Leang
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\POSController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\ChatbotController;
use Illuminate\Support\Facades\Route;

// Make the store page the default landing page
Route::get('/', [StoreController::class, 'index'])->name('home');

// Public store routes
Route::get('/store', [StoreController::class, 'index'])->name('store.index');

// Chatbot route
Route::post('/chatbot/recommendation', [ChatbotController::class, 'getRecommendation'])->name('chatbot.recommendation');

Route::get('/welcome', function () {
    return view('welcome');
})->name('welcome');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');

    // Routes for all authenticated users
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // POS Routes - Available to all authenticated users (cashiers included)
    Route::get('pos', [POSController::class, 'index'])->name('pos.index');
    Route::post('pos/checkout', [POSController::class, 'checkout'])->name('pos.checkout');
    
    // Routes requiring Manager or Admin access
    Route::middleware('manager')->group(function () {
        // Product management
        Route::resource('products', ProductController::class);
        Route::resource('categories', CategoryController::class);
        
        // Sales reporting and management
        Route::get('/sales', [SalesController::class, 'index'])->name('sales.index');
        Route::get('/sales/report', [SalesController::class, 'report'])->name('sales.report');
        Route::get('/sales/export', [SalesController::class, 'export'])->name('sales.export');
        Route::resource('sales', SalesController::class);
        
        // Order management
        Route::resource('orders', OrderController::class);
        // Add the quick status update route
        Route::patch('/orders/{order}/update-status', [OrderController::class, 'updateStatus'])->name('orders.update-status');

        // Add this route to your routes/web.php file inside the middleware(['auth', 'verified']) group
        Route::get('/dashboard/export', [App\Http\Controllers\DashboardController::class, 'export'])->name('dashboard.export');
        
        // Register management
        Route::get('registers', [POSController::class, 'registers'])->name('pos.registers');
        Route::post('registers/open', [POSController::class, 'openRegister'])->name('pos.open-register');
        Route::post('registers/close', [POSController::class, 'closeRegister'])->name('pos.close-register');
        Route::get('registers/sales', [POSController::class, 'registerSales'])->name('pos.register-sales');
        Route::post('/pos/create-register', [POSController::class, 'createRegister'])->name('pos.create-register');
        Route::delete('/pos/delete-register/{id}', [POSController::class, 'deleteRegister'])->name('pos.delete-register');
    });
    
    // Admin only routes
    Route::middleware('admin')->group(function () {
        // User management
        Route::resource('users', UserController::class);
        Route::get('users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('users', [UserController::class, 'store'])->name('users.store');
    });
    
    // Language switch available to all
    Route::get('/language/{locale}', [LanguageController::class, 'switchLang'])->name('language.switch');
});

require __DIR__ . '/auth.php';