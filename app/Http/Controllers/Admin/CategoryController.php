<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(): View
    {
        $categories = Category::withCount('products')->orderBy('name')->paginate(20);

        return view('admin.categories.index', compact('categories'));
    }

    public function create(): View
    {
        return view('admin.categories.form', ['category' => new Category()]);
    }

    public function store(Request $request): RedirectResponse
    {
        Category::create($this->validatedData($request));

        return redirect()->route('admin.categories.index')->with('success', 'Categoría creada.');
    }

    public function edit(Category $category): View
    {
        return view('admin.categories.form', compact('category'));
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $category->update($this->validatedData($request, $category));

        return redirect()->route('admin.categories.index')->with('success', 'Categoría actualizada.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        if ($category->products()->exists()) {
            return back()->withErrors(['category' => 'No puedes eliminar una categoría que todavía tiene productos.']);
        }

        $category->delete();

        return redirect()->route('admin.categories.index')->with('success', 'Categoría eliminada.');
    }

    private function validatedData(Request $request, ?Category $category = null): array
    {
        $slugRule = Rule::unique('categories', 'slug');
        if ($category) {
            $slugRule->ignore($category->id);
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', $slugRule],
        ]);

        $baseSlug = Str::slug(($data['slug'] ?? '') ?: $data['name']);
        $slug = $baseSlug;
        $suffix = 2;
        while (Category::where('slug', $slug)
            ->when($category, fn ($query) => $query->where('id', '!=', $category->id))
            ->exists()) {
            $slug = $baseSlug . '-' . $suffix++;
        }

        $data['slug'] = $slug;

        return $data;
    }
}