# PERF website

Website for **Poudyal Education & Research Foundation (PERF)** – *Education • Employment • Empowerment*.
Live at <https://perf.org.in> (Hostinger Premium shared hosting, PHP).

Pages: Home, About, Services (with category filters), Gallery & Success Stories, Why PERF, FAQs, Contact (enquiry form, map, WhatsApp), plus a 404 page.

## Editing content

Almost everything is edited in two files – no HTML knowledge needed:

| File | What's in it |
|---|---|
| `public_html/includes/config.php` | Phone, WhatsApp, email, address, map, office hours, social links, enquiry inbox, stats, testimonials, mission/vision, founder message, partners, Google Analytics |
| `public_html/includes/data.php` | Services/courses, "Find your path" goals (home page), "Why PERF" points, process steps, FAQs |

Anything left empty in `config.php` is hidden automatically, so the site never shows placeholder text.
For example, the WhatsApp buttons appear as soon as `whatsapp_intl` is filled in, and the
testimonial section appears once it has entries. Until `stats` is filled in, the home page shows
counts taken from the site itself (number of programs, career areas, steps).

**Gallery photos:** upload images (JPG/PNG/WebP, ideally ≤ 1600px wide) into one of
`public_html/assets/img/gallery/achievements|events|awards|international/`. They appear automatically,
newest first; the caption comes from the file name (`neet-topper-2026.jpg` → "Neet topper 2026").

**Brochure:** upload it as `public_html/assets/brochure/PERF-Brochure.pdf` and a download button appears on the Services page.

**Founder photo:** upload to `public_html/assets/img/team/` and set `founder.photo` in `config.php`.

## Enquiries (leads)

The contact form (`enquiry.php`) emails each enquiry to `form_to` and **also saves it to
`public_html/storage/leads.csv`**, which is blocked from the web. Download it from hPanel File Manager
and open it in Excel. It includes a honeypot field, a time check and per-IP rate limiting against spam.

For reliable email delivery, create the `form_from` mailbox (default `no-reply@perf.org.in`) in **hPanel → Emails**.

## Deploying to Hostinger

### Option A – upload a zip (simplest)
1. Run `./scripts/build-zip.sh` → creates `dist/perf-site.zip`.
2. hPanel → **Websites → perf.org.in → File Manager** → open `public_html`, delete the default files
   (`default.php`, old `index.html` etc.).
3. Upload `perf-site.zip`, right-click → **Extract** into `public_html` so `index.php` sits directly inside it.
4. Make sure the `storage` folder is writable (permission 755 is normal on Hostinger).
5. hPanel → **Security → SSL** → install the free SSL. `.htaccess` then forces `https://` and removes `www.`.

### Option B – automatic deploy from GitHub
`.github/workflows/deploy.yml` uploads `public_html/` over FTP on every push to `main`.
Add repository secrets `FTP_SERVER` (`89.116.133.35`), `FTP_USERNAME` and `FTP_PASSWORD`
(hPanel → Files → FTP Accounts). If your FTP login does not open in the folder that contains
`public_html`, set a repository variable `FTP_DIR` (e.g. `domains/perf.org.in/public_html/`).
Without secrets the workflow just skips.

## Preview on GitHub Pages

The same code also builds as a plain-HTML preview with the same look and feel, published to
`https://bsntas.github.io/perf/` on every push to `main` (`.github/workflows/pages.yml`).
One-time setup: **repo Settings → Pages → Source: GitHub Actions**.

| | Hostinger (live) | GitHub Pages (preview) |
|---|---|---|
| Pages | PHP, rendered on each visit | pre-rendered HTML (`scripts/build-static.sh`) |
| Enquiry form | `enquiry.php`: emails + saves to `leads.csv` | opens WhatsApp (or the visitor's email app) with the enquiry filled in |
| Search engines | indexed | `noindex`; canonical links point to perf.org.in |

Build it locally with `PERF_BASE=/perf/ ./scripts/build-static.sh` → `dist/static/`.

### After going live
- Submit `https://perf.org.in/sitemap.xml` in Google Search Console.
- Create a Google Business Profile and add the address/map to `config.php`.

## Local preview

Requires PHP 8+ (or Docker):

```bash
php -S localhost:8000 -t public_html dev/router.php
# or
docker run --rm -p 8000:8000 -v "$PWD":/app -w /app php:8.2-cli php -S 0.0.0.0:8000 -t public_html dev/router.php
```

Then open <http://localhost:8000>. (The router mimics the `.htaccess` clean URLs; email isn't sent locally but leads are still saved to the CSV.)

## Structure

```
public_html/            ← everything that goes on the server
  index.php about.php services.php gallery.php why-perf.php faqs.php contact.php 404.php
  enquiry.php           form handler
  includes/             config, content data, header/footer (blocked from the web)
  storage/              leads.csv (blocked from the web)
  assets/css, js, img, brochure
  .htaccess robots.txt sitemap.xml
dev/router.php          local preview router
scripts/build-zip.sh    builds dist/perf-site.zip
```
