# MarketLink Internal (marketlink-web) — Production Notes

آخر تحديث: 2026-07-25

النظام الداخلي لتقسيم الشغل داخل الشركة (عملاء، مشاريع، خطط شهرية، مهام، بوابة موظفين).

---

## الروابط

| النوع | الرابط |
|------|--------|
| GitHub | https://github.com/MahmedRahman/MarketLink-Web |
| الموقع الحي | https://marketlink.app |
| الوصول الداخلي (LAN) | http://192.168.68.223:8006 |
| النسخة القديمة (احتياطي) | ريبو `marketlink` على منفذ `8008` |

## السيرفر

| البند | القيمة |
|------|--------|
| SSH | `test@192.168.68.223` |
| المسار | `/home/test/marketlink-web` |
| الحاوية | `marketlink_web_app` (منفذ `8006:80`) |
| DB | SQLite — `database/database.sqlite` |
| الباك أب | `~/backups/YYYYMMDD/` (كود + sqlite النظام القديم) |

> **مهم:** Cloudflare Tunnel مربوط بـ `localhost:8006`. لذلك النظام الداخلي يعمل على `8006` ليظهر على `https://marketlink.app`.

## حسابات الفريق

seeded عبر `database/seeders/MarketLinkTeamSeeder.php` — منظمة `marketlink` باشتراك داخلي فعّال.

الدخول موحّد من `/login` (أدمن + موظفين).

| الاسم | الدور | الإيميل |
|------|------|---------|
| محمد عبد الرحمن | أدمن | mohamed@marketlink.app |
| مها رافت | أدمن | maha@marketlink.app |
| الاء رآفت | أكونت منجر + نشر سوشيال | alaa@marketlink.app |
| نيرة | كتابة محتوى | nira@marketlink.app |
| يوسف محمد | مصمم | youssef@marketlink.app |
| مريم | مصممة | mariam@marketlink.app |
| نفين عبد الله | فيديو | nevin@marketlink.app |

كلمات المرور المؤقتة في [`docs/TEAM_CREDENTIALS.md`](./TEAM_CREDENTIALS.md).

## مسار توزيع الشغل

1. الأدمن ينشئ/يتابع العملاء والمشاريع (المستوردة من النظام القديم)
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

## Cloudflare Tunnel

الـ tunnel token-managed ويشير إلى `http://localhost:8006` → `marketlink.app`.
لا تغيّر منفذ الحاوية عن `8006` إلا بعد تعديل Public Hostname في Cloudflare.
