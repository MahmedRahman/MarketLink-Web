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
     * شاشة رقابة موحّدة للمدير: كل موظف والمهام الحالية المسندة له.
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        abort_unless($user && $user->is_admin, 403);

        $orgId = (int) $user->organization_id;

        $stages = WorkTask::pipelineStages();
        $contentTypes = WorkTask::contentTypes();
        $roleLabels = $this->roleLabels();

        // أول زيارة بدون فلاتر → المهام النشطة الحالية فقط
        $filters = [
            'stage' => $request->string('stage')->toString() ?: null,
            'state' => $request->has('state')
                ? ($request->string('state')->toString() ?: null)
                : 'active',
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

        $tasks = $tasks->filter(function (WorkTask $task) use ($filters) {
            return match ($filters['state']) {
                'active' => $task->status !== 'done' && ! in_array($task->pipeline_stage, ['published', 'archived'], true),
                'overdue' => $task->is_overdue,
                'done' => $task->status === 'done' || in_array($task->pipeline_stage, ['published', 'archived'], true),
                default => true,
            };
        })->values();

        // صف واحد لكل تاسك تحت الموظف المسؤول الحالي عن مرحلته
        $rows = collect();
        foreach ($tasks as $task) {
            $assignment = $this->currentAssignmentForTask($task);
            if (! $assignment) {
                continue;
            }

            $rows->push([
                'employee_id' => $assignment['employee_id'],
                'employee' => $assignment['employee'],
                'task_role' => $assignment['task_role'],
                'task_role_label' => $assignment['task_role_label'],
                'task' => $task,
            ]);
        }

        if ($filters['employee_id']) {
            $employees = $employees->where('id', $filters['employee_id'])->values();
            $rows = $rows->where('employee_id', $filters['employee_id'])->values();
        }

        if ($filters['role'] && array_key_exists($filters['role'], $roleLabels)) {
            $employees = $employees->where('role', $filters['role'])->values();
            $rows = $rows->filter(function (array $row) use ($filters) {
                $emp = $row['employee'];

                return $emp && $emp->role === $filters['role'];
            })->values();
        }

        $rowsByEmployee = $rows->groupBy('employee_id');

        $narrowFilters = $filters['q'] || $filters['stage'] || $filters['activity_id']
            || in_array($filters['state'], ['overdue', 'done'], true);

        $grouped = $employees
            ->map(function (Employee $employee) use ($rowsByEmployee) {
                $taskRows = ($rowsByEmployee->get($employee->id) ?? collect())->values();
                $activeCount = $taskRows->filter(
                    fn ($r) => $r['task']->status !== 'done'
                        && ! in_array($r['task']->pipeline_stage, ['published', 'archived'], true)
                )->count();
                $overdueCount = $taskRows->filter(fn ($r) => $r['task']->is_overdue)->count();

                return [
                    'employee' => $employee,
                    'employee_id' => (int) $employee->id,
                    'rows' => $taskRows,
                    'active_count' => $activeCount,
                    'overdue_count' => $overdueCount,
                    'total_count' => $taskRows->count(),
                ];
            })
            ->when($narrowFilters, fn (Collection $groups) => $groups->filter(fn ($g) => $g['total_count'] > 0)->values())
            ->sortByDesc(fn ($g) => [$g['overdue_count'], $g['active_count'], $g['total_count']])
            ->values();

        $idleEmployees = $grouped
            ->filter(fn ($g) => $g['active_count'] === 0)
            ->map(fn ($g) => $g['employee'])
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
            'employees' => Employee::query()
                ->where('organization_id', $orgId)
                ->where('status', 'active')
                ->orderBy('name')
                ->get(),
            'kpis' => $kpis,
            'idleEmployees' => $idleEmployees,
            'showIdleSection' => false,
        ]);
    }

    /**
     * المهمة الحالية «مع» الموظف = مسؤول مرحلتها الآن.
     *
     * @return array{employee_id:int,employee:Employee,task_role:string,task_role_label:string}|null
     */
    private function currentAssignmentForTask(WorkTask $task): ?array
    {
        $ownerId = $task->stageOwnerId();
        if (! $ownerId) {
            return null;
        }

        $employee = null;
        if ((int) $task->content_writer_id === (int) $ownerId) {
            $employee = $task->contentWriter;
        } elseif ((int) $task->designer_id === (int) $ownerId) {
            $employee = $task->designer;
        } elseif ((int) $task->assigned_to === (int) $ownerId) {
            $employee = $task->assignedEmployee;
        }

        if (! $employee) {
            return null;
        }

        [$taskRole, $taskRoleLabel] = match ($task->pipeline_stage) {
            'planning' => ['planning', 'تخطيط'],
            'writing' => ['content_writer', 'كاتب محتوى'],
            'design' => ['designer', 'مصمم'],
            'ready_to_publish', 'published', 'archived' => ['publisher', 'ناشر'],
            default => ['assignee', 'المسؤول الحالي'],
        };

        return [
            'employee_id' => (int) $ownerId,
            'employee' => $employee,
            'task_role' => $taskRole,
            'task_role_label' => $taskRoleLabel,
        ];
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
