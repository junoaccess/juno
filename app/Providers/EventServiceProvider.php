<?php

namespace App\Providers;

use App\Events\InvitationAccepted;
use App\Events\InvitationCreated;
use App\Events\OrganisationCreated;
use App\Events\RolesAssignedToUser;
use App\Events\UserJoinedOrganisation;
use App\Listeners\MarkInvitationAsAccepted;
use App\Listeners\OnboardNewOrganisation;
use App\Listeners\SendInvitationEmail;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        OrganisationCreated::class => [
            OnboardNewOrganisation::class,
        ],
        InvitationCreated::class => [
            SendInvitationEmail::class,
        ],
        InvitationAccepted::class => [
            MarkInvitationAsAccepted::class,
        ],
        UserJoinedOrganisation::class => [
            // Add listeners here as needed (e.g., SendWelcomeNotification)
        ],
        RolesAssignedToUser::class => [
            // Add listeners here as needed (e.g., LogRoleAssignment)
        ],
    ];
}
