<?php

namespace App\Notifications;

use App\Mail\OrganizationOwnerInvitation;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class OrganizationOwnerNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Organization $organization,
        public User $owner,
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
    public function toMail(object $notifiable): OrganizationOwnerInvitation
    {
        return new OrganizationOwnerInvitation($this->organization, $this->owner);
    }

    /**
     * Get the array representation of the notification for database storage.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'organization_id' => $this->organization->id,
            'organization_name' => $this->organization->name,
            'organization_slug' => $this->organization->slug,
            'owner_email' => $this->owner->email,
        ];
    }
}
