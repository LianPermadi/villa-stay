<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Villa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class VillaController extends Controller
{
    public function index()
    {
        $villas = Villa::with('images')->latest()->paginate(10);
        return view("admin.villas.index", compact("villas"));
    }
    
    public function create()
    {
        return view("admin.villas.create");
    }
    
    public function store(Request $request)
    {
        $request->validate([
            "name" => "required|string|max:255",
            "description" => "required|string",
            "price_per_night" => "required|numeric|min:0",
            "capacity" => "required|integer|min:1",
            "bedrooms" => "required|integer|min:1",
            "bathrooms" => "required|integer|min:1",
            "area" => "nullable|numeric|min:0",
            "status" => "required|in:available,unavailable,maintenance",
            "amenities" => "nullable|string",
            "images.*" => "nullable|file|mimes:jpeg,png,jpg|max:2048",
        ]);
        
        // Parse amenities from newline-separated string to array
        $amenities = null;
        if ($request->amenities) {
            $amenities = array_filter(array_map('trim', explode("\n", $request->amenities)));
        }

        $villa = Villa::create($request->except("images", "is_featured", "amenities") + [
            "is_featured" => $request->has("is_featured"),
            "amenities" => $amenities ? json_encode($amenities) : null,
        ]);
        
        if ($request->hasFile("images")) {
            $primaryIndex = $request->input('primary_image_index', 0);
            foreach ($request->file("images") as $index => $image) {
                if ($image->isValid()) {
                    $path = $image->store("villas", "public");
                    $villa->images()->create([
                        "image_path" => $path,
                        "is_primary" => $index == $primaryIndex,
                        "sort_order" => $index,
                    ]);
                }
            }
        }
        
        return redirect()->route("admin.villas.index")->with("success", "Villa berhasil ditambahkan!");
    }
    
    public function edit(Villa $villa)
    {
        $villa->load('images');
        // Decode amenities JSON to array for form textarea (newline-separated)
        if (is_string($villa->amenities)) {
            $decoded = json_decode($villa->amenities, true);
            $villa->amenities = is_array($decoded) ? implode("\n", $decoded) : '';
        } elseif (is_array($villa->amenities)) {
            $villa->amenities = implode("\n", $villa->amenities);
        } else {
            $villa->amenities = '';
        }
        return view("admin.villas.edit", compact("villa"));
    }
    
    public function update(Request $request, Villa $villa)
    {
        $request->validate([
            "name" => "required|string|max:255",
            "description" => "required|string",
            "price_per_night" => "required|numeric|min:0",
            "capacity" => "required|integer|min:1",
            "bedrooms" => "required|integer|min:1",
            "bathrooms" => "required|integer|min:1",
            "area" => "nullable|numeric|min:0",
            "status" => "required|in:available,unavailable,maintenance",
            "amenities" => "nullable|string",
            "images.*" => "nullable|file|mimes:jpeg,png,jpg|max:2048",
        ]);

        // Parse amenities from newline-separated string to array
        $amenities = null;
        if ($request->amenities) {
            $amenities = array_filter(array_map('trim', explode("\n", $request->amenities)));
        }
        
        $villa->update($request->except("images", "is_featured", "amenities") + [
            "is_featured" => $request->has("is_featured"),
            "amenities" => $amenities,
        ]);

        // Handle existing image primary selection
        if ($request->filled('existing_primary_id')) {
            $existingPrimaryId = $request->input('existing_primary_id');
            // Demote all existing images
            $villa->images()->update(["is_primary" => false]);
            // Promote selected image
            $villa->images()->where('id', $existingPrimaryId)->update(["is_primary" => true]);
        }

        // Handle new image uploads
        if ($request->hasFile("images")) {
            $primaryIndex = $request->input('primary_image_index', -1);
            $hasExistingPrimary = $villa->images()->where("is_primary", true)->exists();
            
            foreach ($request->file("images") as $index => $image) {
                if ($image->isValid()) {
                    $path = $image->store("villas", "public");
                    
                    // Determine if this new image should be primary
                    $isPrimary = false;
                    if ($primaryIndex >= 0 && $index == $primaryIndex) {
                        $isPrimary = true;
                        // Demote existing primary if any
                        if ($hasExistingPrimary) {
                            $villa->images()->where("is_primary", true)->update(["is_primary" => false]);
                        }
                    } else if (!$hasExistingPrimary && $index == 0) {
                        // No existing primary and this is first image
                        $isPrimary = true;
                    }
                    
                    $villa->images()->create([
                        "image_path" => $path,
                        "is_primary" => $isPrimary,
                        "sort_order" => $index,
                    ]);
                } else {
                    \Log::warning('Image upload failed during update', [
                        'villa' => $villa->id,
                        'error_code' => $image->getError(),
                        'original_name' => $image->getClientOriginalName(),
                    ]);
                }
            }
        }
        
        return redirect()->route("admin.villas.index")->with("success", "Villa berhasil diperbarui!");
    }
    
    public function destroy(Villa $villa)
    {
        // Check if villa has any bookings
        if ($villa->bookings()->count() > 0) {
            return redirect()->route('admin.villas.index')
                ->with('error', 'Villa tidak dapat dihapus karena masih memiliki pemesanan.');
        }

        // Delete associated images from storage
        foreach ($villa->images as $image) {
            Storage::disk('public')->delete($image->image_path);
            $image->delete();
        }
        
        $villa->delete();
        return redirect()->route("admin.villas.index")->with("success", "Villa berhasil dihapus!");
    }
}
