<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(): View
    {
        $products = Product::with('category')->latest()->paginate(20);

        return view('admin.products.index', compact('products'));
    }

    public function create(): View
    {
        return view('admin.products.form', [
            'product' => new Product(['is_active' => true]),
            'categories' => Category::orderBy('name')->get(),
            'imageUrl' => null,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        [$data, $imageUrl] = $this->validatedData($request);
        $product = Product::create($data);
        $this->saveMainImage($product, $imageUrl);

        return redirect()->route('admin.products.index')->with('success', 'Producto creado.');
    }

    public function edit(Product $product): View
    {
        $product->load('mainImage');

        return view('admin.products.form', [
            'product' => $product,
            'categories' => Category::orderBy('name')->get(),
            'imageUrl' => $product->mainImage?->image_path,
        ]);
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        [$data, $imageUrl] = $this->validatedData($request, $product);
        $product->update($data);
        $this->saveMainImage($product, $imageUrl);

        return redirect()->route('admin.products.index')->with('success', 'Producto actualizado.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $product->delete();

        return redirect()->route('admin.products.index')->with('success', 'Producto eliminado.');
    }

    private function validatedData(Request $request, ?Product $product = null): array
    {
        $slugRule = Rule::unique('products', 'slug');
        if ($product) {
            $slugRule->ignore($product->id);
        }

        $data = $request->validate([
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', $slugRule],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'discount_price' => ['nullable', 'numeric', 'min:0', 'lt:price'],
            'stock' => ['required', 'integer', 'min:0'],
            'size' => ['nullable', 'string', 'max:255'],
            'color' => ['nullable', 'string', 'max:255'],
            'is_active' => ['sometimes', 'boolean'],
            'image_url' => ['nullable', 'url', 'max:2048'],
        ]);

        $baseSlug = Str::slug(($data['slug'] ?? '') ?: $data['name']);
        $slug = $baseSlug;
        $suffix = 2;
        while (Product::where('slug', $slug)
            ->when($product, fn ($query) => $query->where('id', '!=', $product->id))
            ->exists()) {
            $slug = $baseSlug . '-' . $suffix++;
        }

        $imageUrl = $data['image_url'] ?? null;
        $data['slug'] = $slug;
        $data['is_active'] = $request->boolean('is_active');
        unset($data['image_url']);

        return [$data, $imageUrl];
    }

    private function saveMainImage(Product $product, ?string $imageUrl): void
    {
        if ($imageUrl) {
            ProductImage::updateOrCreate(
                ['product_id' => $product->id, 'is_main' => true],
                ['image_path' => $imageUrl],
            );
        }
    }
}