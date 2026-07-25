<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\Organization;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * فريق MarketLink الداخلي:
 * - محمد / مها: أدمن (User على مستوى المنظمة + is_admin)
 * - ألاء: أكونت منجر + نشر على السوشيال (Employee: account_manager)
 * - نيرة: كتابة محتوى (Employee: content_writer)
 * - يوسف / مريم: تصميم (Employee: designer)
 * - نفين: فيديو (Employee: video_editor)
 *
 * كلمات المرور مؤقتة — تُغيَّر بعد أول تسجيل دخول.
 */
class MarketLinkTeamSeeder extends Seeder
{
    public function run(): void
    {
        $organization = Organization::updateOrCreate(
            ['slug' => 'marketlink'],
            [
                'name' => 'MarketLink',
                'email' => 'info@marketlink.app',
                'is_active' => true,
            ]
        );

        // اشتراك داخلي دائم حتى لا يقف CheckTrialStatus في وجه الفريق
        Subscription::updateOrCreate(
            ['organization_id' => $organization->id, 'status' => 'active'],
            [
                'starts_at' => now(),
                'ends_at' => now()->addYears(10),
            ]
        );

        $admins = [
            ['name' => 'محمد عبد الرحمن', 'email' => 'mohamed@marketlink.app', 'password' => 'Mohamed@2026'],
            ['name' => 'مها رافت', 'email' => 'maha@marketlink.app', 'password' => 'Maha@2026'],
        ];

        foreach ($admins as $admin) {
            User::updateOrCreate(
                ['email' => $admin['email']],
                [
                    'name' => $admin['name'],
                    'password' => Hash::make($admin['password']),
                    'email_verified_at' => now(),
                    'organization_id' => $organization->id,
                    'is_admin' => true,
                    'status' => 'active',
                ]
            );
        }

        $employees = [
            [
                'name' => 'الاء رآفت',
                'email' => 'alaa@marketlink.app',
                'password' => 'Alaa@2026',
                'role' => 'account_manager',
                'phone' => '01002089079',
                'notes' => 'أكونت منجر + مسؤولة النشر على حسابات السوشيال ميديا',
            ],
            [
                'name' => 'نيرة',
                'email' => 'nira@marketlink.app',
                'password' => 'Nira@2026',
                'role' => 'content_writer',
                'phone' => null,
                'notes' => 'كتابة المحتوى',
            ],
            [
                'name' => 'يوسف محمد',
                'email' => 'youssef@marketlink.app',
                'password' => 'Youssef@2026',
                'role' => 'designer',
                'phone' => '01009905189',
                'notes' => 'إنتاج محتوى: تصميمات',
            ],
            [
                'name' => 'مريم',
                'email' => 'mariam@marketlink.app',
                'password' => 'Mariam@2026',
                'role' => 'designer',
                'phone' => null,
                'notes' => 'إنتاج محتوى: تصميمات',
            ],
            [
                'name' => 'نفين عبد الله',
                'email' => 'nevin@marketlink.app',
                'password' => 'Nevin@2026',
                'role' => 'video_editor',
                'phone' => '01003980947',
                'notes' => 'إنتاج محتوى: فيديوهات',
            ],
        ];

        foreach ($employees as $employee) {
            Employee::updateOrCreate(
                ['email' => $employee['email']],
                [
                    'name' => $employee['name'],
                    'password' => Hash::make($employee['password']),
                    'role' => $employee['role'],
                    'status' => 'active',
                    'organization_id' => $organization->id,
                    'phone' => $employee['phone'],
                    'notes' => $employee['notes'],
                ]
            );
        }

        $this->command?->info('MarketLink team seeded: 2 admins + 5 employees (org: marketlink).');
    }
}
