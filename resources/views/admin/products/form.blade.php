@extends('layouts.app')

@php($editing = $product->exists)
@section('title', ($editing ? 'Editar producto' : 'Nuevo producto') . ' | Administración')

@section('content')
<div class="admin-heading">
    <div><h1>{{ $editing ? 'Editar producto' : 'Nuevo producto' }}</h1><p>Completa la información del catálogo.</p></div>
    <a href="{{ route('admin.products.index') }}">Volver a productos</a>
</div>
<form class="admin-form" method="POST" action="{{ $editing ? route('admin.products.update', $product) : route('admin.products.store') }}">
    @csrf
    @if($editing) @method('PUT') @endif
    @if($errors->any())
        <div class="admin-error" role="alert"><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
    @endif
    <div class="admin-form-grid">
        <div class="form-field"><label for="name">Nombre</label><input id="name" name="name" value="{{ old('name', $product->name) }}" required></div>
        <div class="form-field"><label for="slug">Slug <span class="admin-muted">(opcional)</span></label><input id="slug" name="slug" value="{{ old('slug', $product->slug) }}"></div>
        <div class="form-field"><label for="category_id">Categoría</label><select id="category_id" name="category_id" required><option value="">Seleccionar</option>@foreach($categories as $category)<option value="{{ $category->id }}" @selected(old('category_id', $product->category_id) == $category->id)>{{ $category->name }}</option>@endforeach</select></div>
        <div class="form-field"><label for="price">Precio</label><input id="price" name="price" type="number" min="0" step="0.01" value="{{ old('price', $product->price) }}" required></div>
        <div class="form-field"><label for="discount_price">Precio rebajado <span class="admin-muted">(opcional)</span></label><input id="discount_price" name="discount_price" type="number" min="0" step="0.01" value="{{ old('discount_price', $product->discount_price) }}"></div>
        <div class="form-field"><label for="stock">Existencias</label><input id="stock" name="stock" type="number" min="0" step="1" value="{{ old('stock', $product->stock ?? 0) }}" required></div>
        <div class="form-field"><label for="size">Tallas <span class="admin-muted">(separadas por coma)</span></label><input id="size" name="size" value="{{ old('size', $product->size) }}"></div>
        <div class="form-field"><label for="color">Colores <span class="admin-muted">(separados por coma)</span></label><input id="color" name="color" value="{{ old('color', $product->color) }}"></div>
        <div class="form-field"><label for="image_url">URL de imagen principal</label><input id="image_url" name="image_url" type="url" value="{{ old('image_url', $imageUrl) }}"></div>
    </div>
    <div class="form-field"><label for="description">Descripción</label><textarea id="description" name="description">{{ old('description', $product->description) }}</textarea></div>
    <label class="admin-check"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $product->is_active ?? true))> Publicar en la tienda</label>
    <button class="button-dark" type="submit">{{ $editing ? 'Guardar cambios' : 'Crear producto' }}</button>
</form>
@endsection