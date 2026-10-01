<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Cart;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StorefrontFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_create_an_account(): void
    {
        $this->post(route('register.submit'), [
            'name' => 'María López',
            'email' => 'maria@example.com',
            'password' => 'moda-segura-2026',
            'password_confirmation' => 'moda-segura-2026',
            'role' => User::ROLE_ADMIN,
        ])->assertRedirect(route('home'));

        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'email' => 'maria@example.com',
            'role' => User::ROLE_CLIENT,
        ]);
        $this->get(route('account'))->assertOk()->assertSee('Mis pedidos');
    }

    public function test_first_visit_offers_sign_in_or_registration_and_protects_the_store(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Iniciar sesión')
            ->assertSee('Crear cuenta');

        $this->get(route('products.index'))->assertRedirect(route('login'));
        $this->get(route('cart.index'))->assertRedirect(route('login'));
        $this->get(route('compare.index'))->assertRedirect(route('login'));
    }

    public function test_cart_requires_and_keeps_the_selected_product_variants(): void
    {
        $this->actingAs(User::factory()->create());
        $category = Category::create(['name' => 'Hombre', 'slug' => 'hombre']);
        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Pantalón relaxed',
            'slug' => 'pantalon-relaxed',
            'price' => 25,
            'stock' => 8,
            'size' => 'S, M, L',
            'color' => 'Negro, Azul',
            'is_active' => true,
        ]);

        $this->get(route('products.show', $product->slug))->assertOk()->assertSee('Selecciona tu talla');
        $this->get(route('products.index', ['size' => 'S']))->assertOk()->assertSee('Pantalón relaxed');

        $this->from(route('products.show', $product->slug))
            ->post(route('cart.add', $product))
            ->assertSessionHasErrors(['size', 'color']);

        $this->post(route('cart.add', $product), [
            'size' => 'M',
            'color' => 'Negro',
            'quantity' => 2,
        ])->assertRedirect();

        $this->post(route('cart.add', $product), [
            'size' => 'L',
            'color' => 'Azul',
        ])->assertRedirect();

        $this->assertDatabaseCount('cart_items', 2);
        $this->assertDatabaseHas('cart_items', [
            'product_id' => $product->id,
            'size' => 'M',
            'color' => 'Negro',
            'quantity' => 2,
        ]);
        $this->assertDatabaseHas('cart_items', [
            'product_id' => $product->id,
            'size' => 'L',
            'color' => 'Azul',
            'quantity' => 1,
        ]);
    }

    public function test_checkout_keeps_variants_and_uses_the_discounted_price(): void
    {
        $user = User::factory()->create();
        $category = Category::create(['name' => 'Mujer', 'slug' => 'mujer']);
        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Vestido midi',
            'slug' => 'vestido-midi',
            'price' => 25,
            'discount_price' => 15,
            'stock' => 5,
            'size' => 'S, M',
            'color' => 'Vino, Negro',
            'is_active' => true,
        ]);
        $cart = Cart::create(['user_id' => $user->id]);
        $cart->items()->create([
            'product_id' => $product->id,
            'size' => 'M',
            'color' => 'Vino',
            'quantity' => 2,
        ]);

        $this->actingAs($user)->post(route('order.store'), [
            'address' => 'Tegucigalpa, Honduras',
        ])->assertRedirect(route('home'));

        $this->assertDatabaseHas('orders', ['user_id' => $user->id, 'total' => 30]);
        $this->assertDatabaseHas('order_items', [
            'product_id' => $product->id,
            'size' => 'M',
            'color' => 'Vino',
            'quantity' => 2,
            'price' => 15,
        ]);
        $this->assertDatabaseCount('cart_items', 0);
    }

}