# Manalas Dungeon — manalasdungeon.lv

[![Testit](https://github.com/ManalasDungeon/manalasdungeon.lv/actions/workflows/testit.yml/badge.svg)](https://github.com/ManalasDungeon/manalasdungeon.lv/actions/workflows/testit.yml)

A trilingual personal website with **passwordless administration (WebAuthn / FIDO2 passkeys)**, built from scratch with **PHP 8.4 and MySQL** — no CMS, no framework.

**Live:** [manalasdungeon.lv](https://manalasdungeon.lv) · **Case study:** [manalasdungeon.lv/portfolio.php](https://manalasdungeon.lv/portfolio.php) · **Links:** [manalasdungeon.lv/linkit](https://manalasdungeon.lv/linkit)

> 🇱🇻 **Latviski:** Trīsvalodu (latviešu, angļu, somu) personīga vietne ar bezparoles administrēšanu (passkey, WebAuthn / FIDO2). Izstrādāta no nulles ar PHP 8.4 un MySQL bez satura pārvaldības sistēmas.
>
> 🇫🇮 **Suomeksi:** Kolmikielinen (latvia, englanti, suomi) henkilökohtainen sivusto, jonka ylläpito toimii ilman salasanoja (passkey, WebAuthn / FIDO2). Rakennettu alusta asti PHP 8.4:llä ja MySQL:llä ilman julkaisujärjestelmää.

---

## Highlights

- **Passwordless admin** — admin pages open only on a registered device with a fingerprint, face, device PIN or security key. The server side is plain PHP with no external libraries: CBOR decoding, COSE → PEM key conversion and ES256 / RS256 signature verification with OpenSSL.
- **Three languages on every page** — Latvian (default), English and Finnish, with `hreflang`, canonical URLs, Open Graph and a multilingual sitemap.
- **Security by design** — follows the OWASP Top 10: prepared statements, output escaping, CSRF tokens, an enforced Content-Security-Policy with nonces, HTTPS with HSTS, safe uploads.
- **Privacy by default** — Google Analytics loads only after the visitor agrees; declining is as easy as accepting, and the choice can be changed with a switch on the trilingual privacy policy page. Fonts are self-hosted, so visitor IPs don’t reach Google Fonts.
- **Tested on every push** — 87 automated page tests (Playwright) run in GitHub Actions: 14 pages in three languages at desktop and phone sizes, PHP/JS errors, links, forms, CSP violations and accessibility (axe-core, WCAG 2.1 AA). Locally the same suite also compares screenshots pixel by pixel. Plus 38 unit tests for the passkey cryptography and browser tests with a virtual FIDO2 authenticator.
- **Responsive** — mobile first; works on phones, tablets and desktops.

## Pages

| Page | File | What it is |
| --- | --- | --- |
| Home | `index.php` | Entry page with links, language picker, a “new post” badge and a **Comment** button |
| Feedback | `palaute.php` | Private feedback sent straight to email (pop-up on the home page; also a standalone page without JavaScript) |
| News (Ziņas) | `blogi.php` | News blog, read from the database |
| Travel (Ceļojums) | `celojums.php` | Travel blog; same table as news, separated by category |
| Experience Vidzeme | `vidzeme.php` | Travel guide to Vidzeme: food, sights, nature trails, accommodation — by municipality and town, plus a history section (the three stars of the Freedom Monument, coat of arms, timeline) |
| Yoga | `joga.php` | History and philosophy of yoga, Vinyasa and Core yoga |
| Guestbook | `viesu_gramata.php` | Moderated guestbook with spam protection |
| Portfolio | `portfolio.php` | Public case study of this project |
| Architecture | `arhitektura.php` | Animated public architecture overview |
| Privacy | `privatums.php` | Privacy policy with a cookie-choice switch |
| News feed | `rss.php` | RSS 2.0 feed of the latest news |
| Not found | `404.php` | Custom trilingual 404 page (`ErrorDocument`) |
| Links | `linkit.php` → `/linkit` | Link-in-bio page with QR code |
| Vitrupe Beach | `vitrupe/` | Separate travel site with a gallery and lightbox (own `.htaccess`, clean `/en/` and `/fi/` URLs) |
| Admin | `blogi_admin.php`, `viesu_admin.php` | Post editor and guestbook moderation — passkey only |
| Devices | `passkey/` | Passkey sign-in and device management |

## Tech stack

| Area | Used |
| --- | --- |
| Server | PHP 8.4, MySQL / MariaDB, LiteSpeed (Apache-compatible `.htaccess`), cPanel shared hosting |
| Front end | HTML5, CSS3 (custom properties, grid, masks, `background-clip: text`), vanilla JavaScript, Bootstrap 5 on the Vitrupe site |
| Authentication | WebAuthn / FIDO2 passkeys, PHP sessions, OpenSSL |
| Images | Python + Pillow (resizing, WebP, duotone tints, line art → transparent PNG, Open Graph images) |
| Fonts | Self-hosted variable WOFF2 (Fontsource, OFL), latin + latin-ext subsets via `unicode-range` |
| Testing | Playwright page tests with axe-core and pixelmatch, GitHub Actions with a MariaDB service; PHP unit tests; Chromium’s virtual WebAuthn authenticator |
| Analytics | Google Analytics 4, loaded only after consent (link clicks as events) |

## Security

| Threat | Protection |
| --- | --- |
| Stolen or phished passwords | No passwords at all: passkeys are bound to the domain (`rp_id`), user verification is required, and the signature counter is checked |
| SQL injection | Every query uses PDO prepared statements |
| XSS | All output is escaped with `htmlspecialchars()`; links in text are created only after escaping; image filenames are whitelisted |
| CSRF | Every admin action, including sign-out, requires a one-time token |
| Session hijacking | Session expires after 30 min idle and 8 h at most; secure cookies |
| Plain-text traffic | `.htaccess` redirects everything to one canonical HTTPS address (no `www`) |
| Malicious uploads | File type checked from content, size limited, PHP execution disabled in the upload folder |
| Guestbook spam | Moderation, hidden honeypot field, per-IP time limit (stored as a hash) |
| Feedback form abuse | CSRF token, honeypot, time trap (under 3 s = bot), limits per IP (1 / min, 5 / day) and site-wide (40 / day); the limit file lives outside the web root and stores only IP hashes |
| Email header injection | Name and email are stripped of line breaks and RFC 2047-encoded; the email is validated with `filter_var` |
| Image hotlinking | Blocked for other sites; social-media preview bots, share images and direct requests are allowed |
| Injected scripts | Enforced Content-Security-Policy: scripts only from the site itself and Google Analytics, inline scripts need a per-request nonce; violations are reported to a size- and rate-limited log outside the web root |
| Framing (clickjacking) | `X-Frame-Options: SAMEORIGIN`, plus `nosniff`, `Referrer-Policy` and `Permissions-Policy` |
| Connection downgrade | HSTS for one year, so browsers use HTTPS even on the first request |
| Visitor tracking | Analytics only with consent; declining deletes the `_ga` cookies and stops sending; IP addresses are stored only as hashes |

### How the passkey sign-in works

1. **Register** — the server sends a random challenge; the device creates a key pair and returns the public key (CBOR / COSE). The server converts it to PEM and stores it in `admin_passkeys`, looked up by a SHA-256 hash of the credential ID.
2. **Sign in** — the server sends a new challenge; the device signs it after a fingerprint, face or PIN. The server verifies the signature with the stored public key, checks the origin, `rp_id`, the user-verification flag and the signature counter.
3. **First device** — a one-time setup token works only while no device is registered. After that, new devices are added from the device page while signed in.

`passkey/guard.php` protects the admin pages: one `require` line at the top of a page is enough.

## Architecture notes

- **Languages:** each page holds its texts in a PHP array (`$TT['lv' | 'en' | 'fi']`). The language comes from `?lang=` (default Latvian); the Vitrupe site uses clean `/en/` and `/fi/` paths.
- **One table, two blogs:** news and travel posts share the `zinas` table; the `kategorija` column decides where a post appears. Images are placed in the text with a `[kuva:filename.jpg]` shortcode.
- **Clean URLs:** `.htaccess` rewrites `/linkit` to `linkit.php` internally and redirects old `linkit.php` links to `/linkit` with a 301.
- **Cache busting:** CSS, JS and images are linked with `?v=<file modification time>`, so browsers can cache them for a year and still get updates at once.
- **Feedback pop-up:** a native `<dialog>` sized to its content. The CSRF token is fetched only when the dialog opens (`palaute.php?token=1`), so the home page sets no cookies for ordinary visitors. The form is sent with `fetch` and gets JSON back; without JavaScript the same form works as a normal page. The texts for all three languages are in `palaute_tekstit.php`.
- **Data-driven pages:** links, timelines and cards are PHP arrays rendered in loops, so adding content is one line of code.
- **Shared bootstrap:** every page first loads `includes/bootstrap.php` — language selection, `h()`, `asset()`, `page_url()`, the `<head>` helpers (hreflang, fonts, analytics) and the security headers live in one place.
- **Consent without a third party:** `analytics.js` defines `gtag()` at once (so page events never fail) but injects the Google script only after consent. The choice is kept in `localStorage` for 12 months; `privatums.php` shows the current state with a switch.
- **Backups:** a CLI-only script in the home folder (outside the web root) dumps the database every week via cron and keeps the latest eight gzipped copies.

## Project structure

```
/
├── index.php, manala.css          Home page
├── palaute.php                    Feedback endpoint + no-JS page
├── palaute_tekstit.php            Feedback texts (lv / en / fi)
├── palaute.css, palaute.js        Comment button and pop-up
├── blogi.php, blogi.css, blogi.js News
├── celojums.php, celojums.css     Travel blog
├── blogi_admin.php                Post editor (passkey)
├── viesu_gramata.php              Guestbook
├── viesu_admin.php                Guestbook moderation (passkey)
├── vidzeme.php, vidzeme.css       Vidzeme travel guide
├── joga.php, joga.css             Yoga page
├── portfolio.php, portfolio.css   Case study
├── linkit.php, linkit.css         Links page
├── arhitektura.php/.css/.js       Architecture overview
├── privatums.php, privatums.css   Privacy policy + cookie switch
├── analytics.js, consent.css      GA4 only after consent
├── 404.php, 404.css               Custom 404 page
├── rss.php                        News RSS feed
├── csp-raportti.php               CSP violation reports → log outside the web root
├── includes/bootstrap.php         Shared helpers and security headers
├── fonts.css, fonts/              Self-hosted fonts + OFL licences
├── varmuuskopio/                  Weekly DB backup script (goes OUTSIDE public_html)
├── tests/                         Automated tests (not uploaded to the server)
├── .github/workflows/testit.yml   Runs the tests on every push
├── passkey/                       WebAuthn sign-in
│   ├── lib.php                    CBOR, COSE → PEM, signature checks
│   ├── api.php                    Register / sign-in endpoints (JSON)
│   ├── guard.php                  Protects admin pages
│   ├── login.php, manage.php      Sign-in and device pages
│   ├── passkey.js, passkey.css
│   ├── schema.sql                 admin_passkeys table
│   └── config.example.php         Settings template
├── vitrupe/                       Vitrupe Beach site
├── images/                        Images, Open Graph images, QR code
├── blogi_taulu.sql                zinas table
├── celojums_taulu.sql             Adds the category column
├── db_config.example.php          Database connection template
├── sitemap.xml, robots.txt
└── .htaccess                      HTTPS, clean URLs, caching, hotlink protection
```

## Running it locally

Requirements: PHP 8.4 with the `pdo_mysql` and `openssl` extensions, and MySQL or MariaDB.

```bash
# 1. Database
mysql -u root -p -e "CREATE DATABASE manalas CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
mysql -u root -p manalas < blogi_taulu.sql
mysql -u root -p manalas < celojums_taulu.sql
mysql -u root -p manalas < passkey/schema.sql

# 2. Settings (these files are not in the repository)
cp db_config.example.php db_config.php            # fill in the database details
cp passkey/config.example.php passkey/config.php  # set rp_id, origins and a setup token

# 3. Start
php -S localhost:8000
```

Passkeys need a secure context: browsers allow them on `https://` and on `http://localhost`. For local testing set `rp_id` to `localhost` and `origins` to `http://localhost:8000` in `passkey/config.php`.

## Tests

```bash
cd tests
npm install
npx playwright install chromium
npm test            # full run, including screenshot comparison
npm run test:quick  # without screenshot comparison (as in GitHub Actions)
```

The tests copy the site to a temporary folder and use their own MariaDB/MySQL test database (`manalas_test`) and test configs from `tests/fixtures/` — they never touch the real site, database or secrets. See [`tests/README.md`](tests/README.md).

## Deployment

The site runs on shared cPanel hosting (LiteSpeed, PHP 8.4). Files are uploaded to the web root; the root `.htaccess` handles HTTPS, clean URLs, caching, gzip and hotlink protection. The Vitrupe site has its own `.htaccess` in `vitrupe/`, because rewrite rules in a subfolder do not inherit from the parent.

**Deploying from GitHub (cPanel Git Version Control):** the repository is cloned to `~/repositories/manalasdungeon.lv`. *Update from Remote* fetches the latest commit and *Deploy HEAD Commit* runs `.cpanel.yml`, which copies the site files to `public_html` with `rsync` — without deleting anything, so server-only files (`db_config.php`, `passkey/config.php`, uploaded images) stay in place. Tests, GitHub settings, SQL scripts and docs are not copied to the web root, and the backup script goes to `~/varmuuskopio/`.

**Never commit or upload to a public place:** `db_config.php`, `passkey/config.php` (setup token) or diagnostic scripts. See `.gitignore`.

## Author

**Marko Nakari** — software developer (PHP / MySQL, JavaScript, C#, Kotlin, Python)

[LinkedIn](https://www.linkedin.com/in/manalainen) · [GitHub](https://github.com/ManalasDungeon) · marko.nakari(at)inbox.lv

The site’s texts, photos and images are © Marko Nakari unless otherwise noted.
