# Radix Power Solutions — Website

Corporate + lead-generation website for **Radix Power Solutions Pvt. Ltd.**, a battery
manufacturer (inverter, automotive, solar, e-rickshaw, bike, lithium) with a 650+ dealer
network and export business. Replaces the current site at radixbattery.com.

This file is the shared contract for everyone (human and AI) working in this repo.
Read it before your first commit. If you change a convention here, change it in the same
PR as the code that introduces it.

---

## 1. Hard rules — do not violate

These are project owner instructions, not preferences.

| Rule | Detail |
|---|---|
| **Additive migrations only** | Never write a migration that drops or destructively alters a column/table holding real data. Need a change? Add a new column/table and migrate the data forward. |
| **No destructive artisan commands** | `migrate:fresh`, `migrate:refresh`, `migrate:reset`, `db:wipe` are **banned** in every environment, including local. If your local DB is wrong, fix it forward. |
| **Never edit an applied migration** | Once a migration has run anywhere beyond your machine, it is immutable. Write a new one. |
| **No general seeders** | Do not seed demo/faker content. Radix supplies real data through the admin panel. `DatabaseSeeder` stays empty. The only permitted seeder-like code is an idempotent bootstrap for roles/permissions (see §5). |
| **No enums** | No PHP `enum` types and no MySQL `enum` columns. Use `string` columns plus `public const` values on the model and a `Rule::in()` validation rule. Rationale: enum columns need destructive ALTERs to extend, which conflicts with the additive rule. |
| **No `Co-Authored-By` trailer** | Commit messages must not include a co-author trailer of any kind. |
| **Never push to `main`** | See §2. |

## 2. Git workflow

- `main` is protected by convention. **Only the initial scaffold was committed directly.**
  Everything after that lands via pull request.
- Branch from the latest `main`:
  - `feat/<short-slug>` — new functionality
  - `fix/<short-slug>` — bug fixes
  - `chore/<short-slug>` — deps, config, tooling
  - `docs/<short-slug>` — documentation only
- Push the branch to `origin`, open a PR, get it reviewed, then merge. No direct commits
  to `main`, no force-pushing shared branches.
- Commit messages: imperative mood, present tense, scoped where useful.
  `feat(products): add product detail page` — not `added stuff`.
- Keep PRs scoped to one phase item where possible. A PR that touches migrations, admin,
  and public views at once is hard to review and hard to revert.

Repo: `https://github.com/ArunSharma-digilinkers/radix.git`

## 3. Stack

| Layer | Choice |
|---|---|
| Framework | Laravel 13 (PHP 8.3+; local runs 8.4) |
| Database | MySQL, schema `radix` |
| Frontend | Blade + Tailwind CSS v4 + Alpine.js, built with Vite 8 |
| Interactivity / admin | Livewire (custom-built admin — **no Filament, no Nova**) |
| Auth | Laravel's own; **no Breeze/Jetstream scaffolding** |
| RBAC | `spatie/laravel-permission` |
| Translations | `spatie/laravel-translatable` (JSON columns) |
| Formatting | Laravel Pint (`vendor/bin/pint`) before every commit |
| Tests | PHPUnit (`php artisan test`) |

Deliberate exclusions: no SPA framework, no Inertia, no headless CMS. The site is
server-rendered Blade for SEO and for the <2.5s load target in the brief.

## 4. Content architecture conventions

**Translatable from day one, English-only at launch.** Every user-facing content field
(titles, descriptions, body copy, meta tags) is a JSON column cast via
`spatie/laravel-translatable`'s `$translatable` array. We ship `en` now; `hi` is switched
on later without a schema change. Non-content fields (slugs, SKUs, numeric specs, flags)
are **not** translatable.

**Slugs are permanent.** Public URLs must be clean and stable — `/products/solar-batteries`,
never `?row=31`. The old site's query-string URLs get 301-redirected at launch (Phase 6).

**No enums, in practice:**

```php
class Enquiry extends Model
{
    public const STATUS_NEW      = 'new';
    public const STATUS_CONTACTED = 'contacted';
    public const STATUS_CLOSED   = 'closed';

    public const STATUSES = [self::STATUS_NEW, self::STATUS_CONTACTED, self::STATUS_CLOSED];
}
// migration: $table->string('status', 32)->default(Enquiry::STATUS_NEW)->index();
// validation: ['status' => ['required', Rule::in(Enquiry::STATUSES)]]
```

## 5. Roles & permissions

