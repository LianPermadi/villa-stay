<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Payment;
use App\Models\User;
use App\Models\Villa;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminVillaReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_save_a_villa_with_us_dollar_currency(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->post(route('admin.villas.store'), $this->villaData([
            'currency' => 'USD',
            'price_per_night' => 125.50,
        ]));

        $response->assertSessionHasNoErrors()->assertRedirect(route('admin.villas.index'));
        $this->assertDatabaseHas('villas', [
            'name' => 'Villa Multi Currency',
            'currency' => 'USD',
            'price_per_night' => 125.50,
        ]);
    }

    public function test_admin_can_delete_an_existing_villa_image_while_editing(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['role' => 'admin']);
        $villa = Villa::create($this->villaData());
        Storage::disk('public')->put('villa-images/deleted.jpg', 'image');
        $image = $villa->images()->create([
            'image_path' => 'villa-images/deleted.jpg',
            'is_primary' => true,
            'sort_order' => 0,
        ]);

        $response = $this->actingAs($admin)->put(route('admin.villas.update', $villa), $this->villaData([
            'delete_images' => [$image->id],
        ]));

        $response->assertSessionHasNoErrors()->assertRedirect(route('admin.villas.index'));
        $this->assertDatabaseMissing('villa_images', ['id' => $image->id]);
        Storage::disk('public')->assertMissing('villa-images/deleted.jpg');
    }

    public function test_admin_can_download_a_valid_excel_financial_report(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $villa = Villa::create($this->villaData(['currency' => 'USD', 'price_per_night' => 100]));
        $booking = Booking::create([
            'user_id' => $admin->id,
            'villa_id' => $villa->id,
            'check_in' => now()->addDays(5)->toDateString(),
            'check_out' => now()->addDays(7)->toDateString(),
            'num_nights' => 2,
            'num_guests' => 2,
            'total_price' => 200,
            'currency' => 'USD',
            'guest_name' => $admin->name,
            'guest_email' => $admin->email,
            'guest_phone' => '081234567890',
            'status' => 'confirmed',
        ]);
        Payment::create([
            'booking_id' => $booking->id,
            'amount' => 200,
            'payment_method' => 'bank_transfer',
            'status' => 'verified',
            'payment_type' => 'down_payment',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.reports.export', [
            'currency' => 'USD',
            'date_from' => now()->subDay()->toDateString(),
            'date_to' => now()->addDay()->toDateString(),
        ]));

        $response->assertOk()->assertDownload();
        $path = $response->baseResponse->getFile()->getPathname();
        $this->assertSame('PK', file_get_contents($path, false, null, 0, 2));
        $archive = new \PharData($path);
        $this->assertTrue(isset($archive['xl/workbook.xml']));
        $this->assertTrue(isset($archive['xl/worksheets/sheet1.xml']));
    }

    private function villaData(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Villa Multi Currency',
            'description' => 'Villa untuk pengujian fitur admin.',
            'price_per_night' => 1000000,
            'currency' => 'IDR',
            'capacity' => 4,
            'bedrooms' => 2,
            'bathrooms' => 1,
            'area' => 120,
            'status' => 'available',
            'amenities' => "Kolam renang\nWiFi",
        ], $overrides);
    }
}
