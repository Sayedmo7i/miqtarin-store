# 0001 Monorepo Path-Filtered CI Workflows

## Status
accepted

## Context
mo7i-store is a monorepo containing a Laravel 10 backend and three Flutter mobile applications (Customer, Delivery Boy, Seller). Running all linters, analyzers, and test suites across all four sub-projects on every commit creates long feedback cycles and consumes excessive GitHub Actions runner minutes.

## Decision
We implement independent, path-filtered GitHub Actions workflows:
1. `backend-ci.yml` triggered exclusively on changes to `backend/**`.
2. `mobile-apps-ci.yml` triggered on changes to `mobile-apps/**` using a matrix across all three Flutter applications.
3. Automated cancellation of redundant runs (`concurrency: cancel-in-progress: true`).

## Consequences
Pull Requests that touch only mobile applications do not execute or wait on Laravel backend tests, and vice versa. Status checks on GitHub branch protection rules must account for conditional paths.
