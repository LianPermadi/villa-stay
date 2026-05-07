<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Villa;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $featuredVillas = Villa::with('images')
            ->where("is_featured", true)
            ->where("status", "available")
            ->take(4)
            ->get();
        
        return view("frontend.home", compact("featuredVillas"));
    }
}
