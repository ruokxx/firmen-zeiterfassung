<?php
$user = \App\Models\User::first();
auth()->login($user);

// Setup
$m = \App\Models\Material::where('name', 'Kabel 50m')->first();

// Drop it further below threshold
$oldStock = $m->stock_count;
$numQuantity = 1;

$m->stock_count -= $numQuantity;
$m->save();

// Evaluate
if ($oldStock > $m->low_stock_threshold && $m->stock_count <= $m->low_stock_threshold) {
    // Controller logic to trigger order
    $existingOrder = \App\Models\MaterialOrder::where('item_name', $m->name)
        ->where('is_ordered', false)
        ->exists();

    if (!$existingOrder) {
        \App\Models\MaterialOrder::create([
            'user_id' => auth()->id(),
            'item_name' => $m->name,
        ]);
        echo "Successfully triggered duplicate order creation!\n";
    }
}

// verify exist
$orders = \App\Models\MaterialOrder::where('item_name', 'Kabel 50m')->get();
echo "Total Active Orders: " . $orders->count() . "\n";
