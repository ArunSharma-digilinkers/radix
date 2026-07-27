# Radix Power Solutions — Website

Corporate and lead-generation website for **Radix Power Solutions Pvt. Ltd.**, a battery
manufacturer with a 650+ dealer network across India and a growing export business.
Replaces the existing site at radixbattery.com.

## Getting started

Requires PHP 8.3+, Composer, Node 20+, and MySQL.

```bash
git clone https://github.com/ArunSharma-digilinkers/radix.git
cd radix
composer setup     # installs deps, creates .env, generates key, migrates, builds assets
composer dev       # serve + queue worker + logs + vite
```

Then set your database credentials in `.env` (schema name: `radix`).

## Everyday commands

```bash
composer dev       # full dev environment on http://localhost:8000
composer test      # run the test suite (in-memory SQLite, never touches MySQL)
vendor/bin/pint    # format — run before every commit
npm run build      # production asset build
```

## Read this before your first commit

**[`CLAUDE.md`](CLAUDE.md)** is the working agreement for this repo — conventions,
non-negotiable rules, design tokens, and the git workflow. It applies to every
contributor, human or AI.

A few rules that bite if you miss them:

- **Migrations are additive only.** `migrate:fresh`, `migrate:refresh`, `migrate:reset`
  and `db:wipe` are banned in every environment, local included.
- **No seeders for content.** `DatabaseSeeder` stays empty; Radix enters real data
  through the admin panel.
- **No enums** — PHP or MySQL. Use string columns with model constants.
- **Never push to `main`.** Branch, push, open a PR.

**[`docs/PROJECT_PLAN.md`](docs/PROJECT_PLAN.md)** is the phase-wise delivery plan.
Check which phase your work belongs to before starting.

## Stack

Laravel 13 · MySQL · Blade + Tailwind v4 + Alpine · Livewire (custom admin, no Filament) ·
`spatie/laravel-permission` for RBAC · `spatie/laravel-translatable` for content
(English at launch, Hindi-ready schema).

## Design

The approved direction is **"Editorial Red"**, in
[`design_handoff/homepage-concept/`](design_handoff/homepage-concept/) alongside the client
brief. Open `index.html` in that folder to view both concepts — the Charcoal/Blue direction
is kept for reference only and must not be used as a styling source.

Tokens live in `resources/css/app.css`. Never hardcode a hex value in a template.
