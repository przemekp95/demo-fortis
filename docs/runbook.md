# Ops Runbook

## Migrations
```bash
docker compose exec app php artisan migrate --force
```

## Seed demo data
```bash
docker compose exec app php artisan db:seed
```

## Run draws manually
```bash
docker compose exec app php artisan lottery:run-draws
```

## Process GDPR jobs
```bash
docker compose exec app php artisan lottery:run-retention
```

## Queue/Horizon
```bash
docker compose logs -f queue
```

## E2E smoke
```bash
cd tests/e2e
npm ci
npx playwright install chromium
E2E_BASE_URL=http://127.0.0.1:8000 npm test
```

## Performance baseline
```bash
# Public API throughput
k6 run tests/perf/public-api.js

# Participant receipt burst (requires authenticated participant cookie and CSRF token)
K6_SESSION_COOKIE="<laravel_session=...; XSRF-TOKEN=...>" \
K6_CSRF_TOKEN="<csrf-token>" \
k6 run tests/perf/receipt-submit-burst.js

# Admin dashboard load (requires authenticated admin cookie)
K6_ADMIN_SESSION_COOKIE="<laravel_session=...; XSRF-TOKEN=...>" \
k6 run tests/perf/admin-kpi-dashboard.js
```

## Rollback (Helm)
```bash
helm history lottery-staging -n lottery-staging
helm rollback lottery-staging <REVISION> -n lottery-staging
```
