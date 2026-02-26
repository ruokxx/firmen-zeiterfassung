<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;

class MonthlyReportMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $monthName;
    public $year;
    protected $pdfContent;

    public function __construct($user, $monthName, $year, $pdfContent)
    {
        $this->user = $user;
        $this->monthName = $monthName;
        $this->year = $year;
        $this->pdfContent = $pdfContent;
    }

    public function envelope(): Envelope
    {
        $subject = \App\Models\Setting::where("key", "monthly_report_subject")->value("value") ?: "Monatsbericht {month} {year}";
        $subject = str_replace(
            ["{month}", "{year}", "{name}"], 
            [$this->monthName, $this->year, $this->user->name ?? "Mitarbeiter"], 
            $subject
        );

        return new Envelope(
            subject: $subject,
        );
    }

    public function content(): Content
    {
        $body = \App\Models\Setting::where("key", "monthly_report_body")->value("value") ?: "Hallo,\n\nanbei erhalten Sie den Monatsbericht von {name} fuer {month} {year}.\n\nMit freundlichen Gruessen,\n" . config("app.name");
        
        $body = str_replace(
            ["{month}", "{year}", "{name}"], 
            [$this->monthName, $this->year, $this->user->name ?? "Mitarbeiter"], 
            $body
        );

        return new Content(
            view: "emails.monthly_report",
            with: ["customBody" => $body],
        );
    }

    public function attachments(): array 
    {
        return [
            Attachment::fromData(fn () => $this->pdfContent, "Monatsbericht_{$this->monthName}_{$this->year}.pdf")
                ->withMime("application/pdf"),
        ];
    }
}
// Force git update
