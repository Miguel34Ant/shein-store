<!DOCTYPE html>
<html lang="es">
<head>
 <meta charset="UTF-8">
 <meta name="viewport" content="width=device-width, initial-scale=1.0">
 <meta name="theme-color" content="#ffffff">
 <title>@yield('title', 'Prisma Studio | Moda para todos')</title>
 <link rel="preconnect" href="https://fonts.googleapis.com">
 <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
 <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700;800&family=Manrope:wght@500;600;700;800&display=swap" rel="stylesheet">
 <link rel="stylesheet" href="{{ asset('css/storefront.css') }}">
 <script src="https://cdn.tailwindcss.com"></script>
 <script>
 tailwind.config = {
 theme: {
 extend: {
 colors: {
 brand: {
 DEFAULT: '#ff4d6d',
 dark: '#c9184a',
 }
 }
 }
 }
 }
 </script>
</head>
<body class="storefront">
 <header class="site-header">
  <div class="site-header__main">
   <a href="{{ route('home') }}" class="brand-mark" aria-label="Prisma Studio, inicio">prisma<span>studio</span><i>.</i></a>
   <nav class="desktop-links" aria-label="Categorías principales">
	<a href="{{ route('products.index') }}">Novedades</a>
	@foreach(\App\Models\Category::all() as $cat)
	 <a href="{{ route('products.index', ['category' => $cat->slug]) }}">{{ $cat->name }}</a>
	@endforeach
   </nav>
   <form action="{{ route('products.index') }}" method="GET" class="header-search">
	<span aria-hidden="true">⌕</span>
	<input type="search" name="q" value="{{ request('q') }}" placeholder="Buscar prendas, estilos y más">
	<button type="submit" aria-label="Buscar">Buscar</button>
   </form>
   <div class="header-actions">
   <a class="icon-link account-link" href="{{ auth()->check() ? route('account') : route('login') }}" aria-label="Mi cuenta">
	 <span aria-hidden="true">♙</span><small>{{ auth()->check() ? auth()->user()->name : 'Ingresar' }}</small>
	</a>
   @auth
      @if(auth()->user()->isAdmin())
         <a class="icon-link" href="{{ route('admin.dashboard') }}" aria-label="Administración"><span aria-hidden="true">⚙</span><small>Admin</small></a>
      @endif
   @endauth
   <a class="icon-link compare-link" href="{{ route('compare.index') }}" aria-label="Comparar productos"><span aria-hidden="true">⇄</span><small>Comparar {{ count(session('compare_products', [])) ?: '' }}</small></a>
	<a class="icon-link bag-link" href="{{ route('cart.index') }}" aria-label="Ver carrito"><span aria-hidden="true">♧</span><small>Carrito</small></a>
   </div>
  </div>
  <form action="{{ route('products.index') }}" method="GET" class="mobile-search">
   <span aria-hidden="true">⌕</span>
   <input type="search" name="q" value="{{ request('q') }}" placeholder="Pantalones, tops, accesorios...">
   <button type="submit" aria-label="Buscar">Buscar</button>
  </form>
  <nav class="mobile-categories" aria-label="Explorar categorías">
   <a href="{{ route('products.index') }}">Todo</a>
   @foreach(\App\Models\Category::all() as $cat)
	<a href="{{ route('products.index', ['category' => $cat->slug]) }}">{{ $cat->name }}</a>
   @endforeach
  </nav>
 </header>
 <!-- Mensajes flash -->
 @if(session('success'))
 <div class="flash-message">
 <div class="flash-message__inner">
 {{ session('success') }}
 </div>
 </div>
 @endif
 <main class="page-content">
 @yield('content')
 </main>
 <footer class="site-footer">
  <a href="{{ route('home') }}" class="brand-mark">prisma<span>studio</span><i>.</i></a>
  <p>Estilo para todos los días.</p>
  <small>© {{ date('Y') }} Prisma Studio</small>
 </footer>
 <nav class="bottom-nav" aria-label="Navegación principal">
  <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'is-active' : '' }}"><span aria-hidden="true">⌂</span><small>Comprar</small></a>
  <a href="{{ route('products.index') }}" class="{{ request()->routeIs('products.*') ? 'is-active' : '' }}"><span aria-hidden="true">⌕</span><small>Categoría</small></a>
  <a href="{{ route('products.index', ['sort' => 'price_asc']) }}"><span aria-hidden="true">↗</span><small>Tendencias</small></a>
  <a href="{{ route('cart.index') }}" class="{{ request()->routeIs('cart.*') ? 'is-active' : '' }}"><span aria-hidden="true">♧</span><small>Cesta</small></a>
   <a href="{{ auth()->check() ? route('account') : route('login') }}" class="{{ request()->routeIs('login', 'register', 'account') ? 'is-active' : '' }}"><span aria-hidden="true">♙</span><small>Yo</small></a>
 </nav>
 <script src="{{ asset('js/storefront.js') }}" defer></script>
</body>
</html>