Roles are created by an **idempotent** `RolePermissionSeeder` (uses `firstOrCreate`, safe to
re-run, never truncates). This is the one exception to the no-seeders rule because roles are
structure, not content.

| Role | Scope |
|---|---|
| `super-admin` | Everything, including user management |
| `content-editor` | Products, blog, careers, pages, media — no user or lead access |
| `sales` | Enquiries and dealer records only |

Gate every admin route and Livewire action with permissions, not role name checks.

## 6. Design system

Source of truth: `design_handoff/` — the client brief PDF and the approved
**"Direction B — Light Editorial + Radix Red"** homepage concept. The rejected
Charcoal/Blue direction is kept for reference only; **do not** take styling from it.

Tokens are defined once in `resources/css/app.css` under Tailwind v4 `@theme`. Never
hardcode a hex value in a Blade template.

| Token | Value | Use |
|---|---|---|
| `--color-radix-red` | `#e0231c` | Accent: CTAs, eyebrows, numerals, rules |
| `--color-radix-dark` | `#12233b` | Dark sections, headings, footer |
| `--color-radix-dark-2` | `#1b2f4d` | Dark section insets |
| `--color-ink` | `#16202e` | Body text |
| `--color-muted` | `#5c6a7e` | Secondary text |
| `--color-muted-2` | `#8a97a8` | Meta / captions |
| `--color-surface` | `#f7f8fa` | Alternating section background |
| `--color-hairline` | `#eef1f5` | Borders, dividers |

**Type:** Archivo (600/700/800/900) for headings, tight tracking `-0.02em`.
IBM Plex Sans (400/500/600) for body. IBM Plex Mono (500/600) for eyebrows and labels —
uppercase, `0.1–0.16em` letter-spacing, 10–11px.
Fonts are **self-hosted**, not loaded from Google's CDN (render-blocking; hurts the load target).

**Layout:** content container `max-width: 1060px`. Desktop section padding `74px 56px`.
Radii: `10px` buttons, `16–18px` cards and media frames.

**Mobile-first is mandatory** — 60–70% of traffic is mobile per the brief. The handoff is
desktop-only; write the mobile layout first and enhance upward.

**Patterns from the brief that are non-negotiable:**
- No auto-rotating hero carousel (use the looping factory video)
- No stock photography — real factory/product/team photos only
- No walls of paragraph text — scannable sections with headings, icons, short stats
- Persistent "Enquire Now" + WhatsApp click-to-chat affordance
- Scroll reveal via `IntersectionObserver` on a `data-rev` attribute (respect
  `prefers-reduced-motion`)

## 7. Company facts

Use these numbers consistently sitewide; they are pulled from a single settings source, not
retyped into templates.

25+ years manufacturing · 650+ distributors · 10L+ customers served · 5+ export countries
(Nigeria, UAE, Afghanistan, Nepal) · ISO & BIS certified · tagline "Fit it & Forget it"

**Eight product lines:** Inverter · Automotive · Solar Batteries · Solar Power Generating
Systems · E-Rickshaw · Bike · E-Rickshaw Lithium · Inverter Lithium.

The **Solar Power Generating System** (panel + battery + inverter + charge controller sold
as one warrantied bundle) is absent from the current site and is a priority product line —
it gets its own page treatment, not a bullet on a list.

## 8. Unverified content — do not publish

Brief §11 flags that the live site's data is stale. These are **blocked pending client
input** and must not be invented, guessed, or copied from the old site into production:

- Employee headcount (the old site contradicts itself: 150+ vs 350+)
- Current factory photos, machinery, capacity figures, QC lab video
- Live product SKU list, specs, warranty terms, lithium line launch status
- Leadership names, bios, photos
- New certifications, awards, packaging artwork
- Named testimonials and case studies

Placeholder copy in the repo must be obviously fake and tracked. Never let a placeholder
number reach a production template.

## 9. Commands

```bash
composer setup      # first-time install (creates .env, key, migrate, npm build)
composer dev        # serve + queue + logs + vite, all at once
composer test       # config:clear then php artisan test
vendor/bin/pint     # format before committing
```

## 10. Where things live

```
app/Models/                 Eloquent models
app/Livewire/               Livewire components (public + Admin/ subfolder)
app/Http/Controllers/       Thin controllers for public pages
resources/views/layouts/    Base layouts (public, admin)
resources/views/components/ Blade components — the design system
resources/views/pages/      Public page templates
resources/css/app.css       Tailwind v4 theme tokens
design_handoff/             Client brief + approved design concept (reference, not built code)
docs/PROJECT_PLAN.md        Phase-wise delivery plan — check before starting work
```
