<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LowStockAlertMail extends Mailable
{
    use Queueable, SerializesModels;

    public $material;

    /**
     * Create a new message instance.
     */
    public function __construct($material)
    {
        $this->material = $material;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $subject = \App\Models\Setting::where('key', 'email_template_low_stock_subject')->value('value') ?: 'Lager-Warnung: {material_name} geht zur Neige';
        $subject = str_replace('{material_name}', $this->material->name, $subject);

        $body = \App\Models\Setting::where('key', 'email_template_low_stock_body')->value('value') ?: "Hallo,\n\ndas Material {material_name} hat die Warnschwelle erreicht oder unterschritten.\nAktueller Bestand: {stock} (Warnschwelle: {warning_threshold})\n\nBitte nachbestellen!";
        $body = str_replace(
        ['{material_name}', '{stock}', '{warning_threshold}'],
        [$this->material->name, $this->material->stock, $this->material->warning_threshold],
            $body
        );

        return $this->subject($subject)
            ->markdown('emails.materials.low_stock')
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
