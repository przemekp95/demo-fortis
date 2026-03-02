<?php

use Inertia\Testing\AssertableInertia as Assert;

test('privacy policy page can be rendered', function () {
    $response = $this->get(route('privacy-policy'));

    $response->assertStatus(200);
    $response->assertInertia(fn (Assert $page) => $page->component('Public/PrivacyPolicy'));
});

test('terms page can be rendered', function () {
    $response = $this->get(route('terms'));

    $response->assertStatus(200);
    $response->assertInertia(fn (Assert $page) => $page->component('Public/Terms'));
});
