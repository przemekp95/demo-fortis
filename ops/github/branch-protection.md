# Branch Protection

Observed via `gh api` on 2026-03-14:

- `main` is not protected
- no repository rulesets are configured
- `staging` and `production` environments exist without protection rules
- default workflow permissions are `read`
- workflows cannot approve pull request reviews

Target settings for `main`:

- Require pull request before merging
- Require at least 1 approving review
- Dismiss stale approvals when new commits are pushed
- Require status checks to pass before merging:
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
- Require linear history
- Restrict force pushes and deletion
- Enable auto-merge with squash strategy
- Set workflow permissions to `read repository contents` and keep `security-events: write` available for SARIF uploads
