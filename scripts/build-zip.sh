#!/usr/bin/env bash
# Builds dist/perf-site.zip for upload through Hostinger File Manager.
set -euo pipefail
cd "$(dirname "$0")/.."
mkdir -p dist && rm -f dist/perf-site.zip
cd public_html
zip -rq ../dist/perf-site.zip . -x 'storage/leads.csv' 'storage/rl_*' '*.gitkeep' '.DS_Store'
echo "Created dist/perf-site.zip ($(du -h ../dist/perf-site.zip | cut -f1))"
