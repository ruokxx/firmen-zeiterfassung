<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MaterialOrderController extends Controller
{
    public function index()
    {
        // Fetch all orders, ordered by newest first
        $orders = \App\Models\MaterialOrder::with('user')->latest()->get();

        // Fetch Catalog Items
        $catalogItems = \App\Models\Material::orderBy('name')->get();

        // Group by Date (Y-m-d)
        $groupedOrders = $orders->groupBy(function ($order) {
            return $order->created_at->format('Y-m-d');
        });

        $activeGroups = collect();
        $archivedGroups = collect();

        foreach ($groupedOrders as $date => $group) {
            // Check if ALL items in this group are ordered
            $allOrdered = $group->every(function ($order) {
                return $order->is_ordered;
            });

            if ($allOrdered) {
                $archivedGroups->put($date, $group);
            }
            else {
                $activeGroups->put($date, $group);
            }
        }

        return view('material_orders.index', compact('activeGroups', 'archivedGroups', 'catalogItems'));
    }

    public function store(Request $request)
    {
        if (auth()->user()->role === 'azubi') {
            abort(403, 'Auszubildende können keine Bestellungen aufgeben.');
        }

        $request->validate([
            'item_name' => 'required|string|max:255',
        ]);

        \App\Models\MaterialOrder::create([
            'user_id' => auth()->id(),
            'item_name' => $request->item_name,
        ]);

        return back()->with('success', 'Bestellung wurde aufgegeben.');
    }

    public function toggle(\App\Models\MaterialOrder $order)
    {
        if (!auth()->user()->is_admin) {
            abort(403);
        }

        $order->is_ordered = !$order->is_ordered;
        $order->save();

        return back();
    }

    public function update(Request $request, \App\Models\MaterialOrder $order)
    {
        if (!auth()->user()->is_admin) {
            abort(403);
        }

        $request->validate([
            'admin_comment' => 'nullable|string|max:255',
        ]);

        $order->update($request->only('admin_comment'));

        return back()->with('success', 'Kommentar aktualisiert.');
    }

    public function destroy(\App\Models\MaterialOrder $order)
    {
        // Allow creator or admin to delete
        if (auth()->id() !== $order->user_id && !auth()->user()->is_admin) {
            abort(403);
        }

        $order->delete();

        return back()->with('success', 'Bestellung gelöscht.');
    }
}
