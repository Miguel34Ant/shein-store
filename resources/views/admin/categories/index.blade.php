@extends('layouts.app')

@section('title', 'Categorías | Administración')

@section('content')
<div class="admin-heading">
    <div><h1>Categorías</h1><p>Organiza los productos de la tienda.</p></div>
    <a class="button-dark" href="{{ route('admin.categories.create') }}">Nueva categoría</a>
</div>
<nav class="admin-nav" aria-label="Administración">
    <a href="{{ route('admin.dashboard') }}">Resumen</a>
    <a href="{{ route('admin.products.index') }}">Productos</a>
    <a class="is-active" href="{{ route('admin.categories.index') }}">Categorías</a>
    <a href="{{ route('admin.users.index') }}">Usuarios</a>
</nav>
@error('category')<p class="admin-error" role="alert">{{ $message }}</p>@enderror
@if($categories->isEmpty())
    <div class="admin-empty">Todavía no hay categorías.</div>
@else
    <div class="admin-table-wrap">
        <table class="admin-table">
            <thead><tr><th>Nombre</th><th>Slug</th><th>Productos</th><th>Acciones</th></tr></thead>
            <tbody>
            @foreach($categories as $category)
                <tr>
                    <td>{{ $category->name }}</td><td>{{ $category->slug }}</td><td>{{ $category->products_count }}</td>
                    <td><div class="admin-actions">
                        <a href="{{ route('admin.categories.edit', $category) }}">Editar</a>
                        @if($category->products_count === 0)
                            <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" onsubmit="return confirm('¿Eliminar esta categoría?')">
                                @csrf @method('DELETE')
                                <button class="admin-delete" type="submit">Eliminar</button>
                            </form>
                        @else
                            <span class="admin-muted">Tiene productos</span>
                        @endif
                    </div></td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <div class="pagination">{{ $categories->links() }}</div>
@endif
@endsection