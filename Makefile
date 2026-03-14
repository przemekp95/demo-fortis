SHELL := /bin/bash

.PHONY: setup up down qa test e2e

setup:
	test -f apps/web/.env || cp apps/web/.env.example apps/web/.env
	docker compose up -d --build
	docker compose exec app composer install
	docker compose exec app php artisan key:generate
	docker compose exec app php artisan migrate --seed
	cd tests/e2e && npm ci

up:
	docker compose up -d --build

down:
	docker compose down

qa:
	cd apps/web && \
	composer install && \
	npm ci && \
	./vendor/bin/pint --test && \
	./vendor/bin/phpstan analyse --memory-limit=1G && \
	npm run check:api-types && \
	npm run lint && \
	npm run format:check

test:
	cd apps/web && \
	php artisan test && \
	npm run test:coverage

e2e:
	cd tests/e2e && \
	npm ci && \
	npx playwright install chromium && \
	E2E_BASE_URL="$${E2E_BASE_URL:-http://127.0.0.1:8000}" npm test
