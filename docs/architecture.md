# Architecture Overview

## Stack
- Backend: Laravel 9, MySQL 8, Redis, Horizon
- Frontend: Inertia.js + Vue 3 + Tailwind
- PWA: Manifest + Service Worker offline shell
- Infra: Docker Compose (local), Helm/Kubernetes (staging/production)

## Domains
- Identity: users, profiles, consents
- Campaigns: campaigns, campaign_rules, prizes
- Lottery: receipts, entries, draw_schedules, draw_runs, winners
- Risk: fraud_signals, fraud_reviews
- Integrations: api_clients, api_tokens, webhook_endpoints, webhook_deliveries
- Compliance: dsr_requests, data_retention_jobs, audit_logs

## Processing
1. Participant submits receipt.
2. EntrySubmissionService validates campaign/rules and computes fraud score.
3. Entry is approved/flagged/rejected.
4. Scheduler runs draw jobs, creates winners, emits webhook events.
5. Admin exports winner CSV for external fulfillment.
