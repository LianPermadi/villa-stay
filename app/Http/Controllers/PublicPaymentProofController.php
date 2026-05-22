<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PublicPaymentProofController extends Controller
{
    public function show(Payment $payment)
    {
        $payment->loadMissing('booking');
        $user = Auth::user();

        abort_unless($user && ($user->isAdmin() || $payment->booking->user_id === $user->id), 403);
        abort_unless($payment->proof_image_exists, 404);

        return Storage::disk('public')->response($payment->proof_image);
    }
}
