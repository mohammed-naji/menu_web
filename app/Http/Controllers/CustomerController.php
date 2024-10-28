<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;

class CustomerController extends Controller
{
    public function index($code)
    {
        if (!Auth::check()) return redirect()->back();

        $restaurant = Restaurant::where('code', $code)->firstOrFail();

        $settings = [];
        foreach ($restaurant->settings as $setting) {
            $settings[$setting->key] = $setting->value;
        }

        /** @var \App\Models\User */
        $user = Auth::user();
        $orders = $user->orders()->latest()->paginate(10);

        return Inertia::render('Customer/Index', compact('restaurant', 'settings', 'orders'));
    }

    function login($code, $type)
    {
        if (Auth::check()) return redirect()->back();

        $restaurant = Restaurant::where('code', $code)->firstOrFail();
        $settings = [];
        foreach ($restaurant->settings as $setting) {
            $settings[$setting->key] = $setting->value;
        }

        return Inertia::render('Customer/Login', compact('restaurant', 'settings', 'type'));
    }

    function store(Request $request, $code, $type)
    {
        $request->validate([
            'email' => 'required|exists:users,email',
            'password' => 'required'
        ]);

        $user = User::where('email', $request->email)->where('restaurant_id', $request->restaurant_id)->first();

        if ($user) {
            if (Hash::check($request->password, $user->password)) {
                Auth::login($user);
                $request->session()->regenerateToken();
                return redirect()->intended(route('menu', [$code, $type]));
            } else {
                return redirect()
                    ->back()
                    ->withErrors(['email' => __('The password provided is not correct')])
                    ->withInput();
            }
        } else {
            return redirect()
                ->back()
                ->withErrors(['email' => __('The email provided does not match any account')])
                ->withInput();
        }
    }

    function register($code, $type)
    {
        if (Auth::check()) return redirect()->back();

        $restaurant = Restaurant::where('code', $code)->firstOrFail();
        $settings = [];
        foreach ($restaurant->settings as $setting) {
            $settings[$setting->key] = $setting->value;
        }

        return Inertia::render('Customer/Register', compact('restaurant', 'settings', 'type'));
    }

    function register_store(Request $request, $code, $type)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:' . User::class,
            'phone' => 'required|unique:users,phone',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $auth = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'restaurant_id' => $request->restaurant_id
        ]);

        Auth::login($auth);

        return redirect()->route('menu', [$code, $type]);
    }

    function logout(Request $request, $code, $type)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('menu', [$code, $type]);
    }
}
