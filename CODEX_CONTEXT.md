# CODEX_CONTEXT

## Project purpose
- Multi-tenant wedding RSVP SaaS.
- Marketing pages and public wedding pages, with invitation-code RSVP.
- Customer/admin tooling for parties, guests, RSVPs, content, publishing, and billing.

## Tech stack
- Backend: Laravel 11 (PHP 8.2+)
- Frontend: Vue 3 + Vite
- Styling: Tailwind CSS
- Local database default: SQLite
- Billing: Stripe webhook + checkout/portal flows

## Key routes
- Public: `/`, `/pricing`, `/features`, `/demo`, `/rsvp/{code?}`, `/{public_slug}`. Legacy `/w/{public_slug}` links still resolve.
- Auth: `/register`, `/login`, `/verify-email`, `/forgot-password`
- Customer app: `/app` and `/app/admin/*`
- Admin area: `/admin/*`
- Staff area: `/staff/*`
- Billing: `/app/billing/*`, `/stripe/webhook`
- Local-only dev helpers:
  - `/dev/test-email`
  - `/dev/verify-email/{email}`

## Key models
- `Account`, `Site`, `SiteSetting`
- `PlatformSetting`
- `Party`, `Guest`, `Rsvp`
- `User`
- `StripeWebhookEvent`
- `StaffAuditLog`

## Tenancy, auth, billing notes
- Tenant resolution is enforced via middleware and scoped controllers for customer/public routes.
- Customer app routes are protected by `auth` and mostly `verified`.
- Staff and admin areas use separate middleware (`staff.auth`, `admin.auth`).
- Billing state controls site publishing/access and is updated by Stripe webhooks.
- Public slug preview rules:
  - Unpublished sites can be viewed only by same-account logged-in users or staff users.
  - Unpublished slug RSVP endpoints are limited to same-account users and staff users.
  - Published sites remain billing-gated for public guests.

## Local environment assumptions
- Typical WSL workspace path: `/home/<user>/code/wedding-rsvp`
- Windows Explorer access path example: `\\wsl$\\Ubuntu\\home\\<user>\\code\\wedding-rsvp`
- Default app URL: `http://127.0.0.1:8000`
- Vite dev server default: `http://127.0.0.1:5173`
- Optional Mailpit UI: `http://127.0.0.1:8025`
- Local mail defaults:
  - `MAIL_MAILER=log`
  - `QUEUE_CONNECTION=sync`

## Do not do
- Do not commit `.env` or secrets.
- Do not add production-only bypasses; local shortcuts must stay local-only.
- Keep changes minimal and scoped; avoid unrelated refactors.
- Use UK spelling in user-facing copy where practical.
- Avoid reusing generic/template placeholder content for product-facing text.

## Recent implementation notes
- Staff template management:
  - Global content-help text editor moved to dedicated staff tab: `/staff/template-management`.
  - Changes apply platform-wide to all customer content editors.
  - Uses `platform_settings` table key: `admin_field_help_texts`.
- Public content merge now uses list replacement semantics in `PublicSiteController` to prevent deleted list items (timeline/FAQ/menu) from reappearing.
- Public timeline cards use responsive auto-fit grid and stable list keys.
- Admin content UX updates:
  - timeline section label renamed to `Wedding Timeline`
  - image focus labels renamed to `Image Horizontal Focus Point` / `Image Vertical Focus Point`
  - info-tooltip popovers now open to the right of icons (mobile fallback below)
  - rich text toolbar includes Undo/Redo icon buttons.
- Preview banner is fixed at top in unpublished preview mode with role-specific messaging:
  - Staff: "Staff preview mode" + staff dashboard button.
  - Customer: "Preview mode" + account button + subscribe CTA to `/app`.
- Preview banner and controls are aligned to project palette and existing button system classes.
- Admin content menu mode defaults to `set_menu`; switching from guests-choose back to set menu is blocked when any course has multiple items.
- Content save/read merge behaviour now replaces list arrays (timeline, FAQs, menu courses/items) to prevent deleted items reappearing after save/reload.
- Public story section layout is image-left/text-right on desktop.
- Content save notification copy and duration were tuned to make preview-refresh guidance clearer for users.
