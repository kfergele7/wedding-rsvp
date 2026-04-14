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

## Working across Mac + Windows
- Use Git as the source of truth for cross-machine continuity.
- End of session: commit, push, then run:
```bash
npm run handoff
```
- Start of session on the other machine:
```bash
git pull --rebase
bash scripts/start.sh
```
- Use Codex worktrees for parallel tasks and cleaner diffs.
- Windows WSL path example:
  - `\\wsl$\Ubuntu\home\<user>\code\wedding-rsvp`

## Local Email Testing
Use local-safe email handling for verification and password reset flows.

### Default local setup: log mailer (zero setup)
In local `.env`, use:
```env
QUEUE_CONNECTION=sync
MAIL_MAILER=log
```
With `MAIL_MAILER=log`, emails are written to:
- `storage/logs/laravel.log`

### Optional: Mailpit (SMTP capture)
If you want an inbox UI, run Mailpit and use:
```env
QUEUE_CONNECTION=sync
MAIL_MAILER=smtp
MAIL_HOST=127.0.0.1
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
```
Mailpit:
- SMTP: `127.0.0.1:1025`
- UI: `http://127.0.0.1:8025`

### After changing `.env`
Run:
```bash
php artisan optimize:clear
```
Keep local queue synchronous so verification emails send immediately:
```env
QUEUE_CONNECTION=sync
```

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

## Preview And Publishing Rules
- Public URL format is `GET /{public_slug}`.
- Legacy `GET /w/{public_slug}` links still resolve for backwards compatibility.
- If a site is unpublished:
  - Owner account users can preview the full site while logged in.
  - Staff users can preview any unpublished site while logged in.
  - Everyone else sees `Coming Soon`.
- Unpublished RSVP endpoints (`/{public_slug}/rsvp/*`) are blocked for non-auth users and non-owner/non-staff users.
- Published sites are still gated by billing status (`active`/`gifted` etc.) for public availability.

## Menu Mode Rules (Content Area)
- Default mode is `Set menu for all guests`.
- In `set_menu` mode:
  - Each course should have one menu item.
  - Additional per-course options cannot be added.
- In `Guests choose meal options` mode:
  - Multiple options per course can be added.
- If you try switching back to `set_menu` while any course has more than one item, the UI blocks the switch and shows a warning to remove extra items first.
- Public menu headings show `Starter/Main/Dessert` by default and only show `... Options` when a course has more than one item.

## Content Save Behaviour
- Content list sections (timeline items, FAQs, menu courses/items) are persisted with list replacement semantics.
- Deleting items in the content editor and clicking `Save Content` now keeps those deletions on reload (no fallback re-population of removed list entries).
- Save confirmation text is explicit: `Content Saved, make sure to refresh your preview page to see changes made` and remains visible longer for readability.
- Story section layout now renders image-left and story-right on desktop.

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

Marketing placeholders use local files in:
- `public/images/marketing/hero.jpg`
- `public/images/marketing/collage-1.jpg`
- `public/images/marketing/collage-2.jpg`
- `public/images/marketing/collage-3.jpg`
- `public/images/marketing/rsvp-flow.jpg`
- `public/images/marketing/template-1.jpg`
- `public/images/marketing/template-2.jpg`
- `public/images/marketing/template-3.jpg`
- `public/images/brand/logo-dark.svg`
- `public/images/brand/logo-white.svg`

Replace these with optimised `.jpg/.png/.webp` assets (same filenames) to keep Core Web Vitals healthy.

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
- `GET /how-it-works`
- `GET /faq`
- `GET /{public_slug}`
- `POST /{public_slug}/rsvp/lookup`
- `POST /{public_slug}/rsvp/{code}`
- Legacy: `GET /w/{public_slug}`
- Legacy: `POST /w/{public_slug}/rsvp/lookup`
- Legacy: `POST /w/{public_slug}/rsvp/{code}`

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
