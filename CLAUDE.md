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

> **Why this matters more than usual here:** the local MySQL instance is shared with
> roughly fifteen other project databases, including what appear to be live ones. A
> destructive command run against the wrong connection does damage well beyond this
> project. Tests are pinned to in-memory SQLite in `phpunit.xml` and can never reach
> MySQL — keep it that way.

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
| Framework | Laravel 13.22 (PHP 8.3+; local runs 8.4) |
| Database | MySQL 8.4, schema `radix` |
| Frontend | Blade + Tailwind CSS v4 + Alpine.js, built with Vite 8 |
| Interactivity / admin | Livewire 4.3 (custom-built admin — **no Filament, no Nova**) |
| Auth | Laravel's own; **no Breeze/Jetstream scaffolding** |
| RBAC | `spatie/laravel-permission` 8.3 |
| Translations | `spatie/laravel-translatable` 6.14 (JSON columns) |
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

**These rules are enforced by `SchemaConventionsTest`,** which fails the build on an enum
column, a `dropColumn`/`renameColumn` in a migration's `up()`, or a `DatabaseSeeder` that
creates records. Documentation erodes; a failing test does not.

### 4.1 Model conventions

- Content models use `Spatie\Translatable\HasTranslations` with a `public array $translatable`.
  Factories must pass the locale map (`['en' => 'Inverter Batteries']`), not a bare string.
- `App\Models\Concerns\Listable` gives `active()`, `ordered()` and `forDisplay()`. It needs
  **both** `is_active` and `sort_order` columns — a model with only the flag (dealers,
  redirects) uses `Activatable` instead, so `ordered()` never compiles against a column
  that isn't there.
- Images attach through the polymorphic `media` table via `HasMedia`, never a path column
  on the owning model.
- Anything a person submitted (enquiries, applications, callbacks) is soft-deleted only,
  and its request metadata is in `$hidden`.
- Public models are routed by `slug`, never by id.
- Factories live in `database/factories` and are **for tests only** — never call one from
  a seeder or a command.

**Database portability:** tests run on SQLite, production on MySQL. Raw SQL must work on
both. `Dealer::nearest()` documents a concrete instance — `LEAST()` and `HAVING` on a
select alias are MySQL-only, so it uses the haversine form and a `WHERE` instead.

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

**Tokens live in `resources/css/app.css`** under Tailwind v4 `@theme`, each with a comment
explaining its role. That file is the source of truth — the summary below is orientation,
not a second copy to keep in sync.

Never hardcode a hex value in a Blade template. If the colour you need isn't a token, add
it to `app.css` first so the palette stays reviewable in one place.

| Group | Utilities |
|---|---|
| Brand | `radix-red`, `radix-red-deep`, `radix-dark`, `radix-dark-2` |
| Text ramp | `ink` → `ink-soft` → `nav` → `lead` → `muted` → `meta` → `placeholder`, plus `on-dark` |
| Surfaces | `surface`, `surface-raised`, `surface-sunken` |
| Lines | `hairline`, `line`, `line-strong` |
| Layout | `max-w-radix` (1060px content column) |
| Radii | `rounded-btn` (10px), `rounded-card` (16px), `rounded-frame` (18px) |

The text ramp looks long, but each step is a distinct value the concept actually uses —
they were kept faithful rather than collapsed, so pixel-matching the design doesn't require
arbitrary hexes.

**Contrast is enforced, not assumed.** `npm run check:contrast` audits every
foreground/background pair the UI actually uses against WCAG AA and exits non-zero on a
failure. Run it after touching any colour token. Several of the concept's values failed and
were adjusted — see §6.1.

**Two Blade gotchas** that cost real debugging time here:

- Never name a `@foreach` variable `$component`. Blade reserves it inside a component's
  slot, and a nested `<x-…>` tag reassigns it mid-loop.
- Tailwind v4 puts the important modifier at the **end** (`py-12!`), not the start. The v3
  `!py-12` form silently compiles to nothing. Prefer a component prop over `!` either way.

Tailwind only sees literal class strings, so `bg-{{ $token }}` never compiles. For genuinely
dynamic colour, set the CSS variable inline: `style="background: var(--color-{{ $token }})"`.

### 6.1 Deviations from the approved concept

Three changes were made to meet the accessibility requirement in brief §7. All are small,
and all are reversible if the client prefers exact fidelity — but the design would then ship
knowingly failing AA.

| Concept | Changed to | Why |
|---|---|---|
| Caption grey `#8a97a8`, placeholder `#9aa6b5` | `#63707f` / `#667487` | 2.97:1 and 2.47:1 on white — well under the 4.5:1 needed for small text |
| Red eyebrows on dark navy `#e0231c` | `#ff5a4d` on dark, `#c41d17` on light | Brand red is 3.33:1 on `radix-dark`; both replacements clear 4.5:1 |
| Button border `#d3d9e2` | `line-control` `#8090a1` | 1.42:1 — a control's visible boundary needs 3:1 (WCAG 1.4.11) |

Side effect worth knowing: the compliant greys sit close together, so the hierarchy below
`muted` is now carried by size and weight rather than lightness. There is no lighter grey
that stays legible on white.

**Type:** Archivo (600/700/800/900) for headings — `font-display`, tracking `tracking-display`.
IBM Plex Sans (400/500/600) for body — `font-sans`. IBM Plex Mono (500/600) for eyebrows and
labels — `font-mono`, uppercase, `tracking-eyebrow`, 10–11px.

Fonts are **self-hosted**. They are declared in `vite.config.js` via
`laravel-vite-plugin/fonts` (Bunny provider), downloaded at build time, and emitted by
`{{ Vite::fonts() }}` in the layout head. **Never** add a `fonts.googleapis.com` or
`fonts.bunny.net` `<link>` — a render-blocking cross-origin request works against the
<2.5s target, and `HomePageTest` fails the build if one appears.

Only above-the-fold weights are preloaded (Archivo 800, IBM Plex Sans 400). Adding more
preloads competes with the hero video for mobile bandwidth — change deliberately, not casually.

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
npm run build       # production assets; also re-downloads fonts if vite.config.js changed
npm run check:contrast  # WCAG AA audit of the colour tokens — run after touching colours
npm run build:maps      # regenerate the homepage SVG maps (output is committed)
```

`/styleguide` renders every component live. It is registered outside production only.

## 10. Where things live

```
app/Models/                 Eloquent models
app/Models/Concerns/        Shared model traits (Listable, Activatable, HasMedia)
app/Livewire/               Livewire components (public + Admin/ subfolder)
app/Http/Controllers/       Thin controllers for public pages
app/Support/Content/        Phase 1 content scaffold — the seam Phase 4 replaces
resources/views/components/layouts/  Base layouts (<x-layouts.public>)
resources/views/components/ui/       Design system components
resources/views/components/site/     Header, footer, quick actions
resources/views/components/map/      GENERATED — see npm run build:maps
scripts/                    Build-time tooling (maps, contrast audit)
resources/views/pages/      Public page templates
resources/css/app.css       Tailwind v4 theme tokens
design_handoff/             Client brief + approved design concept (reference, not built code)
docs/PROJECT_PLAN.md        Phase-wise delivery plan — check before starting work
```
