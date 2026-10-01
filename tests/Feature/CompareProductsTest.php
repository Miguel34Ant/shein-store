<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompareProductsTest extends TestCase
{
    use RefreshDatabase;

    public function test_customers_can_compare_up_to_four_active_products(): void
    {
        $this->actingAs(User::factory()->create());
        $category = Category::create(['name' => 'Mujer', 'slug' => 'mujer']);
        $products = collect(range(1, 5))->map(fn ($number) => Product::create([
            'category_id' => $category->id,
            'name' => 'Prenda ' . $number,
            'slug' => 'prenda-' . $number,
            'price' => 20 + $number,
            'stock' => 2,
            'is_active' => true,
        ]));

        foreach ($products->take(4) as $product) {
            $this->post(route('compare.add', $product))->assertRedirect();
        }

        $this->post(route('compare.add', $products[0]))
            ->assertRedirect()
            ->assertSessionHas('success', 'Este producto ya está en tu comparación.');
        $this->from(route('products.show', $products[4]->slug))
            ->post(route('compare.add', $products[4]))
            ->assertRedirect(route('products.show', $products[4]->slug))
            ->assertSessionHasErrors('compare');

        $this->get(route('compare.index'))
            ->assertOk()
            ->assertSee('Prenda 1')
            ->assertSee('Prenda 4')
            ->assertDontSee('Prenda 5');

        $this->delete(route('compare.remove', $products[1]))
            ->assertRedirect(route('compare.index'));
        $this->get(route('compare.index'))->assertDontSee('Prenda 2');
    }
}