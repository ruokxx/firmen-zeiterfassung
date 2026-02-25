<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OpenMaterialOrdersMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct($orders)
    {
        $this->orders = $orders;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $subject = \App\Models\Setting::where('key', 'email_template_open_material_orders_subject')->value('value') ?: 'Offene Materialbestellungen';

        $body = \App\Models\Setting::where('key', 'email_template_open_material_orders_body')->value('value') ?: "Hallo,\n\nes gibt aktuell unbestellte Materialbestellungen in der Warteschlange, die zur Prüfung bereitstehen.";

        return $this->subject($subject)
            ->markdown('emails.material_orders')
            ->with([
            'orders' => $this->orders,
            'customBody' => $body,
        ]);
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
