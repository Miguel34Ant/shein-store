<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
 public function store(Request $request)
 {
 $request->validate([
 'address' => 'required|string|max:255',
 ]);
		$cart = Cart::with('items.product')->where('user_id', auth()->id())->firstOrFail();
 $total = $cart->items->sum(function ($item) {
			return ($item->product->discount_price ?? $item->product->price) * $item->quantity;
 });
 $order = Order::create([
 'user_id' => auth()->id(),
 'total' => $total,
 'address' => $request->address,
 ]);
 foreach ($cart->items as $item) {
 $order->items()->create([
 'product_id' => $item->product_id,
				'size' => $item->size,
				'color' => $item->color,
 'quantity' => $item->quantity,
				'price' => $item->product->discount_price ?? $item->product->price,
 ]);
 }
 $cart->items()->delete();
 return redirect()->route('home')->with('success', '¡Compra realizada con éxito!');
 }
}
