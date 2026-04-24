#!/usr/bin/env bash
set -euo pipefail

rm -f deploy.zip

zip -r deploy.zip . \
  --exclude "vendor/*" \
  --exclude "node_modules/*" \
  --exclude ".git/*" \
  --exclude ".idea/*" \
  --exclude "__MACOSX/*" \
  --exclude "*/__MACOSX/*" \
  --exclude ".DS_Store" \
  --exclude "*/.DS_Store" \
  --exclude ".env" \
  --exclude ".env.backup" \
  --exclude "storage/logs/*" \
  --exclude "storage/framework/cache/*" \
  --exclude "storage/framework/sessions/*" \
  --exclude "storage/framework/views/*" \
  --exclude "deploy.zip"

echo "Created deploy.zip"
