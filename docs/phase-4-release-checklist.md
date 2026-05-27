# Phase 4 Release Checklist

Use this checklist before pushing Phase 4 to production.

## 1) Build and Test

- Run `npm run build` and verify `public/build/manifest.json` exists.
- Run `php artisan test` and confirm all tests pass.
- Confirm no IDE diagnostics on changed files.

## 2) Route and SEO Safety

- Validate key routes return `200`:
  - `/`
  - `/about-us`
  - `/contact-us`
  - `/legal-attire`
  - `/shop-attire/graduation-attire`
  - `/shop-attire/legal-attire`
  - `/shop-attire/church-wear`
  - `/bulk-inquiry`
  - `/the-gown-journal`
  - `/privacy-policy`
  - `/terms-and-conditions`
  - `/return-policy`
  - `/copyright`
- Confirm `/sitemap.xml` loads and includes protected URLs.
- Confirm `public/robots.txt` contains `Sitemap: /sitemap.xml`.
- Spot-check page source contains:
  - canonical tag
  - OpenGraph tags
  - Twitter tags

## 3) Assistant and WhatsApp Widgets

- Verify assistant button appears bottom-left.
- Submit assistant form with valid payload and confirm success message.
- Verify honeypot/rate limit protections remain active.
- Verify WhatsApp button appears bottom-right and opens correct number.

## 4) Data and Database

- Apply migrations in target environment.
- Confirm `assistant_requests` table exists and stores submissions.
- Confirm admin email recipient is set (`ASSISTANT_ADMIN_EMAIL` or `MAIL_FROM_ADDRESS`).

## 5) Post-Deploy Smoke Checks

- Verify homepage loads without layout regressions on mobile and desktop.
- Verify dropdown navigation links resolve correctly.
- Verify `/up` health route still responds successfully.
- Verify no broken assets (CSS/JS/images) after deploy.
