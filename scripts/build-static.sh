#!/usr/bin/env bash
# Renders the PHP site to plain HTML for GitHub Pages (preview) in dist/static/.
# Usage: PERF_BASE=/perf/ ./scripts/build-static.sh      (PERF_BASE = path the site is served from)
# Needs PHP 8+; falls back to Docker if php is not installed.
set -euo pipefail
cd "$(dirname "$0")/.."
OUT=dist/static
rm -rf "$OUT" && mkdir -p "$OUT"
export PERF_STATIC=1 PERF_BASE="${PERF_BASE:-/}"

if command -v php >/dev/null; then PHP=(php)
else PHP=(docker run --rm -e PERF_STATIC -e PERF_BASE -v "$PWD":/app -w /app php:8.2-cli php); fi

for page in index about services gallery why-perf faqs contact 404; do
  "${PHP[@]}" -d display_errors=stderr "public_html/$page.php" > "$OUT/$page.html"
done

cp -r public_html/assets "$OUT/"
find "$OUT/assets" -name .gitkeep -delete
printf 'User-agent: *\nDisallow: /\n' > "$OUT/robots.txt"   # preview only: keep it out of search engines
touch "$OUT/.nojekyll"
echo "Static preview built in $OUT (base: $PERF_BASE)"
