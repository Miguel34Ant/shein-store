@extends('layouts.app')

@section('title', 'Mi cuenta | Prisma Studio')

@section('content')
<div class="catalogue-top">
    <div><h1>Hola, {{ auth()->user()->name }}</h1><p>{{ auth()->user()->email }}</p></div>
    <form method="POST" action="{{ route('logout') }}">@csrf<button class="button-dark" type="submit">Cerrar sesión</button></form>
</div>
<section aria-labelledby="orders-title">
    <div class="section-heading"><div><h2 id="orders-title">Mis pedidos</h2><p>Revisa el estado de tus compras</p></div></div>
    @forelse($orders as $order)
        <article class="cart-summary" style="margin-bottom: 10px">
            <div class="cart-summary__row"><strong>Pedido #{{ $order->id }}</strong><span>{{ $order->created_at->format('d/m/Y') }}</span></div>
            <div class="cart-summary__row"><span>{{ ucfirst($order->status) }} · {{ $order->items->sum('quantity') }} artículos</span><strong>${{ number_format($order->total, 2) }}</strong></div>
        </article>
    @empty
        <div class="empty-state"><span aria-hidden="true">⌂</span><h2>Aún no tienes pedidos</h2><p>Cuando hagas tu primera compra, aparecerá aquí.</p><a class="button-dark" href="{{ route('products.index') }}">Explorar prendas</a></div>
    @endforelse
</section>
@endsection