# Public API v1

Base path: `/api/v1`
Auth: Bearer token from `api_tokens` (`api.client` middleware)
Contract snapshot: `docs/openapi/v1.yaml`
Frontend types: `apps/web/resources/js/types/public-api.d.ts`

Regenerate types after contract changes:

```bash
cd apps/web
npm run generate:api-types
```

## Endpoints
- `GET /campaign/current` returns `{ data: PublicCampaign }`
- `GET /campaign/current/prizes` returns `{ data: PublicPrize[] }`
- `GET /draws/history` returns paginated `{ data, links, meta }`
- `GET /winners` returns paginated `{ data, links, meta }` and only published winners
- `GET /stats/public` returns `{ data: PublicStats }`

## Webhook Events
- `entry.accepted`
- `entry.flagged`
- `draw.completed`
- `winner.published`

Envelope:
```json
{
  "event_id": "uuid",
  "event_type": "winner.published",
  "occurred_at": "2026-01-01T10:00:00Z",
  "campaign_id": 1,
  "payload": {}
}
```
Headers:
- `X-Webhook-Event`
- `X-Webhook-Event-Id`
- `X-Webhook-Signature` (HMAC SHA-256)
- `X-Webhook-Schema-Version` (`v1`)
