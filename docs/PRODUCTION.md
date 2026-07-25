# MarketLink Internal (marketlink-web) — Production Notes

آخر تحديث: 2026-07-25

النظام الداخلي لتقسيم الشغل داخل الشركة (عملاء، مشاريع، خطط شهرية، مهام، بوابة موظفين).

---

## الروابط

| النوع | الرابط |
|------|--------|
| GitHub | https://github.com/MahmedRahman/MarketLink-Web |
| الوصول الداخلي (LAN) | http://192.168.68.223:8007 |
| الوصول العام (بعد ربط الـ tunnel) | https://app.marketlink.app |
| الـ landing العام (مشروع منفصل) | https://marketlink.app — ريبو `marketlink` على منفذ 8006 |

## السيرفر

| البند | القيمة |
|------|--------|
| SSH | `test@192.168.68.223` |
| المسار | `/home/test/marketlink-web` |
| الحاوية | `marketlink_web_app` (منفذ `8007:80`) |
| DB | SQLite — `database/database.sqlite` |
| الباك أب | `~/backups/YYYYMMDD/` (كود + sqlite النظام القديم) |

## حسابات الفريق

seeded عبر `database/seeders/MarketLinkTeamSeeder.php` — منظمة `marketlink` باشتراك داخلي فعّال.

| الاسم | الدور | البوابة | الإيميل |
|------|------|---------|---------|
| محمد عبد الرحمن | أدمن | `/login` | mohamed@marketlink.app |
| مها رافت | أدمن | `/login` | maha@marketlink.app |
| الاء رآفت | أكونت منجر + نشر سوشيال | `/employee/login` | alaa@marketlink.app |
| نيرة | كتابة محتوى | `/employee/login` | nira@marketlink.app |
| يوسف محمد | مصمم | `/employee/login` | youssef@marketlink.app |
| مريم | مصممة | `/employee/login` | mariam@marketlink.app |
| نفين عبد الله | فيديو | `/employee/login` | nevin@marketlink.app |

كلمات المرور المؤقتة في الـ seeder — تُغيَّر بعد أول دخول.

## مسار توزيع الشغل

1. الأدمن ينشئ/يتابع العملاء والمشاريع (المستوردة من النظام القديم: 11 عميل، 7 مشاريع)
2. لكل مشروع: خطة شهرية (Monthly Plan) بأهداف (بوستات/ريلز/إعلانات…)
3. توليد/إضافة مهام (Plan Tasks) وتعيينها: كتابة → نيرة، تصميم → يوسف/مريم، فيديو → نفين
4. كل موظف يشوف مهامه من بوابة الموظف ويسلّم (ملفات/تعليقات)
5. الجاهز للنشر يظهر في قائمة publish → ألاء تنشر على السوشيال

## النشر

```bash
ssh test@192.168.68.223
cd /home/test/marketlink-web && ./deploy.sh
```

ملاحظات:
- assets الـ Vite مبنية محليًا و committed في `public/build` (السيرفر لا يصل لـ npm registry بشكل موثوق) — بعد تعديل CSS/JS شغّل `npm run build` محليًا واعمل commit
- الاستيراد من النظام القديم: `php artisan marketlink:import-legacy storage/legacy.sqlite` (آمن لإعادة التشغيل)
- بعد تعديل `.env` على السيرفر: `docker compose up -d --force-recreate` (الحاوية تقرأ env عند الإنشاء)

## Cloudflare Tunnel (خطوة يدوية)

الـ tunnel الحالي token-managed. لإتاحة `app.marketlink.app`:
Cloudflare Zero Trust → Networks → Tunnels → التونل الحالي → Public Hostname → Add:
`app.marketlink.app` → Service: `http://localhost:8007`
