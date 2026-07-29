<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\User;
use App\Models\WorkActivity;
use App\Models\WorkFolder;
use App\Models\WorkTask;
use App\Models\WorkIdea;
use App\Support\WorkHub;
use Illuminate\Http\Request;

class WorkActivityController extends Controller
{
    public function index(Request $request)
    {
        $organizationId = WorkHub::organizationId($request);
        abort_unless($organizationId, 403);

        $query = WorkActivity::where('organization_id', $organizationId)
            ->withCount([
                'tasks',
                'tasks as done_tasks_count' => fn ($q) => $q->where('status', 'done'),
                'tasks as ready_to_publish_count' => fn ($q) => $q->where('pipeline_stage', 'ready_to_publish'),
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

        $viewMode = $request->input('view', 'title');
        if (! in_array($viewMode, ['title', 'month', 'folder'], true)) {
            $viewMode = 'title';
        }

        // "حسب الشهر" متاح من لوحة الويب فقط (مش من employee hub).
        if (WorkHub::isEmployeeHub($request) && $viewMode === 'month') {
            $viewMode = 'title';
        }

        $folders = WorkFolder::query()
            ->where('organization_id', $organizationId)
            ->orderBy('order')
            ->orderBy('title')
            ->get();

        $activitiesByMonth = collect();
        if ($viewMode === 'month') {
            $activitiesByMonth = $activities
                ->groupBy(function (WorkActivity $activity) {
                    $date = $activity->event_date ?? $activity->created_at;

                    return $date ? $date->format('Y-m') : '0000-00';
                })
                ->sortKeysDesc()
                ->map(function ($monthActivities, string $monthKey) {
                    $first = $monthActivities->first();

                    return [
                        'key' => $monthKey,
                        'label' => $monthKey === '0000-00' ? 'بدون تاريخ' : ($first?->month_label ?? $monthKey),
                        'activities' => $monthActivities->values(),
                    ];
                })
                ->values();
        }

        $activitiesByFolder = collect();
        if ($viewMode === 'folder') {
            $grouped = $activities->groupBy(fn (WorkActivity $a) => $a->folder_id ?: 0);

            $activitiesByFolder = $folders->map(function (WorkFolder $folder) use ($grouped) {
                return [
                    'folder' => $folder,
                    'activities' => ($grouped->get($folder->id) ?? collect())->values(),
                ];
            })->values();

            $unfiled = ($grouped->get(0) ?? collect())->values();
            // دائماً نظهر «بدون فولدر» كمنطقة إسقاط حتى لو فاضية
            $activitiesByFolder->push([
                'folder' => null,
                'activities' => $unfiled,
            ]);
        }

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
            'activitiesByMonth' => $activitiesByMonth,
            'activitiesByFolder' => $activitiesByFolder,
            'folders' => $folders,
            'viewMode' => $viewMode,
            'follow' => $follow,
            'types' => WorkActivity::types(),
            'statuses' => WorkActivity::statuses(),
            'filterType' => $request->type,
            'filterStatus' => $request->status,
            'canManageFolders' => WorkHub::canManageFolders($request),
            'showMonthView' => ! WorkHub::isEmployeeHub($request),
        ]);
    }

