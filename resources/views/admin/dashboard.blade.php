@extends('layouts.app')

@section('title', 'Administración | Prisma Studio')

@section('content')
<div class="admin-heading">
    <div><h1>Administración</h1><p>Gestiona el catálogo y las cuentas registradas.</p></div>
    <a class="button-dark" href="{{ route('home') }}">Ver tienda</a>
</div>
<nav class="admin-nav" aria-label="Administración">
    <a class="is-active" href="{{ route('admin.dashboard') }}">Resumen</a>
    <a href="{{ route('admin.products.index') }}">Productos</a>
    <a href="{{ route('admin.categories.index') }}">Categorías</a>
    <a href="{{ route('admin.users.index') }}">Usuarios</a>
</nav>
<section class="admin-stats" aria-label="Resumen de la tienda">
    <div class="admin-stat"><strong>{{ $productsCount }}</strong><span>Productos</span></div>
    <div class="admin-stat"><strong>{{ $categoriesCount }}</strong><span>Categorías</span></div>
    <div class="admin-stat"><strong>{{ $usersCount }}</strong><span>Cuentas registradas</span></div>
</section>
@endsection