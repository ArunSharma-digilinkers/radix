# Radix Website — Phase-wise Delivery Plan

Companion to `CLAUDE.md`. That file says *how* we work; this one says *what* we build and
*in what order*. Update the status column as phases land.

**Status legend:** ☐ not started · ◐ in progress · ☑ done

| Phase | Title | Status |
|---|---|---|
| 0 | Foundation & repo setup | ☑ |
| 1 | Design system & homepage shell | ☑ |
| 2 | Content schema & models | ☑ |
| 3 | Auth, RBAC & admin panel | ☐ |
| 4 | Public pages, CMS-driven | ☐ |
| 5 | Lead capture & conversion tools | ☐ |
| 6 | SEO, performance & accessibility | ☐ |
| 7 | Content population & launch | ☐ |
| 8 | Post-launch backlog | ☐ |

---

## Sequencing rationale

Two ordering decisions worth stating up front, because they look wrong otherwise:

**Schema (Phase 2) comes before the pages that use it (Phase 4), but after the homepage
shell (Phase 1).** The homepage is the client's review artifact — it needs to exist early.
So Phase 1 builds the *markup* against a hardcoded view-model class, and Phase 4 swaps the
data source without touching the templates. We build the markup once, not twice.

**Admin (Phase 3) precedes public pages (Phase 4)** so that Radix can begin entering real
content while we build the pages that render it. Given the brief §11 content-readiness risk,
starting data entry early is the schedule's biggest lever.

---

## Phase 0 — Foundation & repo setup

*The only phase committed directly to `main`.*

