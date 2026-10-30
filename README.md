# Event Management Laravel

Authenticated event publishing and attendee registration with persisted attendance counts, organizer-controlled cancellation/completion, and capacity enforcement. No seeded users or fabricated events.

## Local setup (Windows)

```powershell
Copy-Item .env.example .env
php C:/Users/wondr/.config/herd-lite/bin/composer.phar install
npm install
php artisan key:generate
php artisan migrate --seed
npm run build
php artisan serve
```

Register to publish events or RSVP. Organizers may see the attendee list and change their event status; other users may only view published events. Duplicate registrations are prevented by a database unique constraint. Capacity checks execute inside a transaction with a row lock; use a database engine with row-level locking (e.g. PostgreSQL/MySQL) for reliable concurrent capacity enforcement. The local SQLite setup is for development/testing.

Events have title, description, venue, start/end timestamps, optional capacity, organizer and status. Published event search and listing use persisted data. No payment processing, waitlist, email notifications, calendar integration, or organizer role management is included.

## Verification

```powershell
php artisan migrate:fresh --seed --no-interaction
php artisan view:clear
php artisan view:cache
php artisan test --compact
npm run build
php C:/Users/wondr/.config/herd-lite/bin/composer.phar validate --no-check-publish
php C:/Users/wondr/.config/herd-lite/bin/composer.phar audit
npm audit --omit=dev
```

Keep `.env`, personal data, dependencies, SQLite databases, and build output out of Git.


## Screenshots

### Dashboard Overview
![Dashboard](docs/screenshots/01_dashboard.png)

### Login Overview
![Login](docs/screenshots/02_login.png)
