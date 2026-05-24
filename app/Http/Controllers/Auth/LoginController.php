<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view("auth.login");
    }
    
    public function login(Request $request)
    {
        $request->validate([
            "email" => "required|email",
            "password" => "required|string",
        ]);
        
        if (Auth::attempt($request->only("email", "password"), $request->has("remember"))) {
            $request->session()->regenerate();
            
            if (Auth::user()->role === "admin") {
                return redirect()->route("admin.dashboard");
            }
            
            return redirect()->route("home");
        }
        
        return back()
            ->withInput($request->only("email", "remember"))
            ->withErrors(["login" => "Email atau password salah. Silakan periksa kembali data login Anda."]);
    }
    
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route("home");
    }
}
