# 0002 Testing Strategy and Unified Quality Gate

## Status
accepted

## Context
Running full database and mobile builds inside a monorepo CI environment requires fast feedback and resilience against branch protection deadlocks.

## Decision
1. For backend tests, execute PHPUnit with in-memory SQLite (`:memory:`) to achieve rapid execution without spinning up an external database container.
2. For mobile applications, run `customer`, `deliveryboy`, and `seller` concurrently using GitHub Actions matrix runners with Flutter pub caching.
3. Use a unified `quality-gate` workflow that detects modified paths with `dorny/paths-filter` and orchestrates backend and mobile checks into a single required status check for pull request verification.

## Consequences
Test runs complete quickly and reliably. GitHub branch protection rules only need to enforce the single `quality-gate` check, preventing path-filtered PR deadlocks.
