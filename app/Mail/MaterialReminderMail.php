<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MaterialReminderMail extends Mailable
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
     *
     * @return $this
     */
    public function build()
    {
        $subject = \App\Models\Setting::where('key', 'email_template_material_reminder_subject')->value('value') ?: 'Erinnerung: Materialbuchung';
        $subject = str_replace('{name}', $this->user->name, $subject);

        $body = \App\Models\Setting::where('key', 'email_template_material_reminder_body')->value('value') ?: "Hallo {name},\n\ndu hast heute Arbeitszeit erfasst, aber keine Materialentnahme am Lager ausgebucht.\nWenn du Material entnommen hast, hole die Buchung bitte noch nach.\n\nMit freundlichen Grüßen,\nDein Team";
        $body = str_replace('{name}', $this->user->name, $body);

        return $this->subject($subject)
            ->markdown('emails.materials.reminder')
            ->with(['customBody' => $body]);
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
