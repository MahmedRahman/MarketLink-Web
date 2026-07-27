<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\PlanTask;
use App\Models\WorkActivity;
use App\Models\WorkTask;
use App\Models\WorkTaskFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class EmployeeWorkTaskController extends Controller
{
    /**
     * مساحة العمل للموظف: فولدرات الأنشطة اللي فيها مهام مطلوبة منه.
     */
    public function index(Request $request)
    {
        $employee = Auth::guard('employee')->user();

        if ($employee->isWorkHubAdmin()) {
            return redirect()->route('employee.hub.index');
        }

        $myTasks = WorkTask::forEmployeeCurrentStage($employee->id)
            ->with('activity')
            ->get();

        $activityIds = $myTasks->pluck('work_activity_id')->unique()->filter()->values();

        $activitiesQuery = WorkActivity::whereIn('id', $activityIds)
            ->where('organization_id', $employee->organization_id);

        if ($request->filled('type')) {
            $activitiesQuery->where('type', $request->type);
        }
        if ($request->filled('status')) {
            $activitiesQuery->where('status', $request->status);
        }

        $activities = $activitiesQuery
            ->orderByRaw("CASE WHEN status = 'done' THEN 1 ELSE 0 END")
            ->orderBy('event_date')
            ->latest()
            ->get()
            ->map(function (WorkActivity $activity) use ($myTasks) {
                $tasks = $myTasks->where('work_activity_id', $activity->id)->values();
                $done = $tasks->where('status', 'done')->count();
                $total = $tasks->count();

                $activity->my_tasks_count = $total;
                $activity->my_done_tasks_count = $done;
                $activity->my_progress = $total > 0 ? (int) round(($done / $total) * 100) : 0;

                return $activity;
            });

        $follow = [
            'overdue' => $myTasks->filter(fn ($t) => $t->is_overdue)->values(),
            'in_progress' => $myTasks->where('status', 'in_progress')->values(),
            'review' => $myTasks->where('status', 'review')->values(),
            'todo' => $myTasks->where('status', 'todo')->values(),
        ];

        $planTasks = PlanTask::where('assigned_to', $employee->id)
            ->with(['monthlyPlan.project', 'goal'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('employee.work.index', [
            'activities' => $activities,
            'follow' => $follow,
            'types' => WorkActivity::types(),
            'statuses' => WorkActivity::statuses(),
            'filterType' => $request->type,
            'filterStatus' => $request->status,
            'planTasks' => $planTasks,
            'employee' => $employee,
        ]);
    }

    /**
     * عرض نشاط: بايبلاين بمهام الموظف فقط.
     */
    public function showActivity(WorkActivity $work)
    {
        $employee = Auth::guard('employee')->user();

        if ($employee->isWorkHubAdmin()) {
            return redirect()->route('employee.hub.show', $work);
        }

        abort_unless((int) $work->organization_id === (int) $employee->organization_id, 403);

        $work->ensureShareToken();

        $myTasks = WorkTask::forEmployeeCurrentStage($employee->id)
            ->where('work_activity_id', $work->id)
            ->with(['assignedEmployee', 'contentWriter', 'designer'])
            ->orderBy('order')
            ->orderBy('id')
            ->get();

        abort_unless($myTasks->isNotEmpty(), 403);

        $pipelineStages = [];
        foreach (WorkTask::pipelineStages() as $key => $label) {
            $stageTasks = $myTasks->where('pipeline_stage', $key)->values();

            $pipelineStages[] = [
                'key' => $key,
                'label' => $label,
                'icon' => match ($key) {
                    'design' => 'palette',
                    'ready_to_publish' => 'schedule_send',
                    'published' => 'check_circle',
                    default => 'edit_note',
                },
                'tasks' => $stageTasks,
                'count' => $stageTasks->count(),
            ];
        }

        $done = $myTasks->where('status', 'done')->count();
        $total = $myTasks->count();
        $progress = $total > 0 ? (int) round(($done / $total) * 100) : 0;

        $contentCounts = [
            'total' => $total,
            'post' => $myTasks->where('content_type', 'post')->count(),
            'reels' => $myTasks->where('content_type', 'reels')->count(),
            'carousel' => $myTasks->where('content_type', 'carousel')->count(),
            'other' => $myTasks->filter(fn ($t) => ! in_array($t->content_type, ['post', 'reels', 'carousel'], true))->count(),
        ];

        return view('employee.work.activity', [
            'activity' => $work,
            'pipelineStages' => $pipelineStages,
            'contentCounts' => $contentCounts,
            'progress' => $progress,
            'doneCount' => $done,
            'employee' => $employee,
        ]);
    }

    public function show(WorkTask $task)
    {
        $employee = Auth::guard('employee')->user();
        abort_unless($task->isVisibleToEmployee($employee->id), 403);

        $task->load(['activity', 'contentWriter', 'designer', 'assignedEmployee', 'files.task.activity']);
        if ($task->activity) {
            $task->activity->ensureShareToken();
        }

        return view('employee.work.show', [
            'task' => $task,
            'statuses' => WorkTask::statuses(),
            'designAssetKinds' => WorkTask::designAssetKinds(),
            'suggestedAssetKind' => WorkTask::suggestedDesignAssetKind($task->content_type),
            'cardShareUrl' => $task->public_share_url,
        ]);
    }

    public function updateStatus(Request $request, WorkTask $task)
    {
        $employee = Auth::guard('employee')->user();
        abort_unless($task->isVisibleToEmployee($employee->id), 403);

        $validated = $request->validate([
            'status' => 'required|in:todo,in_progress,review,done',
            'notes' => 'nullable|string',
        ]);

        $fromStatus = $task->status;
        $fromStage = $task->pipeline_stage;
        $fromAssignee = $task->assigned_to;

        $updates = [
            'status' => $validated['status'],
            'notes' => $validated['notes'] ?? $task->notes,
        ];

        // اكتمال المرحلة الحالية → نقل للمرحلة التالية
        if ($validated['status'] === 'done' && $fromStatus !== 'done') {
            $nextStage = WorkTask::nextPipelineStage($fromStage);
            if ($nextStage) {
                $orgId = (int) $employee->organization_id;
                $updates['pipeline_stage'] = $nextStage;

                if ($nextStage === 'design') {
                    if (! $task->designer_id) {
                        $updates['designer_id'] = WorkTask::suggestAssigneeId($orgId, 'design');
                    }
                    $updates['assigned_to'] = $updates['designer_id'] ?? $task->designer_id ?? $task->assigned_to;
                    $updates['status'] = 'review';
                } elseif ($nextStage === 'ready_to_publish') {
                    $updates['assigned_to'] = WorkTask::suggestAssigneeId($orgId, 'publish')
                        ?? $task->assigned_to;
                    $updates['status'] = 'review';
                } else { // published
                    $updates['assigned_to'] = WorkTask::suggestAssigneeId($orgId, 'publish')
                        ?? $task->assigned_to;
                    $updates['status'] = 'done';
                }
            }
        }

        $task->update($updates);
        $task->refresh();

        if ($fromStatus !== $task->status) {
            $task->logEvent(
                'status_changed',
                'تغيّرت الحالة من «'.(WorkTask::statuses()[$fromStatus] ?? $fromStatus).'» إلى «'.(WorkTask::statuses()[$task->status] ?? $task->status).'» بواسطة '.$employee->name,
                'status',
                $fromStatus,
                $task->status,
                ['employee_id' => $employee->id]
            );
        }

        if ($fromStage !== $task->pipeline_stage) {
            $task->logEvent(
                'stage_changed',
                'نُقل من «'.(WorkTask::pipelineStages()[$fromStage] ?? $fromStage).'» إلى «'.(WorkTask::pipelineStages()[$task->pipeline_stage] ?? $task->pipeline_stage).'» بعد اكتمال المرحلة بواسطة '.$employee->name,
                'pipeline_stage',
                $fromStage,
                $task->pipeline_stage,
                ['employee_id' => $employee->id]
            );
        }

        if ((int) $fromAssignee !== (int) $task->assigned_to) {
            $task->logEvent(
                'assignee_changed',
                'تغيّر المسؤول بعد اكتمال المرحلة بواسطة '.$employee->name,
                'assigned_to',
                $fromAssignee,
                $task->assigned_to,
                ['employee_id' => $employee->id]
            );
        }

        $message = 'تم تحديث حالة المهمة';
        if ($fromStage !== $task->pipeline_stage) {
            $message = 'تم اكتمال المرحلة ونقل المحتوى إلى «'.(WorkTask::pipelineStages()[$task->pipeline_stage] ?? $task->pipeline_stage).'»';
        }

        // لو المهمة اتقلت لمرحلة مش مسئوليته، رجّعه لصفحة النشاط
        if (! $task->isVisibleToEmployee($employee->id) && $task->work_activity_id) {
            return redirect()
                ->route('employee.work.activity', $task->work_activity_id)
                ->with('success', $message);
        }

        return redirect()->route('employee.work.show', $task)->with('success', $message);
    }

    public function moveStage(Request $request, WorkActivity $work, WorkTask $task)
    {
        $employee = Auth::guard('employee')->user();

        if ($employee->isWorkHubAdmin()) {
            return redirect()->route('employee.hub.show', $work);
        }

        abort_unless((int) $work->organization_id === (int) $employee->organization_id, 403);
        abort_unless((int) $task->work_activity_id === (int) $work->id, 404);
        abort_unless($task->isVisibleToEmployee($employee->id), 403);

        $validated = $request->validate([
            'pipeline_stage' => 'required|in:writing,design,ready_to_publish,published',
        ]);

        $stage = $validated['pipeline_stage'];
        $orgId = (int) $employee->organization_id;
        $updates = ['pipeline_stage' => $stage];

        if ($stage === 'writing') {
            $updates['assigned_to'] = $task->content_writer_id
                ?? WorkTask::suggestAssigneeId($orgId, 'content')
                ?? $task->assigned_to;
            $updates['status'] = $task->status === 'done' ? 'in_progress' : $task->status;
        } elseif ($stage === 'design') {
            if (! $task->designer_id) {
                $updates['designer_id'] = WorkTask::suggestAssigneeId($orgId, 'design');
            }
            $updates['assigned_to'] = $updates['designer_id'] ?? $task->designer_id ?? $task->assigned_to;
            $updates['status'] = 'review';
        } elseif ($stage === 'ready_to_publish') {
            $updates['assigned_to'] = WorkTask::suggestAssigneeId($orgId, 'publish')
                ?? $task->assigned_to;
            $updates['status'] = $task->status === 'done' ? 'review' : ($task->status ?: 'review');
        } else {
            $updates['assigned_to'] = WorkTask::suggestAssigneeId($orgId, 'publish')
                ?? $task->assigned_to;
            $updates['status'] = 'done';
        }

        $fromStage = $task->pipeline_stage;
        $fromStatus = $task->status;
        $fromAssignee = $task->assigned_to;

        $task->update($updates);
        $task->refresh();

        if ($fromStage !== $task->pipeline_stage) {
            $task->logEvent(
                'stage_changed',
                'نُقل من «'.(WorkTask::pipelineStages()[$fromStage] ?? $fromStage).'» إلى «'.(WorkTask::pipelineStages()[$task->pipeline_stage] ?? $task->pipeline_stage).'» بواسطة '.$employee->name,
                'pipeline_stage',
                $fromStage,
                $task->pipeline_stage,
                ['employee_id' => $employee->id]
            );
        }
        if ($fromStatus !== $task->status) {
            $task->logEvent(
                'status_changed',
                'تغيّرت الحالة من «'.(WorkTask::statuses()[$fromStatus] ?? $fromStatus).'» إلى «'.(WorkTask::statuses()[$task->status] ?? $task->status).'» بواسطة '.$employee->name,
                'status',
                $fromStatus,
                $task->status,
                ['employee_id' => $employee->id]
            );
        }
        if ((int) $fromAssignee !== (int) $task->assigned_to) {
            $task->logEvent(
                'assignee_changed',
                'تغيّر المسؤول بعد نقل المرحلة بواسطة '.$employee->name,
                'assigned_to',
                $fromAssignee,
                $task->assigned_to,
                ['employee_id' => $employee->id]
            );
        }

        $stillVisible = $task->isVisibleToEmployee($employee->id);
        $message = 'تم نقل المحتوى إلى مرحلة «'.(WorkTask::pipelineStages()[$stage] ?? $stage).'»';

        return response()->json([
            'success' => true,
            'message' => $message,
            'pipeline_stage' => $stage,
            'task_id' => $task->id,
            'removed' => ! $stillVisible,
        ]);
    }

    public function reorder(Request $request, WorkActivity $work)
    {
        $employee = Auth::guard('employee')->user();

        if ($employee->isWorkHubAdmin()) {
            return redirect()->route('employee.hub.show', $work);
        }

        abort_unless((int) $work->organization_id === (int) $employee->organization_id, 403);

        $validated = $request->validate([
            'pipeline_stage' => 'required|in:writing,design,ready_to_publish,published',
            'task_ids' => 'required|array|min:1',
            'task_ids.*' => 'integer',
        ]);

        $taskIds = array_values(array_unique(array_map('intval', $validated['task_ids'])));
        $stage = $validated['pipeline_stage'];

        $visibleIds = WorkTask::forEmployeeCurrentStage($employee->id)
            ->where('work_activity_id', $work->id)
            ->where('pipeline_stage', $stage)
            ->whereIn('id', $taskIds)
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();

        // بعد النقل لمرحلة جديدة، التاسك ممكن يبقى مش visible للموظف — نسمح بأي ID تابع للنشاط
        $ownedIds = $work->tasks()
            ->whereIn('id', $taskIds)
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();

        if (count($ownedIds) !== count($taskIds)) {
            return response()->json(['success' => false, 'message' => 'بعض المهام غير موجودة'], 422);
        }

        foreach ($taskIds as $index => $taskId) {
            WorkTask::where('work_activity_id', $work->id)
                ->where('id', $taskId)
                ->update(['order' => $index + 1]);
        }

        return response()->json([
            'success' => true,
            'message' => 'تم حفظ الترتيب',
            'pipeline_stage' => $stage,
            'task_ids' => $taskIds,
            'visible_ids' => $visibleIds,
        ]);
    }

    public function uploadFile(Request $request, WorkTask $task)
    {
        $employee = Auth::guard('employee')->user();
        abort_unless($task->isVisibleToEmployee($employee->id), 403);

        $validated = $request->validate([
            'asset_kind' => 'required|in:image,video,pdf',
            'files' => 'required|array|min:1|max:30',
            'files.*' => 'file|max:102400',
            'description' => 'nullable|string|max:500',
        ]);

        $result = app(\App\Services\DesignFileUploader::class)->uploadMany(
            $task,
            $request->file('files', []),
            $validated['asset_kind'],
            $validated['description'] ?? null,
            null
        );

        $kindLabel = WorkTask::designAssetKinds()[$validated['asset_kind']] ?? $validated['asset_kind'];
        $folderNote = $result['folder'] ? ' داخل فولدر '.$result['folder'] : '';
        $task->logEvent(
            'file_uploaded',
            'تم رفع '.$result['count'].' ملف تصميم ('.$kindLabel.')'.$folderNote.' بواسطة '.$employee->name,
            'file',
            null,
            (string) $result['count'],
            [
                'asset_kind' => $validated['asset_kind'],
                'employee_id' => $employee->id,
                'batch' => $result['batch'],
                'folder' => $result['folder'],
            ]
        );

        $message = $result['count'] === 1
            ? 'تم رفع ملف التصميم'
            : 'تم رفع '.$result['count'].' ملفات في فولدر واحد';
        if (config('academy_nas.enabled')) {
            $message .= ' — هيتنسخوا لسيرفر الملفات خلال لحظات';
        }

        return redirect()
            ->route('employee.work.show', $task)
            ->with('success', $message);
    }

    public function downloadFile(WorkTask $task, WorkTaskFile $file)
    {
        $employee = Auth::guard('employee')->user();
        abort_unless($task->isVisibleToEmployee($employee->id), 403);
        abort_unless($file->work_task_id === $task->id, 404);
        abort_unless(Storage::disk('public')->exists($file->file_path), 404);

        return Storage::disk('public')->download($file->file_path, $file->file_name);
    }

    public function deleteFile(WorkTask $task, WorkTaskFile $file)
    {
        $employee = Auth::guard('employee')->user();
        abort_unless($task->isVisibleToEmployee($employee->id), 403);
        abort_unless($file->work_task_id === $task->id, 404);

        $fileName = $file->file_name;
        $result = app(\App\Services\DesignFileArchiver::class)->archiveFile($file, true);

        $task->logEvent(
            'file_deleted',
            'تم أرشفة ملف تصميم إلى deleted: '.$fileName.' بواسطة '.$employee->name,
            'file',
            $fileName,
            null,
            [
                'employee_id' => $employee->id,
                'local' => $result['local'],
                'nas' => $result['nas'],
            ]
        );

        return redirect()
            ->route('employee.work.show', $task)
            ->with('success', 'تم نقل الملف لفولدر deleted على الموقع وسيرفر الملفات');
    }
}
