<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\User;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function account()
    {
        $orders = Order::with('items.product')
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        return view('auth.account', compact('orders'));
    }

    public function login(Request $request)
    {
        $guestCart = $this->guestCart($request);
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors(['email' => 'Los datos de acceso no coinciden.'])->onlyInput('email');
        }

        $this->mergeGuestCart($guestCart, Auth::user());
        $request->session()->regenerate();

        $destination = Auth::user()->isAdmin() ? 'admin.dashboard' : 'home';

        return redirect()->intended(route($destination));
    }

    public function register(Request $request)
    {
        $guestCart = $this->guestCart($request);
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $user = new User([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
        $user->forceFill(['role' => User::ROLE_CLIENT])->save();

        Auth::login($user);
        $this->mergeGuestCart($guestCart, $user);
        $request->session()->regenerate();

        return redirect()->route('home')->with('success', '¡Tu cuenta está lista!');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }

    private function guestCart(Request $request): ?Cart
    {
        return Cart::with('items')->where('session_id', $request->session()->getId())->first();
    }

    private function mergeGuestCart(?Cart $guestCart, User $user): void
    {
        if (! $guestCart) {
            return;
        }

        $customerCart = Cart::firstOrCreate(['user_id' => $user->id]);
        foreach ($guestCart->items as $guestItem) {
            $customerItem = $customerCart->items()
                ->where('product_id', $guestItem->product_id)
                ->where('size', $guestItem->size)
                ->where('color', $guestItem->color)
                ->first();

            if ($customerItem) {
                $customerItem->increment('quantity', $guestItem->quantity);
                $guestItem->delete();
            } else {
                $guestItem->update(['cart_id' => $customerCart->id]);
            }
        }

        $guestCart->delete();
    }
}