<?php

namespace App\Notifications;

use App\Mail\OrganizationInvitationMail;
use App\Models\Invitation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class OrganisationInvitationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Invitation $invitation,
        public string $token,
    ) {}

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): OrganizationInvitationMail
    {
        return new OrganizationInvitationMail($this->invitation, $this->token);
    }

    /**
     * Get the array representation of the notification for database storage.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'invitation_id' => $this->invitation->id,
            'organization_name' => $this->invitation->organization->name,
            'organization_slug' => $this->invitation->organization->slug,
            'inviter_name' => $this->invitation->inviter?->name,
            'roles' => $this->invitation->roles,
            'token' => $this->token,
            'expires_at' => $this->invitation->expires_at?->toDateTimeString(),
        ];
    }
}
