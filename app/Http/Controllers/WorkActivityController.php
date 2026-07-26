<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\WorkActivity;
use App\Models\WorkTask;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WorkActivityController extends Controller
{
    public function index(Request $request)
    {
        $organizationId = $request->user()->organization_id;

        $query = WorkActivity::where('organization_id', $organizationId)
            ->withCount([
                'tasks',
                'tasks as done_tasks_count' => fn ($q) => $q->where('status', 'done'),
            ])
            ->with('tasks');

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $activities = $query->orderByRaw("CASE WHEN status = 'done' THEN 1 ELSE 0 END")
            ->orderBy('event_date')
            ->latest()
            ->get();

        // متابعة عامة عبر كل الأنشطة
        $allTasks = WorkTask::whereHas('activity', fn ($q) => $q->where('organization_id', $organizationId))
            ->with(['activity', 'assignedEmployee'])
            ->get();

        $follow = [
            'overdue' => $allTasks->filter(fn ($t) => $t->is_overdue)->values(),
            'in_progress' => $allTasks->where('status', 'in_progress')->values(),
            'review' => $allTasks->where('status', 'review')->values(),
            'unassigned' => $allTasks->whereNull('assigned_to')->where('status', '!=', 'done')->values(),
        ];

        return view('work.index', [
            'activities' => $activities,
            'follow' => $follow,
            'types' => WorkActivity::types(),
            'statuses' => WorkActivity::statuses(),
            'filterType' => $request->type,
            'filterStatus' => $request->status,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:free_lecture,live_lecture,paid_round,educational,other',
            'description' => 'nullable|string',
            'event_date' => 'nullable|date',
            'with_template' => 'nullable|boolean',
        ]);

        $withTemplate = (bool) ($validated['with_template'] ?? false);
        unset($validated['with_template']);

        $validated['organization_id'] = $request->user()->organization_id;
        $validated['created_by'] = $request->user()->id;
        $validated['status'] = 'planning';

        $activity = WorkActivity::create($validated);

        // توليد التاسكات القياسية للمحاضرة (حسب دليل تنظيم ملفات المحاضرة)
        $tasksCreated = 0;
        if ($withTemplate && $activity->is_lecture) {
            $tasksCreated = $this->createLectureTemplateTasks($activity);
        }

        $message = $tasksCreated > 0
            ? "تم إنشاء النشاط مع {$tasksCreated} مهمة قياسية موزّعة على الفريق"
            : 'تم إنشاء النشاط بنجاح';

        return redirect()->route('work.show', $activity)->with('success', $message);
    }

    /**
     * ينشئ التاسكات القياسية للمحاضرة المجانية ويعيّنها حسب الدور،
     * بمواعيد نسبية لتاريخ المحاضرة.
     */
    private function createLectureTemplateTasks(WorkActivity $activity): int
    {
        $order = 0;
        foreach (WorkActivity::lectureTaskTemplate() as $template) {
            $dueDate = $activity->event_date
                ? $activity->event_date->copy()->addDays($template['offset'])->toDateString()
                : null;

            WorkTask::create([
                'work_activity_id' => $activity->id,
                'title' => $template['title'],
                'idea' => $template['idea'],
                'kind' => $template['kind'],
                'content_type' => $template['content_type'] ?? null,
                'platforms' => $template['platforms'] ?? null,
                'assigned_to' => WorkTask::suggestAssigneeId($activity->organization_id, $template['kind']),
                'content_writer_id' => WorkTask::suggestAssigneeId($activity->organization_id, 'content'),
                'designer_id' => WorkTask::suggestAssigneeId($activity->organization_id, 'design'),
                'status' => 'todo',
                'due_date' => $dueDate,
                'publish_date' => ! empty($template['content_type']) ? $dueDate : null,
                'order' => ++$order,
            ]);
        }

        return $order;
    }

    public function show(Request $request, WorkActivity $work)
    {
        $this->authorizeActivity($request, $work);

        $work->load(['tasks.assignedEmployee', 'tasks.contentWriter', 'tasks.designer']);

        $employees = Employee::where('organization_id', $request->user()->organization_id)
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        $employeesById = $employees->keyBy('id');
        $taskGroups = $this->buildTaskGroupsByPerson($work->tasks, $employeesById);

        return view('work.show', [
            'activity' => $work,
            'employees' => $employees,
            'taskGroups' => $taskGroups,
            'kinds' => WorkTask::kinds(),
            'taskStatuses' => WorkTask::statuses(),
            'activityStatuses' => WorkActivity::statuses(),
            'kindRoleMap' => WorkTask::kindRoleMap(),
            'contentTypes' => WorkTask::contentTypes(),
            'platforms' => WorkTask::platforms(),
        ]);
    }

    /**
     * يجمع التاسكات في جروبات حسب الشخص المطلوب منه (كاتب / مصمم / معيّن).
     */
    private function buildTaskGroupsByPerson($tasks, $employeesById): array
    {
        $buckets = [];

        foreach ($tasks as $task) {
            $rolesByEmployee = [];

            if ($task->content_writer_id) {
                $rolesByEmployee[$task->content_writer_id][] = 'كاتب محتوى';
            }
            if ($task->designer_id) {
                $rolesByEmployee[$task->designer_id][] = 'مصمم';
            }
            if ($task->assigned_to && ! isset($rolesByEmployee[$task->assigned_to])) {
                $rolesByEmployee[$task->assigned_to][] = 'مسؤول';
            }

            if ($rolesByEmployee === []) {
                $key = 'unassigned';
                if (! isset($buckets[$key])) {
                    $buckets[$key] = [
                        'key' => $key,
                        'employee' => null,
                        'name' => 'غير معيّن',
                        'role_label' => null,
                        'items' => [],
                    ];
                }
                $buckets[$key]['items'][] = [
                    'task' => $task,
                    'roles' => [],
                ];
                continue;
            }

            foreach ($rolesByEmployee as $employeeId => $roles) {
                $key = (string) $employeeId;
                if (! isset($buckets[$key])) {
                    $employee = $employeesById->get($employeeId);
                    if (! $employee) {
                        if ((int) $task->content_writer_id === (int) $employeeId) {
                            $employee = $task->contentWriter;
                        } elseif ((int) $task->designer_id === (int) $employeeId) {
                            $employee = $task->designer;
                        } else {
                            $employee = $task->assignedEmployee;
                        }
                    }

                    $buckets[$key] = [
                        'key' => $key,
                        'employee' => $employee,
                        'name' => $employee?->name ?? 'موظف #'.$employeeId,
                        'role_label' => $employee?->role_badge ?? null,
                        'items' => [],
                    ];
                }

                $buckets[$key]['items'][] = [
                    'task' => $task,
                    'roles' => array_values(array_unique($roles)),
                ];
            }
        }

        // رتب: الأشخاص أولاً بالاسم، ثم غير معيّن في الآخر
        uasort($buckets, function ($a, $b) {
            if ($a['key'] === 'unassigned') {
                return 1;
            }
            if ($b['key'] === 'unassigned') {
                return -1;
            }

            return strcmp($a['name'], $b['name']);
        });

        return array_values($buckets);
    }

    public function update(Request $request, WorkActivity $work)
    {
        $this->authorizeActivity($request, $work);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:free_lecture,live_lecture,paid_round,educational,other',
            'description' => 'nullable|string',
            'event_date' => 'nullable|date',
            'status' => 'required|in:planning,in_progress,done,cancelled',
        ]);

        $work->update($validated);

        return redirect()->route('work.show', $work)->with('success', 'تم تحديث النشاط');
    }

    public function destroy(Request $request, WorkActivity $work)
    {
        $this->authorizeActivity($request, $work);

        $work->delete();

        return redirect()->route('work.index')->with('success', 'تم حذف النشاط');
    }

    private function authorizeActivity(Request $request, WorkActivity $work): void
    {
        abort_unless($work->organization_id === $request->user()->organization_id, 403);
    }
}
