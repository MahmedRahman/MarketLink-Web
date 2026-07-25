<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\Organization;
use App\Models\User;
use App\Models\WorkActivity;
use App\Models\WorkTask;
use Illuminate\Database\Seeder;

/**
 * نشاط تجريبي لمساحة عمل الأكاديمية: محاضرة لايف مع مهام موزّعة حسب الدور.
 * آمن لإعادة التشغيل (idempotent) عبر updateOrCreate على العنوان.
 */
class AcademyWorkSeeder extends Seeder
{
    public function run(): void
    {
        $organization = Organization::where('slug', 'marketlink')->first();
        if (! $organization) {
            $this->command?->warn('AcademyWorkSeeder: organization "marketlink" not found — skipping.');

            return;
        }

        $admin = User::where('organization_id', $organization->id)->where('is_admin', true)->first();

        $activity = WorkActivity::updateOrCreate(
            [
                'organization_id' => $organization->id,
                'title' => 'محاضرة لايف — أساسيات التسويق الرقمي',
            ],
            [
                'created_by' => $admin?->id,
                'type' => 'live_lecture',
                'description' => "محاضرة لايف تعريفية.\n- تصميم بوستر إعلاني\n- فيديو تشويقي قصير\n- كتابة وصف المحاضرة\n- نشر على السوشيال",
                'event_date' => now()->addDays(5)->toDateString(),
                'status' => 'in_progress',
            ]
        );

        $byRole = fn (string $role) => Employee::where('organization_id', $organization->id)
            ->where('status', 'active')->where('role', $role)->value('id');

        $tasks = [
            ['title' => 'تصميم بوستر إعلان المحاضرة', 'kind' => 'design', 'role' => 'designer'],
            ['title' => 'فيديو تشويقي قصير (Reel)', 'kind' => 'video', 'role' => 'video_editor'],
            ['title' => 'كتابة وصف وكابشن المحاضرة', 'kind' => 'content', 'role' => 'content_writer'],
            ['title' => 'نشر المحتوى على حسابات السوشيال', 'kind' => 'publish', 'role' => 'account_manager'],
        ];

        foreach ($tasks as $i => $t) {
            WorkTask::updateOrCreate(
                ['work_activity_id' => $activity->id, 'title' => $t['title']],
                [
                    'kind' => $t['kind'],
                    'assigned_to' => $byRole($t['role']),
                    'status' => 'todo',
                    'due_date' => now()->addDays(3)->toDateString(),
                    'order' => $i + 1,
                ]
            );
        }

        $this->command?->info('AcademyWorkSeeder: sample live-lecture activity with 4 role-assigned tasks seeded.');
    }
}
