<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class BranchController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $branches = Auth::user()->restaurant->branches()
            ->latest()
            ->paginate(10);

        return Inertia::render('Branches/Index', compact('branches'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Branches/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'address' => 'required',
            'phone' => 'required',
            'location' => 'required',
        ]);

        Auth::user()->restaurant->branches()->create([
            'address' => $request->address,
            'phone' => $request->phone,
            'location' => $request->location,
        ]);

        return Redirect::route('branches.index')
            ->with('message', 'Branch added successfully')
            ->with('class', 'bg-green-200');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Branch $branch)
    {
        return Inertia::render('Branches/Edit', compact('branch'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Branch $branch)
    {
        $request->validate([
            'address' => 'required',
            'phone' => 'required',
            'location' => 'required',
        ]);

        $branch->update([
            'address' => $request->address,
            'phone' => $request->phone,
            'location' => $request->location,
        ]);

        return Redirect::route('branches.index')
            ->with('message', 'Branch updated successfully')
            ->with('class', 'bg-green-200');
    }

    function delete_selected(Request $request)
    {
        Branch::destroy($request->ids);
    }
}
