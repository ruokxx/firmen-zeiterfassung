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
        return $this->subject('Täglicher Materialbericht - ' . $this->date)
            ->view('emails.materials.daily_report');
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
