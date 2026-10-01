<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\CompareController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::middleware('guest')->group(function () {
	Route::get('/ingresar', [AuthController::class, 'showLogin'])->name('login');
	Route::post('/ingresar', [AuthController::class, 'login'])->name('login.submit');
	Route::get('/crear-cuenta', [AuthController::class, 'showRegister'])->name('register');
	Route::post('/crear-cuenta', [AuthController::class, 'register'])->name('register.submit');
});

Route::middleware('auth')->group(function () {
	Route::get('/productos', [ProductController::class, 'index'])->name('products.index');
	Route::get('/productos/{slug}', [ProductController::class, 'show'])->name('products.show');
	Route::get('/comparar', [CompareController::class, 'index'])->name('compare.index');
	Route::post('/comparar/{product}', [CompareController::class, 'add'])->name('compare.add');
	Route::delete('/comparar/{product}', [CompareController::class, 'remove'])->name('compare.remove');
	Route::get('/carrito', [CartController::class, 'index'])->name('cart.index');
	Route::post('/carrito/agregar/{product}', [CartController::class, 'add'])->name('cart.add');
	Route::delete('/carrito/eliminar/{item}', [CartController::class, 'remove'])->name('cart.remove');
	Route::get('/cuenta', [AuthController::class, 'account'])->name('account');
	Route::post('/salir', [AuthController::class, 'logout'])->name('logout');
 Route::post('/orden', [OrderController::class, 'store'])->name('order.store');
});

Route::prefix('admin')->name('admin.')->middleware(['auth', 'can:admin'])->group(function () {
	Route::get('/', DashboardController::class)->name('dashboard');
	Route::resource('products', AdminProductController::class)->except(['show']);
	Route::resource('categories', AdminCategoryController::class)->except(['show']);
	Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
	Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');
});
