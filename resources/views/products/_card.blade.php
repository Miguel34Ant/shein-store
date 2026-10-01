@php
	$productColors = collect(preg_split('/[,|]/', $product->color ?? ''))->map(fn ($color) => trim($color))->filter();
	$price = $product->discount_price ?? $product->price;
@endphp
<article class="product-tile">
	<div class="product-tile__media">
		<a href="{{ route('products.show', $product->slug) }}" aria-label="Ver {{ $product->name }}">
			@if($product->mainImage?->image_path)
				<img src="{{ $product->mainImage->image_path }}" alt="{{ $product->name }}" loading="lazy">
			@else
				<div class="product-tile__fallback" role="img" aria-label="Imagen no disponible">Prisma Studio</div>
			@endif
		</a>
		@if($product->discount_price)
			<span class="product-badge">Oferta</span>
		@endif
		<button class="favorite-button" type="button" aria-label="Guardar {{ $product->name }}" aria-pressed="false" data-favorite-toggle data-product-id="{{ $product->id }}">♡</button>
	</div>
	<div class="product-tile__info">
		<a href="{{ route('products.show', $product->slug) }}" class="product-tile__name">{{ $product->name }}</a>
		<div class="product-tile__prices">
			<span class="product-tile__price">${{ number_format($price, 2) }}</span>
			@if($product->discount_price)
				<span class="product-tile__old">${{ number_format($product->price, 2) }}</span>
			@endif
		</div>
		@if($productColors->isNotEmpty())
			<div class="product-tile__options"><span class="swatch-dots" aria-hidden="true"><i></i>@if($productColors->count() > 1)<i></i>@endif</span>{{ $productColors->count() }} {{ $productColors->count() === 1 ? 'color' : 'colores' }}</div>
		@endif
		<form class="compare-action" method="POST" action="{{ route('compare.add', $product) }}">
			@csrf
			<button type="submit">+ Comparar</button>
		</form>
	</div>
</article>