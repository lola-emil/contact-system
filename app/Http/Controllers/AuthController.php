<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    //

    public function signInPage()
    {
        return Inertia::render("Login");
    }

    public function signUpPage()
    {
        return Inertia::render("Register");
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            "firstname" => "required|string|max:255",
            "lastname" => "required|string|max:255",
            "email" => "required|email",
            "password" => "required",
            "confirmPassword" => "required"
        ]);

        // Check if email already exists
        $matchedUser = User::select()->where("email", $validated["email"])->first();

        if ($matchedUser) {
            return back()->withErrors([
                "email" => "Email already taken."
            ]);
        }


        // check password confirmation
        if ($validated["password"] != $validated["confirmPassword"]) {
            return back()->withErrors([
                "password" => "Passwords doesn't match."
            ]);
        }

        User::create([
            "firstname" => $validated["firstname"],
            "lastname" => $validated["lastname"],
            "email" => $validated["email"],
            "password" => Hash::make($validated["password"])
        ]);

        return redirect()->route("login")->with("success", "Registered successfully.");
    }

    public function signIn(Request $request)
    {
        $credentials = $request->validate([
            "email" => ["required", "email"],
            "password" => ["required"]
        ]);


        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended("/");
        }

        return back()->withErrors([
            "email" => "Invalid credentials."
        ])->onlyInput("email");
    }
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route("login");
    }
}
