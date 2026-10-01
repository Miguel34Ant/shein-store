@extends('layouts.app')

@section('title', 'Crear cuenta | Prisma Studio')

@section('content')
<div class="auth-wrap">
    <section class="auth-panel">
        <h1>Tu estilo empieza aquí.</h1>
        <p>Crea tu cuenta para guardar tus datos y comprar con facilidad.</p>
        <form method="POST" action="{{ route('register.submit') }}">
            @csrf
            <div class="form-field">
                <label for="name">Nombre</label>
                <input id="name" name="name" type="text" value="{{ old('name') }}" autocomplete="name" required>
                @error('name')<span class="field-error">{{ $message }}</span>@enderror
            </div>
            <div class="form-field">
                <label for="email">Correo electrónico</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" required>
                @error('email')<span class="field-error">{{ $message }}</span>@enderror
            </div>
            <div class="form-field">
                <label for="password">Contraseña</label>
                <input id="password" name="password" type="password" autocomplete="new-password" required>
                <span style="color: #888; font-size: 11px">Usa al menos 8 caracteres.</span>
                @error('password')<span class="field-error">{{ $message }}</span>@enderror
            </div>
            <div class="form-field">
                <label for="password_confirmation">Confirma tu contraseña</label>
                <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required>
            </div>
            <button type="submit" class="button-dark">Crear mi cuenta</button>
        </form>
        <div class="auth-switch">¿Ya tienes cuenta? <a href="{{ route('login') }}">Ingresar</a></div>
    </section>
</div>
@endsection