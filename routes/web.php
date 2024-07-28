<?php

use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Middleware\isAdminMiddleware;
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
    Route::get('/product', [ProductController::class, 'index'])->name('product.index');

    Route::get('/product/create', [ProductController::class, 'create'])->name('product.create')->middleware(isAdminMiddleware::class);
    Route::post('/product/store', [ProductController::class, 'store'])->name('product.store')->middleware(isAdminMiddleware::class);
    Route::get('/product/{product}/edit', [ProductController::class, 'edit'])->name('product.edit')->middleware(isAdminMiddleware::class);
    Route::put('/product/{product}', [ProductController::class, 'update'])->name('product.update')->middleware(isAdminMiddleware::class);
    Route::delete('/product/{product}', [ProductController::class, 'destroy'])->name('product.destroy')->middleware(isAdminMiddleware::class);

});


require __DIR__ . '/auth.php';
