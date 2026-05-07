<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function profile()
    {
        $user = Auth::user();
        $bookings = $user->bookings()->latest()->get();
        return view("frontend.users.profile", compact("user", "bookings"));
    }
    
    public function update(Request $request)
    {
        $request->validate([
            "name" => "required|string|max:255",
            "email" => "required|email|unique:users,email," . Auth::id(),
            "phone" => "nullable|string",
            "address" => "nullable|string",
        ]);
        
        $user = Auth::user();
        $user->update($request->only("name", "email", "phone", "address"));
        
        return back()->with("success", "Profil berhasil diperbarui!");
    }
}
