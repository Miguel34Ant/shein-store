<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#f7f7f6">
    <title>Bienvenido | Prisma Studio</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700;800&family=Manrope:wght@500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/storefront.css') }}">
</head>
<body class="storefront auth-entry">
    <main class="entry-shell">
        <section class="entry-visual" aria-label="Prisma Studio">
            <a href="{{ route('home') }}" class="brand-mark" aria-label="Prisma Studio">prisma<span>studio</span><i>.</i></a>
            <div class="entry-visual__copy">
                <span class="eyebrow">Prisma Studio</span>
                <h1>Tu estilo empieza aquí.</h1>
                <p>Prendas para combinar a tu manera.</p>
            </div>
        </section>
        <section class="entry-panel" aria-labelledby="entry-title">
            <span class="entry-kicker">Bienvenido</span>
            <h2 id="entry-title">Entra a tu cuenta</h2>
            <p>Inicia sesión o crea una cuenta para continuar.</p>
            <a class="button-dark entry-button" href="{{ route('login') }}">Iniciar sesión</a>
            <a class="button-coral entry-button" href="{{ route('register') }}">Crear cuenta</a>
        </section>
    </main>
</body>
</html>