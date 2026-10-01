<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        return view('admin.users.index', [
            'users' => User::latest()->paginate(20),
            'adminCount' => User::where('role', User::ROLE_ADMIN)->count(),
        ]);
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->is(auth()->user())) {
            return back()->withErrors(['user' => 'No puedes eliminar tu propia cuenta desde el panel.']);
        }

        if ($user->isAdmin() && User::where('role', User::ROLE_ADMIN)->count() <= 1) {
            return back()->withErrors(['user' => 'No se puede eliminar al último administrador.']);
        }

        Cart::where('user_id', $user->id)->delete();
        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'Usuario eliminado. Sus pedidos asociados también se eliminarán.');
    }
}