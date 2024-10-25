<?php

namespace App\Http\Controllers;

use App\Models\MenuItem;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MainController extends Controller
{
    function menu($code, $type = 'in-restaurant')
    {
        $restaurant = Restaurant::where('code', $code)
            ->with(['categories', 'menuItems' => function ($query) {
                $query->where('active', 1)->latest();
            }, 'menuItems.variations', 'menuItems.sizeVariations'])
            ->firstOrFail();

        $categories = $restaurant->categories;

        return Inertia::render('Menu', compact('restaurant', 'categories', 'type'));
    }
}
