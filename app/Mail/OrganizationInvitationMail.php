<?php

namespace App\Mail;

use App\Models\Invitation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrganizationInvitationMail extends Mailable implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    public function __construct(public Invitation $invitation, public string $token) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "You've been invited to join {$this->invitation->organization->name}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.invitation.organization-invitation',
            with: [
                'invitationName' => $this->invitation->name ?? $this->invitation->email,
                'organizationName' => $this->invitation->organization->name,
                'inviterName' => $this->invitation->inviter?->full_name ?? 'Someone',
                'roles' => $this->formatRoles(),
                'acceptUrl' => route('invitations.accept', ['token' => $this->token]),
                'expiresAt' => $this->invitation->expires_at?->format('F j, Y'),
            ],
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

    /**
     * Format roles for display.
     */
    protected function formatRoles(): string
    {
        if (empty($this->invitation->roles)) {
            return 'Member';
        }

        $roles = is_array($this->invitation->roles) ? $this->invitation->roles : [$this->invitation->roles];

        return implode(', ', array_map('ucfirst', $roles));
    }
}
