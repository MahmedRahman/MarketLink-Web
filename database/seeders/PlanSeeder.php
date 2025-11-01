<?php

namespace Database\Seeders;

use App\Models\Plan;
use App\Models\PlanFeature;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plans = [
            [
                'name' => 'بيسك',
                'slug' => 'basic',
                'description' => 'خطة أساسية للمشاريع الصغيرة',
                'price_egp' => 99.00,
                'duration_days' => 30,
                'is_active' => true,
                'sort_order' => 1,
                'features' => [
                    'إدارة العملاء',
                    'إدارة المشاريع',
                    'تقارير أساسية',
                    'دعم فني',
                ],
            ],
            [
                'name' => 'بروفيشنال',
                'slug' => 'professional',
                'description' => 'خطة احترافية للشركات المتوسطة',
                'price_egp' => 199.00,
                'duration_days' => 30,
                'is_active' => true,
                'sort_order' => 2,
                'features' => [
                    'كل ميزات البيسك',
                    'تقارير متقدمة',
                    'إدارة الموظفين',
                    'دعم فني أولوية',
                    'تصدير البيانات',
                ],
            ],
            [
                'name' => 'إنتربرايز',
                'slug' => 'enterprise',
                'description' => 'خطة شاملة للشركات الكبيرة',
                'price_egp' => 499.00,
                'duration_days' => 30,
                'is_active' => true,
                'sort_order' => 3,
                'features' => [
                    'كل ميزات البروفيشنال',
                    'API مخصص',
                    'دعم مخصص 24/7',
                    'تدريب فريق',
                    'تخصيص النظام',
                ],
            ],
        ];

        foreach ($plans as $planData) {
            $features = $planData['features'];
            unset($planData['features']);

            $plan = Plan::updateOrCreate(
                ['slug' => $planData['slug']],
                $planData
            );

            // حذف الميزات القديمة وإضافة الجديدة
            $plan->features()->delete();

            foreach ($features as $index => $featureName) {
                PlanFeature::create([
                    'plan_id' => $plan->id,
                    'feature_name' => $featureName,
                    'order' => $index,
                ]);
            }
        }
    }
}
