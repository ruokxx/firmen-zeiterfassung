<?php
try {
    $mail = new \App\Mail\TestMail();
    $mail->render();
    echo "SUCCESS\n";
}
catch (\Exception $e) {
    echo "ERROR MESSAGE: " . $e->getMessage() . "\n";
    echo "FILE: " . $e->getFile() . "\n";
    echo "LINE: " . $e->getLine() . "\n";
}
