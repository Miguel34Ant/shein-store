@extends('layouts.app')

@section('title', 'Comparar productos | Prisma Studio')

@section('content')
<div class="catalogue-top">
    <div><h1>Comparar productos</h1><p>Compara hasta cuatro prendas antes de elegir.</p></div>
    <a href="{{ route('products.index') }}">Seguir explorando</a>
</div>
@error('compare')<p class="admin-error" role="alert">{{ $message }}</p>@enderror
@if($products->isEmpty())
    <div class="empty-state">
        <span aria-hidden="true">⇄</span>
        <h2>Aún no tienes productos para comparar</h2>
        <p>Añade prendas desde el catálogo y aquí verás sus diferencias.</p>
        <a class="button-dark" href="{{ route('products.index') }}">Explorar productos</a>
    </div>
@else
    <div class="compare-table-wrap">
        <table class="compare-table">
            <thead>
                <tr><th scope="row">Producto</th>
                    @foreach($products as $product)
                        <th scope="col">
                            <a href="{{ route('products.show', $product->slug) }}" class="compare-product-name">{{ $product->name }}</a>
                            @if($product->mainImage?->image_path)<img src="{{ $product->mainImage->image_path }}" alt="{{ $product->name }}">@else<div class="product-tile__fallback">Sin imagen</div>@endif
                            <form method="POST" action="{{ route('compare.remove', $product) }}">@csrf @method('DELETE')<button class="admin-delete" type="submit">Quitar</button></form>
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                <tr><th scope="row">Categoría</th>@foreach($products as $product)<td>{{ $product->category->name }}</td>@endforeach</tr>
                <tr><th scope="row">Precio</th>@foreach($products as $product)<td class="compare-price">${{ number_format($product->discount_price ?? $product->price, 2) }} @if($product->discount_price)<del>${{ number_format($product->price, 2) }}</del>@endif</td>@endforeach</tr>
                <tr><th scope="row">Tallas</th>@foreach($products as $product)<td>{{ $product->size ?: 'No especificadas' }}</td>@endforeach</tr>
                <tr><th scope="row">Colores</th>@foreach($products as $product)<td>{{ $product->color ?: 'No especificados' }}</td>@endforeach</tr>
                <tr><th scope="row">Existencias</th>@foreach($products as $product)<td>{{ $product->stock > 0 ? 'Disponible' : 'Agotado' }}</td>@endforeach</tr>
                <tr><th scope="row">Descripción</th>@foreach($products as $product)<td>{{ $product->description ?: 'Sin descripción' }}</td>@endforeach</tr>
            </tbody>
        </table>
    </div>
@endif
@endsection