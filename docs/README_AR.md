# مشروع MarketLink Web - Laravel

## نظرة عامة
هذا مشروع Laravel جديد تم إنشاؤه باستخدام أحدث إصدار من Laravel (v12.35.0).

## المتطلبات
- PHP 8.4.4 أو أحدث
- Composer 2.8.6 أو أحدث
- SQLite (مُعد مسبقاً)

## التثبيت والإعداد

### 1. تثبيت التبعيات
```bash
composer install
```

### 2. إعداد البيئة
```bash
cp .env.example .env
php artisan key:generate
```

### 3. إعداد قاعدة البيانات
```bash
php artisan migrate
```

### 4. تشغيل الخادم
```bash
php artisan serve
```

## الوصول للمشروع
بعد تشغيل الخادم، يمكنك الوصول للمشروع عبر:
- http://127.0.0.1:8000
- http://localhost:8000

## هيكل المشروع
```
marketlink-web/
├── app/                 # منطق التطبيق
├── bootstrap/           # ملفات التهيئة
├── config/             # ملفات التكوين
├── database/           # قاعدة البيانات والـ migrations
├── public/             # الملفات العامة
├── resources/          # الـ views والـ assets
├── routes/             # ملفات التوجيه
├── storage/            # ملفات التخزين
└── tests/              # الاختبارات
```

## الميزات المتاحة
- ✅ Laravel Framework v12.35.0
- ✅ SQLite Database
- ✅ Authentication System
- ✅ Migration System
- ✅ Artisan Commands
- ✅ Blade Templating
- ✅ Eloquent ORM

## الأوامر المفيدة

### تشغيل الخادم
```bash
php artisan serve
```

### إنشاء Controller جديد
```bash
php artisan make:controller ControllerName
```

### إنشاء Model جديد
```bash
php artisan make:model ModelName
```

### إنشاء Migration جديد
```bash
php artisan make:migration create_table_name
```

### تشغيل الـ Migrations
```bash
php artisan migrate
```

### مسح Cache
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
```

## التطوير
هذا المشروع جاهز للتطوير. يمكنك البدء في:
1. إنشاء Models و Controllers
2. إعداد Routes
3. إنشاء Views
4. إعداد Authentication
5. إضافة الميزات المطلوبة

## الدعم
للمساعدة في التطوير، راجع:
- [Laravel Documentation](https://laravel.com/docs)
- [Laravel API Reference](https://laravel.com/api)

---
تم إنشاء المشروع في: $(date)
Laravel Version: 12.35.0
PHP Version: 8.4.4
