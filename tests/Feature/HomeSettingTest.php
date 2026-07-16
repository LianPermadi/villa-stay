<?php

namespace Tests\Feature;

use App\Models\HomeSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class HomeSettingTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_displays_the_default_com_timor_leste_location(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Beach Road, Lautem District, Com, Timor-Leste')
            ->assertSee('-8.3597510');
    }

    public function test_admin_can_update_home_content_location_and_background(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->put(route('admin.settings.home.update'), [
            'content' => [
                'hero_title' => 'Menginap Nyaman di Timor-Leste',
                'location_title' => 'Kunjungi Villa-Sina',
            ],
            'address' => 'Avenida de Portugal, Dili, Timor-Leste',
            'latitude' => -8.5500000,
            'longitude' => 125.5700000,
            'map_zoom' => 16,
            'hero_background' => $this->fakePng('hero.png'),
        ]);

        $response->assertSessionHasNoErrors()->assertRedirect();
        $settings = HomeSetting::query()->firstOrFail();
        $this->assertSame('Menginap Nyaman di Timor-Leste', $settings->text('hero_title'));
        $this->assertSame('Avenida de Portugal, Dili, Timor-Leste', $settings->address);
        Storage::disk('public')->assertExists($settings->hero_background_path);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Menginap Nyaman di Timor-Leste')
            ->assertSee('Avenida de Portugal, Dili, Timor-Leste');
        $this->get(route('home-background.show', 'hero'))->assertOk();
    }

    private function fakePng(string $name): UploadedFile
    {
        return UploadedFile::fake()->createWithContent(
            $name,
            base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+A8AAQUBAScY42YAAAAASUVORK5CYII='),
        );
    }
}
