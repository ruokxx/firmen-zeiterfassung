<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AccountApprovedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function envelope(): Envelope
    {
        $subject = \App\Models\Setting::where("key", "email_template_account_approved_subject")->value("value") ?: "Dein Account wurde freigeschaltet";
        $subject = str_replace(
            ["{name}", "{app_name}"], 
            [$this->user->name, config("app.name")], 
            $subject
        );

        return new Envelope(
            subject: $subject,
        );
    }

    public function content(): Content
    {
        $body = \App\Models\Setting::where("key", "email_template_account_approved_body")->value("value") ?: "Hallo {name},\n\nDein Account wurde erfolgreich freigeschaltet.\nDu kannst dich nun einloggen und deine Arbeitszeiten erfassen.\n\nZum Login: {login_url}\n\nMit freundlichen Gruessen,\n{app_name}";

        $body = str_replace(
            ["{name}", "{login_url}", "{app_name}"], 
            [$this->user->name, route("login"), config("app.name")], 
            $body
        );

        return new Content(
            view: "emails.account_approved",
            with: ["customBody" => $body],
        );
    }

    public function attachments(): array 
    {
        return [];
    }
}

// Force git update
