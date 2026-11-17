# Eventix

Eventix helps people discover events, buy tickets and enter with a digital
pass. Organizers can manage events, review confirmed ticket holders and check
attendees in at the venue.

## Features

- Search and browse upcoming events with ticket availability and pricing.
- Organizer dashboards for event management, sales and check-in.
- Attendee lists with buyer details, purchase status and a link to each pass.
- Printable digital tickets showing the event, venue, ticket type and order.
- QR passes contain a Laravel-encrypted ticket token. Organizers can scan the
  QR or enter the printed ticket code; each ticket can only be checked in once.
- Keep `APP_KEY` stable while issued passes are in use; rotating it invalidates
  their encrypted QR tokens.
- Demo seeding adds nine sample events and is safe to run again.

## Run locally on Windows

Requirements: PHP 8.3 or later, Composer, and PHP's `fileinfo`, `pdo_sqlite`
and `sqlite3` extensions enabled.

From the project directory:

```powershell
composer install
if (-not (Test-Path .\database\database.sqlite)) {
    New-Item -ItemType File -Path .\database\database.sqlite
}
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

Open [http://127.0.0.1:8000](http://127.0.0.1:8000). Leave the terminal running
while you use the site. The local `.env` should use SQLite, file sessions and
cache, and the synchronous queue.

Demo accounts all use the password `password`:

| Role | Email |
| --- | --- |
| Admin | `admin@eventix.test` |
| Organizer | `organizer@eventix.test` |
| Attendee | `user@eventix.test` |

## Tests

```powershell
php artisan test
```

For production, configure a persistent MySQL-compatible database and set
production environment variables securely. Do not deploy the demo accounts or
the default demo password.
