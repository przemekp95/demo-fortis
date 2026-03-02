# Branch Protection

Apply these settings on `main`:

- Require pull request before merging
- Require at least 1 approving review
- Dismiss stale approvals when new commits are pushed
- Require status checks to pass before merging:
  - `backend`
  - `frontend-build`
  - `e2e-smoke`
- Require linear history
- Restrict force pushes and deletion
- Enable auto-merge with squash strategy
