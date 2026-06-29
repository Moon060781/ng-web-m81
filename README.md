# NoorGee WebMaster — Portfolio & Web Services Site

> **Live domain:** https://web.noorgee.pk
> **GitHub Pages mirror:** https://moon060781-bot.github.io/ng-web-m81
> **Author / Owner:** Nooruddin (Moon Din)
> **Stack:** Static HTML + Tailwind (CDN) + vanilla JavaScript + PHP (cPanel backend) + MySQL

A bilingual (English / Urdu) portfolio and lead-generation website for a freelance webmaster based in Pakistan. It showcases web projects and AI-generated media work, captures client messages into a MySQL inbox, and ships a private admin panel + a git deployment tool.

---

## Table of Contents

1. [Overview](#overview)
2. [Key Features](#key-features)
3. [Repository Structure](#repository-structure)
4. [Pages & User Flow](#pages--user-flow)
5. [Backend (PHP + MySQL)](#backend-php--mysql)
6. [Admin & Deployment Tools](#admin--deployment-tools)
7. [SEO & Analytics](#seo--analytics)
8. [Image / Media Assets](#image--media-assets)
9. [Local Development](#local-development)
10. [Deployment](#deployment)
11. [Known Issues & Notes](#known-issues--notes)
12. [License](#license)

---

## Overview

`ng-web-m81` is the source for the `web.noorgee.pk` subdomain — the "Web Designing & AI Generation" arm of the NoorGee network. The site doubles as:

- A **marketing portfolio** — animated hero, services, an interactive Web/AI project gallery, team section, and a contact form.
- A **lead-capture funnel** — form submissions are stored in a MySQL `messages` table and reviewed from a private admin panel.
- A **self-deployment tool** — an authenticated `deploy.php` lets the owner pull/push/revert git commits on the production server without SSH access.

The front-end is intentionally dependency-light: Tailwind, Font Awesome, GSAP, and Lenis are all loaded via CDN. The only server-side runtime requirement is PHP 8+ with the `pdo_mysql` extension (and optionally `mail()`).

---

## Key Features

- **Bilingual UI** — English + Urdu (Noto Nastaliq) with RTL/LTR switching on the FAQ page.
- **Interactive portfolio gallery** — tabbed Web / AI categories, list + preview layout, modal overlay, lazy-loaded images and `.mp4` videos, YouTube embed support.
- **GSAP / Lenis animations** — smooth scroll, sliding-door intro on `masterpieces.html`, fade-in transitions.
- **Service tooltip popups** — hover previews for each service line item.
- **Lead capture** — `send_message.php` validates and stores submissions with the referring site source.
- **Admin inbox** — `admin_panel.php` lists, filters, copies, forwards, and bulk-deletes messages.
- **Git deploy panel** — `deploy.php` performs pull / force-pull / push / revert / restore / undo-local against the `main-m81` branch.
- **SEO ready** — JSON-LD `LocalBusiness` + `FAQPage` schema, `sitemap.xml`, `robots.txt`, Bing site auth, Clarity analytics.
- **Hardened cPanel config** — `.htaccess` forces HTTPS, gzip, browser caching, blocks dotfiles, and 301-redirects `noorgee.pk/Web/*` to `web.noorgee.pk/*`.

---

## Repository Structure

```
ng-web-m81/
├── index.html              # Main marketing site (hero, services, portfolio, team, contact)
├── masterpieces.html       # Standalone sliding-door portfolio gallery (GSAP)
├── faq.html                # Bilingual FAQ with accordion + FAQPage schema
├── help.html               # "How to Work with Us" client onboarding guide
├── requirements.html       # Client requirements intake form
├── terms-and-conditions.html
├── send_message.php        # Contact-form handler → MySQL insert
├── admin_panel.php         # Private inbox for stored messages
├── deploy.php              # Private git deployment UI
├── .htaccess               # HTTPS, caching, redirects, dotfile protection
├── robots.txt
├── sitemap.xml
├── BingSiteAuth.xml
├── _config.yml             # GitHub Pages config
├── .nojekyll               # Disable Jekyll on GitHub Pages
├── .gitignore
├── img/                    # Project screenshots, GIFs, AI images, UGC videos
│   └── thumbnails/         # Compressed thumbnails for grid views
├── SKILL.md                # Skill manifesto (internal metadata)
├── DEPLOYMENT.md           # GitHub Pages deployment guide
├── MASTERPIECES_GUIDE.md   # Integration guide for the portfolio component
├── IMAGE_MAPPING.md        # Audit of which images exist / are missing
├── GITHUB_PAGES_SETUP.txt
├── error_log               # cPanel PHP error log (dev reference)
├── bk-wb-index.html        # Backup of a previous index.html revision
└── README.md               # This file
```

---

## Pages & User Flow

| Page | Purpose | Audience |
|---|---|---|
| `index.html` | Landing page — hero, services, portfolio gallery, team, contact form | Public |
| `masterpieces.html` | Standalone portfolio with sliding-door intro and AI pitch generator | Public |
| `faq.html` | Bilingual FAQ accordion | Public |
| `help.html` | Step-by-step client onboarding (inquiry → proposal → 75% deposit → build) | Public |
| `requirements.html` | Project requirements intake form | Prospects |
| `terms-and-conditions.html` | Legal terms + newsletter signup | Public |
| `send_message.php` | POST endpoint that stores form submissions | Forms |
| `admin_panel.php` | Private inbox UI for reading/deleting messages | Owner |
| `deploy.php` | Private git deployment UI | Owner |

**Typical visitor flow:** `index.html` → browse portfolio → fill contact form → `send_message.php` stores a row in MySQL → owner reviews it in `admin_panel.php`.

---

## Backend (PHP + MySQL)

### `send_message.php`
- Connects to MySQL via PDO (`utf8mb4`).
- Reads `name`, `email`, `subject`, `contact_no`, `message` from POST.
- Auto-captures `site_source` from `$_SERVER['HTTP_REFERER']` (defaults to `Direct Access`).
- Validates that `name`, `email`, and `message` are non-empty.
- Inserts into the `messages` table. `contact_no` is prepended into the message body.
- Returns JSON: `{ success: bool, message: string }`.

### Required MySQL table

```sql
CREATE TABLE messages (
  id INT AUTO_INCREMENT PRIMARY KEY,
  site_source VARCHAR(255),
  name VARCHAR(255) NOT NULL,
  email VARCHAR(255) NOT NULL,
  subject VARCHAR(255),
  message TEXT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### Credentials
DB credentials live in `send_message.php` and `admin_panel.php` and are read from the cPanel environment. **Rotate them before any public commit** and move them to an `.env`-style include if you open-source this repo.

---

## Admin & Deployment Tools

### `admin_panel.php`
- Password-gated session (`$ADMIN_PASSWORD`).
- Filter messages by `site_source` (`noorgee.pk/Web`, `noorgee.pk/Dev`, `nm.noorgee.pk`, or all).
- Per-row actions: copy contact info to clipboard, forward via `mailto:`, delete.
- Bulk select + bulk delete with a floating status bar.
- Select-all checkbox with indeterminate state.

### `deploy.php`
- Password-gated session.
- Branch target: `main-m81`.
- Actions: `pull`, `force_pull`, `push` (with commit message + extended description), `revert_last`, `restore_commit` (by hash), `undo_local`.
- Shows last 10 commits with subject, date, body, and a "time ago" indicator.
- Status indicator compares local HEAD to `origin/main-m81` (`Applied` vs `Pending / Not Applied`).
- Git identity setup form (`user.name` / `user.email`) if missing.

> **Security note:** both panels use a hardcoded password (`"123"`). Change this before deploying, and consider IP-allowlisting or moving the panels behind HTTP basic auth.

---

## SEO & Analytics

- **JSON-LD schema** in `index.html` and `faq.html`:
  - `LocalBusiness` + `ProfessionalService` with a service catalog and PKR pricing.
  - `FAQPage` with 4 questions (English / Roman-Urdu).
- **`sitemap.xml`** — lists 6 pages with `lastmod` 2026-06-03.
- **`robots.txt`** — allows all, disallows `/admin_panel.php`, `/deploy.php`, `/.env`, `/.git/`.
- **`BingSiteAuth.xml`** — Bing webmaster verification token.
- **Microsoft Clarity** — heatmap / session-replay tag (`x1d871q3xm`) in `index.html`.
- **`.htaccess`** — 301 redirect from `noorgee.pk/Web/*` to `web.noorgee.pk/*`, force HTTPS, gzip, browser caching, dotfile protection.

---

## Image / Media Assets

All media lives under `img/`. A full audit is in `IMAGE_MAPPING.md`.

**Present (used by portfolio):**
- Screenshots / GIFs: `bic-screen.gif`, `noorgee_scroll.gif`, `ng-us_claud.gif`, `ng-pk_claud.gif`, `noorgee_pk_Web.gif`, `noorgee_pk_Dev_.gif`, `it-ng-site-tool-screen.gif`, `kwa-screen.gif`, `nm-noorgee_claud.gif`, `blog_noorgee_preview.gif`, `blog_noorgee_pk_preview.gif`, `fsk_claude_preview-1.gif`, `businessitc-site-screenshot-Animation.gif`, `ng-pk-anim-gif-design.gif`, `portfolio-ng-pk-02.gif`, `noorgee_pk_claude.gif`
- Stills: `NG2026.jpg`, `NG-green-brand.png`, `FSL-ng.jpg`, `ng-ai-img.jpg`, `ng-ai-vid.jpg`, `ng-ai-automat.jpg`, `ng-ai-content.jpg`
- Videos: `NG-ugc-Wan_Avatar_CharacterSwap.mp4`, `Perfume_bottle_on_202603181837.mp4`
- `img/thumbnails/` — compressed `tn_*` variants for fast grid rendering.

**Missing (referenced in code but not in repo):**
- `Hbiba-top-gerry-ai.png`, `NoorGee Logo 1756693576 (1).jpg`, `kids infogrph ur.png`, `us-ng-ecomerce.jpg`, `Bad Prompt vs. Good Prompt.mp4`

The portfolio JS in `index.html` falls back to a gradient placeholder with the project's initials when an image is missing.

---

## Local Development

This is a static-first site. You can preview the front-end without PHP:

```bash
# from the project root
python3 -m http.server 8000
# open http://localhost:8000
```

To exercise the PHP endpoints locally, use PHP's built-in server with a MySQL instance:

```bash
php -S localhost:8000
```

Create the `messages` table (see [Backend](#backend-php--mysql)) and update the DB credentials in `send_message.php` and `admin_panel.php` to point at your local MySQL.

> The dev server in this environment is already running — do not start, stop, or restart it yourself.

---

## Deployment

### Production (cPanel + Apache)
1. Push to the `main-m81` branch on GitHub.
2. On the server, open `https://web.noorgee.pk/deploy.php`, log in, and click **Pull** (or **Force** to discard local changes).
3. Verify the status indicator flips to **Applied**.
4. Confirm DB credentials in `send_message.php` and `admin_panel.php` match the cPanel MySQL user.

### GitHub Pages mirror
- `_config.yml` + `.nojekyll` configure Pages to serve from the repo root.
- Pages URL: `https://moon060781-bot.github.io/ng-web-m81`.
- Pages only serves the static front-end; PHP endpoints (`send_message.php`, `admin_panel.php`, `deploy.php`) do not run on Pages.

See `DEPLOYMENT.md` and `GITHUB_PAGES_SETUP.txt` for the full Pages walkthrough.

---

## Known Issues & Notes

- **`error_log`** shows two recurring production failures:
  1. `mysqli` access denied for `noorgeec_pf@localhost` — caused by a stale password in an older revision of `send_message.php`. The current PDO version uses `noorgeec_wb` and should resolve this.
  2. `Call to undefined function mail()` — the `mail()` PHP extension is disabled on the host. The contact form now stores to MySQL instead of emailing; the legacy `bk-wb-index.html` still references `mail()` and is kept only as a backup.
- **Hardcoded passwords** in `admin_panel.php` and `deploy.php` (`"123"`). Rotate before exposing the repo.
- **`index.html`** has a duplicate JSON-LD schema block (one in `<head>`, one after the gallery script). The second block is wrapped in JS-style comments and is ignored by browsers; it should be removed in a future cleanup.
- **`masterpieces.html`** references a Gemini API key left blank (`const apiKey = ""`) — the AI pitch generator will fail until a key is injected at runtime.
- **`faq.html`** has a syntax issue: the JSON-LD `<script>` block is placed after `</body>` and uses JS-style `//` comments inside HTML, which can render as text. Move it inside `<body>` and remove the comment lines.

---

## License

© 2026 NoorGee WebMaster. All rights reserved. Source is private to the NoorGee network; do not redistribute without permission.
