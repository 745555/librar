# Release & Rollback

## Prerequisites
- PHP 8.2+ (project supports 8.2; CI uses 8.3)
- Composer 2.x
- Node.js (for Vite build)
- Database configured in `.env`

## Local / Staging deploy checklist
- Pull latest code
- Install dependencies

```bash
composer install --no-interaction --prefer-dist
npm ci
```

- Configure environment
  - Copy `.env.example` to `.env` if needed
  - Ensure `APP_KEY` exists

```bash
php artisan key:generate
```

- Run migrations

```bash
php artisan migrate --force
```

- Build frontend assets

```bash
npm run build
```

- Run quick quality gates (recommended)

```bash
composer lint
composer static
php artisan test
```

## Rollback strategy

### Code rollback
- Revert to previous known-good commit/tag in your deployment system.

### Database rollback (when safe)
Laravel supports rolling back migrations, but rollback can be unsafe if:
- destructive migrations already ran in production,
- data is already relied upon by the running version.

For typical safe rollbacks:

```bash
php artisan migrate:rollback --step=1
```

If rollback is not safe, prefer:
- deploy previous code version that is compatible with current DB schema, or
- run a forward-fix migration.

## Notes
- Any migration affecting critical tables should include explicit rollback notes.
- Prefer small PRs + CI-green merges to keep release risk low.

