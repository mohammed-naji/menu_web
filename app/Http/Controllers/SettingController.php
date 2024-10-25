<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class SettingController extends Controller
{
    function index()
    {
        return Inertia::render('Settings');
    }

    function save(Request $request)
    {
        foreach ($request->all() as $key => $val) {
            if ($request->hasFile($key)) {
                $path = '/' . $request->file($key)->store('uploads', 'custom');
            }
            if (is_array($val)) {
                $val = json_encode($val, JSON_UNESCAPED_UNICODE);
            }
            Setting::updateOrCreate([
                'restaurant_id' => Auth::user()->restaurant->id,
                'key' => $key
            ], [
                'value' => $path ?? $val
            ]);
        }
    }
}
