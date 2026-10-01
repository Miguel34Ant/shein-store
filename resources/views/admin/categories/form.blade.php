@extends('layouts.app')

@php($editing = $category->exists)
@section('title', ($editing ? 'Editar categoría' : 'Nueva categoría') . ' | Administración')

@section('content')
<div class="admin-heading">
    <div><h1>{{ $editing ? 'Editar categoría' : 'Nueva categoría' }}</h1><p>Define el nombre y la dirección de la categoría.</p></div>
    <a href="{{ route('admin.categories.index') }}">Volver a categorías</a>
</div>
<form class="admin-form" method="POST" action="{{ $editing ? route('admin.categories.update', $category) : route('admin.categories.store') }}">
    @csrf
    @if($editing) @method('PUT') @endif
    @if($errors->any())
        <div class="admin-error" role="alert"><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
    @endif
    <div class="form-field"><label for="name">Nombre</label><input id="name" name="name" value="{{ old('name', $category->name) }}" required></div>
    <div class="form-field"><label for="slug">Slug <span class="admin-muted">(opcional; se genera desde el nombre)</span></label><input id="slug" name="slug" value="{{ old('slug', $category->slug) }}"></div>
    <button class="button-dark" type="submit">{{ $editing ? 'Guardar cambios' : 'Crear categoría' }}</button>
</form>
@endsection