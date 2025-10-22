# دليل النشر على السيرفر - MarketLink

## متطلبات السيرفر

### 1. متطلبات النظام
- PHP 8.4+ 
- Composer 2.8+
- MySQL 8.0+ أو MariaDB 10.4+
- Apache أو Nginx
- SSL Certificate (HTTPS)

### 2. إعدادات Apache
```apache
# في ملف .htaccess أو Virtual Host
<VirtualHost *:443>
    ServerName yourdomain.com
    DocumentRoot /var/www/html/marketlink-web/public
    
    SSLEngine on
    SSLCertificateFile /path/to/your/certificate.crt
    SSLCertificateKeyFile /path/to/your/private.key
    
    <Directory /var/www/html/marketlink-web/public>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>

# Redirect HTTP to HTTPS
<VirtualHost *:80>
    ServerName yourdomain.com
    Redirect permanent / https://yourdomain.com/
</VirtualHost>
```

### 3. إعدادات Nginx
```nginx
server {
    listen 80;
    server_name yourdomain.com;
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    server_name yourdomain.com;
    root /var/www/html/marketlink-web/public;
    index index.php;

    ssl_certificate /path/to/your/certificate.crt;
    ssl_certificate_key /path/to/your/private.key;
    
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers ECDHE-RSA-AES256-GCM-SHA512:DHE-RSA-AES256-GCM-SHA512:ECDHE-RSA-AES256-GCM-SHA384:DHE-RSA-AES256-GCM-SHA384;
    ssl_prefer_server_ciphers off;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.4-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

## خطوات النشر

### 1. رفع الملفات
```bash
# رفع جميع الملفات إلى السيرفر
scp -r marketlink-web/ user@yourdomain.com:/var/www/html/
```

### 2. إعداد البيئة
```bash
# على السيرفر
cd /var/www/html/marketlink-web

# نسخ ملف البيئة للإنتاج
cp production.env .env

# تحديث إعدادات قاعدة البيانات في .env
nano .env
```

### 3. تحديث ملف .env للإنتاج
```env
APP_NAME=MarketLink
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=marketlink_db
DB_USERNAME=your_db_user
DB_PASSWORD=your_db_password

SESSION_ENCRYPT=true
SESSION_DOMAIN=yourdomain.com

FORCE_HTTPS=true
```

### 4. تثبيت التبعيات
```bash
# تثبيت Composer dependencies
composer install --optimize-autoloader --no-dev

# تثبيت NPM dependencies
npm install

# بناء الأصول
npm run build
```

### 5. إعداد قاعدة البيانات
```bash
# إنشاء قاعدة البيانات
mysql -u root -p
CREATE DATABASE marketlink_db;
CREATE USER 'marketlink_user'@'localhost' IDENTIFIED BY 'strong_password';
GRANT ALL PRIVILEGES ON marketlink_db.* TO 'marketlink_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;

# تشغيل Migrations
php artisan migrate --force

# إنشاء مفتاح التطبيق
php artisan key:generate
```

### 6. إعداد الصلاحيات
```bash
# إعطاء صلاحيات للمجلدات
chown -R www-data:www-data /var/www/html/marketlink-web
chmod -R 755 /var/www/html/marketlink-web
chmod -R 775 /var/www/html/marketlink-web/storage
chmod -R 775 /var/www/html/marketlink-web/bootstrap/cache
```

### 7. إعداد Cron Jobs
```bash
# إضافة cron job للـ scheduler
crontab -e

# إضافة السطر التالي
* * * * * cd /var/www/html/marketlink-web && php artisan schedule:run >> /dev/null 2>&1
```

### 8. إعداد SSL Certificate

#### باستخدام Let's Encrypt (مجاني)
```bash
# تثبيت Certbot
sudo apt update
sudo apt install certbot python3-certbot-apache

# الحصول على شهادة SSL
sudo certbot --apache -d yourdomain.com

# تجديد تلقائي
sudo crontab -e
# إضافة: 0 12 * * * /usr/bin/certbot renew --quiet
```

## إعدادات الأمان

### 1. تحديث AppServiceProvider
```php
// في app/Providers/AppServiceProvider.php
public function boot()
{
    if (app()->environment('production')) {
        URL::forceScheme('https');
    }
}
```

### 2. إعدادات إضافية في .env
```env
# أمان إضافي
SESSION_SECURE_COOKIE=true
SESSION_HTTP_ONLY=true
SESSION_SAME_SITE=strict

# إعدادات البريد الإلكتروني
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your_email@gmail.com
MAIL_PASSWORD=your_app_password
MAIL_ENCRYPTION=tls
```

## اختبار النشر

### 1. التحقق من HTTPS
```bash
# اختبار إعادة التوجيه
curl -I http://yourdomain.com
# يجب أن يعيد 301 redirect إلى HTTPS

# اختبار HTTPS
curl -I https://yourdomain.com
# يجب أن يعيد 200 OK
```

### 2. التحقق من الأمان
- فتح الموقع في المتصفح
- التحقق من وجود قفل الأمان في شريط العنوان
- اختبار النماذج للتأكد من عدم ظهور رسالة "not secure"

## استكشاف الأخطاء

### 1. مشاكل SSL
```bash
# التحقق من حالة SSL
openssl s_client -connect yourdomain.com:443 -servername yourdomain.com

# التحقق من شهادة SSL
curl -I https://yourdomain.com
```

### 2. مشاكل Laravel
```bash
# عرض الأخطاء
tail -f /var/www/html/marketlink-web/storage/logs/laravel.log

# مسح الكاش
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### 3. مشاكل الصلاحيات
```bash
# إعادة تعيين الصلاحيات
sudo chown -R www-data:www-data /var/www/html/marketlink-web
sudo chmod -R 755 /var/www/html/marketlink-web
sudo chmod -R 775 /var/www/html/marketlink-web/storage
sudo chmod -R 775 /var/www/html/marketlink-web/bootstrap/cache
```

## نصائح مهمة

1. **تأكد من استخدام HTTPS** في جميع الروابط
2. **فعل HSTS** للأمان الإضافي
3. **استخدم كلمات مرور قوية** لقاعدة البيانات
4. **احتفظ بنسخ احتياطية** منتظمة
5. **راقب سجلات الأخطاء** بانتظام
6. **حدث النظام** بانتظام للأمان
