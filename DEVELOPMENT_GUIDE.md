# دليل التطوير - MarketLink Web

## الخطوات التالية للتطوير

### 1. إعداد قاعدة البيانات
```bash
# إنشاء قاعدة بيانات MySQL (اختياري)
mysql -u root -p
CREATE DATABASE marketlink_db;
```

### 2. تكوين قاعدة البيانات في .env
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=marketlink_web_db
DB_USERNAME=root
DB_PASSWORD=your_password
```

### 3. إنشاء Models أساسية
```bash
# User Model (موجود مسبقاً)
php artisan make:model Product
php artisan make:model Category
php artisan make:model Order
php artisan make:model Cart
```

### 4. إنشاء Controllers
```bash
php artisan make:controller ProductController --resource
php artisan make:controller CategoryController --resource
php artisan make:controller OrderController --resource
php artisan make:controller CartController --resource
php artisan make:controller HomeController
```

### 5. إعداد Authentication
```bash
# تثبيت Laravel Breeze
composer require laravel/breeze --dev
php artisan breeze:install
npm install && npm run dev
php artisan migrate
```

### 6. إنشاء Migrations
```bash
php artisan make:migration create_products_table
php artisan make:migration create_categories_table
php artisan make:migration create_orders_table
php artisan make:migration create_cart_items_table
```

### 7. إعداد Routes
```php
// routes/web.php
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::resource('products', ProductController::class);
Route::resource('categories', CategoryController::class);
Route::resource('orders', OrderController::class);
Route::resource('cart', CartController::class);
```

### 8. إنشاء Views
```bash
mkdir -p resources/views/products
mkdir -p resources/views/categories
mkdir -p resources/views/orders
mkdir -p resources/views/cart
```

## هيكل قاعدة البيانات المقترح

### جدول المنتجات (products)
- id (primary key)
- name (string)
- description (text)
- price (decimal)
- category_id (foreign key)
- image (string)
- stock_quantity (integer)
- created_at, updated_at

### جدول الفئات (categories)
- id (primary key)
- name (string)
- description (text)
- created_at, updated_at

### جدول الطلبات (orders)
- id (primary key)
- user_id (foreign key)
- total_amount (decimal)
- status (enum: pending, processing, shipped, delivered, cancelled)
- shipping_address (text)
- created_at, updated_at

### جدول عربة التسوق (cart_items)
- id (primary key)
- user_id (foreign key)
- product_id (foreign key)
- quantity (integer)
- created_at, updated_at

## الميزات المقترحة للتطوير

### 1. نظام المستخدمين
- تسجيل الدخول والخروج
- ملف المستخدم الشخصي
- إدارة الطلبات

### 2. نظام المنتجات
- عرض المنتجات
- البحث والفلترة
- تفاصيل المنتج
- إدارة المخزون

### 3. نظام الطلبات
- إضافة المنتجات للعربة
- عملية الدفع
- تتبع الطلبات
- إشعارات البريد الإلكتروني

### 4. لوحة الإدارة
- إدارة المنتجات
- إدارة الفئات
- إدارة الطلبات
- إحصائيات المبيعات

## أدوات التطوير المفيدة

### 1. Laravel Debugbar
```bash
composer require barryvdh/laravel-debugbar --dev
```

### 2. Laravel Telescope
```bash
composer require laravel/telescope --dev
php artisan telescope:install
php artisan migrate
```

### 3. Laravel IDE Helper
```bash
composer require --dev barryvdh/laravel-ide-helper
php artisan ide-helper:generate
```

## اختبار المشروع

### 1. تشغيل الاختبارات
```bash
php artisan test
```

### 2. تشغيل الخادم للتطوير
```bash
php artisan serve
```

### 3. تشغيل Vite للـ Assets
```bash
npm run dev
# أو
npm run build
```

## نصائح للتطوير

1. **استخدم Git**: قم بإنشاء repository و commit التغييرات بانتظام
2. **اتبع Laravel Conventions**: استخدم الأسماء المتفق عليها في Laravel
3. **اكتب الاختبارات**: تأكد من كتابة اختبارات للوظائف المهمة
4. **استخدم Environment Variables**: لا تضع المعلومات الحساسة في الكود
5. **قم بتحسين الأداء**: استخدم Eager Loading و Caching عند الحاجة

## المراجع المفيدة
- [Laravel Documentation](https://laravel.com/docs)
- [Laravel Best Practices](https://github.com/alexeymezenin/laravel-best-practices)
- [Laravel Cheat Sheet](https://laravel.com/docs/cheatsheet)
