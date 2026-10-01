<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\AuthController;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/productos', [ProductController::class, 'index'])->name('products.index');
Route::get('/productos/{slug}', [ProductController::class, 'show'])->name('products.show');
Route::get('/carrito', [CartController::class, 'index'])->name('cart.index');
Route::post('/carrito/agregar/{product}', [CartController::class, 'add'])->name('cart.add');
Route::delete('/carrito/eliminar/{item}', [CartController::class, 'remove'])->name('cart.remove');

Route::middleware('guest')->group(function () {
	Route::get('/ingresar', [AuthController::class, 'showLogin'])->name('login');
	Route::post('/ingresar', [AuthController::class, 'login'])->name('login.submit');
	Route::get('/crear-cuenta', [AuthController::class, 'showRegister'])->name('register');
	Route::post('/crear-cuenta', [AuthController::class, 'register'])->name('register.submit');
});

Route::middleware('auth')->group(function () {
	Route::get('/cuenta', [AuthController::class, 'account'])->name('account');
	Route::post('/salir', [AuthController::class, 'logout'])->name('logout');
 Route::post('/orden', [OrderController::class, 'store'])->name('order.store');
});
