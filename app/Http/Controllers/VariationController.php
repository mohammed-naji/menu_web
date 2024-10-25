<?php

namespace App\Http\Controllers;

use App\Models\Variation;
use App\Models\VariationValue;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class VariationController extends Controller
{
    public function index()
    {
        $variations = Auth::user()->restaurant->variations()->latest()->paginate(10);
        return Inertia::render('Variations/Index', [
            'variations' => $variations,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Variations/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name_en' => 'required',
            'name_ar' => 'required'
        ]);

        $variation = Auth::user()->restaurant->variations()->create([
            'name' => [
                'en' => $request->name_en,
                'ar' => $request->name_ar
            ]
        ]);

        foreach ($request->variation_values as $value) {
            if (!empty($value['name_en']) && !empty($value['name_ar'])) {
                VariationValue::create([
                    'variation_id' => $variation->id,
                    'name' => [
                        'en' => $value['name_en'],
                        'ar' => $value['name_ar'],
                    ],
                    'price' => $value['price']
                ]);
            }
        }

        return Redirect::route('variations.index')
            ->with('message', 'Variation added successfully')
            ->with('class', 'bg-green-200');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $variation = Variation::with('variationValues')->findOrFail($id);
        return Inertia::render('Variations/Edit', [
            'variation' => $variation
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Variation $variation)
    {
        $request->validate([
            'name_en' => 'required',
            'name_ar' => 'required'
        ]);

        $variation->update([
            'name' => [
                'en' => $request->name_en,
                'ar' => $request->name_ar
            ]
        ]);

        foreach ($request->variation_values as $value) {
            if (!empty($value['name_en']) && !empty($value['name_ar'])) {

                VariationValue::updateOrCreate(
                    [
                        'id' => $value['id'] ?? null,
                    ],
                    [
                        'variation_id' => $variation->id,
                        'name' => [
                            'en' => $value['name_en'],
                            'ar' => $value['name_ar'],
                        ],
                        'price' => $value['price']
                    ]
                );
            }
        }

        return Redirect::route('variations.index')
            ->with('message', 'Variation updated successfully')
            ->with('class', 'bg-green-200');
    }

    function delete_selected(Request $request)
    {
        Variation::destroy($request->ids);
    }

    function delete_variation_values($variation, $id)
    {
        VariationValue::destroy($id);
        return redirect(route('variations.edit', $variation));
    }

    function variation_values($id)
    {
        $variation_values = VariationValue::where('variation_id', $id)->get();
        return $variation_values;
    }
}
