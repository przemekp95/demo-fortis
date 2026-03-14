<?php

return [
    'brand' => env('LEGAL_BRAND', 'Fortis'),
    'organization_name' => env('LEGAL_ORGANIZATION_NAME', 'Fortis Promotions Sp. z o.o.'),
    'support_email' => env('LEGAL_SUPPORT_EMAIL', 'kontakt@fortis.test'),
    'privacy_email' => env('LEGAL_PRIVACY_EMAIL', 'rodo@fortis.test'),
    'complaints_email' => env('LEGAL_COMPLAINTS_EMAIL', 'reklamacje@fortis.test'),
    'support_phone' => env('LEGAL_SUPPORT_PHONE', '+48 22 100 20 30'),
    'support_hours' => env('LEGAL_SUPPORT_HOURS', 'Pn-Pt: 08:00-16:00'),
    'privacy_last_updated' => env('LEGAL_PRIVACY_LAST_UPDATED', '2026-03-14'),
    'terms_version' => env('LEGAL_TERMS_VERSION', '1.1'),
    'terms_effective_at' => env('LEGAL_TERMS_EFFECTIVE_AT', '2026-03-14'),
];
