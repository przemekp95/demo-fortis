# Fortis Lottery Platform

Senior-level monorepo for a fictional e-commerce lottery platform built with Laravel 9, Vue 3, MySQL, Tailwind, PWA, and enterprise delivery practices.

## Monorepo Structure

- `apps/web` Laravel + Inertia + Vue app
- `infra/helm/lottery` Helm chart for Kubernetes
- `infra/k8s` local bootstrap manifests for Minikube namespaces
- `infra/mikrus` production Docker Compose runtime for Mikrus deploys
- `ops/github` GitHub governance files (CODEOWNERS, templates)
- `tests/e2e` Playwright smoke tests
- `tests/perf` k6 performance scenarios
- `docs` architecture and operational docs

## Quick Start (Docker Compose)

```bash
cp apps/web/.env.example apps/web/.env
docker compose up -d --build

docker compose exec app composer install
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate --seed
```

App URLs:

- Web: `http://localhost:8080`
- Vite dev server: `http://localhost:5173`
- Mailpit: `http://localhost:8025`

Default seeded admin:

- Email: `admin@fortis.test`
- Password: `Password123!`

## Local Quality Gates

```bash
cd apps/web
composer install
npm install

./vendor/bin/pint --test
./vendor/bin/phpstan analyse --memory-limit=1G
composer audit --format=json --no-interaction > composer-audit.json || true
npm audit --omit=dev --audit-level=high
npm run lint
npm run format:check
npm run test:coverage # frontend utility coverage
php artisan test
```

Backend coverage enforcement in CI applies to the critical API and service paths listed in `apps/web/phpunit.xml`.

## E2E

```bash
cd tests/e2e
npm ci
npx playwright install chromium
E2E_BASE_URL=http://127.0.0.1:8000 npm test
```

Note: auth endpoints use a honeypot with a minimum 1 second dwell time.

## Performance (k6)

Available scenarios:

- `tests/perf/public-api.js`
- `tests/perf/receipt-submit-burst.js`
- `tests/perf/admin-kpi-dashboard.js`

## Mikrus Deploy

CD deploys to a Mikrus host over SSH using `infra/mikrus/docker-compose.yml`.

Required GitHub environment secrets for `staging` and/or `production`:

- `MIKRUS_HOST`
- `MIKRUS_USER`
- `MIKRUS_SSH_KEY`
- `MIKRUS_KNOWN_HOSTS`
- `MIKRUS_ENV_FILE`
- `GHCR_USERNAME`
- `GHCR_TOKEN`

Optional GitHub environment variables:

- `MIKRUS_PORT` default `22`
- `MIKRUS_DEPLOY_PATH` default `/home/<user>/apps/demo-fortis`

`MIKRUS_ENV_FILE` should contain the Laravel runtime env plus deployment values like:

```dotenv
APP_NAME="Fortis Lottery"
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:...
APP_URL=https://example.com
DB_DATABASE=fortis_lottery
DB_USERNAME=fortis
DB_PASSWORD=strong-password
DB_ROOT_PASSWORD=another-strong-password
APP_PORT=8080
MAIL_MAILER=smtp
MAIL_HOST=smtp.example.com
MAIL_PORT=587
MAIL_USERNAME=...
MAIL_PASSWORD=...
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@example.com
MAIL_FROM_NAME="Fortis Lottery"
```

Host prerequisites:

- Docker Engine with `docker compose`
- outbound access to `ghcr.io`
- SSH access for the configured deploy user

## Kubernetes (Optional / Local)

```bash
kubectl apply -f infra/k8s/namespaces.yaml
helm upgrade --install lottery-staging infra/helm/lottery -n lottery-staging -f infra/helm/lottery/values-staging.yaml
helm upgrade --install lottery-production infra/helm/lottery -n lottery-production -f infra/helm/lottery/values-production.yaml
```

## GitHub Flow

- Protect `main` branch
- Work on short-lived branches: `feature/*`, `fix/*`, `hotfix/*`
- Open PR for every change
- Merge with squash after required checks pass
