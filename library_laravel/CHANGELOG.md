# Changelog

All notable changes to this project will be documented in this file.

The format is based on Keep a Changelog, and this project adheres to Semantic Versioning where applicable.

## Unreleased

### Added
- CI workflow running tests, formatting check, static analysis, and frontend build.
- PHPStan (Larastan) baseline configuration (`phpstan.neon`) and composer scripts (`lint`, `static`).
- Baseline regression tests for critical flows (auth protection, admin permission page authorization, CRUD safety).

### Changed
- Hardened authorization for staff permissions management (`system.manage` gate) and added route-level `can` middleware.
- Reduced 500-risk by using `findOrFail` for update/delete paths and adding null guards where appropriate.
- Extracted create/update persistence logic into `app/Actions/*` for better layering.

