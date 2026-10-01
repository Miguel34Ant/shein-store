@extends('layouts.app')

@section('title', 'Productos | Administración')

@section('content')
<div class="admin-heading">
    <div><h1>Productos</h1><p>Administra precios, existencias y visibilidad.</p></div>
    <a class="button-dark" href="{{ route('admin.products.create') }}">Nuevo producto</a>
</div>
<nav class="admin-nav" aria-label="Administración">
    <a href="{{ route('admin.dashboard') }}">Resumen</a>
    <a class="is-active" href="{{ route('admin.products.index') }}">Productos</a>
    <a href="{{ route('admin.categories.index') }}">Categorías</a>
    <a href="{{ route('admin.users.index') }}">Usuarios</a>
</nav>
@if($products->isEmpty())
    <div class="admin-empty">Todavía no hay productos.</div>
@else
    <div class="admin-table-wrap">
        <table class="admin-table">
            <thead><tr><th>Producto</th><th>Categoría</th><th>Precio</th><th>Existencias</th><th>Estado</th><th>Acciones</th></tr></thead>
            <tbody>
            @foreach($products as $product)
                <tr>
                    <td>{{ $product->name }}</td>
                    <td>{{ $product->category->name }}</td>
                    <td>${{ number_format($product->discount_price ?? $product->price, 2) }}</td>
                    <td>{{ $product->stock }}</td>
                    <td>{{ $product->is_active ? 'Publicado' : 'Oculto' }}</td>
                    <td><div class="admin-actions">
                        <a href="{{ route('admin.products.edit', $product) }}">Editar</a>
                        <form method="POST" action="{{ route('admin.products.destroy', $product) }}" onsubmit="return confirm('¿Eliminar este producto?')">
                            @csrf @method('DELETE')
                            <button class="admin-delete" type="submit">Eliminar</button>
                        </form>
                    </div></td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <div class="pagination">{{ $products->links() }}</div>
@endif
@endsection