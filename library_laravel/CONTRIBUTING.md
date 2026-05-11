# Contributing

## Local setup

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
npm install
npm run build
```

## Quality gates (run before opening a PR)

```bash
php artisan test
composer lint
composer static
```

## Guidelines
- Keep PRs small and focused.
- Add/adjust tests for any bug fix or behavior change.
- For sensitive actions (roles/permissions), enforce authorization at both route and component levels.

