<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
 public function index(Request $request)
 {
        $query = Product::with('mainImage')->where('is_active', true);
 if ($request->filled('category')) {
 $query->whereHas('category', function ($q) use ($request) {
 $q->where('slug', $request->category);
 });
 }
        if ($request->filled('q')) {
            $term = trim($request->string('q')->toString());
            $query->where(function ($q) use ($term) {
                $q->where('name', 'like', "%{$term}%")
                    ->orWhere('description', 'like', "%{$term}%");
            });
        }
        if ($request->filled('size')) {
            $this->whereOptionMatches($query, 'size', $request->input('size'));
        }
        if ($request->filled('color')) {
            $this->whereOptionMatches($query, 'color', $request->input('color'));
        }

        match ($request->input('sort')) {
            'price_asc' => $query->orderByRaw('COALESCE(discount_price, price) ASC'),
            'price_desc' => $query->orderByRaw('COALESCE(discount_price, price) DESC'),
            default => $query->latest(),
        };

        $products = $query->paginate(12)->withQueryString();
 $categories = Category::all();
        $options = Product::where('is_active', true)->get(['size', 'color']);
        $sizes = $this->uniqueOptions($options->pluck('size'));
        $colors = $this->uniqueOptions($options->pluck('color'));

        return view('products.index', compact('products', 'categories', 'sizes', 'colors'));
 }

 public function show(string $slug)
 {
        $product = Product::with(['mainImage', 'images', 'category'])
            ->where('slug', $slug)
            ->firstOrFail();

        $relatedQuery = Product::with('mainImage')
            ->where('is_active', true)
            ->where('id', '!=', $product->id)
            ->where(function ($query) use ($product) {
                $query->where('category_id', $product->category_id);
                if ($product->color) {
                    $query->orWhere('color', 'like', '%' . $product->color . '%');
                }
            });
        $related = $relatedQuery->get()
            ->sortByDesc(fn ($item) => ($item->category_id === $product->category_id ? 2 : 0)
                + ($item->color && $item->color === $product->color ? 1 : 0))
            ->take(8);

 return view('products.show', compact('product', 'related'));
 }

    private function uniqueOptions($values): array
    {
        return $values
            ->flatMap(fn ($value) => preg_split('/[,|]/', $value ?? ''))
            ->map(fn ($option) => trim($option))
            ->filter()
            ->unique()
            ->sort()
            ->values()
            ->all();
    }

    private function whereOptionMatches($query, string $column, string $value): void
    {
        $query->where(function ($filter) use ($column, $value) {
            $patterns = [
                $value,
                $value . ',%',
                $value . ', %',
                '%,' . $value . ',%',
                '%, ' . $value . ', %',
                '%,' . $value,
                '%, ' . $value,
                $value . '|%',
                '%|' . $value . '|%',
                '%|' . $value,
            ];

            foreach ($patterns as $pattern) {
                $filter->orWhere($column, 'like', $pattern);
            }
        });
    }
}
