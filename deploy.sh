#!/bin/bash

# MarketLink Deployment Script
# استخدم هذا السكريبت لنشر المشروع على السيرفر

echo "🚀 بدء نشر MarketLink..."

# متغيرات النشر
SERVER_USER="your_username"
SERVER_HOST="yourdomain.com"
SERVER_PATH="/var/www/html/marketlink-web"
LOCAL_PATH="./"

# التحقق من الاتصال بالسيرفر
echo "📡 التحقق من الاتصال بالسيرفر..."
ssh -o ConnectTimeout=10 $SERVER_USER@$SERVER_HOST "echo 'الاتصال بالسيرفر نجح'"

if [ $? -ne 0 ]; then
    echo "❌ فشل الاتصال بالسيرفر. تأكد من الإعدادات."
    exit 1
fi

# رفع الملفات
echo "📤 رفع الملفات إلى السيرفر..."
rsync -avz --delete \
    --exclude='.git' \
    --exclude='node_modules' \
    --exclude='storage/logs' \
    --exclude='.env' \
    --exclude='vendor' \
    $LOCAL_PATH $SERVER_USER@$SERVER_HOST:$SERVER_PATH/

# تشغيل الأوامر على السيرفر
echo "⚙️ إعداد المشروع على السيرفر..."
ssh $SERVER_USER@$SERVER_HOST << 'EOF'
cd /var/www/html/marketlink-web

# نسخ ملف البيئة للإنتاج
if [ ! -f .env ]; then
    cp production.env .env
    echo "تم نسخ ملف البيئة للإنتاج"
fi

# تثبيت التبعيات
composer install --optimize-autoloader --no-dev

# بناء الأصول
npm install
npm run build

# إعداد الصلاحيات
sudo chown -R www-data:www-data /var/www/html/marketlink-web
sudo chmod -R 755 /var/www/html/marketlink-web
sudo chmod -R 775 /var/www/html/marketlink-web/storage
sudo chmod -R 775 /var/www/html/marketlink-web/bootstrap/cache

# مسح الكاش
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# تشغيل Migrations (إذا لزم الأمر)
# php artisan migrate --force

echo "✅ تم إعداد المشروع بنجاح"
EOF

echo "🎉 تم نشر المشروع بنجاح!"
echo "🌐 يمكنك الوصول للموقع على: https://$SERVER_HOST"
echo ""
echo "📋 خطوات إضافية مطلوبة:"
echo "1. تأكد من إعداد قاعدة البيانات في ملف .env"
echo "2. شغل: php artisan migrate --force"
echo "3. تأكد من إعداد SSL Certificate"
echo "4. اختبر الموقع للتأكد من عمل HTTPS"