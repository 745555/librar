# Rules Compliance Implementation

## Scope
- Repository: `library_laravel`
- Standards applied: `engineering-global`, `workflow-feature-delivery`, `review-checklist`
- Outcome: full-repo compliance audit + actionable implementation backlog by priority.

## 1) Compliance Matrix (Todo: compliance-matrix)

| Rule area | Status | Evidence | Gap |
|---|---|---|---|
| Auth required for app routes | Partial | `routes/web.php` uses `Route::middleware(['auth'])` group | Sensitive routes are not capability-guarded at route level. |
| Authorization for sensitive actions | Missing (P0) | `app/Livewire/Admin/StaffPermissions.php` (`togglePermission`, `toggleRole`) | No explicit authorize/can checks before role/permission mutation. |
| Explicit error handling | Partial | Multiple `catch (\Exception $e)` in `app/Livewire/*` | Generic catch with no structured logging/`report($e)`. |
| Data integrity constraints | Partial | FK exists in `books.department_id` | `faculty_borrowings` has no FK to `books`; quantity invariants are app-only. |
| Migration rollback safety | Partial | Most migrations use `dropIfExists` | `2026_05_09_093127_create_permission_tables.php` uses `Schema::drop(...)`. |
| Testing pyramid coverage | Missing (P0) | Only `tests/Unit/ExampleTest.php`, `tests/Feature/ExampleTest.php` | No business-flow unit/integration regression tests. |
| CI quality gate | Missing (P0) | No `.github/workflows/*.yml` | No automated lint/test/build/security gate. |
| Maintainable architecture layering | Partial | Logic concentrated in Livewire components | Domain/app logic mixed with UI layer in `app/Livewire/*`. |
| Security hardening and least privilege | Partial | Auth exists globally | Permission boundary enforcement inconsistent. |
| Observability (logs/metrics/traces) | Partial | Default Laravel logging | No structured observability baseline for key flows/errors. |

## 2) P0 Hardening Backlog (Todo: p0-hardening)

### P0-A: Protect permission management from privilege escalation
- **Files**: `app/Livewire/Admin/StaffPermissions.php`, `routes/web.php`
- **Required changes**:
  - Enforce permission check in `mount`, `togglePermission`, and `toggleRole`.
  - Add route middleware for `/staff/permissions` using explicit capability (for defense in depth).
  - Ensure only authorized admins can enumerate users/roles/permissions.
- **Acceptance**:
  - Unauthorized authenticated user receives 403 on page access and mutation attempts.

### P0-B: Stop null dereference crashes in write/delete flows
- **Files**: `app/Livewire/Borrowings/Index.php`, `app/Livewire/Books/BookForm.php`, `app/Livewire/Projects/ProjectForm.php`, `app/Livewire/Staff/Index.php`
- **Required changes**:
  - Replace `find(...)->update/delete` with `findOrFail` or explicit null guard + safe failure path.
  - Keep user-facing message, but also record technical context (see P1 observability).
- **Acceptance**:
  - Missing record IDs never produce unhandled 500s.

### P0-C: Add CI enforcement and baseline regression suite
- **Files**: `.github/workflows/ci.yml`, `tests/Feature/*`, optional `tests/Unit/*`
- **Required changes**:
  - CI pipeline with install, `php artisan test`, `vendor/bin/pint --test`, and frontend build.
  - Add first wave of feature tests for auth + authorization + core CRUD behavior.
- **Acceptance**:
  - PR merge gate fails on test/lint/build regressions.

## 3) Regression Test Plan (Todo: tests-regression)

### High-priority tests (must exist before/with P0 fixes)
1. `tests/Feature/Auth/LoginFlowTest.php`
   - Valid credentials succeed.
   - Invalid credentials fail with expected feedback.
2. `tests/Feature/Auth/ProtectedRoutesTest.php`
   - Guest redirected from protected routes.
3. `tests/Feature/Admin/StaffPermissionsAuthorizationTest.php`
   - Non-privileged user cannot open permissions page.
   - Non-privileged user cannot toggle role/permission.
   - Privileged admin can perform mutations.
4. `tests/Feature/Borrowings/BorrowingCrudSafetyTest.php`
   - Create/update/delete works for authorized user.
   - Missing borrowing ID is handled without fatal error.
5. `tests/Feature/Books/BookFormValidationTest.php`
   - Quantity and available quantity validation invariants enforced.
   - Update on missing record handled safely.
6. `tests/Feature/Projects/ProjectFormValidationTest.php`
   - Required fields + missing-record safety.

### Execution gate
- Local: `php artisan test`
- CI: run tests on every push/PR.

## 4) P1/P2 Roadmap (Todo: p1-p2-roadmap)

### Phase P1 (after P0)
1. **Reliability + Error telemetry**
   - Replace generic catches with meaningful handling + `report($e)` context.
   - Standardize domain error messages versus internal logs.
2. **Data integrity enforcement**
   - Add constraints for inventory invariants (`available_quantity` bounds).
   - Plan migration toward relational borrowing model (introduce `book_id`, keep history compatibility).
3. **Quality tooling**
   - Add composer scripts for lint/check.
   - Add static analysis (Larastan/PHPStan baseline level and CI step).

### Phase P2
1. **Layering refactor**
   - Move business logic from `app/Livewire/*` into services/use-cases.
   - Keep Livewire components focused on orchestration/UI state.
2. **Observability maturity**
   - Add structured logs for critical flows (auth, permissions, borrow lifecycle).
   - Define error-rate and latency alerts for core flows.
3. **Release hygiene**
   - Add deploy/rollback notes and changelog discipline in repo docs.

## Priority Order for Delivery
1. P0-A (authorization boundary)
2. P0-B (runtime crash prevention)
3. P0-C (CI + baseline tests)
4. P1 reliability/integrity/tooling
5. P2 architecture/observability

## Definition of Done for This Implementation Package
- Compliance matrix completed with evidence paths.
- P0 backlog decomposed into executable items with acceptance criteria.
- Regression suite requirements defined per critical risk.
- P1/P2 roadmap documented with sequencing and outcomes.
