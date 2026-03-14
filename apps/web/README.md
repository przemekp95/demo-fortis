# Fortis Web App

Laravel 10 + Inertia + Vue 3 application for a lottery demo.

## Runtime Requirements

- PHP `^8.3`
- Node.js `20 LTS`

## What Lives Here

- public landing page and legal pages
- participant dashboard, receipt submission, DSR and web-push opt-in
- admin dashboard, campaign management, draw review and winner exports
- public read-only API under `/api/v1`

## Quick Start

From the repo root the fastest path is:

```bash
make setup
```

Useful follow-up commands:

```bash
make qa
make test
make e2e
```

## Run With Docker

From the repo root:

```bash
cp apps/web/.env.example apps/web/.env
docker compose up -d --build

docker compose exec app composer install
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate --seed
```

App URLs:

- app: `http://localhost:8080`
- Vite: `http://localhost:5173`
- Mailpit: `http://localhost:8025`

Seeded users:

- admin: `admin@fortis.test` / `Password123!`
- participant: `participant@fortis.test` / `Password123!`

## Run Locally Without Docker

Quick verification can run on sqlite even though `.env.example` is wired for Docker services:

```bash
cp .env.example .env
composer install
npm ci
php artisan key:generate
touch database/database.sqlite
DB_CONNECTION=sqlite \
DB_DATABASE="$(pwd)/database/database.sqlite" \
CACHE_DRIVER=file \
QUEUE_CONNECTION=sync \
SESSION_DRIVER=file \
php artisan migrate --seed

DB_CONNECTION=sqlite \
DB_DATABASE="$(pwd)/database/database.sqlite" \
CACHE_DRIVER=file \
QUEUE_CONNECTION=sync \
SESSION_DRIVER=file \
php artisan serve
```

In a second terminal:

```bash
npm run dev
```

## Quality Gates

```bash
make qa
make test
```

If you want to run checks directly inside `apps/web`:

```bash
./vendor/bin/pint --test
./vendor/bin/phpstan analyse --memory-limit=1G
npm run check:api-types
npm run lint
npm run format:check
npm run test:coverage
php artisan test
```

Coverage scope is intentional:

- frontend coverage gate covers utility modules in `resources/js/utils`
- backend coverage gate covers critical public API and service paths configured in `phpunit.xml`
- generated public API types in `resources/js/types/public-api.d.ts` are regenerated from `../../docs/openapi/v1.yaml`

## E2E

```bash
make e2e
```

The auth form uses a honeypot and a minimum 1 second dwell time, so browser automation must not submit login immediately after page load.
