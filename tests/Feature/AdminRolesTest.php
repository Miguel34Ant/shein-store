<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Cart;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\User;
use Database\Seeders\AdminUserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminRolesTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_administrators_can_open_the_admin_area(): void
    {
        $this->get(route('admin.dashboard'))->assertRedirect(route('login'));

        $customer = User::factory()->create();
        $this->actingAs($customer)->get(route('admin.dashboard'))->assertForbidden();

        $admin = $this->createAdmin();
        $this->actingAs($admin)->get(route('admin.dashboard'))->assertOk()->assertSee('Administración');
    }

    public function test_admin_can_sign_in_from_the_login_page_and_reaches_the_dashboard(): void
    {
        $admin = User::factory()->create(['email' => 'admin@example.com']);
        $admin->forceFill(['role' => User::ROLE_ADMIN])->save();

        $this->get(route('login'))
            ->assertOk()
            ->assertSee('Correo electrónico')
            ->assertSee('Contraseña')
            ->assertSee('Iniciar sesión');

        $this->post(route('login.submit'), [
            'email' => 'admin@example.com',
            'password' => 'password',
        ])->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticatedAs($admin);
    }

    public function test_configured_admin_seeder_creates_an_administrator(): void
    {
        config([
            'admin.name' => 'Ana Administradora',
            'admin.email' => 'ana.admin@example.com',
            'admin.password' => 'clave-admin-segura',
        ]);

        $this->seed(AdminUserSeeder::class);

        $admin = User::where('email', 'ana.admin@example.com')->firstOrFail();
        $this->assertSame(User::ROLE_ADMIN, $admin->role);
        $this->assertTrue(Hash::check('clave-admin-segura', $admin->password));
    }

    public function test_promotion_command_promotes_the_only_account_without_changing_its_password(): void
    {
        $user = User::factory()->create();

        $this->artisan('app:promote-admin')
            ->expectsOutput('Cuenta promovida a administrador.')
            ->assertExitCode(0);

        $this->assertDatabaseHas('users', ['id' => $user->id, 'role' => User::ROLE_ADMIN]);
        $this->assertTrue(Hash::check('password', $user->fresh()->password));
    }

    public function test_admin_password_reset_uses_hidden_confirmed_input(): void
    {
        $admin = $this->createAdmin();

        $this->artisan('app:reset-admin-password')
            ->expectsQuestion('Nueva contraseña (mínimo 8 caracteres)', 'nueva-clave-segura')
            ->expectsQuestion('Confirma la nueva contraseña', 'nueva-clave-segura')
            ->expectsOutput('Contraseña del administrador actualizada.')
            ->assertExitCode(0);

        $this->assertTrue(Hash::check('nueva-clave-segura', $admin->fresh()->password));
    }

    public function test_admin_can_create_update_and_delete_products_and_categories(): void
    {
        $admin = $this->createAdmin();
        $category = Category::create(['name' => 'Mujer', 'slug' => 'mujer']);
        $this->actingAs($admin);

        $this->post(route('admin.products.store'), [
            'category_id' => $category->id,
            'name' => 'Chaqueta ligera',
            'price' => 42.50,
            'discount_price' => 35,
            'stock' => 4,
            'size' => 'S, M, L',
            'color' => 'Negro',
            'image_url' => 'https://images.example.com/chaqueta.jpg',
            'is_active' => 1,
        ])->assertRedirect(route('admin.products.index'));

        $product = Product::where('slug', 'chaqueta-ligera')->firstOrFail();
        $this->assertDatabaseHas('product_images', [
            'product_id' => $product->id,
            'image_path' => 'https://images.example.com/chaqueta.jpg',
            'is_main' => true,
        ]);

        $this->put(route('admin.products.update', $product), [
            'category_id' => $category->id,
            'name' => 'Chaqueta urbana',
            'price' => 40,
            'stock' => 8,
            'size' => 'M, L',
            'color' => 'Azul',
            'is_active' => 1,
        ])->assertRedirect(route('admin.products.index'));

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Chaqueta urbana',
            'slug' => 'chaqueta-urbana',
            'stock' => 8,
        ]);

        $this->from(route('admin.categories.index'))
            ->delete(route('admin.categories.destroy', $category))
            ->assertRedirect(route('admin.categories.index'))
            ->assertSessionHasErrors('category');

        $this->delete(route('admin.products.destroy', $product))
            ->assertRedirect(route('admin.products.index'));
        $this->assertDatabaseMissing('products', ['id' => $product->id]);
        $this->assertDatabaseMissing('product_images', ['product_id' => $product->id]);

        $this->post(route('admin.categories.store'), ['name' => 'Accesorios'])
            ->assertRedirect(route('admin.categories.index'));
        $newCategory = Category::where('slug', 'accesorios')->firstOrFail();
        $this->put(route('admin.categories.update', $newCategory), [
            'name' => 'Complementos',
        ])->assertRedirect(route('admin.categories.index'));
        $this->assertDatabaseHas('categories', ['id' => $newCategory->id, 'slug' => 'complementos']);

        $this->delete(route('admin.categories.destroy', $newCategory))
            ->assertRedirect(route('admin.categories.index'));
        $this->assertDatabaseMissing('categories', ['id' => $newCategory->id]);
    }

    public function test_customer_cannot_submit_admin_catalog_changes(): void
    {
        $customer = User::factory()->create();
        $category = Category::create(['name' => 'Mujer', 'slug' => 'mujer']);

        $this->actingAs($customer)->post(route('admin.products.store'), [
            'category_id' => $category->id,
            'name' => 'Producto no autorizado',
            'price' => 20,
            'stock' => 1,
        ])->assertForbidden();

        $this->assertDatabaseMissing('products', ['name' => 'Producto no autorizado']);
    }

    public function test_admin_can_delete_customers_but_not_their_own_account(): void
    {
        $admin = $this->createAdmin();
        $customer = User::factory()->create();
        $cart = Cart::create(['user_id' => $customer->id]);
        $this->actingAs($admin);

        $this->delete(route('admin.users.destroy', $customer))
            ->assertRedirect(route('admin.users.index'));
        $this->assertDatabaseMissing('users', ['id' => $customer->id]);
        $this->assertDatabaseMissing('carts', ['id' => $cart->id]);

        $this->from(route('admin.users.index'))
            ->delete(route('admin.users.destroy', $admin))
            ->assertRedirect(route('admin.users.index'))
            ->assertSessionHasErrors('user');
        $this->assertDatabaseHas('users', ['id' => $admin->id, 'role' => User::ROLE_ADMIN]);
    }

    public function test_inactive_products_are_not_visible_to_customers(): void
    {
        $category = Category::create(['name' => 'Mujer', 'slug' => 'mujer']);
        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Producto oculto',
            'slug' => 'producto-oculto',
            'price' => 20,
            'stock' => 1,
            'is_active' => false,
        ]);

        $this->actingAs(User::factory()->create())
            ->get(route('products.show', $product->slug))
            ->assertNotFound();
    }

    private function createAdmin(): User
    {
        $admin = User::factory()->create();
        $admin->forceFill(['role' => User::ROLE_ADMIN])->save();

        return $admin;
    }
}