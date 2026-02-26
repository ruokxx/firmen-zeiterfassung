<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewUserRegistered extends Mailable
{
    use Queueable, SerializesModels;

    public $user;

    public function __construct($user)
    {
        $this->user = $user;
    }

    public function envelope(): Envelope
    {
        $subject = \App\Models\Setting::where("key", "email_template_new_user_subject")->value("value") ?: "Neuer Benutzer registriert: {name}";
        $subject = str_replace("{name}", $this->user->name, $subject);

        return new Envelope(
            subject: $subject,
        );
    }

    public function content(): Content
    {
        $body = \App\Models\Setting::where("key", "email_template_new_user_body")->value("value") ?: "Hallo,\n\nEin neuer Benutzer hat sich gerade registriert.\n\nName: {name}\nE-Mail: {email}\n\nBitte pruefe die Daten und schalte den Account frei.\nZum Admin-Bereich: {admin_url}";
        
        $body = str_replace(
            ["{name}", "{email}", "{admin_url}"], 
            [$this->user->name, $this->user->email, route("admin.dashboard")], 
            $body
        );

        return new Content(
            view: "emails.admin.new-user-registered",
            with: ["customBody" => $body],
        );
    }

    public function attachments(): array 
    {
        return [];
    }
}
// Force git update
