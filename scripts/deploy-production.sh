#!/bin/bash
# Run on the production server (cPanel Terminal or SSH):
#   cd /home/internationalvacationclub/public_html && bash scripts/deploy-production.sh
set -euo pipefail

ROOT="${1:-/home/internationalvacationclub/public_html}"
cd "$ROOT"

echo "Current remote:"
git remote -v || { echo "Not a git repository: $ROOT"; exit 1; }

echo "Pointing origin at lukalinks/IVC..."
git remote set-url origin https://github.com/lukalinks/IVC.git

echo "Fetching latest main..."
git fetch origin main

echo "Updating working tree..."
git reset --hard origin/main

echo "Done. Latest commit:"
git log -1 --oneline

if [ ! -f /home/db-config2.php ] && [ ! -f ivc/db-config2.php ]; then
	echo ""
	echo "WARNING: No db-config2.php found. Copy ivc/db-config.example.php and set MySQL credentials."
fi
