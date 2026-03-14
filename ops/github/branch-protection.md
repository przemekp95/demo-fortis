# Branch Protection

Apply these settings on `main`:

- Require pull request before merging
- Require at least 1 approving review
- Dismiss stale approvals when new commits are pushed
- Require status checks to pass before merging:
  - `backend`
  - `frontend-tests`
  - `frontend-build`
  - `container-security`
  - `php-security`
  - `e2e-smoke`
  - `Analyze (actions)`
  - `Analyze (javascript-typescript)`
- Require linear history
- Restrict force pushes and deletion
- Enable auto-merge with squash strategy
