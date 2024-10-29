<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\MenuItem;
use App\Models\SizeVariation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class MenuItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $menuItems = Auth::user()->restaurant->menuItems()->with('category')->latest();

        if ($request->has('q')) {
            $locale = app()->currentLocale();
            $menuItems = $menuItems->whereRaw("LOWER(JSON_UNQUOTE(JSON_EXTRACT(name, '$.\"$locale\"'))) LIKE ?", ['%' . strtolower($request->q) . '%']);
        }

        if ($request->has('category') && $request->category != 'all') {
            $menuItems = $menuItems->where('category_id', $request->category);
        }

        $menuItems = $menuItems->paginate(10);

        $categories = Category::select('id', 'name')->get();

        return Inertia::render('MenuItems/Index', compact('menuItems', 'categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Auth::user()->restaurant->categories()->select('id', 'name')->get();
        $variations = Auth::user()->restaurant->variations()->with('variationValues')->select('id', 'name')->get();

        return Inertia::render('MenuItems/Create', compact('categories', 'variations'));
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
            'price' => 'required|numeric',
            'category_id' => 'required|exists:categories,id'
        ]);

        if ($request->hasFile('image')) {
            $path = '/' . $request->file('image')->store('uploads', 'custom');
        }

        $menuItem = Auth::user()->restaurant->menuItems()->create([
            'name' => [
                'en' => $request->name_en,
                'ar' => $request->name_ar
            ],
            'description' => [
                'en' => $request->description_en,
                'ar' => $request->description_ar,
            ],
            'image' => $path ?? null,
            'category_id' => $request->category_id,
            'price' => $request->price,
            'discount' => $request->discount,
            'order' => $request->order,
        ]);

        if ($request->has_size_variation) {
            foreach ($request->size_variations as $size) {
                SizeVariation::create([
                    'menu_item_id' => $menuItem->id,
                    'name' => [
                        'en' => $size['name_en'],
                        'ar' => $size['name_ar']
                    ],
                    'price' => $size['price'],
                    'default' => $size['default']
                ]);
            }
        }

        if ($request->has_variation) {
            $menuItem->variations()->attach($request->variation_ids);
        }

        return Redirect::route('menu-items.index')
            ->with('message', 'MenuItem added successfully')
            ->with('class', 'bg-green-200');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MenuItem $menuItem)
    {
        $categories = Auth::user()->restaurant->categories()->select('id', 'name')->get();
        $variations = Auth::user()->restaurant->variations()->with('variationValues')->select('id', 'name')->get();

        $menuItem->load('category', 'variations', 'sizeVariations');

        if (count($menuItem->variations) > 0) {
            $variation_options = [];
            foreach ($menuItem->variations->groupBy('variation_id') as $variation_id => $value) {
                $variation_options[] = [
                    'id' => $variation_id,
                    'variation_id' => $variation_id,
                    'options' => $value[0]->variation->variationValues
                ];
            }
        } else {
            $variation_options = [
                [
                    'id' => 0,
                    'variation_id' => "",
                    'options' =>  [],
                ]
            ];
        }

        return Inertia::render('MenuItems/Edit', compact('menuItem', 'categories', 'variations', 'variation_options'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MenuItem $menuItem)
    {
        $request->validate([
            'name_en' => 'required',
            'name_ar' => 'required',
            'price' => 'required|numeric',
            'category_id' => 'required|exists:categories,id'
        ]);

        $path = $menuItem->image;
        if ($request->hasFile('image')) {
            File::delete($menuItem->image);
            $path = '/' . $request->file('image')->store('uploads', 'custom');
        }

        $menuItem->update([
            'name' => [
                'en' => $request->name_en,
                'ar' => $request->name_ar
            ],
            'description' => [
                'en' => $request->description_en,
                'ar' => $request->description_ar,
            ],
            'image' => $path ?? null,
            'category_id' => $request->category_id,
            'price' => $request->price,
            'discount' => $request->discount,
            'order' => $request->order,
        ]);

        if ($request->has_size_variation) {
            foreach ($request->size_variations as $size) {
                SizeVariation::updateOrCreate([
                    'menu_item_id' => $menuItem->id,
                    'id' => $size['id'] ?? null
                ], [
                    'name' => [
                        'en' => $size['name_en'],
                        'ar' => $size['name_ar']
                    ],
                    'price' => $size['price'],
                    'default' => $size['default']
                ]);
            }
        }

        if ($request->has_variation) {
            $menuItem->variations()->sync($request->variation_ids);
        }

        return Redirect::route('menu-items.index')
            ->with('message', 'MenuItem updated successfully')
            ->with('class', 'bg-green-200');
    }

    function delete_selected(Request $request)
    {
        MenuItem::destroy($request->ids);
    }

    function delete_variation($id)
    {
        SizeVariation::destroy($id);
    }

    function update_status($id)
    {
        MenuItem::find($id)->update(['active' => request()->status]);
    }
}
