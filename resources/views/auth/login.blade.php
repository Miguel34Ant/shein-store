@extends('layouts.app')

@section('title', 'Iniciar sesión | Prisma Studio')

@section('content')
<div class="auth-wrap">
    <section class="auth-panel">
        <h1>Iniciar sesión</h1>
        <p>Ingresa el correo electrónico y la contraseña de tu cuenta.</p>
        <form method="POST" action="{{ route('login.submit') }}">
            @csrf
            <div class="form-field">
                <label for="email">Correo electrónico</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" required>
                @error('email')<span class="field-error">{{ $message }}</span>@enderror
            </div>
            <div class="form-field">
                <label for="password">Contraseña</label>
                <input id="password" name="password" type="password" autocomplete="current-password" required>
                @error('password')<span class="field-error">{{ $message }}</span>@enderror
            </div>
            <label style="display: flex; align-items: center; gap: 8px; color: #666; font-size: 12px"><input type="checkbox" name="remember" value="1"> Mantener mi sesión iniciada</label>
            <button type="submit" class="button-dark">Iniciar sesión</button>
        </form>
        <div class="auth-switch">¿Aún no tienes cuenta? <a href="{{ route('register') }}">Crear cuenta</a></div>
    </section>
</div>
@endsection