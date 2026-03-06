## Current objective
- Finalise and ship content editor persistence fixes so deleted list items (timeline/FAQ/menu) never reappear after save.

## Next steps
- [ ] Manual smoke test in Admin > Content: delete timeline/FAQ/menu items, save, reload, confirm deletions persist.
- [ ] Verify menu heading labels show `Course` vs `Course Options` based on per-course item count.
- [ ] Push latest bugfix/docs commit to remote.

## Auto-generated session snapshot
This section is updated by `bash scripts/handoff.sh`. Do not edit manually.

<!-- AUTO-GENERATED:START -->
### What changed today
- ad15fa0 Add cross-machine handoff workflow and tighten preview/menu UX
- 657be08 Polish content admin spacing, labels, and section layout
- 78769e5 Polish admin content layout, labels, and drag controls
- 2cc28bf Refine admin content UX, ordering, palette, and drag reordering
- 8caa7f9 Add staff customer-site support editing and customer site settings tools

### Current branch + last commit hash
- Branch: `main`
- Last commit: `ad15fa0` - Add cross-machine handoff workflow and tighten preview/menu UX

### Working tree status
- Status: `dirty`

### Changed files
-  M CODEX_CONTEXT.md
-  M HANDOFF.md
-  M README.md
-  M app/Http/Controllers/Admin/ContentController.php
-  M resources/js/components/public/DetailsSection.vue
- ?? public/images/wedding/uploads/details-image-20260306211019.jpg
- ?? public/images/wedding/uploads/hero-image-20260306205444.jpg
- ?? public/images/wedding/uploads/story-image-20260306210814.jpg
- ?? public/images/wedding/uploads/story-image-20260306210825.jpg
- ?? public/images/wedding/uploads/welcome-image-20260306205836.jpg
- ?? tests/Feature/ContentListReplacementTest.php

### Diffstat
` 5 files changed, 55 insertions(+), 9 deletions(-)`

### Recent commits (last 10)
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
