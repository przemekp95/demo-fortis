# Security Policy

This repository is a demo application, but security issues in the codebase,
dependencies, container image, and deployment manifests are still handled
seriously. Fixes are published on a best-effort basis and are expected to land
on `main` first.

## Supported Versions

| Ref | Supported | Notes |
| --- | --- | --- |
| `main` | Yes | Receives security fixes and dependency maintenance. |
| Tagged snapshots and historical commits | No | Move to `main` or cherry-pick the relevant fix. |
| Feature branches and forks | No | Maintained by their owners only. |

## Reporting a Vulnerability

Do not open a public issue for an undisclosed vulnerability.

1. Use GitHub private vulnerability reporting for this repository if that option
   is enabled in the Security tab.
2. If private reporting is unavailable, contact the maintainer privately through
   GitHub and clearly mark the message with `SECURITY:`.
3. Include the affected area, reproduction steps or proof of concept, expected
   impact, affected commit SHA or image tag, and any mitigation you already
   tested.

## Response Targets

- Initial acknowledgement: within 3 business days
- Triage for reproducible reports: within 7 business days
- Fix publication: best effort, normally on `main`

## Scope

Included:

- application code in `apps/web`
- workflows in `.github/workflows`
- container build in `apps/web/Dockerfile`
- Helm and Kubernetes manifests in `infra/helm` and `infra/k8s`
- direct Composer and npm dependencies tracked in this repository

Excluded:

- local machine configuration outside this repository
- vulnerabilities introduced only in downstream forks
- infrastructure that is not managed from this repository