- [x] Git init, remote, `.gitignore`
- [x] `CLAUDE.md` — shared conventions
- [x] `docs/PROJECT_PLAN.md` — this document
- [x] Install deps: Livewire 4.3, `spatie/laravel-permission` 8.3, `spatie/laravel-translatable` 6.14
- [x] Self-host Archivo / IBM Plex Sans / IBM Plex Mono via `laravel-vite-plugin/fonts`
- [x] Tailwind v4 `@theme` tokens in `resources/css/app.css` (see CLAUDE.md §6)
- [x] Confirm MySQL connection and baseline `php artisan migrate` runs clean
- [x] Empty `DatabaseSeeder` (scaffold's test-user seed removed)
- [x] Minimal public layout + holding page + smoke tests; project README

**Exit:** a fresh clone runs `composer setup && composer dev` and serves a themed blank page. ✅

**Carried into Phase 1:** the permission tables migration was published and run here so the
schema baseline is complete in one pass; roles and permissions are actually wired in Phase 3.

---

## Phase 1 — Design system & homepage shell
`feat/design-system` · `feat/homepage`

Translate the approved *Editorial Red* concept into reusable Blade, mobile-first.

- Base public layout: sticky header, nav, footer, persistent Enquire/WhatsApp affordance
- Blade component library: button variants, eyebrow label, section wrapper, stat block,
  numbered index row, card, pull-quote, CTA band, media frame
- Homepage sections in order: hero (looping factory video, **no carousel**) → trust stats →
  Find Your Battery finder (UI only) → 01–08 product index → solar-as-one-system →
  Why Radix → infrastructure video + process flow → dealer map → export map →
  testimonials → blog teaser → red CTA band → footer
- Scroll-reveal utility honouring `prefers-reduced-motion`
- `/styleguide` route (non-production) rendering every component for review
- Data comes from a single `HomePageData` class of hardcoded arrays — one seam to replace

**Exit:** homepage faithful to the concept on desktop, coherent and fast on mobile,
reviewable by the client. No database involved. ✅

**Delivered beyond the original list:**

- `npm run check:contrast` — WCAG AA audit of every colour pair, wired to fail loudly.
  It found seven failures in the concept palette; see CLAUDE.md §6.1 for what changed.
- `npm run build:maps` — the concept's two maps fetched d3 + topojson + a world atlas from
  three CDNs at runtime inside iframes. They are now pre-rendered to inline SVG at build
  time: no runtime JS, no external requests, 49KB raw / 17KB gzipped.
- Concept media optimised from 13MB to 1.1MB and marked as placeholder pending §11 assets.

**Note:** the handoff is desktop-only and covers the homepage alone. Mobile layouts and the
About / product-detail concepts are pending from the design side; we derived them from tokens
in the meantime and will reconcile when they arrive.

**Known gaps carried forward:**

- The two factory videos are the concept's originals (2.3MB). No `ffmpeg` was available to
  re-encode to a web profile — folded into Phase 6.
- Links point at on-page anchors. Real routes arrive in Phase 4; buttons without a
  destination render as `<button>` rather than dead `<a href="#">`.

---

## Phase 2 — Content schema & models
`feat/content-schema`

Additive migrations, translatable JSON columns, no enums, no seeders.

| Domain | Tables |
|---|---|
| Products | `product_categories`, `products`, `product_variants`, `product_specs`, `product_faqs`, `product_documents` (datasheet PDFs) |
| Solar | Modelled as a product line with typed components (panel / battery / inverter / charge controller) |
| Editorial | `posts`, `post_categories`, `authors` |
| Network | `dealers` (city, state, PIN, lat/lng, type), `export_markets` |
| Company | `team_members`, `certifications`, `milestones`, `testimonials`, `infrastructure_media` |
| Careers | `job_openings`, `job_applications` |
| Leads | `enquiries`, `callback_requests` |
| System | `site_settings` (single source for the 25+/650+/10L+ figures), `media`, `redirects` |

Also: models with relationships, `$translatable` arrays, query scopes, factories for
**tests only** (never invoked as seed data), and model-level tests.

**Exit:** `php artisan migrate` on a clean DB produces the full schema; model tests pass. ✅

**Delivered:** 22 tables across 7 additive migrations, 21 models, 5 factories, 53 tests.

**Two shape decisions worth knowing:**

- A `product` is a *line* (Inverter Batteries), not an SKU. Individual models are
  `product_variants`. `product_components` exists only for the Solar Power Generating
  System, which the brief singles out as a bundled solution.
- No `authors` table. Posts point at the admin user who wrote them, with an
  `author_name` override for the house byline ("Team Radix") the concept uses. A second
  identity table would be two places to keep one name correct.
- No `infrastructure_media` table either — factory and QC photos attach through the
  polymorphic `media` table like every other image, so upload and alt-text handling is
  written once.

**Conventions are now enforced by tests,** not just documented: `SchemaConventionsTest`
fails the build if a migration declares an enum, drops or renames a column in `up()`, or
if `DatabaseSeeder` creates any record.

---

## Phase 3 — Auth, RBAC & admin panel
`feat/admin-auth` · `feat/admin-crud`

- Custom login (no Breeze/Jetstream), password reset, session security
- `spatie/laravel-permission` with the three roles from CLAUDE.md §5; idempotent
  `RolePermissionSeeder`
- Admin shell: sidebar nav, permission-gated menu, breadcrumb, flash messaging
- Livewire CRUD per domain: list (search/filter/sort/paginate), create/edit forms with
  validation, soft-delete + restore, drag-reorder where ordering matters
- Media manager: upload, alt text (accessibility requirement), automatic WebP conversion
  and responsive sizes
- Enquiry inbox for the `sales` role: status transitions, notes, CSV export
- Activity log on content mutations

**Exit:** a `content-editor` can create a product with specs, images, FAQs and a datasheet
end-to-end without touching code or the database.

---

## Phase 4 — Public pages, CMS-driven
`feat/pages-<name>` — one branch per page group

Swap Phase 1's hardcoded data for real queries, then build out the sitemap:

- **Home** — bind to live models
- **Products hub** — grid of 8 lines
- **Product detail ×8** — hero, spec table, variants, datasheet download, use cases, FAQs,
  prefilled "Enquire Now". Solar Power Generating System gets the bundled-solution treatment
  (each component explained, plus the system as a whole)
- **About** — single clean narrative (resolves the 150+/350+ contradiction), founder story,
  2001→2025 milestone timeline, leadership, certifications
- **Infrastructure** — factory/QC gallery, raw material → assembly → testing → dispatch flow,
  capacity figures, certificate images
- **Export** — interactive world map, per-region blurbs, logistics overview, partner form
- **Dealer locator** — *new page*; search by city/state/PIN, map + result list
- **Blog** — filterable card grid, detail page, related posts
- **Career** — openings list with application form, or "send your CV" fallback
- **Contact** — map embed, regional offices, WhatsApp, enquiry form with product dropdown

**Exit:** every route in the brief §4 sitemap resolves at a clean URL and renders live data.

---

## Phase 5 — Lead capture & conversion tools
`feat/lead-capture` · `feat/battery-finder` · `feat/dealer-search`

- Enquiry pipeline: validation, spam protection (reCAPTCHA), persistence, admin
  notification, **instant customer auto-acknowledgement**, queued mail
- WhatsApp Business click-to-chat with page-context prefill
- Callback request widget
- CRM/email routing hook (target system **TBC with client**)
- **Find Your Battery** finder — category → application → backup, mapped to real products
- Dealer locator search: geocoding strategy, nearest-first ordering
- Call tracking on the toll-free number

**Exit:** an enquiry from any page reaches the inbox and the customer's mailbox within seconds.

---

## Phase 6 — SEO, performance & accessibility
`feat/seo` · `feat/performance` · `feat/a11y`

- Unique, natural title/meta per page — editable in admin, **no keyword stuffing**
- schema.org JSON-LD: Organization, Product, LocalBusiness, BreadcrumbList, Article
- XML sitemap, `robots.txt`, canonicals, OG/Twitter cards
- **301 redirect map** from old query-string URLs (`?row=31`) to clean equivalents —
  driven by the `redirects` table so it is maintainable
- Performance to <2.5s / Core Web Vitals: responsive WebP, lazy loading, deferred video,
  critical CSS, cache headers, CDN (Cloudflare or similar)
- Accessibility: contrast ratios, alt text enforcement, keyboard navigation, focus states,
  landmarks, reduced-motion
- GA4 + Google Search Console

**Exit:** Lighthouse ≥90 across performance/SEO/accessibility on home, a product page, and a
blog post — verified on throttled mobile, not just desktop.

---

## Phase 7 — Content population & launch
`chore/launch-prep`

**Gated on the brief §11 client inputs** — see CLAUDE.md §8. This phase cannot complete
until Radix supplies current factory photos and video, the correct headcount, the live
product list with specs and warranty terms, lithium launch status, leadership bios and
photos, certifications, and packaging artwork.

- Staging environment for client review; real content entered via admin
- Cross-browser and real-device QA
- Forms, WhatsApp, and analytics verified in production config
- SSL, automated backups, uptime monitoring, error tracking
- 301 map verified against the live old site's URLs
- Go-live, then post-launch monitoring window

**Exit:** site live on the production domain, old URLs redirecting, no placeholder data.

---

## Phase 8 — Post-launch backlog

From brief §8, plus items deferred along the way:

- **Hindi locale activation** — schema is already translation-ready (Phase 2); this is
  translation + a locale switcher, not a migration
- Warranty registration / e-warranty card lookup
- Dealer & distributor portal with order tracking
- Live chat / chatbot
- Additional export-market languages

---

## Open questions for the client

1. **CRM target** — where should enquiries be routed? (Zoho, HubSpot, plain email?)
2. **Hosting** — who provisions production and the CDN, and what's the deploy pipeline?
3. **Dealer data** — in what format do the 650+ dealer records arrive, and do they include
   PIN codes or coordinates for the locator?
4. **Design coverage** — confirm mobile layouts plus About and product-detail concepts are
   coming from Claude Design, and roughly when.
5. **Map provider** — the handoff ships a self-contained SVG map; the locator likely needs
   Google Maps. Who supplies the API key and covers billing?
