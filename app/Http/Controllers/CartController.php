<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CartController extends Controller
{
 public function index()
 {
		$cart = $this->getOrCreateCart()->load('items.product.mainImage');
 return view('carts.index', compact('cart'));
 }
 public function add(Request $request, Product $product)
 {
		if ($product->stock < 1) {
			return back()->withErrors(['quantity' => 'Esta prenda está agotada.']);
		}

		$sizes = $this->options($product->size);
		$colors = $this->options($product->color);
		$validated = $request->validate([
			'size' => $sizes ? ['required', 'string', Rule::in($sizes)] : ['nullable', 'string', 'max:80'],
			'color' => $colors ? ['required', 'string', Rule::in($colors)] : ['nullable', 'string', 'max:80'],
			'quantity' => ['nullable', 'integer', 'min:1', 'max:' . $product->stock],
		]);
		$quantity = (int) ($validated['quantity'] ?? 1);

 $cart = $this->getOrCreateCart();
		$item = $cart->items()
			->where('product_id', $product->id)
			->where('size', $validated['size'] ?? null)
			->where('color', $validated['color'] ?? null)
			->first();
		if ($item && $item->quantity + $quantity > $product->stock) {
			return back()->withErrors(['quantity' => 'No hay suficientes unidades disponibles.']);
		}
 if ($item) {
			$item->increment('quantity', $quantity);
 } else {
 $cart->items()->create([
 'product_id' => $product->id,
				'size' => $validated['size'] ?? null,
				'color' => $validated['color'] ?? null,
				'quantity' => $quantity,
 ]);
 }
 return back()->with('success', 'Producto agregado al carrito.');
 }
 public function remove(CartItem $item)
 {
 $item->delete();
 return back()->with('success', 'Producto eliminado del carrito.');
 }
 private function getOrCreateCart()
 {
 if (auth()->check()) {
 return Cart::firstOrCreate(['user_id' => auth()->id()]);
 }
 $sessionId = session()->getId();
 return Cart::firstOrCreate(['session_id' => $sessionId]);
 }

	private function options(?string $value): array
	{
		return collect(preg_split('/[,|]/', $value ?? ''))
			->map(fn ($option) => trim($option))
			->filter()
			->values()
			->all();
	}
}

