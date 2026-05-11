<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\Villa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class BookingController extends Controller
{
    public function create($villaId)
    {
        $villa = Villa::with('images')->findOrFail($villaId);
        return view("frontend.bookings.create", compact("villa"));
    }
    
    public function store(Request $request, $villaId)
    {
        $villa = Villa::findOrFail($villaId);
        
        $request->validate([
            "check_in" => "required|date|after:today",
            "check_out" => "required|date|after:check_in",
            "num_guests" => "required|integer|min:1|max:" . $villa->capacity,
            "guest_name" => "required|string|max:255",
            "guest_email" => "required|email",
            "guest_phone" => "required|string",
            "payment_plan" => "required|in:dp,full",
        ]);
        
        $checkIn = Carbon::parse($request->check_in);
        $checkOut = Carbon::parse($request->check_out);
        $numNights = $checkIn->diffInDays($checkOut);
        
        if (!$villa->isAvailable($request->check_in, $request->check_out)) {
            return back()->withErrors(["villa" => "Villa tidak tersedia untuk tanggal tersebut"]);
        }
        
        $totalPrice = $villa->price_per_night * $numNights;
        
        // Calculate payment amounts based on selected plan
        if ($request->payment_plan === 'full') {
            $downPaymentAmount = $totalPrice; // Full amount as DP
            $remainingAmount = 0;
        } else {
            $downPaymentAmount = $villa->calculateDownPaymentAmount($numNights);
            $remainingAmount = $totalPrice - $downPaymentAmount;
        }
        
        // Final payment is open from H-7 until H-1 before check-in.
        $paymentDueDate = $checkIn->copy()->subDay();
        
        $booking = Booking::create([
            "user_id" => Auth::id(),
            "villa_id" => $villa->id,
            "check_in" => $request->check_in,
            "check_out" => $request->check_out,
            "num_nights" => $numNights,
            "num_guests" => $request->num_guests,
            "total_price" => $totalPrice,
            "down_payment_amount" => $downPaymentAmount,
            "remaining_amount" => $remainingAmount,
            "payment_status" => "none",
            "payment_due_date" => $paymentDueDate,
            "guest_name" => $request->guest_name,
            "guest_email" => $request->guest_email,
            "guest_phone" => $request->guest_phone,
            "special_requests" => $request->special_requests,
            "status" => "pending",
        ]);
        
        $message = "Booking berhasil dibuat! ";
        if ($request->payment_plan === 'full') {
            $message .= "Harap lakukan pembayaran lunas sebelum batas waktu.";
        } else {
            $message .= "Harap lakukan pembayaran DP sebelum batas waktu.";
        }
        
        return redirect()->route("bookings.show", $booking)->with("success", $message);
    }
     
    public function index(Request $request)
    {
        $request->validate([
            'approved_from' => 'nullable|date',
            'approved_to' => 'nullable|date|after_or_equal:approved_from',
        ]);

        $approvedFrom = $request->input('approved_from', now()->subMonths(3)->toDateString());
        $approvedTo = $request->input('approved_to', now()->addMonths(3)->toDateString());

        $baseQuery = Booking::where("user_id", Auth::id())
            ->with(['villa', 'payments' => function ($q) {
                $q->latest()->limit(1);
            }]);

        $approvedBookings = (clone $baseQuery)
            ->where('payment_status', 'fully_paid')
            ->whereDate('check_in', '>=', $approvedFrom)
            ->whereDate('check_in', '<=', $approvedTo)
            ->latest()
            ->get();

        $unapprovedBookings = (clone $baseQuery)
            ->where('payment_status', '!=', 'fully_paid')
            ->latest()
            ->get();

        return view("frontend.bookings.index", compact("approvedBookings", "unapprovedBookings", "approvedFrom", "approvedTo"));
    }
    
    public function show(Booking $booking)
    {
        if ($booking->user_id != Auth::id()) {
            abort(403);
        }
        $booking->load(['villa', 'payments']);
        return view("frontend.bookings.show", compact("booking"));
    }

    /**
     * Upload payment proof (DP or final payment)
     */
    public function uploadPaymentProof(Request $request, Booking $booking)
    {
        if ($booking->user_id != Auth::id()) {
            abort(403);
        }

        // Prevent upload if booking is cancelled or completed
        if (in_array($booking->status, ['cancelled', 'completed'])) {
            return back()->withErrors(['payment' => 'Tidak dapat mengupload pembayaran untuk booking ini.']);
        }

        // Check for existing payment (not verified) of the same type (for transaction_id uniqueness)
        $existingPayment = $booking->payments()
            ->where('payment_type', $request->payment_type)
            ->whereIn('status', ['pending', 'rejected'])
            ->first();

        // Build validation rules
        $rules = [
            'payment_method' => 'required|string|max:255',
            'proof_image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'payment_type' => 'required|in:down_payment,final_payment',
        ];

        // Transaction ID only needs to be unique for the same payment type.
        // DP and final payment are separate payment stages and may reuse a bank reference.
        $transactionIdRule = Rule::unique('payments', 'transaction_id')
            ->where(fn ($query) => $query->where('payment_type', $request->payment_type));

        if ($existingPayment) {
            $transactionIdRule->ignore($existingPayment->id);
        }

        $rules['transaction_id'] = ['required', 'string', 'max:255', $transactionIdRule];

        $request->validate($rules, [
            'transaction_id.unique' => 'No. rekening/transaksi ini sudah digunakan untuk jenis pembayaran yang sama.',
        ]);

        $paymentType = $request->payment_type;

        // Check if payment of this type already verified
        $verifiedPaymentExists = $booking->payments()
            ->where('payment_type', $paymentType)
            ->where('status', 'verified')
            ->exists();
        if ($verifiedPaymentExists) {
            return back()->withErrors(['payment' => ucfirst(str_replace('_', ' ', $paymentType)) . ' untuk booking ini sudah diverifikasi.']);
        }

        // Additional validations based on payment type
        if ($paymentType === 'final_payment') {
            // Ensure DP has been verified first
            $dpPaid = $booking->payments()
                ->where('payment_type', 'down_payment')
                ->where('status', 'verified')
                ->exists();
            if (!$dpPaid) {
                return back()->withErrors(['payment' => 'DP belum dibayarkan. Harap lunasi DP terlebih dahulu sebelum melakukan pelunasan.']);
            }

            $now = Carbon::now();
            $finalPaymentStartDate = Carbon::parse($booking->check_in)->subDays(7)->startOfDay();
            $finalPaymentEndDate = Carbon::parse($booking->check_in)->subDay()->endOfDay();

            if ($now->lt($finalPaymentStartDate)) {
                return back()->withErrors([
                    'payment' => 'Pelunasan dapat dilakukan mulai H-7 check-in (' . $finalPaymentStartDate->format('d M Y') . ') sampai H-1 check-in (' . $finalPaymentEndDate->format('d M Y') . ').'
                ]);
            }

            if ($now->gt($finalPaymentEndDate)) {
                return back()->withErrors([
                    'payment' => 'Batas pelunasan sudah lewat. Pelunasan hanya dapat dilakukan sampai H-1 check-in (' . $finalPaymentEndDate->format('d M Y') . ').'
                ]);
            }
        }

        // Handle file upload
        $proofPath = $request->file('proof_image')->store('payment_proofs', 'public');

        $amount = ($paymentType === 'down_payment') ? $booking->down_payment_amount : $booking->remaining_amount;

        if ($existingPayment) {
            // Update existing pending payment
            $existingPayment->payment_method = $request->payment_method;
            $existingPayment->transaction_id = $request->transaction_id;
            $existingPayment->proof_image = $proofPath;
            $existingPayment->notes = $request->notes;
            $existingPayment->save();
        } else {
            // Create new payment record
            $booking->payments()->create([
                "amount" => $amount,
                "payment_method" => $request->payment_method,
                "transaction_id" => $request->transaction_id,
                "proof_image" => $proofPath,
                "status" => "pending",
                "payment_type" => $paymentType,
                "notes" => $request->notes,
            ]);
        }

        // Update booking payment status based on verified payments
        $downPaid = $booking->payments()->where('payment_type', 'down_payment')->where('status', 'verified')->exists();
        $finalPaid = $booking->payments()->where('payment_type', 'final_payment')->where('status', 'verified')->exists();

        if ($downPaid && $finalPaid) {
            $booking->payment_status = 'fully_paid';
        } elseif ($downPaid) {
            $booking->payment_status = 'dp_paid';
        }
        $booking->save();

        return back()->with("success", "Bukti pembayaran berhasil diupload! Menunggu verifikasi admin.");
    }

    /**
     * Cancel booking by user (before payment)
     */
    public function cancel(Booking $booking)
    {
        if ($booking->user_id != Auth::id()) {
            abort(403);
        }

        if ($booking->status !== 'pending') {
            return back()->withErrors(['cancel' => 'Booking tidak dapat dibatalkan']);
        }

        $booking->status = 'cancelled';
        $booking->save();

        return back()->with("success", "Booking berhasil dibatalkan");
    }
}
