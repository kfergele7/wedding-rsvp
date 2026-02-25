# Wedding RSVP Website (Laravel 11 + Vue 3)

Elegant wedding website with:
- Public homepage (editable via CMS)
- Guest RSVP by invitation code (no account required)
- Admin CMS for content, households, guests, and RSVP management
- CSV import/export tools

## Stack
- Laravel 11
- Vue 3
- Vite
- Tailwind CSS
- SQLite by default (works with MySQL-compatible schema)

## Quick Start
1. Install PHP dependencies:
```bash
composer install
```

2. Install JS dependencies:
```bash
npm install
```

3. Configure environment:
```bash
cp .env.example .env
php artisan key:generate
```

4. Set admin password in `.env`:
```env
ADMIN_PASSWORD=your-secure-password
```

5. Run database migrations + seed demo data:
```bash
php artisan migrate --seed
```

6. Start app:
```bash
php artisan serve
npm run dev
```

7. Open:
- Marketing site: `http://127.0.0.1:8000`
- Demo wedding page: `http://127.0.0.1:8000/demo`
- RSVP: `http://127.0.0.1:8000/rsvp`
- Admin login: `http://127.0.0.1:8000/admin/login`

## Local Email Testing
Use local-safe mail capture for verification/password reset flows.

### Preferred: Mailpit (SMTP capture)
1. Start Mailpit (example):
```bash
mailpit
```
2. Ensure local `.env` uses:
```env
APP_URL=http://127.0.0.1:8000
QUEUE_CONNECTION=sync
MAIL_MAILER=smtp
MAIL_HOST=127.0.0.1
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"
```
3. Open Mailpit inbox:
- `http://127.0.0.1:8025`

### Fallback: Log Mailer
If Mailpit is not running, switch to:
```env
MAIL_MAILER=log
```
Emails will be written to:
- `storage/logs/laravel.log`

### Local test helper route (local only)
Authenticated users can trigger test emails:
- Verification: `GET /dev/test-email`
- Password reset: `GET /dev/test-email?type=reset`

This route is only registered when `APP_ENV=local`.

### Troubleshooting
- Clear config cache:
```bash
php artisan config:clear
```
- Confirm queue is synchronous locally:
```env
QUEUE_CONNECTION=sync
```
- Restart local servers after `.env` changes (`php artisan serve`, `npm run dev`).

## Guest RSVP Flow
- Guests use an invitation code (3-10 letters, case-insensitive).
- Supported formats:
  - `GET /rsvp/{code}`
  - `GET /rsvp?code={code}`
- Guests can return and update RSVP with the same code.

## Demo RSVP Codes (Seeded)
- `CARTER`
- `BROOKS`
- `ROSE`

## Admin Features
- Session-based login using `ADMIN_PASSWORD` from `.env`
- Dashboard stats:
  - Total households
  - Invited guests
  - Attending
  - Not attending
  - No response
- Household management:
  - Create/edit/delete party
  - RSVP code
  - Max guests
  - Notes
- Guest management inside each party
- RSVP management + manual updates
- Content management for homepage sections
- CSV import/export for households and RSVPs

## CSV Import Format
Upload in Admin > Households.

Required headers:
- `party_display_name`
- `max_guests`
- `first_name`
- `last_name`

Optional headers:
- `code`
- `notes`
- `is_child`

Example:
```csv
party_display_name,code,max_guests,notes,first_name,last_name,is_child
The Carter Family,CARTER,4,Family table near dance floor,James,Carter,no
The Carter Family,CARTER,4,Family table near dance floor,Elena,Carter,no
The Carter Family,CARTER,4,Family table near dance floor,Liam,Carter,yes
```

## CSV Exports
- Households export: `GET /admin/api/parties/export`
- RSVP export: `GET /admin/api/rsvps/export`

## Homepage Content Storage
Homepage content is stored in `site_settings` under key:
- `homepage_content`

Editable in Admin > Content:
- Couple names
- Date/location text
- Welcome letter
- Timeline items
- Our story
- Venue/travel details
- FAQ items
- RSVP CTA text

## Local Images / Replacement Instructions
Replace only local files in:
- `public/images/wedding/hero-couple.svg`
- `public/images/wedding/welcome-couple.svg`
- `public/images/wedding/story-couple.svg`
- `public/images/wedding/venue-map.svg`

You can replace with `.jpg/.png/.webp`; then update corresponding image paths in Admin > Content (or defaults in `config/wedding.php`).

## Important Routes
### Public
- `GET /` (marketing)
- `GET /demo` (demo wedding page)
- `GET /rsvp`
- `GET /rsvp/{code?}`
- `POST /rsvp/lookup`
- `POST /rsvp/{code}`
- `GET /pricing`
- `GET /features`
- `GET /w/{public_slug}`
- `POST /w/{public_slug}/rsvp/lookup`
- `POST /w/{public_slug}/rsvp/{code}`

### Admin
- `GET /admin/login`
- `POST /admin/login`
- `POST /admin/logout`
- `GET /admin`
- `GET /admin/parties`
- `GET /admin/rsvps`
- `GET /admin/content`

## Notes
- RSVP lookup and save endpoints are throttled.
- This project intentionally uses no guest sign-up/account flow.
