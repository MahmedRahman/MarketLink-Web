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

        $task->load(['activity', 'contentWriter', 'designer', 'assignedEmployee', 'files']);

        return view('employee.work.show', [
            'task' => $task,
            'statuses' => WorkTask::statuses(),
            'designAssetKinds' => WorkTask::designAssetKinds(),
            'suggestedAssetKind' => WorkTask::suggestedDesignAssetKind($task->content_type),
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

        $task->update($validated);

        return redirect()->route('employee.work.show', $task)->with('success', 'تم تحديث حالة المهمة');
    }

    public function uploadFile(Request $request, WorkTask $task)
    {
        $employee = Auth::guard('employee')->user();
        abort_unless($task->isVisibleToEmployee($employee->id), 403);

        $validated = $request->validate([
            'asset_kind' => 'required|in:image,video,pdf',
            'file' => 'required|file|max:102400',
            'description' => 'nullable|string|max:500',
        ]);

        $file = $request->file('file');
        $ext = strtolower($file->getClientOriginalExtension());
        $mime = (string) $file->getMimeType();

        $allowed = match ($validated['asset_kind']) {
            'image' => ['jpg', 'jpeg', 'png', 'gif', 'webp'],
            'video' => ['mp4', 'mov', 'webm', 'm4v'],
            'pdf' => ['pdf'],
        };

        if (! in_array($ext, $allowed, true)) {
            return back()->with('error', 'امتداد الملف غير مناسب لنوع التصميم المختار');
        }

        $mimeOk = match ($validated['asset_kind']) {
            'image' => str_starts_with($mime, 'image/'),
            'video' => str_starts_with($mime, 'video/') || in_array($mime, ['application/octet-stream'], true),
            'pdf' => in_array($mime, ['application/pdf', 'application/octet-stream'], true),
        };
        if (! $mimeOk) {
            return back()->with('error', 'نوع الملف غير مدعوم');
        }

        $path = $file->store('work-tasks/'.$task->id, 'public');

        $workFile = WorkTaskFile::create([
            'work_task_id' => $task->id,
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'file_type' => $ext,
            'asset_kind' => $validated['asset_kind'],
            'file_size' => $file->getSize(),
            'uploaded_by' => null,
            'description' => $validated['description'] ?? null,
        ]);

        $taskId = $task->id;
        $workFileId = $workFile->id;
        $nasEnabled = (bool) config('academy_nas.enabled');

        if ($nasEnabled) {
            dispatch(function () use ($taskId, $workFileId) {
                $taskModel = WorkTask::query()->find($taskId);
                $fileModel = WorkTaskFile::query()->find($workFileId);
                if ($taskModel && $fileModel) {
                    app(\App\Services\AcademyNasStorage::class)->syncQuietly($taskModel, $fileModel);
                }
            })->afterResponse();
        }

        $kindLabel = WorkTask::designAssetKinds()[$validated['asset_kind']] ?? $validated['asset_kind'];
        $task->logEvent(
            'file_uploaded',
            'تم رفع ملف تصميم ('.$kindLabel.'): '.$workFile->file_name.' بواسطة '.$employee->name
                .($nasEnabled ? ' — جاري النسخ إلى NAS' : ''),
            'file',
            null,
            $workFile->file_name,
            [
                'asset_kind' => $validated['asset_kind'],
                'employee_id' => $employee->id,
            ]
        );

        $message = 'تم رفع ملف التصميم';
        if ($nasEnabled) {
            $message .= ' — هيتنسخ لسيرفر الملفات خلال لحظات';
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

        if ($file->file_path && Storage::disk('public')->exists($file->file_path)) {
            Storage::disk('public')->delete($file->file_path);
        }
        $file->delete();

        $task->logEvent(
            'file_deleted',
            'تم حذف ملف تصميم: '.$file->file_name.' بواسطة '.$employee->name,
            'file',
            $file->file_name,
            null,
            ['employee_id' => $employee->id]
        );

        return redirect()
            ->route('employee.work.show', $task)
            ->with('success', 'تم حذف الملف');
    }
}
