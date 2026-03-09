## Current objective
- Finalise and ship content editor persistence fixes so deleted list items (timeline/FAQ/menu) never reappear after save.

## Next steps
- [ ] Manual smoke test in Admin > Content: delete timeline/FAQ/menu items, save, reload, confirm deletions persist.
- [ ] Verify menu heading labels show `Course` vs `Course Options` based on per-course item count.
- [ ] Push latest bugfix/docs commit to remote.

## Mac Studio pickup (2026-03-09 11:01:58 GMT)
- Current objective:
  - Re-align Mac workspace with latest repo state from Windows session and verify local email/dev verification flow is present.
- What was verified:
  - Branch is `main`; local working tree is clean.
  - `git fetch` / `git pull` from `origin/main` could not run in this environment due to DNS/network restriction (`Could not resolve host: github.com`).
  - `HANDOFF.md`, `CODEX_CONTEXT.md`, and `README.md` are present and reviewed.
  - Local email setup is implemented:
    - `.env.example` defaults to `MAIL_MAILER=log` and `QUEUE_CONNECTION=sync`.
    - README includes Local Email Testing docs for log mailer and Mailpit.
  - Local-only developer verification/test routes are implemented in `routes/web.php`:
    - `/dev/verify-email/{email}` (local only)
    - `/dev/test-email` (+ `?type=reset`) (local only, auth protected).
  - Dependency and test checks:
    - `composer install --dry-run` reports lockfile consistent.
    - `npm ci --dry-run` reports packages up to date.
    - `php artisan test` passes (30 tests).
- Recommended next steps:
  - Run `git pull --rebase origin main` on Mac terminal with internet access before new feature work.
  - Run `bash scripts/start.sh` to refresh session context and local startup guidance.
  - Continue with manual content-editor persistence smoke test listed above.

## Auto-generated session snapshot
This section is updated by `bash scripts/handoff.sh`. Do not edit manually.

<!-- AUTO-GENERATED:START -->
### What changed today
- 20aba3a Fix content list deletion persistence and document preview/menu rules
- ad15fa0 Add cross-machine handoff workflow and tighten preview/menu UX
- 657be08 Polish content admin spacing, labels, and section layout
- 78769e5 Polish admin content layout, labels, and drag controls
- 2cc28bf Refine admin content UX, ordering, palette, and drag reordering
- 8caa7f9 Add staff customer-site support editing and customer site settings tools

### Current branch + last commit hash
- Branch: `main`
- Last commit: `20aba3a` - Fix content list deletion persistence and document preview/menu rules

### Working tree status
- Status: `dirty`

### Changed files
-  M CODEX_CONTEXT.md
-  M README.md
-  M resources/js/components/public/StorySection.vue
-  M resources/js/pages/AdminPage.vue
- ?? public/images/wedding/uploads/details-image-20260306211019.jpg
- ?? public/images/wedding/uploads/hero-image-20260306205444.jpg
- ?? public/images/wedding/uploads/story-image-20260306210814.jpg
- ?? public/images/wedding/uploads/story-image-20260306210825.jpg
- ?? public/images/wedding/uploads/welcome-image-20260306205836.jpg

### Diffstat
` 4 files changed, 11 insertions(+), 7 deletions(-)`

### Recent commits (last 10)
- 20aba3a Fix content list deletion persistence and document preview/menu rules
- ad15fa0 Add cross-machine handoff workflow and tighten preview/menu UX
- 657be08 Polish content admin spacing, labels, and section layout
- 78769e5 Polish admin content layout, labels, and drag controls
- 2cc28bf Refine admin content UX, ordering, palette, and drag reordering
- 8caa7f9 Add staff customer-site support editing and customer site settings tools
- 36a8af3 Implement multi-tenant SaaS foundation, billing controls, customer account settings, and admin UX updates
- 9cd4459 Describe what you changed
- feb1e6b WIP: wedding RSVP progress

### How to run locally (commands + URLs)
```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan optimize:clear
php artisan serve
npm run dev
```
- App: `http://127.0.0.1:8000`
- Admin login: `http://127.0.0.1:8000/admin/login`
- Mailpit UI (optional): `http://127.0.0.1:8025`

### Known issues / blockers
- None detected automatically. Add manual blockers to `## Current objective` if needed.

### If you're picking this up on another machine
- `git pull --rebase`
- `composer install` (if `vendor/` is missing or lock file changed)
- `npm install` (if `node_modules/` is missing or lock file changed)
- `cp .env.example .env` (first time only; never commit `.env`)
- `php artisan migrate`
- `php artisan optimize:clear`
- Start servers: `php artisan serve` and `npm run dev`
<!-- AUTO-GENERATED:END -->
