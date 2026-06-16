## Current objective
- Completed: configurable evening guest arrival time for invitation messaging.

## Evening guest arrival-time update (2026-06-16)
- What changed:
  - Couples can now set an optional `Evening guest arrival time` in Guest List > RSVP Email Settings.
  - The value is stored on the existing site setting JSON at `homepage_content.guest_list.evening_arrival_time`.
  - No database migration is required.
  - Existing weddings without this key continue to work because the config default is `null`.
- Backend areas touched:
  - Added `App\Support\InvitationTiming` for storage normalisation and display formatting (`19:30` -> `7:30 pm`).
  - Added validation in `SaveSiteSettingsRequest`.
  - Normalised the value in `Admin\ContentController`.
  - Passed evening-only arrival data through `Admin\PartyController` email sends, reminders, test sends, and previews.
  - Added evening arrival data to public RSVP lookup/save payloads in `RsvpController`.
- Vue/UI areas touched:
  - `AdminPage.vue` now shows the time field in RSVP Email Settings and includes it in email preview/test payloads.
  - `AdminPage.vue` includes the evening arrival sentence in copy-to-clipboard and WhatsApp/share invitation text for evening parties only.
  - `RsvpModal.vue` shows a structured `Evening arrival` block for evening guests only.
- Email areas touched:
  - `PartyRsvpInviteMail` and `PartyRsvpReminderMail` accept optional evening arrival text.
  - RSVP invite/reminder Blade templates display `Please arrive from 7:30 pm for the evening celebration.` only when present.
- Tests updated:
  - Save/load coverage for the new guest-list setting.
  - RSVP email preview coverage for evening vs all-day guests.
  - Sent RSVP email payload coverage for evening vs all-day parties.
  - Public RSVP lookup coverage for evening and all-day payloads.
  - Updated one stale customer dashboard tenant-context assertion to match the current dashboard output.
- Checks run:
  - `php artisan test --filter='evening|rsvp_email_preview|evening_guest_invite|evening_arrival'` passed.
  - `npm run build` passed.
  - `php artisan test` passed: 59 tests, 240 assertions.
- Manual testing steps:
  - Open `/app/admin/parties`.
  - Set `Evening guest arrival time` to a time such as `19:30`, then save RSVP Email Settings.
  - Preview/send a test RSVP email as `Evening Guest` and confirm the arrival section says `7:30 pm`.
  - Preview/send as `All Day Guest` and confirm no evening arrival wording appears.
  - Use Guest List copy/WhatsApp actions for an evening party and confirm the plain-text message includes the arrival sentence.
  - Lookup an evening RSVP code on the public site and confirm the RSVP modal shows `Evening arrival`; repeat with an all-day code and confirm it is hidden.
- Known issues / follow-up:
  - No frontend unit test harness exists for copy/WhatsApp helper text; coverage is via Vue build plus manual copy/WhatsApp testing.

## Next steps
- [ ] Manual browser smoke test of the RSVP Email Settings field and invitation copy actions.

## Cross-machine handoff (2026-03-09 16:35 GMT)
- Objective completed:
  - Added staff-managed global template help text editing and continued content/admin UX polish.
- Key implementation updates:
  - New staff tab/page: `/staff/template-management` for global info-icon help text editing.
  - Added global settings model/table for template-wide settings:
    - `PlatformSetting` model
    - migration: `2026_03_09_160000_create_platform_settings_table.php`
  - Customer content tooltips now read global platform help text with safe fallback when table is missing.
  - Public content merge now uses list-replacement logic (not recursive index merge) so deleted timeline/FAQ/menu items do not reappear.
  - Timeline/public refinements:
    - responsive timeline grid for 2–5 cards
    - safer v-for keys to avoid stale rendering after delete/reorder
    - admin section title changed to `Wedding Timeline`
  - Content editor UX:
    - info icon popovers open to the right of icon (mobile fallback below)
    - image focus labels renamed to `Image Horizontal Focus Point` / `Image Vertical Focus Point`
    - rich text toolbar now has Undo/Redo icon buttons at the end.
- Files likely needing extra review on next pickup:
  - `resources/js/pages/AdminPage.vue`
  - `resources/js/components/admin/RichTextEditor.vue`
  - `resources/js/components/public/TimelineSection.vue`
  - `app/Http/Controllers/PublicSiteController.php`
  - `app/Http/Controllers/Staff/TemplateManagementController.php`
  - `routes/web.php`
  - `config/wedding.php`
- Important local step (if not already run):
  - `php artisan migrate` (required for `platform_settings` table).

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
