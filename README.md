# Apilo Order Importer

Tool for importing CSV orders, previewing them, validating stock via Apilo API, and creating orders in Apilo with automatic stock deduction. Frontend is Vue 3 (Inertia) with Bootstrap; backend is Laravel 12.

## Features
- Upload CSV/TXT  and preview rows in the UI.
- Validate order payload, check Apilo stock per SKU, warn on missing/low stock, and optionally continue with available items.
- Create orders in Apilo and decrease stock quantities after success.
- Fetch product details by SKU.

## Requirements
- PHP 8.2+, Composer
- Node 18+ and npm
- SQLite (default) or another DB if you change `.env`

## Setup
```bash
cp .env.example .env
composer install
php artisan key:generate
npm install
npm run build    # or npm run dev for HMR
```

Environment variables to set in `.env`:
- `APP_URL` – base URL for links.
- `APILO_BASE_URL` – Apilo API base (e.g. `https://example.apilo.com/`).
- `APILO_CLIENT_ID`, `APILO_CLIENT_SECRET`, `APILO_PLATFORM_ID` – credentials from Apilo.

## Getting Apilo tokens
Run the built-in command and paste the authorization code from Apilo:
```bash
php artisan apilo:tokens-create
```
Tokens are stored (by default) in `storage/app/apilo_tokens.json`. For production, secure this file or move storage to a safer location/secret manager.

## Running locally
```bash
php artisan serve
npm run dev      # in another terminal
```
UI: `/` (file upload + preview + sending to Apilo)  
Health: `/up`

## API
All routes are under `/api` (stateless middleware).

- `POST /api/send` – send order to Apilo. Multipart form-data:
  - `file` – CSV/TXT file (10 MB max).
  - `generalData` – JSON object with:
    - `client` (string, required)
    - `phone` (string, required)
    - `vat` (0–100)
    - `discount` (0–100)
    - `deliveryMethod` (`Eurohermes` or `RohligSuus`)
    - `taxNumber` (string, required)
  - `notes` – optional string
  - Optional flags (bool-ish): `ignore_missing_sku`, `confirmed_only`, `ignore_low_stock`

  Responses:
  - 200 success: `{ status: "success", message: "..." }`
  - 409 warning (missing/low stock): `{ status: "warning", message, data: { notFound | missingProducts | confirmedProducts } }`
  - 422 validation errors for malformed payload/JSON.

- `GET /api/product/{sku}` – fetch product details by SKU.

Frontend preview helper:
- `POST /preview` – multipart `excel_file` (CSV/TXT) returns parsed rows for UI preview.

## Frontend flow
- Upload file → preview modal (`/preview` endpoint).
- Fill general data (client, VAT, discount, delivery, NIP, phone) + optional notes.
- Submit → backend validates payload, checks stock, shows modals for missing/low stock, then sends order to Apilo and updates stock.

## Production hardening (recommended)
- Secure Apilo tokens storage (encryption/secret manager) and add a lock when refreshing tokens to avoid races.
- Add auth/rate limiting for the UI/API (currently public if deployed as-is).
- Add retries/backoff for Apilo API calls; consider batching stock updates.
- Add logging/monitoring for failed API calls and token refreshes.

## Testing
Sample tests are not included. Suggested coverage:
- Validation of `generalData` and file inputs.
- CSV mapping and client data extraction.
- Stock check decision branches (confirmed/pending/not found).
- `/api/send` happy path and warning paths.

## Troubleshooting
- `Target class [web] does not exist`: ensure `bootstrap/app.php` includes `->withMiddleware(fn ($m) => $m->web()->api());`.
- Token errors: regenerate with `php artisan apilo:tokens-create` and verify `APILO_*` env vars.
