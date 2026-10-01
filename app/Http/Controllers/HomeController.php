<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class HomeController extends Controller
{
 public function index()
 {
 $categories = Category::all();
		$products = Product::with('mainImage')->where('is_active', true)
 ->latest()
			->take(16)
 ->get();
 return view('home', compact('categories', 'products'));
 }
}
