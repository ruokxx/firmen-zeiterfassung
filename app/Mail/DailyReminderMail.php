<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DailyReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;

    /**
     * Create a new message instance.
     */
    public function __construct($user)
    {
        $this->user = $user;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $subject = \App\Models\Setting::where('key', 'email_template_daily_reminder_subject')->value('value') ?: 'Erinnerung: Arbeitszeit noch nicht vollständig';
        $subject = str_replace('{name}', $this->user->name, $subject);

        $body = \App\Models\Setting::where('key', 'email_template_daily_reminder_body')->value('value') ?: "Hallo {name},\n\ndu hast für heute noch keine Arbeitszeit eingetragen oder deine 8h noch nicht voll.\nBitte denk daran, diese noch zu erfassen.\n\nMit freundlichen Grüßen,\nDein Team";
        $body = str_replace('{name}', $this->user->name, $body);

        return $this->subject($subject)
            ->view('emails.daily_reminder')
            ->with(['customBody' => $body]);
    }
}
