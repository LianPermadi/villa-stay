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

        return view('frontend.users.profile', compact('user', 'bookings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.Auth::id(),
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
            'bank_name' => 'nullable|required_with:bank_account_number,bank_account_holder|string|max:100',
            'bank_account_number' => 'nullable|required_with:bank_name,bank_account_holder|string|max:50',
            'bank_account_holder' => 'nullable|required_with:bank_name,bank_account_number|string|max:255',
        ]);

        $user = Auth::user();
        $data = $request->only(
            'name',
            'email',
            'phone',
            'address',
            'bank_name',
            'bank_account_number',
            'bank_account_holder'
        );

        $user->update($data);

        return back()->with('success', 'Profil berhasil diperbarui!');
    }
}
