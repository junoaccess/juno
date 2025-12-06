<?php

namespace App\Notifications;

use App\Models\Invitation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrganisationInvitationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public Invitation $invitation;

    public string $token;

    public function __construct(Invitation $invitation, string $token)
    {
        $this->invitation = $invitation;
        $this->token = $token;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $acceptUrl = route('invitations.accept', ['token' => $this->token]);
        $organizationName = $this->invitation->organization->name;
        $inviterName = optional($this->invitation->inviter)->name ?? 'Someone';
        $recipientName = $this->invitation->name ?? $this->invitation->email;
        $roles = $this->formatRoles();
        $expiryDate = optional($this->invitation->expires_at)->format('F j, Y') ?? 'a future date';

        return (new MailMessage)
            ->subject("You've been invited to join {$organizationName}")
            ->greeting("Hello {$recipientName}!")
            ->line("{$inviterName} has invited you to join {$organizationName}.")
            ->line("You'll be assigned the following role(s): {$roles}")
            ->action('Accept Invitation', $acceptUrl)
            ->line("This invitation will expire on {$expiryDate}.")
            ->line('If you did not expect this invitation, no further action is required.');
    }

    /**
     * Get the array representation of the notification for database storage.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'invitation_id' => $this->invitation->id,
            'organization_name' => $this->invitation->organization->name,
            'organization_slug' => $this->invitation->organization->slug,
            'inviter_name' => optional($this->invitation->inviter)->name,
            'roles' => $this->invitation->roles,
            'token' => $this->token,
            'expires_at' => optional($this->invitation->expires_at)->toDateTimeString(),
        ];
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
