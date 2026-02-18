<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminMaterialController extends Controller
{
    public function index()
    {
        if (!auth()->user()->is_admin) {
            abort(403);
        }
        $materials = \App\Models\Material::orderBy('name')->get();
        return view('admin.materials.index', compact('materials'));
    }

    public function store(Request $request)
    {
        if (!auth()->user()->is_admin) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255|unique:materials,name',
        ]);

        \App\Models\Material::create($request->only('name'));

        return back()->with('success', 'Material hinzugefügt.');
    }

    public function destroy(\App\Models\Material $material)
    {
        if (!auth()->user()->is_admin) {
            abort(403);
        }

        $material->delete();

        return back()->with('success', 'Material gelöscht.');
    }
}
