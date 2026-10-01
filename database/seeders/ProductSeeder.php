<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $styles = [
            ['Jeans rectos de tiro alto', 'mujer', '26, 28, 30, 32', 'Azul claro, Negro'],
            ['Pantalón cargo relaxed', 'hombre', 'S, M, L, XL', 'Verde militar, Negro'],
            ['Vestido midi de punto', 'mujer', 'XS, S, M, L', 'Vino, Negro'],
            ['Camiseta gráfica oversize', 'hombre', 'S, M, L, XL, XXL', 'Blanco, Negro'],
            ['Camisa de cuadros ligera', 'hombre', 'S, M, L, XL', 'Verde, Azul'],
            ['Top básico de canalé', 'mujer', 'XS, S, M, L', 'Blanco, Negro, Rosa'],
            ['Shorts denim wide leg', 'mujer', '26, 28, 30, 32', 'Azul claro'],
            ['Sudadera urbana con capucha', 'niños', '4Y, 6Y, 8Y, 10Y', 'Gris, Azul'],
            ['Pantalón wide leg con pinzas', 'hombre', 'S, M, L, XL', 'Gris, Negro'],
            ['Conjunto casual de dos piezas', 'niños', '4Y, 6Y, 8Y, 10Y', 'Rosa, Beige'],
            ['Chaqueta ligera de mezclilla', 'mujer', 'S, M, L, XL', 'Azul, Negro'],
            ['Bolso mini de hombro', 'accesorios', 'Unitalla', 'Negro, Crema'],
            ['Polo de algodón relaxed', 'hombre', 'S, M, L, XL', 'Blanco, Verde'],
            ['Falda plisada de cintura alta', 'mujer', 'XS, S, M, L', 'Negro, Vino'],
            ['Tenis urbanos con contraste', 'accesorios', '36, 38, 40, 42', 'Blanco, Negro'],
            ['Camiseta estampada infantil', 'niños', '4Y, 6Y, 8Y, 10Y', 'Blanco, Amarillo'],
            ['Pantalón de vestir relaxed', 'hombre', 'S, M, L, XL', 'Negro, Gris'],
            ['Cárdigan suave de punto', 'mujer', 'XS, S, M, L', 'Crema, Rosa'],
            ['Gorra clásica ajustable', 'accesorios', 'Unitalla', 'Negro, Beige'],
            ['Jeans slim con lavado vintage', 'hombre', '28, 30, 32, 34', 'Azul medio'],
        ];
        $photos = [
            'photo-1542272604-787c3835535d', 'photo-1516826957135-700dedea698c',
            'photo-1515886657613-9f3515b0c78f', 'photo-1521572163474-6864f9cf17ab',
            'photo-1591047139829-d91aecb6caea', 'photo-1583743814966-8936f37f4678',
            'photo-1544441893-675973e31985', 'photo-1519238263530-99bdd11df2ea',
            'photo-1517438476312-10d79c077509', 'photo-1519238263530-99bdd11df2ea',
            'photo-1551028719-00167b16eac5', 'photo-1548036328-c9fa89d128fa',
            'photo-1576566588028-4147f3842f27', 'photo-1583496661160-fb5886a0aaaa',
            'photo-1542291026-7eec264c27ff', 'photo-1519238263530-99bdd11df2ea',
            'photo-1473966968600-fa801b869a1a', 'photo-1576566588028-4147f3842f27',
            'photo-1588850561407-ed78c282e89b', 'photo-1542272604-787c3835535d',
        ];
        $categories = Category::pluck('id', 'slug');

        foreach ($styles as $index => [$name, $categorySlug, $size, $color]) {
            $product = Product::updateOrCreate(
                ['slug' => 'producto-de-moda-' . ($index + 1)],
                [
                    'category_id' => $categories[$categorySlug] ?? $categories->first(),
                    'name' => $name,
                    'description' => 'Una prenda versátil de estilo urbano, pensada para combinar todos los días.',
                    'price' => 18 + (($index * 7) % 48),
                    'discount_price' => $index % 3 === 0 ? 15 + (($index * 5) % 32) : null,
                    'stock' => 18 + (($index * 11) % 75),
                    'size' => $size,
                    'color' => $color,
                    'is_active' => true,
                ],
            );

            ProductImage::updateOrCreate(
                ['product_id' => $product->id, 'is_main' => true],
                ['image_path' => 'https://images.unsplash.com/' . $photos[$index] . '?auto=format&fit=crop&w=900&q=85'],
            );
        }
    }
}