<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'forename' => [ 'string', 'max:255'],
            'surname' => [ 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'add1' => [ 'string', 'max:255'],
            'add2' => [ 'string', 'max:255'],
            'city' => [ 'string', 'max:255'],
            'phone' => [ 'string', 'max:10'],
            'postcode' => ['integer'],



        ]);

        $user = User::create([
            'forename' => $request->forename,
            'surname' => $request->surname,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'add1' => $request->add1,
            'add2' => $request->add2,
            'city' => $request->city,
            'postcode' => $request->postcode,
            'phone' => $request->phone,

        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(RouteServiceProvider::HOME);
    }
}
