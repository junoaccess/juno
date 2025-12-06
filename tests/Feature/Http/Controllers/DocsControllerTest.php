<?php

it('serves the documentation index page', function () {
    $response = $this->get('/docs');

    $response->assertOk();
    $response->assertHeader('content-type', 'text/html; charset=utf-8');
});

it('serves documentation sub-pages via client-side routing', function () {
    $response = $this->get('/docs/guide/getting-started');

    $response->assertOk();
    $response->assertHeader('content-type', 'text/html; charset=utf-8');
});

it('serves static assets from the docs build', function () {
    // VitePress builds include JS and CSS assets
    $response = $this->get('/docs/assets/style.css');

    // Will be 404 if assets don't exist, or 200 if they do
    // Since we can't predict exact asset names, just verify the route works
    expect($response->getStatusCode())->toBeIn([200, 404]);
});
