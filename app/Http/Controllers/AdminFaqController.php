<?php

namespace App\Http\Controllers;

use App\Models\Faq;
use Illuminate\Http\Request;

class AdminFaqController extends Controller
{
    public function index()
    {
        if (!auth()->user()->is_admin) {
            abort(403);
        }
        $faqs = Faq::orderBy('order')->get();
        return view('admin.faqs.index', compact('faqs'));
    }

    public function create()
    {
        if (!auth()->user()->is_admin) {
            abort(403);
        }
        return view('admin.faqs.create');
    }

    public function store(Request $request)
    {
        if (!auth()->user()->is_admin) {
            abort(403);
        }

        $validated = $request->validate([
            'question' => 'required|string|max:255',
            'answer' => 'required|string',
            'order' => 'required|integer',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        Faq::create($validated);

        return redirect()->route('admin.faqs.index')->with('success', 'FAQ erfolgreich erstellt.');
    }

    public function edit(Faq $faq)
    {
        if (!auth()->user()->is_admin) {
            abort(403);
        }
        return view('admin.faqs.edit', compact('faq'));
    }

    public function update(Request $request, Faq $faq)
    {
        if (!auth()->user()->is_admin) {
            abort(403);
        }

        $validated = $request->validate([
            'question' => 'required|string|max:255',
            'answer' => 'required|string',
            'order' => 'required|integer',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $faq->update($validated);

        return redirect()->route('admin.faqs.index')->with('success', 'FAQ erfolgreich aktualisiert.');
    }

    public function destroy(Faq $faq)
    {
        if (!auth()->user()->is_admin) {
            abort(403);
        }
        $faq->delete();
        return redirect()->route('admin.faqs.index')->with('success', 'FAQ gelöscht.');
    }
}
