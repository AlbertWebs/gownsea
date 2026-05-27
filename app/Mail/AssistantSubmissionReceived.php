<?php

namespace App\Mail;

use App\Models\AssistantRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AssistantSubmissionReceived extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public AssistantRequest $assistantRequest)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Gownsea Assistant Submission'
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.assistant-submission-received'
        );
    }
}
