<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DashboardController extends Controller
{
    function index()
    {
        $restaurant = Auth::user()->restaurant;
        return Inertia::render('Dashboard', compact('restaurant'));
    }
}
