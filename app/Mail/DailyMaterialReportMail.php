<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class DailyMaterialReportMail extends Mailable
{
    use Queueable, SerializesModels;

    public $transactions;
    public $date;

    /**
     * Create a new message instance.
     *
     * @param \Illuminate\Support\Collection $transactions
     * @param string $date
     */
    public function __construct(Collection $transactions, string $date)
    {
        $this->transactions = $transactions;
        $this->date = $date;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $subject = \App\Models\Setting::where('key', 'email_template_daily_material_report_subject')->value('value') ?: 'Täglicher Materialbericht - {date}';
        $subject = str_replace('{date}', $this->date, $subject);

        $body = \App\Models\Setting::where('key', 'email_template_daily_material_report_body')->value('value') ?: "Hallo,\n\nanbei die heutige Übersicht der Materialentnahmen vom {date}.";
        $body = str_replace('{date}', $this->date, $body);

        return $this->subject($subject)
            ->view('emails.materials.daily_report')
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
