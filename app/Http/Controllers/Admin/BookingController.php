<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\Villa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BookingController extends Controller
{
    public function index()
    {
        $bookings = Booking::with(['villa', 'user', 'payment'])->latest()->paginate(10);
        return view("admin.bookings.index", compact("bookings"));
    }
    
    public function show(Booking $booking)
    {
        $booking->load(['villa', 'user', 'payments']);
        return view("admin.bookings.show", compact("booking"));
    }

    /**
     * Verify payment and confirm booking
     */
    public function verifyPayment(Request $request, Booking $booking)
    {
        $request->validate([
            'payment_id' => 'required|exists:payments,id',
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        $payment = Payment::where('id', $request->payment_id)
            ->where('booking_id', $booking->id)
            ->firstOrFail();

        // Only pending payments can be verified
        if ($payment->status !== 'pending') {
            return back()->withErrors(['payment' => 'Hanya pembayaran dengan status pending yang dapat diverifikasi']);
        }

        if ($payment->status === 'verified') {
            return back()->withErrors(['payment' => 'Pembayaran sudah diverifikasi']);
        }

        // Additional validation: if verifying final payment, ensure DP has been verified first
        if ($payment->payment_type === 'final_payment') {
            $dpPaid = $booking->payments()
                ->where('payment_type', 'down_payment')
                ->where('status', 'verified')
                ->exists();
            if (!$dpPaid) {
                return back()->withErrors(['payment' => 'DP belum dibayarkan. Tidak dapat verifikasi pelunasan sebelum DP.']);
            }
        }

        $payment->status = 'verified';
        $payment->admin_notes = $request->admin_notes;
        $payment->save();

        // Update booking status based on payment type
        if ($payment->payment_type === 'down_payment') {
            $booking->payment_status = 'dp_paid';
            // If no remaining amount, mark as fully paid
            if ($booking->remaining_amount <= 0) {
                $booking->payment_status = 'fully_paid';
            }
            $booking->status = 'confirmed'; // Confirm booking once DP paid
        } elseif ($payment->payment_type === 'final_payment') {
            $booking->payment_status = 'fully_paid';
            $booking->status = 'confirmed';
        }
        $booking->save();

        return redirect()->route("admin.bookings.show", $booking)->with("success", "Pembayaran berhasil diverifikasi!");
    }

    /**
     * Reject payment with refund
     */
    public function rejectPayment(Request $request, Booking $booking)
    {
        $request->validate([
            'payment_id' => 'required|exists:payments,id',
            'rejection_reason' => 'required|string|max:1000',
            'refund_type' => 'required|in:full,partial,none',
            'refund_amount' => 'nullable|numeric|min:0',
        ]);

        $payment = Payment::where('id', $request->payment_id)
            ->where('booking_id', $booking->id)
            ->firstOrFail();

        // Only pending payments can be rejected
        if ($payment->status !== 'pending') {
            return back()->withErrors(['payment' => 'Hanya pembayaran dengan status pending yang dapat ditolak']);
        }

        if ($payment->status === 'rejected') {
            return back()->withErrors(['payment' => 'Pembayaran sudah ditolak']);
        }

        $payment->status = 'rejected';
        $payment->admin_notes = $request->rejection_reason;
        $payment->save();

        $refundAmount = 0;
        if ($request->refund_type === 'full') {
            $refundAmount = $payment->amount;
        } elseif ($request->refund_type === 'partial') {
            $refundAmount = min($request->refund_amount ?? 0, $payment->amount);
        }

        // If there's a refund, create refund payment record and cancel booking
        if ($refundAmount > 0) {
            $booking->refund_amount = $refundAmount;
            $booking->refund_status = 'pending';
            $booking->reject_status = $request->refund_type === 'full' ? 'full_refund' : 'partial_refund';
            $booking->rejection_reason = $request->rejection_reason;
            $booking->status = 'cancelled';
            $booking->save();

            // Create refund payment record
            $booking->payments()->create([
                'amount' => -$refundAmount,
                'payment_method' => 'refund',
                'transaction_id' => 'REFUND-' . $booking->id . '-' . time(),
                'status' => 'pending',
                'payment_type' => 'refund',
                'notes' => "Pengembalian dana karena pembayaran ditolak. " . $request->rejection_reason,
            ]);
        } else {
            $booking->reject_status = 'rejected';
            $booking->rejection_reason = $request->rejection_reason;
            $booking->status = 'cancelled';
            $booking->save();
        }

        return redirect()->route("admin.bookings.show", $booking)->with("success", "Pembayaran ditolak. " . ($refundAmount > 0 ? "Pengembalian dana akan diproses." : ""));
    }

    /**
     * Process refund (admin initiates actual money return)
     */
    public function processRefund(Request $request, Booking $booking)
    {
        $request->validate([
            'refund_amount' => 'required|numeric|min:0',
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        if ($booking->refund_status !== 'pending') {
            return back()->withErrors(['refund' => 'Tidak ada refund yang sedang diproses']);
        }

        // Update refund payment to completed
        $refundPayment = $booking->payments()
            ->where('payment_type', 'refund')
            ->where('status', 'pending')
            ->first();

        if ($refundPayment) {
            $refundPayment->status = 'verified';
            $refundPayment->admin_notes = $request->admin_notes;
            $refundPayment->save();
        }

        $booking->refund_status = 'completed';
        $booking->refund_date = now();
        $booking->save();

        return redirect()->route("admin.bookings.show", $booking)->with("success", "Pengembalian dana berhasil diproses!");
    }

    /**
     * Manual status update
     */
    public function updateStatus(Request $request, Booking $booking)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,completed,cancelled',
        ]);

        $booking->status = $request->status;
        $booking->save();

        return back()->with("success", "Status booking berhasil diperbarui!");
    }
}

