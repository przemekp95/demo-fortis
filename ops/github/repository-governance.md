# Repository Governance

Snapshot date: `2026-03-14`

## Current observed GitHub state

Collected via `gh api` for `przemekp95/demo-fortis`:

- branch protection on `main`: not configured
- repository rulesets: none
- environments present: `staging`, `production`
- environment protection rules: none on either environment
- default workflow permissions: `read`
- workflows can approve pull request reviews: `false`

## Required target state

### Required status checks

- `backend`
- `frontend-tests`
- `frontend-build`
- `container-security`
- `php-security`
- `security-laravel`
- `integration-prod-like`
- `sbom`
- `e2e-smoke`
- `Analyze (actions)`
- `Analyze (javascript-typescript)`

### Pull request policy

- require pull request before merge
- require at least 1 approving review
- dismiss stale approvals on new commits
- require linear history
- disable force pushes and branch deletion on `main`
- prefer squash merge with auto-merge enabled

### Code scanning and evidence

- upload Trivy SARIF from `container-security`
- upload Semgrep SARIF from `php-security`
- keep CodeQL enabled for `actions` and `javascript-typescript`
- upload CycloneDX SBOM artifacts for `apps/web` and `tests/e2e`

### Deployment environments

- add at least one required reviewer to `staging`
- add at least one required reviewer to `production`
- disable admin bypass unless there is an explicit incident policy allowing it
- document deployment branch policy once enforced in GitHub UI

## Secret and variable inventory

Environment secrets expected for `staging` and/or `production`:

- `MIKRUS_HOST`
- `MIKRUS_USER`
- `MIKRUS_SSH_KEY`
- `MIKRUS_KNOWN_HOSTS`
- `MIKRUS_ENV_FILE`

Environment variables expected for `staging` and/or `production`:

- `MIKRUS_PORT`
- `MIKRUS_DEPLOY_PATH`

Laravel runtime env inside `MIKRUS_ENV_FILE` must include reviewed legal/contact data and:

- `APP_KEY`
- `APP_URL`
- `DB_DATABASE`
- `DB_USERNAME`
- `DB_PASSWORD`
- `DB_ROOT_PASSWORD`
- `MAIL_MAILER`
- `MAIL_HOST`
- `MAIL_PORT`
- `MAIL_USERNAME`
- `MAIL_PASSWORD`
- `MAIL_ENCRYPTION`
- `MAIL_FROM_ADDRESS`
- `MAIL_FROM_NAME`
- `LEGAL_COPY_APPROVED=true`
- `LEGAL_ORGANIZATION_NAME`
- `LEGAL_SUPPORT_EMAIL`
- `LEGAL_PRIVACY_EMAIL`
- `LEGAL_COMPLAINTS_EMAIL`
- `LEGAL_SUPPORT_PHONE`
- `LEGAL_SUPPORT_HOURS`
- `LEGAL_PRIVACY_LAST_UPDATED`
- `LEGAL_TERMS_VERSION`
- `LEGAL_TERMS_EFFECTIVE_AT`

## Audit checklist

Before calling the repo production-ready, confirm in GitHub UI or via API that:

- `main` protection matches `ops/github/branch-protection.md`
- both deployment environments have reviewers
- required status checks include the current CI matrix
- code scanning receives Trivy and Semgrep SARIF uploads
- environment secrets are present and current
