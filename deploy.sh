#!/bin/bash

# MarketLink Web - Auto Deployment Script
# This script is executed when GitHub webhook is triggered

set -e  # Exit on any error

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Log function
log() {
    echo -e "${BLUE}[$(date +'%Y-%m-%d %H:%M:%S')]${NC} $1"
}

success() {
    echo -e "${GREEN}[$(date +'%Y-%m-%d %H:%M:%S')] ✅${NC} $1"
}

warning() {
    echo -e "${YELLOW}[$(date +'%Y-%m-%d %H:%M:%S')] ⚠️${NC} $1"
}

error() {
    echo -e "${RED}[$(date +'%Y-%m-%d %H:%M:%S')] ❌${NC} $1"
}

# Start deployment
log "Starting MarketLink Web deployment..."

# Get the directory where the script is located
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "$SCRIPT_DIR"

# Check if we're in a git repository
if [ ! -d ".git" ]; then
    error "Not a git repository. Please run this script from the project root."
    exit 1
fi

# Get current branch
CURRENT_BRANCH=$(git branch --show-current)
log "Current branch: $CURRENT_BRANCH"

# Check if we're on main or master branch
if [[ "$CURRENT_BRANCH" != "main" && "$CURRENT_BRANCH" != "master" ]]; then
    warning "Not on main/master branch. Current branch: $CURRENT_BRANCH"
    exit 0
fi

# Pull latest changes
log "Pulling latest changes from origin..."
git pull origin "$CURRENT_BRANCH"

# Check if composer is available
if ! command -v composer &> /dev/null; then
    error "Composer is not installed or not in PATH"
    exit 1
fi

# Install/update dependencies
log "Installing/updating Composer dependencies..."
composer install --no-dev --optimize-autoloader --no-interaction

# Check if PHP is available
if ! command -v php &> /dev/null; then
    error "PHP is not installed or not in PATH"
    exit 1
fi

# Run Laravel optimizations
log "Running Laravel optimizations..."

# Clear all caches first
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Cache configurations
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run database migrations
log "Running database migrations..."
php artisan migrate --force

# Clear application cache again
php artisan cache:clear

# Restart queue workers (if using queues)
log "Restarting queue workers..."
php artisan queue:restart

# Set proper permissions
log "Setting proper permissions..."
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || true

# Check if deployment was successful
if [ $? -eq 0 ]; then
    success "Deployment completed successfully!"
    log "MarketLink Web is now running the latest version"
else
    error "Deployment failed!"
    exit 1
fi

# Display deployment info
log "Deployment Information:"
echo "  - Branch: $CURRENT_BRANCH"
echo "  - Commit: $(git rev-parse HEAD)"
echo "  - Author: $(git log -1 --pretty=format:'%an')"
echo "  - Message: $(git log -1 --pretty=format:'%s')"
echo "  - Date: $(date)"

success "MarketLink Web deployment completed successfully! 🚀"
