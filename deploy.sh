#!/usr/bin/env bash
# deploy.sh — pull, rebuild if needed, migrate, cache, health check
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "$ROOT_DIR"

HEALTH_URL="${HEALTH_URL:-http://127.0.0.1:8006/}"
BRANCH="${DEPLOY_BRANCH:-main}"

echo "====================================="
echo "MarketLink Internal (web) deploy"
echo "====================================="
echo "Path: $ROOT_DIR"
echo "Branch: $BRANCH"
echo ""

if [ ! -d .git ]; then
  echo "ERROR: not a git repository: $ROOT_DIR"
  exit 1
fi

if [ ! -f .env ]; then
  echo "ERROR: .env missing. Copy .env.example and set APP_KEY / production values."
  exit 1
fi

echo "--> git fetch / pull"
git fetch origin
git pull --ff-only origin "$BRANCH"

NEED_BUILD=0
if [ ! -f .deploy-image-id ]; then
  NEED_BUILD=1
elif ! docker image inspect "$(cat .deploy-image-id 2>/dev/null)" >/dev/null 2>&1; then
  NEED_BUILD=1
elif git diff --name-only ORIG_HEAD HEAD 2>/dev/null | grep -qE '^(Dockerfile|docker-compose\.yml|docker/|package(-lock)?\.json|composer\.(json|lock)|resources/(css|js)/|vite\.config\.js)'; then
  NEED_BUILD=1
fi

if ! docker compose images -q app 2>/dev/null | grep -q .; then
  NEED_BUILD=1
fi

if [ "$NEED_BUILD" -eq 1 ] || [ "${FORCE_BUILD:-0}" = "1" ]; then
  echo "--> docker compose build"
  docker compose build
else
  echo "--> skip build"
fi

echo "--> docker compose up -d"
docker compose up -d

echo "--> wait for container"
for i in $(seq 1 30); do
  if docker compose exec -T app php -v >/dev/null 2>&1; then
    break
  fi
  sleep 1
done

echo "--> artisan migrate / cache"
docker compose exec -T app php artisan migrate --force
docker compose exec -T app php artisan config:cache
docker compose exec -T app php artisan route:cache
docker compose exec -T app php artisan view:cache

IMAGE_ID="$(docker compose images -q app 2>/dev/null | head -1 || true)"
if [ -n "$IMAGE_ID" ]; then
  echo "$IMAGE_ID" > .deploy-image-id
fi

echo "--> health check: $HEALTH_URL"
HTTP_CODE="$(curl -s -o /dev/null -w '%{http_code}' --max-time 15 "$HEALTH_URL" || true)"
if [ "$HTTP_CODE" = "200" ] || [ "$HTTP_CODE" = "302" ]; then
  echo "OK health check HTTP $HTTP_CODE"
else
  echo "WARN health check HTTP ${HTTP_CODE:-failed}"
  exit 1
fi

echo ""
echo "Deploy finished."
echo "====================================="
