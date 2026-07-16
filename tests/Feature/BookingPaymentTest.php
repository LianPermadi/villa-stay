<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Payment;
use App\Models\User;
use App\Models\Villa;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BookingPaymentTest extends TestCase
{
    use RefreshDatabase;

    public function test_reuploading_a_rejected_payment_returns_it_to_pending_review(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $booking = $this->createBooking($user);
        $payment = Payment::create([
            'booking_id' => $booking->id,
            'amount' => $booking->down_payment_amount,
            'payment_method' => 'gopay',
            'proof_image' => 'payment-proofs/old.jpg',
            'status' => 'rejected',
            'payment_type' => 'down_payment',
            'admin_notes' => 'Bukti tidak terbaca',
        ]);

        $response = $this->actingAs($user)->post(route('bookings.upload_payment', $booking), [
            'payment_method' => 'gopay',
            'payment_type' => 'down_payment',
            'proof_image' => $this->fakePng('proof.png'),
            'notes' => 'Bukti pengganti',
        ]);

        $response->assertSessionHasNoErrors();
        $payment->refresh();

        $this->assertSame('pending', $payment->status);
        $this->assertNull($payment->admin_notes);
        $this->assertSame('Bukti pengganti', $payment->notes);
    }

    public function test_user_cannot_cancel_a_booking_while_payment_is_being_reviewed(): void
    {
        $user = User::factory()->create();
        $booking = $this->createBooking($user);
        Payment::create([
            'booking_id' => $booking->id,
            'amount' => $booking->down_payment_amount,
            'payment_method' => 'gopay',
            'status' => 'pending',
            'payment_type' => 'down_payment',
        ]);

        $response = $this->actingAs($user)->post(route('bookings.cancel', $booking));

        $response->assertSessionHasErrors('cancel');
        $this->assertSame('pending', $booking->refresh()->status);
    }

    public function test_admin_cannot_process_more_than_the_approved_refund(): void
    {
        Storage::fake('public');

        $admin = User::factory()->create(['role' => 'admin']);
        $booking = $this->createBooking($admin, [
            'status' => 'cancelled',
            'refund_amount' => 300000,
            'refund_status' => 'pending',
        ]);
        $refund = Payment::create([
            'booking_id' => $booking->id,
            'amount' => -300000,
            'payment_method' => 'refund',
            'status' => 'pending',
            'payment_type' => 'refund',
        ]);

        $response = $this->actingAs($admin)->post(route('admin.bookings.process_refund', $booking), [
            'refund_amount' => 500000,
            'proof_image' => $this->fakePng('refund.png'),
        ]);

        $response->assertSessionHasErrors('refund_amount');
        $this->assertSame('pending', $booking->refresh()->refund_status);
        $this->assertSame('pending', $refund->refresh()->status);
    }

    private function createBooking(User $user, array $overrides = []): Booking
    {
        $villa = Villa::create([
            'name' => 'Villa Test',
            'description' => 'Villa untuk pengujian.',
            'price_per_night' => 1000000,
            'capacity' => 4,
            'bedrooms' => 2,
            'bathrooms' => 1,
            'status' => 'available',
            'down_payment_percentage' => 30,
            'payment_due_days' => 1,
        ]);

        return Booking::create(array_merge([
            'user_id' => $user->id,
            'villa_id' => $villa->id,
            'check_in' => now()->addMonth()->toDateString(),
            'check_out' => now()->addMonth()->addDays(2)->toDateString(),
            'num_nights' => 2,
            'num_guests' => 2,
            'total_price' => 2000000,
            'down_payment_amount' => 600000,
            'remaining_amount' => 1400000,
            'payment_status' => 'none',
            'payment_due_date' => now()->addMonth()->subDay()->toDateString(),
            'guest_name' => $user->name,
            'guest_email' => $user->email,
            'guest_phone' => '081234567890',
            'status' => 'pending',
        ], $overrides));
    }

    private function fakePng(string $name): UploadedFile
    {
        return UploadedFile::fake()->createWithContent(
            $name,
            base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+A8AAQUBAScY42YAAAAASUVORK5CYII='),
        );
    }
}
