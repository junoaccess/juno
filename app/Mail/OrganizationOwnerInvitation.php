<?php

namespace App\Mail;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;

class OrganizationOwnerInvitation extends Mailable implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public Organization $organization,
        public User $owner,
    ) {
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "You've been made owner of {$this->organization->name}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.organization.owner-invitation',
            with: [
                'organizationName' => $this->organization->name,
                'ownerName' => $this->owner->first_name,
                'ownerEmail' => $this->owner->email,
                'loginUrl' => $this->generateLoginUrl(),
            ],
        );
    }

    /**
     * Generate a secure login/set-password URL for the owner.
     */
    protected function generateLoginUrl(): string
    {
        // Generate a signed URL that expires in 7 days
        return URL::temporarySignedRoute(
            'password.reset',
            now()->addDays(7),
            ['email' => $this->owner->email]
        );
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
