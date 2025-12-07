<?php

use App\Models\Organization;
use Inertia\Testing\AssertableInertia as Assert;

it('shows the organization selection form on the root domain', function () {
    $response = $this->get('http://'.config('app.main_domain').'/login');

    $response->assertOk()->assertInertia(function (Assert $page) {
        $page->component('auth/organization-select')
            ->where('mainDomain', config('app.main_domain'));
    });
});

it('redirects to the subdomain login when the organization exists', function () {
    $organization = Organization::factory()->create(['slug' => 'acme']);

    $response = $this->post('http://'.config('app.main_domain').'/login', [
        'slug' => $organization->slug,
    ]);

    $response->assertRedirect('http://'.$organization->slug.'.'.config('app.main_domain').'/login');
});

it('returns validation errors when the organization slug is unknown', function () {
    $response = $this->from('http://'.config('app.main_domain').'/login')->post('http://'.config('app.main_domain').'/login', [
        'slug' => 'missing-org',
    ]);

    $response->assertSessionHasErrors([
        'slug' => 'We could not find that organization.',
    ]);
});
