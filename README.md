# Imóveis API

Laravel REST API for real-estate property management: token authentication,
validated property creation and an automated test suite. Work in progress.

## Requirements
Docker. No local PHP or Composer needed: the first `composer install`
runs inside a container.


## Getting started
```bash
cp .env.example .env

# Install dependencies using a temporary container (no local PHP needed)
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php84-composer:latest \
    composer install --ignore-platform-reqs

./vendor/bin/sail up -d
./vendor/bin/sail artisan key:generate
./vendor/bin/sail artisan migrate --seed
```
The seeder creates the property types and a development user
(`admin@example.com`, password from `SEED_ADMIN_PASSWORD` in your `.env`;
the default in `.env.example` is `change-me-local-only`).

If ports 80 or 3306 are already in use on your machine, set `APP_PORT`
and `FORWARD_DB_PORT` in `.env`.
If you set `APP_PORT`, use that port in the URLs (for example `http://localhost:8080`).

## Running the tests
```bash
./vendor/bin/sail test
```

## Endpoints
All routes are under `/api`. Send `Accept: application/json`.

| Method | Route | Auth | Description |
|---|---|---|---|
| GET | /user | yes | Returns the authenticated user |
| POST | /login | no | Returns an access token |
| POST | /logout | yes | Revokes the current token |
| GET | /property-types | yes | Lists property types |
| POST | /properties | yes | Creates a property |

Authenticated routes need `Authorization: Bearer <token>`.

Example:
```bash
curl -X POST http://localhost/api/login \
  -H "Accept: application/json" -H "Content-Type: application/json" \
  -d '{"email":"admin@example.com","password":"change-me-local-only"}'
```

## Design decisions

**Strings + PHP Enums instead of MySQL `ENUM` columns.**
`status` and `source` are stored as plain strings and constrained by PHP
backed enums. Adding a value to a MySQL `ENUM` requires a schema migration
(`ALTER TABLE`), which can be slow on large tables. The trade-off: the database
no longer rejects invalid values, so validation (`Rule::enum`) is the guard.

**Lookup table for property types.**
Types live in `property_types` and `properties` references them by foreign key,
so the database rejects unknown types and a new type is just a new row.
Each type has a stable `slug` (used in code and imports) and a display `name`
(free to change). `GET /api/property-types` feeds client dropdowns.

**Stable stored values, translated labels.**
Stored values are lowercase English without accents (`available`, `sold`).
Human-readable labels come from the enum's `label()` method, so the data
stays stable even if the wording changes.

**Identity and origin come from the server, never the client.**
`created_by` is taken from the authenticated token and `source` is set by the
endpoint. Values sent by the client for these fields are ignored. The test
for this sends *different* values than the server would use (`source: api`,
another user's id); sending matching values would pass even without protection.

**Money is `decimal(12,2)`, never float.**
Floats store approximations. The model cast `decimal:2` returns a string with
two places, preserving the exact value.

**Validation is the first barrier, the database the last.**
Numeric limits in the request rules match the column types (for example, `max:255`
for `unsignedTinyInteger`), so bad input produces a clear `422` instead of a
database error. Boundary tests cover the last valid and first invalid value.

**API Resources define the response contract.**
Responses list their fields explicitly, so a column added to a table later
is not exposed by accident.

**Authentication with Sanctum.**
Only a hash of each token is stored, and logout deletes the token, which
revokes it immediately. Login returns the same generic message for an unknown
email and a wrong password, and is rate-limited.

**Unique `external_code` (groundwork for imports).**
Planned: the CSV import will use it to update existing properties instead of
duplicating them, so the same file can be re-imported safely.

## Testing

Feature tests run against in-memory SQLite and never touch the development
database. Key tests (forged fields, status, price and numeric limits) were
also verified by deliberately breaking the code they protect and confirming
the test fails.

## Known limitations

- Tests run on SQLite while development uses MySQL: column size limits
  (such as `unsignedTinyInteger`) are enforced only by MySQL, so request validation
  is what the tests protect.
- `state` is free text for now (no normalization).
- Tokens do not expire yet.

## Planned

- Property listing with filters and pagination
- Bulk CSV import running on a queue, with per-row error reporting
- Update and delete endpoints