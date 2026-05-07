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
        $villas = Villa::latest()->paginate(10);
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
            "images.*" => "nullable|image|mimes:jpeg,png,jpg|max:2048",
        ]);
        
        $villa = Villa::create($request->except("images", "is_featured") + [
            "is_featured" => $request->has("is_featured"),
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
            "images.*" => "nullable|image|mimes:jpeg,png,jpg|max:2048",
        ]);
        
        $villa->update($request->except("images", "is_featured") + [
            "is_featured" => $request->has("is_featured"),
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
        $villa->delete();
        return redirect()->route("admin.villas.index")->with("success", "Villa berhasil dihapus!");
    }
}
