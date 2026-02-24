<?php
try {
    $user = \App\Models\User::first();
    $mail = new \App\Mail\MaterialReminderMail($user);
    $mail->render();
}
catch (\Exception $e) {
    echo "ERROR MESSAGE: " . $e->getMessage() . "\n";
    echo "FILE: " . $e->getFile() . "\n";
    echo "LINE: " . $e->getLine() . "\n";

    // Let's print the top 5 levels of the trace
    $trace = $e->getTrace();
    for ($i = 0; $i < min(5, count($trace)); $i++) {
        $frame = $trace[$i];
        $file = $frame['file'] ?? 'N/A';
        $line = $frame['line'] ?? 'N/A';
        $func = $frame['function'] ?? 'N/A';
        echo "#$i $file:$line ($func)\n";
    }
}
