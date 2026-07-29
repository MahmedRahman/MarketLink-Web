<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\WorkActivity;
use App\Models\WorkTask;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class TeamTasksMonitorController extends Controller
{
    /**
     * شاشة رقابة موحّدة للمدير: كل موظف وتاسكاته عبر كل الأنشطة.
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        abort_unless($user && $user->is_admin, 403);

        $orgId = (int) $user->organization_id;

        $stages = WorkTask::pipelineStages();
        $contentTypes = WorkTask::contentTypes();
        $roleLabels = $this->roleLabels();

        $filters = [
            'stage' => $request->string('stage')->toString() ?: null,
            'state' => $request->string('state')->toString() ?: null,
            'activity_id' => $request->filled('activity_id') ? (int) $request->input('activity_id') : null,
            'employee_id' => $request->filled('employee_id') ? (int) $request->input('employee_id') : null,
            'role' => $request->string('role')->toString() ?: null,
            'q' => trim((string) $request->input('q', '')) ?: null,
        ];

        $activities = WorkActivity::query()
            ->where('organization_id', $orgId)
            ->orderByDesc('created_at')
            ->get(['id', 'title']);

        $employees = Employee::query()
            ->where('organization_id', $orgId)
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        $tasksQuery = WorkTask::query()
            ->whereHas('activity', fn ($q) => $q->where('organization_id', $orgId))
            ->with(['activity:id,title,organization_id', 'contentWriter', 'designer', 'assignedEmployee']);

        if ($filters['stage'] && array_key_exists($filters['stage'], $stages)) {
            $tasksQuery->where('pipeline_stage', $filters['stage']);
        }

        if ($filters['activity_id']) {
            $tasksQuery->where('work_activity_id', $filters['activity_id']);
        }

        if ($filters['q']) {
            $q = $filters['q'];
            $tasksQuery->where(function ($query) use ($q) {
                $query->where('title', 'like', "%{$q}%")
                    ->orWhereHas('activity', fn ($aq) => $aq->where('title', 'like', "%{$q}%"));
            });
        }

        $tasks = $tasksQuery->orderBy('due_date')->orderBy('id')->get();

        // فلتر الحالة بعد التحميل (متأخرة تعتمد على accessor)
        $tasks = $tasks->filter(function (WorkTask $task) use ($filters) {
            return match ($filters['state']) {
                'active' => $task->status !== 'done' && ! in_array($task->pipeline_stage, ['published', 'archived'], true),
                'overdue' => $task->is_overdue,
                'done' => $task->status === 'done' || in_array($task->pipeline_stage, ['published', 'archived'], true),
                default => true,
            };
        })->values();

        // صفوف مشاركة: كل تاسكة تحت كل موظف مشارك مع دوره
        $rows = collect();
        foreach ($tasks as $task) {
            foreach ($this->participationsForTask($task) as $participation) {
                $rows->push([
                    'employee_id' => $participation['employee_id'],
                    'employee' => $participation['employee'],
                    'task_role' => $participation['task_role'],
                    'task_role_label' => $participation['task_role_label'],
                    'task' => $task,
                ]);
            }
        }

        if ($filters['employee_id']) {
            $rows = $rows->where('employee_id', $filters['employee_id'])->values();
        }

        if ($filters['role'] && array_key_exists($filters['role'], $roleLabels)) {
            $rows = $rows->filter(function (array $row) use ($filters) {
                $emp = $row['employee'];

                return $emp && $emp->role === $filters['role'];
            })->values();
        }

        $grouped = $rows
            ->groupBy('employee_id')
            ->map(function (Collection $employeeRows, $employeeId) {
                /** @var Employee|null $employee */
                $employee = $employeeRows->first()['employee'] ?? null;
                $taskRows = $employeeRows->values();
                $activeCount = $taskRows->filter(fn ($r) => $r['task']->status !== 'done' && ! in_array($r['task']->pipeline_stage, ['published', 'archived'], true))->count();
                $overdueCount = $taskRows->filter(fn ($r) => $r['task']->is_overdue)->count();

                return [
                    'employee' => $employee,
                    'employee_id' => (int) $employeeId,
                    'rows' => $taskRows,
                    'active_count' => $activeCount,
                    'overdue_count' => $overdueCount,
                    'total_count' => $taskRows->count(),
                ];
            })
            ->sortByDesc(fn ($g) => [$g['overdue_count'], $g['active_count'], $g['total_count']])
            ->values();

        // موظفون بدون مهام (للـ KPI "فاضي") — فقط عند عدم فلترة موظف/بحث ضيق
        $employeesWithTasks = $grouped->pluck('employee_id')->all();
        $idleEmployees = $employees
            ->reject(fn (Employee $e) => in_array((int) $e->id, $employeesWithTasks, true))
            ->values();

        $busiest = $grouped->sortByDesc('active_count')->first();

        $kpis = [
            'active_total' => $grouped->sum('active_count'),
            'overdue_total' => $grouped->sum('overdue_count'),
            'busiest' => $busiest && $busiest['active_count'] > 0 ? $busiest : null,
            'idle' => $idleEmployees->first(),
            'idle_count' => $idleEmployees->count(),
        ];

        return view('team-tasks.index', [
            'groups' => $grouped,
            'filters' => $filters,
            'stages' => $stages,
            'contentTypes' => $contentTypes,
            'roleLabels' => $roleLabels,
            'activities' => $activities,
            'employees' => $employees,
            'kpis' => $kpis,
            'idleEmployees' => $idleEmployees,
        ]);
    }

    /**
     * @return list<array{employee_id:int,employee:Employee,task_role:string,task_role_label:string}>
     */
    private function participationsForTask(WorkTask $task): array
    {
        $slots = [];

        if ($task->content_writer_id && $task->contentWriter) {
            $slots[] = [
                'employee_id' => (int) $task->content_writer_id,
                'employee' => $task->contentWriter,
                'task_role' => 'content_writer',
                'task_role_label' => 'كاتب محتوى',
            ];
        }

        if ($task->designer_id && $task->designer) {
            $slots[] = [
                'employee_id' => (int) $task->designer_id,
                'employee' => $task->designer,
                'task_role' => 'designer',
                'task_role_label' => 'مصمم',
            ];
        }

        if ($task->assigned_to && $task->assignedEmployee) {
            $isPublisher = in_array($task->pipeline_stage, ['ready_to_publish', 'published', 'archived'], true);
            $already = collect($slots)->contains(fn ($s) => $s['employee_id'] === (int) $task->assigned_to);

            // لو المسؤول الحالي مش ظاهر أصلاً ككاتب/مصمم، أظهره؛ أو لو مرحلة نشر أظهره كناشر حتى لو مكرر الدور
            if (! $already || $isPublisher) {
                if ($already && $isPublisher) {
                    // حدّث الدور لناشر إذا كان نفس الشخص
                    foreach ($slots as &$slot) {
                        if ($slot['employee_id'] === (int) $task->assigned_to) {
                            $slot['task_role'] = 'publisher';
                            $slot['task_role_label'] = 'ناشر';
                        }
                    }
                    unset($slot);
                } else {
                    $slots[] = [
                        'employee_id' => (int) $task->assigned_to,
                        'employee' => $task->assignedEmployee,
                        'task_role' => $isPublisher ? 'publisher' : 'assignee',
                        'task_role_label' => $isPublisher ? 'ناشر' : 'المسؤول الحالي',
                    ];
                }
            }
        }

        // فريد حسب employee_id (دور واحد لكل موظف في التاسكة)
        $unique = [];
        foreach ($slots as $slot) {
            $unique[$slot['employee_id']] = $slot;
        }

        return array_values($unique);
    }

    private function roleLabels(): array
    {
        return [
            'content_writer' => 'كاتب محتوى',
            'ad_manager' => 'إدارة إعلانات',
            'designer' => 'مصمم',
            'video_editor' => 'مصمم فيديوهات',
            'page_manager' => 'إدارة الصفحة',
            'account_manager' => 'أكونت منجر',
            'monitor' => 'مونتير',
            'media_buyer' => 'ميديا بايرز',
        ];
    }
}
