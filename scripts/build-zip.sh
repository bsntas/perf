#!/usr/bin/env bash
# Builds dist/perf-site.zip for upload through Hostinger File Manager.
set -euo pipefail
cd "$(dirname "$0")/.."
mkdir -p dist && rm -f dist/perf-site.zip
cd public_html
# storage/ only ships its .htaccess and index.php, so uploading a new zip never overwrites admin changes or enquiries.
zip -rq ../dist/perf-site.zip . -x 'storage/*' '*.gitkeep' '.DS_Store'
zip -q ../dist/perf-site.zip storage/.htaccess storage/index.php
echo "Created dist/perf-site.zip ($(du -h ../dist/perf-site.zip | cut -f1))"
