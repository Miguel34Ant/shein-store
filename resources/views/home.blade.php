@extends('layouts.app')

@section('title', 'Prisma Studio | Moda para todos')

@section('content')
@php($heroImage = $products->first()?->mainImage?->image_path)
<section class="home-hero" style="--hero-image: url('{{ $heroImage ?: 'https://images.unsplash.com/photo-1529139574466-a303027c1d8b?auto=format&fit=crop&w=1800&q=85' }}')">
	<div class="home-hero__copy">
		<span class="eyebrow">Nueva temporada · 2026</span>
		<h1>Tu estilo, sin pedir permiso.</h1>
		<p>Encuentra prendas que se sienten como tú. Novedades cada semana.</p>
		<a class="button-dark" href="{{ route('products.index') }}">Explorar colección <span aria-hidden="true">→</span></a>
	</div>
	<div class="home-hero__stamp">Hasta 30% menos<br>en estilos seleccionados</div>
</section>

<section aria-labelledby="categories-title">
	<div class="section-heading">
		<div><h2 id="categories-title">Compra por categoría</h2><p>Tu próximo favorito está aquí</p></div>
		<a href="{{ route('products.index') }}">Ver todo <span aria-hidden="true">→</span></a>
	</div>
	<div class="category-rail">
		@foreach($categories as $category)
			<a class="category-tile" href="{{ route('products.index', ['category' => $category->slug]) }}"><span>{{ $category->name }}</span></a>
		@endforeach
	</div>
</section>

<a class="promo-strip" href="{{ route('products.index', ['sort' => 'price_asc']) }}">
	<strong>Looks nuevos, precios que te van.</strong>
	<span>Descubre piezas desde $15.00 <b aria-hidden="true">→</b></span>
</a>

<section aria-labelledby="selection-title">
	<div class="section-heading">
		<div><h2 id="selection-title">Selección para ti</h2><p>Prendas para darle un giro a tu semana</p></div>
		<a href="{{ route('products.index') }}">Ver catálogo <span aria-hidden="true">→</span></a>
	</div>
	<div class="product-grid">
		@foreach($products as $product)
			@include('products._card', ['product' => $product])
		@endforeach
	</div>
</section>
@endsection