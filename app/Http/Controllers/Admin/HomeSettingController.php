<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HomeSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class HomeSettingController extends Controller
{
    public function edit()
    {
        return view('admin.settings.home', ['settings' => HomeSetting::current()]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'content' => ['required', 'array'],
            'content.*' => ['required', 'string', 'max:1000'],
            'hero_background' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:6144'],
            'cta_background' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:6144'],
            'remove_hero_background' => ['nullable', 'boolean'],
            'remove_cta_background' => ['nullable', 'boolean'],
            'address' => ['required', 'string', 'max:1000'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'map_zoom' => ['required', 'integer', 'between:1,19'],
        ]);

        $settings = HomeSetting::query()->firstOrNew(['id' => 1]);
        $settings->fill([
            'content' => array_merge(HomeSetting::defaults(), $validated['content']),
            'address' => $validated['address'],
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
            'map_zoom' => $validated['map_zoom'],
        ]);

        $this->updateBackground($request, $settings, 'hero');
        $this->updateBackground($request, $settings, 'cta');
        $settings->save();

        return back()->with('success', 'Pengaturan beranda berhasil diperbarui.');
    }

    private function updateBackground(Request $request, HomeSetting $settings, string $type): void
    {
        $column = $type.'_background_path';
        if ($request->boolean('remove_'.$type.'_background') || $request->hasFile($type.'_background')) {
            if ($settings->{$column}) {
                Storage::disk('public')->delete($settings->{$column});
            }
            $settings->{$column} = null;
        }

        if ($request->hasFile($type.'_background')) {
            $settings->{$column} = $request->file($type.'_background')->storePublicly('home-backgrounds', 'public');
        }
    }
}
