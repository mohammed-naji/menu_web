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
        if (!in_array($type, ['in-restaurant', 'takeaway', 'delivery'])) abort(404);

        $restaurant = Restaurant::where('code', $code)
            ->with(['categories', 'menuItems' => function ($query) {
                $query->where('active', 1)->latest();
            }, 'menuItems.variations', 'menuItems.sizeVariations', 'settings'])
            ->firstOrFail();

        $categories = $restaurant->categories;

        $settings = [];
        foreach ($restaurant->settings as $setting) {
            $settings[$setting->key] = $setting->value;
        }

        return Inertia::render('Menu', compact('restaurant', 'categories', 'type', 'settings'));
    }
}
