# دليل إعداد GitHub Webhook - MarketLink Web

## 🚀 نظرة عامة

تم إعداد نظام webhook آمن ومحسن لـ MarketLink Web ليعمل auto-deploy تلقائياً عند push جديد على GitHub.

## ✅ المكونات المُنجزة

### 1. WebhookController
- ✅ التحقق من صحة GitHub signature
- ✅ معالجة webhook events
- ✅ تنفيذ أوامر deployment آمنة
- ✅ تسجيل مفصل للعمليات

### 2. Deploy Script
- ✅ سكريبت bash محسن للـ deployment
- ✅ ألوان وتنسيق جميل للـ output
- ✅ معالجة الأخطاء
- ✅ تحسينات Laravel

### 3. Security Middleware
- ✅ التحقق من صحة GitHub signature
- ✅ حماية من الهجمات
- ✅ تسجيل محاولات الوصول غير المصرح بها

### 4. Routes Configuration
- ✅ `/webhook/github` - لاستقبال GitHub webhooks
- ✅ `/webhook/status` - لفحص حالة النظام

## 🔧 إعداد GitHub Webhook

### 1. إنشاء GitHub Webhook

1. **اذهب إلى GitHub Repository**:
   - اذهب إلى إعدادات الـ repository
   - اضغط على "Settings" → "Webhooks"
   - اضغط على "Add webhook"

2. **إعداد Webhook**:
   ```
   Payload URL: https://yourdomain.com/webhook/github
   Content type: application/json
   Secret: [أدخل secret قوي]
   Events: Just the push event
   Active: ✅
   ```

3. **اختيار Events**:
   - ✅ Push events فقط
   - ❌ لا تختار events أخرى

### 2. إعداد Environment Variables

أضف المتغيرات التالية إلى `.env`:

```env
# GitHub Webhook Secret
GITHUB_WEBHOOK_SECRET=your_strong_secret_here

# Application Version
APP_VERSION=1.0.0
```

### 3. إعداد Server Permissions

تأكد من أن الـ server لديه الصلاحيات التالية:

```bash
# إعطاء صلاحيات للمجلدات
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# إعطاء صلاحيات للسكريبت
chmod +x deploy.sh
```

## 🔒 الأمان

### 1. GitHub Signature Verification
```php
private function verifyGitHubSignature(Request $request): bool
{
    $signature = $request->header('X-Hub-Signature-256');
    $payload = $request->getContent();
    $secret = config('app.github_webhook_secret');
    
    if (!$signature || !$secret) {
        return false;
    }

    $expectedSignature = 'sha256=' . hash_hmac('sha256', $payload, $secret);
    
    return hash_equals($expectedSignature, $signature);
}
```

### 2. Security Features
- ✅ **Signature Verification**: التحقق من صحة GitHub signature
- ✅ **IP Logging**: تسجيل IP addresses
- ✅ **Request Logging**: تسجيل جميع الطلبات
- ✅ **Error Handling**: معالجة الأخطاء بشكل آمن

## 🚀 عملية الـ Deployment

### 1. تلقائية عند Push
```bash
# عند push جديد على main/master branch
git push origin main

# سيتم تنفيذ الأوامر التالية تلقائياً:
git pull origin main
composer install --no-dev --optimize-autoloader
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan migrate --force
php artisan cache:clear
php artisan queue:restart
```

### 2. Manual Deployment
```bash
# تشغيل السكريبت يدوياً
./deploy.sh
```

## 📊 مراقبة الـ Deployment

### 1. Logs
```bash
# عرض logs
tail -f storage/logs/laravel.log

# أو
php artisan log:show
```

### 2. Status Check
```bash
# فحص حالة النظام
curl https://yourdomain.com/webhook/status
```

### 3. Response Format
```json
{
    "status": "active",
    "last_deployment": "2024-01-01T12:00:00.000Z",
    "environment": "production",
    "version": "1.0.0"
}
```

## 🔧 استكشاف الأخطاء

### 1. مشاكل شائعة

#### Webhook لا يعمل
```bash
# تحقق من الـ logs
tail -f storage/logs/laravel.log

# تحقق من الـ routes
php artisan route:list | grep webhook
```

#### مشاكل في الصلاحيات
```bash
# إصلاح الصلاحيات
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

#### مشاكل في Composer
```bash
# تحديث Composer
composer self-update
composer install --no-dev --optimize-autoloader
```

### 2. اختبار Webhook

#### اختبار محلي
```bash
# اختبار الـ webhook محلياً
curl -X POST http://localhost:8000/webhook/github \
  -H "Content-Type: application/json" \
  -H "X-Hub-Signature-256: sha256=..." \
  -d '{"ref":"refs/heads/main","head_commit":{"id":"test"}}'
```

#### اختبار من GitHub
- اذهب إلى GitHub repository
- اضغط على "Settings" → "Webhooks"
- اضغط على webhook الذي أنشأته
- اضغط على "Recent Deliveries"
- تحقق من الـ responses

## 📱 إشعارات

### 1. Slack Integration
```php
// إضافة إلى WebhookController
private function notifySlack($message)
{
    $webhookUrl = config('app.slack_webhook_url');
    if ($webhookUrl) {
        // Send notification to Slack
    }
}
```

### 2. Email Notifications
```php
// إضافة إلى WebhookController
private function notifyEmail($message)
{
    Mail::raw($message, function ($mail) {
        $mail->to(config('app.admin_email'))
             ->subject('Deployment Notification');
    });
}
```

## 🎯 أفضل الممارسات

### 1. Security
- ✅ استخدم secret قوي ومختلف لكل environment
- ✅ لا تشارك الـ secret في الكود
- ✅ استخدم HTTPS فقط
- ✅ راقب الـ logs بانتظام

### 2. Performance
- ✅ استخدم `--no-dev` في production
- ✅ فعّل جميع الـ caches
- ✅ راقب استخدام الذاكرة
- ✅ استخدم queue workers

### 3. Monitoring
- ✅ راقب الـ deployment logs
- ✅ تحقق من حالة النظام بانتظام
- ✅ استخدم monitoring tools
- ✅ راقب الأداء

## 🔄 التحديثات المستقبلية

### 1. ميزات مخططة
- 🔄 **Database Backups**: نسخ احتياطية قبل الـ deployment
- 🔄 **Rollback System**: إمكانية الرجوع للنسخة السابقة
- 🔄 **Health Checks**: فحص صحة النظام بعد الـ deployment
- 🔄 **Notifications**: إشعارات للـ deployment

### 2. تحسينات
- 🔄 **Parallel Deployment**: deployment متوازي
- 🔄 **Blue-Green Deployment**: deployment بدون downtime
- 🔄 **Canary Deployment**: deployment تدريجي
- 🔄 **A/B Testing**: اختبار A/B

## 📞 الدعم

### 1. المشاكل الشائعة
- **Webhook لا يعمل**: تحقق من الـ secret والـ URL
- **Permission Denied**: تحقق من صلاحيات الملفات
- **Composer Errors**: تحقق من إصدار PHP وComposer
- **Database Errors**: تحقق من إعدادات قاعدة البيانات

### 2. الحصول على المساعدة
- راجع الـ logs في `storage/logs/laravel.log`
- تحقق من إعدادات الـ environment
- تأكد من صحة الـ GitHub webhook
- تحقق من صلاحيات الـ server

---
**تاريخ الإنشاء**: $(date)
**حالة المشروع**: ✅ جاهز للاستخدام
**الخطوة التالية**: إعداد GitHub webhook وإضافة الـ secret
