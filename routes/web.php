<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\POSController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
    
    // Product Routes
    Route::resource('products', ProductController::class);
    Route::resource('categories', CategoryController::class);
    Route::resource('sales', SalesController::class);
    Route::resource('orders', OrderController::class);
    
    // POS Routes
    Route::get('pos', [POSController::class, 'index'])->name('pos.index');
    Route::post('pos/checkout', [POSController::class, 'checkout'])->name('pos.checkout');
    
    // Register Management Routes
    Route::get('registers', [POSController::class, 'registers'])->name('pos.registers');
    Route::post('registers/open', [POSController::class, 'openRegister'])->name('pos.open-register');
    Route::post('registers/close', [POSController::class, 'closeRegister'])->name('pos.close-register');
    Route::get('registers/sales', [POSController::class, 'registerSales'])->name('pos.register-sales');

    // Add these to your routes/web.php file
    Route::get('lang/{lang}', [App\Http\Controllers\LanguageController::class, 'switchLanguage'])->name('lang.switch');


// Create register
Route::post('/pos/create-register', [POSController::class, 'createRegister'])->name('pos.create-register');

// Delete register
Route::delete('/pos/delete-register/{id}', [POSController::class, 'deleteRegister'])->name('pos.delete-register');
    
    // User Routes
    Route::resource('users', UserController::class);
    Route::get('users/create', [UserController::class, 'create'])->name('users.create'); // View for creating user
    Route::post('users', [UserController::class, 'store'])->name('users.store'); // Store the created user
});

require __DIR__.'/auth.php';