@extends('layouts.app')

@section('title', 'Usuarios | Administración')

@section('content')
<div class="admin-heading">
    <div><h1>Usuarios</h1><p>Administra las cuentas registradas. Al borrar una cuenta también se borran sus pedidos.</p></div>
</div>
<nav class="admin-nav" aria-label="Administración">
    <a href="{{ route('admin.dashboard') }}">Resumen</a>
    <a href="{{ route('admin.products.index') }}">Productos</a>
    <a href="{{ route('admin.categories.index') }}">Categorías</a>
    <a class="is-active" href="{{ route('admin.users.index') }}">Usuarios</a>
</nav>
@error('user')<p class="admin-error" role="alert">{{ $message }}</p>@enderror
<div class="admin-table-wrap">
    <table class="admin-table">
        <thead><tr><th>Nombre</th><th>Correo</th><th>Rol</th><th>Registro</th><th>Acciones</th></tr></thead>
        <tbody>
        @forelse($users as $user)
            <tr>
                <td>{{ $user->name }}</td><td>{{ $user->email }}</td>
                <td>{{ $user->isAdmin() ? 'Administrador' : 'Cliente' }}</td>
                <td>{{ $user->created_at->format('d/m/Y') }}</td>
                <td>
                    @if($user->id !== auth()->id() && (! $user->isAdmin() || $adminCount > 1))
                        <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('¿Eliminar esta cuenta y sus pedidos asociados?')">
                            @csrf @method('DELETE')
                            <button class="admin-delete" type="submit">Eliminar usuario</button>
                        </form>
                    @else
                        <span class="admin-muted">Cuenta protegida</span>
                    @endif
                </td>
            </tr>
        @empty
            <tr><td colspan="5">No hay usuarios registrados.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
<div class="pagination">{{ $users->links() }}</div>
@endsection