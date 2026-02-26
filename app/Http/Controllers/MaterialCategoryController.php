<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MaterialCategoryController extends Controller
{
    private function authorizeManagement()
    {
        if (!(auth()->user()->is_admin || auth()->user()->is_chef || auth()->user()->is_materialwart)) {
            abort(403, 'Unauthorized action.');
        }
    }

    public function store(Request $request)
    {
        $this->authorizeManagement();

        $request->validate([
            'name' => 'required|string|max:255|unique:material_categories,name',
        ]);

        \App\Models\MaterialCategory::create($request->only('name'));

        return redirect()->back()->with('success', 'Kategorie erfolgreich angelegt.');
    }

    public function update(Request $request, \App\Models\MaterialCategory $category)
    {
        $this->authorizeManagement();

        $request->validate([
            'name' => 'required|string|max:255|unique:material_categories,name,' . $category->id,
        ]);

        $category->update($request->only('name'));

        return redirect()->back()->with('success', 'Kategorie erfolgreich aktualisiert.');
    }

    public function destroy(\App\Models\MaterialCategory $category)
    {
        $this->authorizeManagement();

        // When deleted, the category_id on materials will be set to NULL due to the nullOnDelete constraint.
        $category->delete();

        return redirect()->back()->with('success', 'Kategorie erfolgreich gelöscht.');
    }
}
