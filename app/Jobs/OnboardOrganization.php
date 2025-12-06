<?php

namespace App\Jobs;

use App\Models\Organization;
use App\Services\OrganizationOnboardingService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class OnboardOrganization implements ShouldQueue
{
    use Queueable;

    public $tries = 3;

    public $backoff = 60;

    public function __construct(
        public Organization $organization,
        public ?array $ownerData = null,
    ) {
    }

    public function handle(OrganizationOnboardingService $service): void
    {
        $service->onboard($this->organization, $this->ownerData);
    }

    public function tags(): array
    {
        return ['onboarding', 'organization:'.$this->organization->id];
    }
}
