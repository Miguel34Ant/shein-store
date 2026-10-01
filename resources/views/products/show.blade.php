@extends('layouts.app')

@section('title', $product->name . ' | Prisma Studio')

@section('content')
@php
	$sizes = collect(preg_split('/[,|]/', $product->size ?? ''))->map(fn ($size) => trim($size))->filter()->values();
	$colors = collect(preg_split('/[,|]/', $product->color ?? ''))->map(fn ($color) => trim($color))->filter()->values();
	$swatchColors = ['negro' => '#222222', 'blanco' => '#f4f2ed', 'azul' => '#527ba7', 'azul claro' => '#a8cce3', 'azul medio' => '#6687ae', 'verde' => '#67805c', 'verde militar' => '#65704b', 'vino' => '#803942', 'rosa' => '#e6a6b6', 'crema' => '#e5d9c5', 'beige' => '#c8b69c', 'gris' => '#999999', 'amarillo' => '#e3c747'];
	$price = $product->discount_price ?? $product->price;
@endphp
<div class="product-detail">
	<div class="product-gallery">
		<div class="product-thumbnails" aria-hidden="true">
			@foreach($product->images as $image)<img src="{{ $image->image_path }}" alt="">@endforeach
		</div>
		<div class="product-main-image">
			@if($product->mainImage?->image_path)
				<img src="{{ $product->mainImage->image_path }}" alt="{{ $product->name }}">
			@else
				<div class="product-tile__fallback" role="img" aria-label="Imagen no disponible">Prisma Studio</div>
			@endif
		</div>
	</div>

	<section class="product-info">
		<a class="product-category" href="{{ route('products.index', ['category' => $product->category->slug]) }}">{{ $product->category->name }}</a>
		<h1>{{ $product->name }}</h1>
		<div class="product-rating"><strong>★ 4.8</strong> &nbsp;|&nbsp; Favorito de la comunidad</div>
		<div class="product-price-row">
			<span class="product-price">${{ number_format($price, 2) }}</span>
			@if($product->discount_price)<span class="product-old-price">${{ number_format($product->price, 2) }}</span><span class="product-discount">PRECIO ESPECIAL</span>@endif
		</div>
		<p class="product-description">{{ $product->description ?: 'Una prenda versátil para combinar con tus básicos favoritos.' }}</p>

		<form action="{{ route('cart.add', $product) }}" method="POST">
			@csrf
			@if($sizes->isNotEmpty())
				<fieldset class="variant-group" style="border: 0; padding: 0">
					<legend class="variant-group__title">Selecciona tu talla <span>Guía de tallas</span></legend>
					<div class="size-options">
						@foreach($sizes as $size)
							<label class="size-option"><input type="radio" name="size" value="{{ $size }}" required @checked(old('size') === $size)><span>{{ $size }}</span></label>
						@endforeach
					</div>
					@error('size')<p class="field-error">{{ $message }}</p>@enderror
				</fieldset>
			@endif

			@if($colors->isNotEmpty())
				<fieldset class="variant-group" style="border: 0; padding: 0">
					<legend class="variant-group__title">Color <span>{{ old('color') ?: 'Elige un tono' }}</span></legend>
					<div class="color-options">
						@foreach($colors as $color)
							@php($swatch = $swatchColors[mb_strtolower($color)] ?? '#bdbdbd')
							<label class="color-option" title="{{ $color }}"><input type="radio" name="color" value="{{ $color }}" required @checked(old('color') === $color)><span style="--swatch: {{ $swatch }}"><em>{{ $color }}</em></span></label>
						@endforeach
					</div>
					@error('color')<p class="field-error">{{ $message }}</p>@enderror
				</fieldset>
			@endif

			<div class="variant-group">
				<label class="variant-group__title" for="quantity">Cantidad <span>{{ $product->stock }} disponibles</span></label>
				<select id="quantity" name="quantity" style="height: 40px; min-width: 82px; padding: 0 10px; border: 1px solid #ddd; background: white" aria-label="Cantidad">
					@for($quantity = 1; $quantity <= min(5, max(1, $product->stock)); $quantity++)<option value="{{ $quantity }}">{{ $quantity }}</option>@endfor
				</select>
			</div>
			<div class="product-stock"><strong>Envío disponible</strong> · Devoluciones fáciles · Pago seguro</div>
			<div class="product-add"><button class="button-coral" type="submit">Añadir a la cesta <span aria-hidden="true">→</span></button></div>
		</form>
		<div class="shipping-note">Entrega estimada en 5–10 días hábiles<br>Compra protegida · Atención al cliente todos los días</div>
	</section>
</div>

<section class="similar-section" aria-labelledby="similar-title">
	<div class="section-heading">
		<div><h2 id="similar-title">También va con tu estilo</h2><p>Más prendas que combinan con esta selección</p></div>
		<a href="{{ route('products.index', ['category' => $product->category->slug]) }}">Ver más <span aria-hidden="true">→</span></a>
	</div>
	<div class="product-grid">
		@forelse($related as $item)
			@include('products._card', ['product' => $item])
		@empty
			<p>No hay más prendas similares por ahora.</p>
		@endforelse
	</div>
</section>
@endsection