    public function store(Request $request)
    {
        $organizationId = WorkHub::organizationId($request);
        abort_unless($organizationId, 403);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:live_lecture,live_lecture_paid,paid_round,educational,other',
            'description' => 'nullable|string',
            'with_template' => 'nullable|boolean',
            'idea_id' => 'nullable|exists:work_ideas,id',
            'folder_id' => 'nullable|exists:work_folders,id',
        ]);

        $withTemplate = (bool) ($validated['with_template'] ?? false);
        unset($validated['with_template']);

        $ideaId = $validated['idea_id'] ?? null;
        unset($validated['idea_id']);

        $folderId = $validated['folder_id'] ?? null;
        unset($validated['folder_id']);
        if ($folderId) {
            $folder = WorkFolder::query()->findOrFail($folderId);
            abort_unless((int) $folder->organization_id === (int) $organizationId, 403);
            $validated['folder_id'] = (int) $folderId;
        }

        $actor = WorkHub::actor($request);
        $validated['organization_id'] = $organizationId;
        $validated['created_by'] = $actor instanceof User ? $actor->id : null;
        $validated['status'] = 'planning';
        // تاريخ النشاط = تاريخ إنشاء السجل تلقائيًا
        $validated['event_date'] = now()->toDateString();

        $activity = WorkActivity::create($validated);

        // توليد التاسكات القياسية للمحاضرة (حسب دليل تنظيم ملفات المحاضرة)
        $tasksCreated = 0;
        if ($withTemplate && $activity->is_lecture) {
            $tasksCreated = $this->createLectureTemplateTasks($activity);
        }

        // لو النشاط اتعمل من تحويل فكرة: امسح الفكرة بعد حفظ النشاط
        if ($ideaId) {
            WorkIdea::query()
                ->where('id', (int) $ideaId)
                ->where('organization_id', (int) $organizationId)
                ->delete();
        }

        $message = $tasksCreated > 0
            ? "تم إنشاء النشاط مع {$tasksCreated} مهمة قياسية موزّعة على الفريق"
            : 'تم إنشاء النشاط بنجاح';

        return redirect()->route(WorkHub::routeName('show'), $activity)->with('success', $message);
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
                'assigned_to' => WorkTask::suggestAssigneeId($activity->organization_id, 'publish')
                    ?? WorkTask::suggestAssigneeId($activity->organization_id, $template['kind']),
                'content_writer_id' => WorkTask::suggestAssigneeId($activity->organization_id, 'content'),
                'designer_id' => WorkTask::suggestAssigneeId($activity->organization_id, 'design'),
                'status' => 'todo',
                'pipeline_stage' => WorkTask::defaultPipelineStage(),
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

        $work->ensureShareToken();
        $work->load(['tasks.assignedEmployee', 'tasks.contentWriter', 'tasks.designer']);

        $employees = Employee::where('organization_id', WorkHub::organizationId($request))
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        $contentCounts = [
            'total' => $work->tasks->count(),
            'post' => $work->tasks->where('content_type', 'post')->count(),
            'reels' => $work->tasks->where('content_type', 'reels')->count(),
            'carousel' => $work->tasks->where('content_type', 'carousel')->count(),
            'other' => $work->tasks->filter(fn ($t) => ! in_array($t->content_type, ['post', 'reels', 'carousel'], true))->count(),
        ];

        $pipelineStages = [];
        foreach (WorkTask::pipelineStages() as $key => $label) {
            $stageTasks = $work->tasks
                ->where('pipeline_stage', $key)
                ->values();

            $pipelineStages[] = [
                'key' => $key,
                'label' => $label,
                'icon' => match ($key) {
                    'planning' => 'pending_actions',
                    'design' => 'palette',
                    'ready_to_publish' => 'schedule_send',
                    'published' => 'check_circle',
                    default => 'edit_note',
                },
                'color' => match ($key) {
                    'planning' => 'yellow',
                    'design' => 'purple',
                    'ready_to_publish' => 'teal',
                    'published' => 'green',
                    default => 'blue',
                },
                'tasks' => $stageTasks,
                'count' => $stageTasks->count(),
            ];
        }

        $designers = $employees->whereIn('role', ['designer', 'video_editor'])->values();
        $contentWriters = $employees->where('role', 'content_writer')->values();
        $publishers = $employees->whereIn('role', ['account_manager', 'page_manager'])->values();
        if ($publishers->isEmpty()) {
            $publishers = $employees->whereIn('role', ['account_manager', 'page_manager', 'media_buyer', 'ad_manager'])->values();
        }

        return view('work.show', [
            'activity' => $work,
            'employees' => $employees,
            'designers' => $designers,
            'contentWriters' => $contentWriters,
            'publishers' => $publishers,
            'pipelineStages' => $pipelineStages,
            'contentCounts' => $contentCounts,
            'kinds' => WorkTask::kinds(),
            'taskStatuses' => WorkTask::statuses(),
            'activityStatuses' => WorkActivity::statuses(),
            'kindRoleMap' => WorkTask::kindRoleMap(),
            'contentTypes' => WorkTask::contentTypes(),
            'platforms' => WorkTask::platforms(),
        ]);
    }

    public function update(Request $request, WorkActivity $work)
    {
        $this->authorizeActivity($request, $work);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:live_lecture,live_lecture_paid,paid_round,educational,other',
            'description' => 'nullable|string',
            'event_date' => 'nullable|date',
            'status' => 'required|in:planning,in_progress,done,cancelled',
        ]);

        $work->update($validated);

        return redirect()->route(WorkHub::routeName('show'), $work)->with('success', 'تم تحديث النشاط');
    }

    public function destroy(Request $request, WorkActivity $work)
    {
        $this->authorizeActivity($request, $work);

        $result = app(\App\Services\DesignFileArchiver::class)->archiveActivity($work);

        return redirect()
            ->route(WorkHub::routeName('index'))
            ->with('success', 'تم حذف النشاط و'.$result['tasks'].' تاسك — الملفات اتنقلت لفولدر deleted ('.$result['files'].' ملف)');
    }

    public function enableShare(Request $request, WorkActivity $work)
    {
        $this->authorizeActivity($request, $work);
        $work->ensureShareToken();

        if ($request->boolean('redirect_to_gallery')) {
            return redirect()
                ->route('public.work.gallery', $work->share_token)
                ->with('success', 'تم تفعيل الرابط العام وفتح معرض التصميم');
        }

        return back()->with('success', 'تم تفعيل الرابط العام — انسخه وشاركه');
    }

    public function regenerateShare(Request $request, WorkActivity $work)
    {
        $this->authorizeActivity($request, $work);
        $work->regenerateShareToken();

        return back()->with('success', 'تم تجديد الرابط العام — الرابط السابق لم يعد يعمل');
    }

    public function disableShare(Request $request, WorkActivity $work)
    {
        $this->authorizeActivity($request, $work);
        $work->disableShareToken();

        return back()->with('success', 'تم إيقاف الرابط العام');
    }

    private function authorizeActivity(Request $request, WorkActivity $work): void
    {
        WorkHub::authorizeOrganization($request, (int) $work->organization_id);
    }
}
