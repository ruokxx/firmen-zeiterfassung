<?php
$user = \App\Models\User::first();
auth()->login($user);

// Get existing or create new Material 
$m = \App\Models\Material::firstOrCreate(
['name' => 'Kabel 50m'],
['stock_count' => 10, 'low_stock_threshold' => 2]
);

// Reset stock to above threshold and clear old orders just in case
$m->stock_count = 10;
$m->save();
\App\Models\MaterialOrder::where('item_name', 'Kabel 50m')->delete();

echo "Stock Before: " . $m->stock_count . "\n";

// Emulate Controller Transaction logic
$oldStock = $m->stock_count;
$numQuantity = 9;

$m->stock_count -= $numQuantity;
$m->save();

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
        echo "Successfully triggered order creation!\n";
    }
}

// verify exist
$order = \App\Models\MaterialOrder::where('item_name', 'Kabel 50m')->first();
if ($order) {
    echo "Found Order: " . $order->item_name . " (is_ordered: " . $order->is_ordered . ")\n";
}
else {
    echo "Failed to find order.\n";
}
