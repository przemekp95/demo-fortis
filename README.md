# Fortis Lottery Platform

Senior-level monorepo for a fictional e-commerce lottery platform built with Laravel 9, Vue 3, MySQL, Tailwind, PWA, and enterprise delivery practices.

## Monorepo Structure

- `apps/web` Laravel + Inertia + Vue app
- `infra/helm/lottery` Helm chart for Kubernetes
- `infra/k8s` local bootstrap manifests for Minikube namespaces
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

## Kubernetes (Minikube)

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
