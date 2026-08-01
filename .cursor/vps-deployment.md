# VPS Deployment Guide — MarketLink Web

## Server Details

- **Host**: `192.168.68.223`
- **SSH User**: `test`
- **SSH Command**: `ssh test@192.168.68.223`
- **Project Path on VPS**: `/home/test/marketlink-web`
- **Site URL**: `http://192.168.68.223:8006/`
- **Branch**: `main`
- **Runtime**: Docker Compose (container name: `marketlink_web_app`)

---

## Quick Deploy (Recommended)

```bash
ssh test@192.168.68.223 "cd /home/test/marketlink-web && bash deploy.sh"
```

`deploy.sh` handles: git pull → docker build (if needed) → migrate → cache clear/rebuild → health check → Docker cleanup (dangling images + build cache) + disk warning if ≥85%.

---

## Manual Deploy Steps

```bash
# 1. SSH into VPS
ssh test@192.168.68.223
cd /home/test/marketlink-web

# 2. Pull latest code
git pull origin main

# 3. Rebuild container (only if Dockerfile, composer, or assets changed)
docker compose build
docker compose up -d

# 4. Run migrations
docker compose exec -T app php artisan migrate --force

# 5. Clear and rebuild caches
docker compose exec -T app php artisan config:cache
docker compose exec -T app php artisan route:cache
docker compose exec -T app php artisan view:cache
```

---

## Common Fixes

### Permission denied (500 error on storage/views)

```bash
docker compose exec -T app sh -c 'chown -R www-data:www-data storage bootstrap/cache && chmod -R 775 storage bootstrap/cache'
```

### Clear all caches

```bash
docker compose exec -T app sh -c 'php artisan view:clear && php artisan cache:clear && php artisan config:clear && php artisan route:clear'
```

### View Laravel logs

```bash
docker compose exec -T app sh -c 'tail -n 100 storage/logs/laravel.log'
```

### View Docker container logs

```bash
docker compose logs --tail=200 app
```

### Check container status

```bash
docker compose ps
```

### Run new migration after adding migration file

```bash
docker compose exec -T app php artisan migrate --force
```
