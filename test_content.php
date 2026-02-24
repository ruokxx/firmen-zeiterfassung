<?php
$user = \App\Models\User::first();
$mail = new \App\Mail\MaterialReminderMail($user);
$content = $mail->content();
print_r([
    'view' => $content->view,
    'markdown' => $content->markdown,
    'html' => $content->html,
    'text' => $content->text,
]);
