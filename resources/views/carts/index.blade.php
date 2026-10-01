@extends('layouts.app')

@section('title', 'Tu cesta | Prisma Studio')

@section('content')
@php($total = $cart->items->sum(fn ($item) => ($item->product->discount_price ?? $item->product->price) * $item->quantity))
<div class="cart-heading"><h1>Tu cesta</h1><span>({{ $cart->items->sum('quantity') }} artículos)</span></div>

@if($cart->items->isEmpty())
	<div class="empty-state">
		<span aria-hidden="true">♧</span>
		<h2>Tu cesta está esperando un look.</h2>
		<p>Explora las novedades y guarda aquí tus favoritos.</p>
		<a class="button-dark" href="{{ route('products.index') }}">Descubrir prendas</a>
	</div>
@else
	<div class="cart-layout">
		<div class="cart-list">
			@foreach($cart->items as $item)
				<article class="cart-item">
					@if($item->product->mainImage?->image_path)
						<img src="{{ $item->product->mainImage->image_path }}" alt="{{ $item->product->name }}">
					@else
							<div class="product-tile__fallback">Prisma Studio</div>
					@endif
					<div class="cart-item__details">
						<h2>{{ $item->product->name }}</h2>
						@if($item->color)<p>Color: {{ $item->color }}</p>@endif
						@if($item->size)<p>Talla: {{ $item->size }}</p>@endif
						<p>Cantidad: {{ $item->quantity }}</p>
						<span class="cart-item__price">${{ number_format(($item->product->discount_price ?? $item->product->price) * $item->quantity, 2) }}</span>
					</div>
					<form action="{{ route('cart.remove', $item) }}" method="POST">
						@csrf
						@method('DELETE')
						<button class="remove-button" type="submit" aria-label="Eliminar {{ $item->product->name }}">Eliminar</button>
					</form>
				</article>
			@endforeach
		</div>
		<aside class="cart-summary">
			<h2>Resumen del pedido</h2>
			<div class="cart-summary__row"><span>Subtotal</span><span>${{ number_format($total, 2) }}</span></div>
			<div class="cart-summary__row"><span>Envío</span><span>Se calcula al confirmar</span></div>
			<div class="cart-summary__row cart-summary__total"><span>Total</span><span>${{ number_format($total, 2) }}</span></div>
			@auth
				<form action="{{ route('order.store') }}" method="POST">
					@csrf
					<label class="form-field" for="address"><span style="font-size: 12px; font-weight: 700">Dirección de entrega</span>
						<input id="address" name="address" value="{{ old('address') }}" placeholder="Calle, ciudad y país" required>
						@error('address')<span class="field-error">{{ $message }}</span>@enderror
					</label>
					<button class="button-dark" type="submit">Confirmar compra</button>
				</form>
			@else
				<a class="button-coral" href="{{ route('login') }}">Ingresar para comprar</a>
				<p style="margin: 10px 0 0; color: #777; text-align: center; font-size: 11px">¿Primera vez aquí? <a href="{{ route('register') }}" style="font-weight: 800; color: #171717">Crea tu cuenta</a></p>
			@endauth
		</aside>
	</div>
@endif
@endsection
