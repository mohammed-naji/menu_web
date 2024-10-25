<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->has('q')) {
            $locale = app()->currentLocale();
            $categories = Auth::user()->restaurant->categories()
                ->latest()
                ->withCount(['menuItems' => function ($query) {
                    $query->where('restaurant_id', Auth::user()->restaurant->id);
                }])
                ->whereRaw("LOWER(JSON_UNQUOTE(JSON_EXTRACT(name, '$.\"$locale\"'))) LIKE ?", ['%' . strtolower($request->q) . '%'])
                ->paginate(10);
        } else {
            $categories = Auth::user()->restaurant->categories()
                ->latest()
                ->withCount(['menuItems' => function ($query) {
                    $query->where('restaurant_id', Auth::user()->restaurant->id);
                }])
                ->paginate(10);
        }

        return Inertia::render('Categories/Index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Categories/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name_en' => 'required',
            'name_ar' => 'required',
            'image' => 'required',
        ]);

        if ($request->hasFile('image')) {
            $path = '/' . $request->file('image')->store('uploads', 'custom');
        }

        Auth::user()->restaurant->categories()->create([
            'name' => [
                'en' => $request->name_en,
                'ar' => $request->name_ar
            ],
            'description' => [
                'en' => $request->description_en,
                'ar' => $request->description_ar,
            ],
            'image' => $path ?? null,
        ]);

        return Redirect::route('categories.index')
            ->with('message', 'Category added successfully')
            ->with('class', 'bg-green-200');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        return Inertia::render('Categories/Edit', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name_en' => 'required',
            'name_ar' => 'required',
        ]);

        $path = $category->image;
        if ($request->hasFile('image')) {
            File::delete($category->image);
            $path = '/' . $request->file('image')->store('uploads', 'custom');
        }

        $category->update([
            'name' => [
                'en' => $request->name_en,
                'ar' => $request->name_ar
            ],
            'description' => [
                'en' => $request->description_en,
                'ar' => $request->description_ar,
            ],
            'image' => $path,
        ]);

        return Redirect::route('categories.index')
            ->with('message', 'Category updated successfully')
            ->with('class', 'bg-green-200');
    }

    function delete_selected(Request $request)
    {
        Category::destroy($request->ids);
    }
}
