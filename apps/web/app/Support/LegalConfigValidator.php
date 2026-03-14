<?php

namespace App\Support;

use RuntimeException;

class LegalConfigValidator
{
    /** @param array<string, mixed> $legal */
    public function validate(string $environment, array $legal): void
    {
        if (! in_array($environment, ['staging', 'production'], true)) {
            return;
        }

        $requiredKeys = [
            'brand',
            'organization_name',
            'support_email',
            'privacy_email',
            'complaints_email',
            'support_phone',
            'support_hours',
            'privacy_last_updated',
            'terms_version',
            'terms_effective_at',
        ];

        $missingKeys = collect($requiredKeys)
            ->filter(fn (string $key): bool => blank($legal[$key] ?? null))
            ->values()
            ->all();

        $placeholderEmails = collect([
            'support_email',
            'privacy_email',
            'complaints_email',
        ])->filter(function (string $key) use ($legal): bool {
            $email = strtolower((string) ($legal[$key] ?? ''));

            return $email === ''
                || str_ends_with($email, '.test')
                || str_ends_with($email, '.invalid')
                || str_contains($email, '@example.com')
                || str_contains($email, '@example.net')
                || str_contains($email, '@example.org');
        })->values()->all();

        $errors = [];

        if (! empty($missingKeys)) {
            $errors[] = 'missing keys: '.implode(', ', $missingKeys);
        }

        if (($legal['copy_approved'] ?? false) !== true) {
            $errors[] = 'LEGAL_COPY_APPROVED must be true';
        }

        if (! empty($placeholderEmails)) {
            $errors[] = 'placeholder legal emails configured: '.implode(', ', $placeholderEmails);
        }

        if ($errors !== []) {
            throw new RuntimeException(sprintf(
                'Legal/contact configuration is not production-ready for %s: %s.',
                $environment,
                implode('; ', $errors),
            ));
        }
    }
}
