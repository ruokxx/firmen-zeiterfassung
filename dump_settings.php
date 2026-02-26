<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$settings = \App\Models\Setting::where('key', 'like', '%email_template_%')->orWhere('key', 'like', '%monthly_report%')->get();
foreach ($settings as $setting) {
    echo $setting->key . " :\n" . $setting->value . "\n---\n";
}
