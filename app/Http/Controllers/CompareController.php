<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CompareController extends Controller
{
    public function index(): View
    {
        $ids = collect(session('compare_products', []))
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->take(4)
            ->values();
        $available = Product::with(['category', 'mainImage'])
            ->where('is_active', true)
            ->whereIn('id', $ids)
            ->get()
            ->keyBy('id');
        $products = $ids->map(fn ($id) => $available->get($id))->filter()->values();

        return view('compare.index', compact('products'));
    }

    public function add(Product $product): RedirectResponse
    {
        abort_unless($product->is_active, 404);

        $ids = collect(session('compare_products', []))
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        if ($ids->contains($product->id)) {
            return back()->with('success', 'Este producto ya está en tu comparación.');
        }

        if ($ids->count() >= 4) {
            return back()->withErrors(['compare' => 'Puedes comparar hasta cuatro productos a la vez.']);
        }

        $ids->push($product->id);
        session(['compare_products' => $ids->all()]);

        return back()->with('success', 'Producto añadido a la comparación.');
    }

    public function remove(Product $product): RedirectResponse
    {
        $ids = collect(session('compare_products', []))
            ->map(fn ($id) => (int) $id)
            ->reject(fn ($id) => $id === $product->id)
            ->values();
        session(['compare_products' => $ids->all()]);

        return redirect()->route('compare.index')->with('success', 'Producto retirado de la comparación.');
    }
}