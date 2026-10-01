@extends('layouts.app')

@section('title', 'Moda para ti | Prisma Studio')

@section('content')
<div class="catalogue-top">
	<div>
		<h1>{{ request('q') ? 'Resultados para “' . request('q') . '”' : (request('category') ? $categories->firstWhere('slug', request('category'))?->name : 'Descubre tu próximo look') }}</h1>
		<p>{{ $products->total() }} prendas para encontrar tu estilo</p>
	</div>
</div>

<nav class="filter-categories" aria-label="Filtrar por categoría">
	<a href="{{ route('products.index', request()->except('category', 'page')) }}" class="{{ request('category') ? '' : 'is-active' }}">Todo</a>
	@foreach($categories as $category)
		<a href="{{ route('products.index', array_merge(request()->except('page'), ['category' => $category->slug])) }}" class="{{ request('category') === $category->slug ? 'is-active' : '' }}">{{ $category->name }}</a>
	@endforeach
</nav>

<form action="{{ route('products.index') }}" method="GET" class="filter-bar">
	@if(request('category'))<input type="hidden" name="category" value="{{ request('category') }}">@endif
	<input type="search" name="q" value="{{ request('q') }}" placeholder="Buscar prendas o estilos" aria-label="Buscar prendas o estilos">
	<select name="size" aria-label="Filtrar por talla">
		<option value="">Todas las tallas</option>
		@foreach($sizes as $size)<option value="{{ $size }}" @selected(request('size') === $size)>{{ $size }}</option>@endforeach
	</select>
	<select name="color" aria-label="Filtrar por color">
		<option value="">Todos los colores</option>
		@foreach($colors as $color)<option value="{{ $color }}" @selected(request('color') === $color)>{{ $color }}</option>@endforeach
	</select>
	<select name="sort" aria-label="Ordenar productos">
		<option value="">Más recientes</option>
		<option value="price_asc" @selected(request('sort') === 'price_asc')>Precio: menor a mayor</option>
		<option value="price_desc" @selected(request('sort') === 'price_desc')>Precio: mayor a menor</option>
	</select>
	<button type="submit">Aplicar filtros</button>
</form>

<div class="product-grid">
	@forelse($products as $product)
		@include('products._card', ['product' => $product])
	@empty
		<div class="empty-state" style="grid-column: 1 / -1">
			<span aria-hidden="true">⌕</span>
			<h2>No encontramos prendas con esos filtros</h2>
			<p>Prueba con otra talla, color o palabra de búsqueda.</p>
			<a class="button-dark" href="{{ route('products.index') }}">Ver todo el catálogo</a>
		</div>
	@endforelse
</div>
<div class="pagination">{{ $products->links() }}</div>
@endsection