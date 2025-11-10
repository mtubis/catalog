# Laravel Product Catalog (Photovoltaic)

A small, production‑minded Laravel backend for browsing, filtering and searching photovoltaic **batteries**, **solar panels** and **connectors**. Data source: three CSV files. No auth/editor. API‑first; minimal optional UI.

---

## Tech Stack

* **Laravel 12** (PHP 8.3+)
* **PostgreSQL** (full‑text search via `tsvector`)
* **Redis** (sessions, cache, queues) via **Laravel Sail**
* Optional: `resources/*` for a minimal demo page

> This repo is Dockerized with **Laravel Sail**. You only need **Docker** & **Docker Compose** installed locally.

---

## Quick Start (90 seconds)

```bash
# 1) Install PHP deps & generate env
composer install
cp .env.example .env

# 2) Configure Sail services (already committed in repo)
./vendor/bin/sail up -d
./vendor/bin/sail artisan key:generate

# 3) Set recommended env switches (cache/sessions on Redis)
sed -i "s/^CACHE_STORE=.*/CACHE_STORE=redis/" .env || true
sed -i "s/^SESSION_DRIVER=.*/SESSION_DRIVER=redis/" .env || true
printf "\nREDIS_HOST=redis\nDB_CONNECTION=pgsql\nDB_HOST=pgsql\nDB_PORT=5432\n" >> .env

# 4) Migrate & seed base data
./vendor/bin/sail artisan migrate --seed

# 5) Import CSVs (see Data section for file names)
./vendor/bin/sail artisan catalog:import --dir=storage/app/data

# 6) Hit the API
curl 'http://localhost/api/products?category=batteries&capacity_min=5&capacity_max=10'
```

> First run may take a moment as Docker pulls images.

---

## Configuration

Key `.env` values (already set by Quick Start):

```
APP_URL=http://localhost
DB_CONNECTION=pgsql
DB_HOST=pgsql
DB_PORT=5432
DB_DATABASE=laravel
DB_USERNAME=sail
DB_PASSWORD=password

CACHE_STORE=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
REDIS_HOST=redis
```

### API Routing (Laravel 11/12)

Ensure `bootstrap/app.php` includes:

```php
$app->withRouting(
    web: __DIR__.'/../routes/web.php',
    api: __DIR__.'/../routes/api.php',
    commands: __DIR__.'/../routes/console.php',
    health: '/up',
);
```

---

## Data

Place the three CSVs in `storage/app/data/` (or point `--dir` to your location). Expected file names & headers:

* `batteries.csv`: `id,name,manufacturer,price,capacity,description`
* `solar_panels.csv`: `id,name,manufacturer,price,power_output,description`
* `connectors.csv`: `id,name,manufacturer,price,connector_type,description`

Importer is **idempotent** using `(source_category, source_id)` and will upsert rows.

```bash
./vendor/bin/sail artisan catalog:import --dir=storage/app/data
```

---

## API

`GET /api/products` with filters (all optional):

* `q` — full‑text search across name, manufacturer, description (Postgres `websearch_to_tsquery`)
* `category` — `batteries | solar-panels | connectors`
* `manufacturer[]` — array of manufacturer names
* `price_min`, `price_max` — decimal
* Batteries: `capacity_min`, `capacity_max`
* Solar panels: `power_min`, `power_max`
* Connectors: `connector_type[]` — array of connector type strings
* Pagination: `page`, `per_page` (default 15, max 100)

Examples:

```
/api/products?category=batteries&capacity_min=5&capacity_max=10
/api/products?category=solar-panels&power_min=350&power_max=500
/api/products?q=mono+perc&price_min=100&price_max=400
/api/products?category=connectors&connector_type[]=MC4&connector_type[]=Type2
/api/products?manufacturer[]=BrandA&manufacturer[]=BrandB
```

Response shape (excerpt):

```json
{
  "data": [
    {
      "id": 1,
      "name": "Alpha Mono 400W",
      "manufacturer": "SunCorp",
      "price": 199.0,
      "description": "...",
      "category": { "slug": "solar-panels", "name": "Solar Panels" },
      "specs": { "capacity": null, "power_output": 400, "connector_type": null }
    }
  ],
  "links": { ... },
  "meta": { ... }
}
```

---

## Testing

Use a separate Postgres database and array drivers for cache/session. Example `.env.testing`:

```
APP_ENV=testing
APP_DEBUG=true
APP_URL=http://localhost
APP_KEY=base64:AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA=

LOG_CHANNEL=null
LOG_LEVEL=warning

DB_CONNECTION=pgsql
DB_HOST=pgsql
DB_PORT=5432
DB_DATABASE=catalog_testing
DB_USERNAME=sail
DB_PASSWORD=password

CACHE_STORE=array
SESSION_DRIVER=array
QUEUE_CONNECTION=sync
MAIL_MAILER=array
BROADCAST_DRIVER=log
FILESYSTEM_DISK=local
TELESCOPE_ENABLED=false
```

Create the DB once and run tests:

```bash
./vendor/bin/sail exec pgsql createdb -U sail catalog_testing || true
./vendor/bin/sail artisan test
```

### Factories note

Models use `HasFactory`. We also force factory name guessing in `AppServiceProvider`:

```php
use Illuminate\Database\Eloquent\Factories\Factory;
Factory::guessFactoryNamesUsing(fn ($model) => 'Database\\Factories\\'.class_basename($model).'Factory');
```

---

## Architecture Notes

* Normalized schema: `products` + per‑category specs (`battery_specs`, `solar_panel_specs`, `connector_specs`).
* Postgres FTS: `searchable tsvector` (stored) + GIN index; query via `websearch_to_tsquery`.
* Filters implemented as a **Pipeline** (composable, testable).
* CSV import is transactional & idempotent.

### MySQL switch (if needed)

* Remove the `tsvector` column & index.
* Add FULLTEXT on (`name`,`manufacturer`,`description`).
* Replace search WHERE with `MATCH(...) AGAINST (? IN BOOLEAN MODE)`.

---

## Common Pitfalls & Fixes

* **404 on /api/products** → ensure `bootstrap/app.php` includes `api: __DIR__.'/../routes/api.php'`.
* **sessions/cache table not found** → use Redis drivers (`SESSION_DRIVER=redis`, `CACHE_STORE=redis`) or create DB tables via `session:table` / `cache:table`.
* **Vite manifest not found** → run `npm run build` or remove `@vite` from the blade.
* **Unique slug errors in tests** → factories provide deterministic states (`batteries/solar/connector`) and random slugs elsewhere.

---

## Deployment (example: Render/Fly/Railway)

* Set env vars: `APP_KEY`, `APP_ENV=production`, `DB_*`, `REDIS_*`.
* Run on release: `php artisan migrate --force && php artisan catalog:import --dir=storage/app/data`.
* Ensure `storage/app/data` is present (mount or bundle sample CSVs) or point importer to your data source.

Sample Procfile:

```
web: vendor/bin/heroku-php-apache2 public/
release: php artisan migrate --force && php artisan catalog:import --dir=storage/app/data
```

---

## Project Structure

```
app/
  Domain/Catalog/
    Models/*
    Filters/*
  Http/
    Controllers/*
    Requests/*
  Console/Commands/ImportCatalog.php
routes/
  api.php, web.php
database/
  migrations/*, seeders/*, factories/*
resources/views/catalog.blade.php (optional demo UI)
```

---

## License

MIT.
