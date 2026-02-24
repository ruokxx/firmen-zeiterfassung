<?php
try {
    $user = App\Models\User::first();
    echo(new App\Mail\MaterialReminderMail($user))->render();

}
catch (\Exception $e) {
    echo $e->getMessage() . PHP_EOL . $e->getTraceAsString();

}
