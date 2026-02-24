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
- Public site: `http://127.0.0.1:8000`
- RSVP: `http://127.0.0.1:8000/rsvp`
- Admin login: `http://127.0.0.1:8000/admin/login`

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
- `GET /`
- `GET /rsvp`
- `GET /rsvp/{code?}`
- `POST /rsvp/lookup`
- `POST /rsvp/{code}`

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
