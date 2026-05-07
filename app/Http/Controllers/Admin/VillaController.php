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
            "amenities" => "nullable|array",
            "amenities.*" => "string|max:100",
            "images.*" => "nullable|image|mimes:jpeg,png,jpg|max:2048",
        ]);
        
        $amenities = $request->amenities ? array_filter(array_map('trim', $request->amenities)) : null;

        $villa = Villa::create($request->except("images", "is_featured", "amenities") + [
            "is_featured" => $request->has("is_featured"),
            "amenities" => $amenities ? json_encode($amenities) : null,
        ]);
        
        if ($request->hasFile("images")) {
            foreach ($request->file("images") as $index => $image) {
                $path = $image->store("villas", "public");
                $villa->images()->create([
                    "image_path" => $path,
                    "is_primary" => $index === 0,
                    "sort_order" => $index,
                ]);
            }
        }
        
        return redirect()->route("admin.villas.index")->with("success", "Villa berhasil ditambahkan!");
    }
    
    public function edit(Villa $villa)
    {
        $villa->load('images');
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
            "amenities" => "nullable|array",
            "amenities.*" => "string|max:100",
            "images.*" => "nullable|image|mimes:jpeg,png,jpg|max:2048",
        ]);

        $amenities = $request->amenities ? array_filter(array_map('trim', $request->amenities)) : null;
        
        $villa->update($request->except("images", "is_featured", "amenities") + [
            "is_featured" => $request->has("is_featured"),
            "amenities" => $amenities,
        ]);
        
        if ($request->hasFile("images")) {
            foreach ($request->file("images") as $index => $image) {
                $path = $image->store("villas", "public");
                $villa->images()->create([
                    "image_path" => $path,
                    "is_primary" => $index === 0 && !$villa->images()->where("is_primary", true)->exists(),
                    "sort_order" => $index,
                ]);
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
