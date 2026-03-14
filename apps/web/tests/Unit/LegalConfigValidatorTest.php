<?php

use App\Support\LegalConfigValidator;

it('allows placeholder legal config outside staging and production', function () {
    $validator = app(LegalConfigValidator::class);

    expect($validator)->toBeInstanceOf(LegalConfigValidator::class);

    expect(fn () => $validator->validate('local', [
        'copy_approved' => false,
        'support_email' => 'kontakt@fortis.test',
    ]))->not->toThrow(RuntimeException::class);
});

it('rejects unapproved legal config in production-like environments', function () {
    $validator = app(LegalConfigValidator::class);

    expect(fn () => $validator->validate('production', [
        'brand' => 'Fortis',
        'organization_name' => 'Fortis Promotions Sp. z o.o.',
        'support_email' => 'kontakt@fortis.test',
        'privacy_email' => 'rodo@fortis.test',
        'complaints_email' => 'reklamacje@fortis.test',
        'support_phone' => '+48 22 100 20 30',
        'support_hours' => 'Pn-Pt: 08:00-16:00',
        'privacy_last_updated' => '2026-03-14',
        'terms_version' => '1.1',
        'terms_effective_at' => '2026-03-14',
        'copy_approved' => false,
    ]))->toThrow(RuntimeException::class, 'LEGAL_COPY_APPROVED must be true');
});

it('rejects placeholder legal email domains in production-like environments', function () {
    $validator = app(LegalConfigValidator::class);

    expect(fn () => $validator->validate('staging', [
        'brand' => 'Fortis',
        'organization_name' => 'Fortis Promotions Sp. z o.o.',
        'support_email' => 'kontakt@fortis.test',
        'privacy_email' => 'rodo@fortis.test',
        'complaints_email' => 'reklamacje@fortis.test',
        'support_phone' => '+48 22 100 20 30',
        'support_hours' => 'Pn-Pt: 08:00-16:00',
        'privacy_last_updated' => '2026-03-14',
        'terms_version' => '1.1',
        'terms_effective_at' => '2026-03-14',
        'copy_approved' => true,
    ]))->toThrow(RuntimeException::class, 'placeholder legal emails configured');
});
