<?php

use Inertia\Testing\AssertableInertia as Assert;

test('privacy policy page can be rendered', function () {
    $response = $this->get(route('privacy-policy'));

    $response->assertStatus(200);
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Public/PrivacyPolicy')
        ->where('legal.organization_name', 'Fortis Promotions Sp. z o.o.')
        ->where('legal.privacy_email', 'rodo@fortis.test'));
});

test('terms page can be rendered', function () {
    $response = $this->get(route('terms'));

    $response->assertStatus(200);
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Public/Terms')
        ->where('legal.organization_name', 'Fortis Promotions Sp. z o.o.')
        ->where('legal.complaints_email', 'reklamacje@fortis.test'));
});
