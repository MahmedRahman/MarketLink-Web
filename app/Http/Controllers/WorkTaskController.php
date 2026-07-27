<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\WorkActivity;
use App\Models\WorkTask;
use App\Models\WorkTaskFile;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class WorkTaskController extends Controller
{
    public function show(Request $request, WorkActivity $work, WorkTask $task)
    {
        $this->authorizeActivity($request, $work);
        $this->authorizeTask($work, $task);

        $task->load([
            'contentWriter',
            'designer',
            'assignedEmployee',
            'files',
            'logs.user',
        ]);

        $employees = Employee::where('organization_id', $request->user()->organization_id)
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        return view('work.tasks.show', [
            'activity' => $work,
            'task' => $task,
            'employees' => $employees,
            'kinds' => WorkTask::kinds(),
            'taskStatuses' => WorkTask::statuses(),
            'contentTypes' => WorkTask::contentTypes(),
            'platforms' => WorkTask::platforms(),
            'designAssetKinds' => WorkTask::designAssetKinds(),
            'suggestedAssetKind' => WorkTask::suggestedDesignAssetKind($task->content_type),
        ]);
    }

    public function edit(Request $request, WorkActivity $work, WorkTask $task)
    {
        $this->authorizeActivity($request, $work);
        $this->authorizeTask($work, $task);

        $employees = Employee::where('organization_id', $request->user()->organization_id)
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        return view('work.tasks.edit', [
            'activity' => $work,
            'task' => $task,
            'employees' => $employees,
            'kinds' => WorkTask::kinds(),
            'taskStatuses' => WorkTask::statuses(),
            'contentTypes' => WorkTask::contentTypes(),
            'platforms' => WorkTask::platforms(),
        ]);
    }

    public function store(Request $request, WorkActivity $work)
    {
        $this->authorizeActivity($request, $work);

        $validated = $this->validateContentTask($request);
        $validated = $this->normalizePlatforms($validated);

        $this->ensureOptionalEmployeesInOrg($request, $validated);

        // اقتراح الموظف تلقائيًا حسب الدور إذا لم يُختر أحد
        if (empty($validated['assigned_to'])) {
            $validated['assigned_to'] = $this->suggestAssignee($request, $validated['kind']);
        }

        // اقتراح كاتب/مصمم حسب الدور إن لم يُحددا
        if (empty($validated['content_writer_id'])) {
            $validated['content_writer_id'] = WorkTask::suggestAssigneeId(
                $request->user()->organization_id,
                'content'
            );
        }
        if (empty($validated['designer_id'])) {
            $validated['designer_id'] = WorkTask::suggestAssigneeId(
                $request->user()->organization_id,
                'design'
            );
        }

        $validated['work_activity_id'] = $work->id;
        $validated['status'] = 'todo';
        $validated['pipeline_stage'] = $validated['pipeline_stage'] ?? 'writing';
        $validated['order'] = ($work->tasks()->max('order') ?? 0) + 1;

        // في مرحلة الكتابة: المعيّن الحالي = كاتب المحتوى
        if (($validated['pipeline_stage'] ?? 'writing') === 'writing' && ! empty($validated['content_writer_id'])) {
            $validated['assigned_to'] = $validated['content_writer_id'];
        }

        $task = WorkTask::create($validated);
        $task->logEvent(
            'created',
            'تم إنشاء المحتوى في مرحلة «'.(WorkTask::pipelineStages()[$task->pipeline_stage] ?? 'كتابة المحتوى').'»',
            'pipeline_stage',
            null,
            $task->pipeline_stage,
            ['status' => $task->status]
        );

        return redirect()->route('work.show', $work)->with('success', 'تمت إضافة المهمة');
    }

    /**
     * لصق نص كامل → DeepSeek يقسّمه لتاسكات محتوى بدون إعادة صياغة المطلوب.
     */
    public function parseBulk(Request $request, WorkActivity $work)
    {
        $this->authorizeActivity($request, $work);

        $validated = $request->validate([
            'bulk_text' => 'required|string|min:20|max:50000',
            'content_writer_id' => 'nullable|exists:employees,id',
            'designer_id' => 'nullable|exists:employees,id',
        ]);

        $this->ensureOptionalEmployeesInOrg($request, $validated);

        $orgId = $request->user()->organization_id;
        $writerId = $validated['content_writer_id']
            ?? WorkTask::suggestAssigneeId($orgId, 'content');
        $designerId = $validated['designer_id']
            ?? WorkTask::suggestAssigneeId($orgId, 'design');

        try {
            $parsed = $this->callDeepSeekSplitTasks($validated['bulk_text'], $work->title);
        } catch (\Throwable $e) {
            Log::error('WorkTask parseBulk failed', [
                'work_id' => $work->id,
                'error' => $e->getMessage(),
            ]);

            return redirect()->route('work.show', $work)
                ->with('error', $e->getMessage() ?: 'فشل تحليل النص. حاول مرة أخرى.');
        }

        if (empty($parsed)) {
            return redirect()->route('work.show', $work)
                ->with('error', 'لم يتم استخراج أي تاسكات من النص.');
        }

        $order = ($work->tasks()->max('order') ?? 0);
        $created = 0;

        foreach ($parsed as $item) {
            $title = trim((string) ($item['title'] ?? ''));
            if ($title === '') {
                continue;
            }

            $contentType = $item['content_type'] ?? null;
            if (! in_array($contentType, ['post', 'reels', 'carousel'], true)) {
                $contentType = null;
            }

            $platforms = $this->normalizePlatformList($item['platforms'] ?? []);

            $task = WorkTask::create([
                'work_activity_id' => $work->id,
                'title' => mb_substr($title, 0, 255),
                'idea' => $this->nullableString($item['idea'] ?? null),
                'tov' => $this->nullableString($item['tov'] ?? null),
                'caption' => $this->nullableString($item['caption'] ?? null),
                'content_type' => $contentType,
                'design_reference' => $this->nullableString($item['design_reference'] ?? null),
                'designer_brief' => $this->nullableString($item['designer_brief'] ?? null),
                'platforms' => $platforms,
                'kind' => 'content',
                'status' => 'todo',
                'pipeline_stage' => 'writing',
                'assigned_to' => $writerId,
                'content_writer_id' => $writerId,
                'designer_id' => $designerId,
                'publish_date' => $this->nullableDate($item['publish_date'] ?? null),
                'due_date' => $this->nullableDate($item['due_date'] ?? null),
                'order' => ++$order,
            ]);
            $task->logEvent(
                'created',
                'تم إنشاء المحتوى من لصق جماعي',
                'pipeline_stage',
                null,
                'writing',
                ['source' => 'parse_bulk']
            );
            $created++;
        }

        if ($created === 0) {
            return redirect()->route('work.show', $work)
                ->with('error', 'التحليل رجع بدون عناوين صالحة.');
        }

        return redirect()->route('work.show', $work)
            ->with('success', "تم إنشاء {$created} تاسك محتوى من النص");
    }

    /**
     * مساعد: يلخّص المطلوب من المصمم بدون تغيير مرجع التصميم الأصلي.
     */
    public function summarizeDesignerBrief(Request $request, WorkActivity $work, WorkTask $task): JsonResponse
    {
        $this->authorizeActivity($request, $work);
        $this->authorizeTask($work, $task);

        $source = trim(implode("\n\n", array_filter([
            $task->design_reference,
            $task->caption,
            $task->tov,
            $task->idea,
            $task->title,
        ])));

        if ($source === '') {
            return response()->json([
                'success' => false,
                'error' => 'لا يوجد محتوى كافٍ لتلخيص المطلوب من المصمم',
            ], 422);
        }

        try {
            $brief = $this->callDeepSeekDesignerBrief($source, $task->content_type);
        } catch (\Throwable $e) {
            Log::error('WorkTask summarizeDesignerBrief failed', [
                'task_id' => $task->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'error' => $e->getMessage() ?: 'فشل التلخيص',
            ], 500);
        }

        $task->update(['designer_brief' => $brief]);

        return response()->json([
            'success' => true,
            'designer_brief' => $brief,
        ]);
    }

    public function moveStage(Request $request, WorkActivity $work, WorkTask $task)
    {
        $this->authorizeActivity($request, $work);
        $this->authorizeTask($work, $task);

        $validated = $request->validate([
            'pipeline_stage' => 'required|in:writing,design,ready_to_publish,published',
            'designer_id' => 'nullable|exists:employees,id',
        ]);

        $stage = $validated['pipeline_stage'];
        $orgId = $request->user()->organization_id;
        $updates = ['pipeline_stage' => $stage];

        if ($stage === 'writing') {
            $updates['assigned_to'] = $task->content_writer_id
                ?? WorkTask::suggestAssigneeId($orgId, 'content');
            $updates['status'] = $task->status === 'done' ? 'in_progress' : $task->status;
        } elseif ($stage === 'design') {
            if (! empty($validated['designer_id'])) {
                $this->ensureEmployeeInOrg($request, (int) $validated['designer_id']);
                $updates['designer_id'] = (int) $validated['designer_id'];
            } elseif (! $task->designer_id) {
                $updates['designer_id'] = WorkTask::suggestAssigneeId($orgId, 'design');
            }
            $updates['assigned_to'] = $updates['designer_id'] ?? $task->designer_id;
            $updates['status'] = 'review';
        } elseif ($stage === 'ready_to_publish') {
            $updates['assigned_to'] = WorkTask::suggestAssigneeId($orgId, 'publish')
                ?? $task->assigned_to;
            $updates['status'] = $task->status === 'done' ? 'review' : ($task->status ?: 'review');
        } else { // published
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
                'نُقل من «'.(WorkTask::pipelineStages()[$fromStage] ?? $fromStage).'» إلى «'.(WorkTask::pipelineStages()[$task->pipeline_stage] ?? $task->pipeline_stage).'»',
                'pipeline_stage',
                $fromStage,
                $task->pipeline_stage
            );
        }
        if ($fromStatus !== $task->status) {
            $task->logEvent(
                'status_changed',
                'تغيّرت الحالة من «'.(WorkTask::statuses()[$fromStatus] ?? $fromStatus).'» إلى «'.(WorkTask::statuses()[$task->status] ?? $task->status).'»',
                'status',
                $fromStatus,
                $task->status
            );
        }
        if ((int) $fromAssignee !== (int) $task->assigned_to) {
            $fromName = $fromAssignee ? (Employee::find($fromAssignee)?->name ?? '#'.$fromAssignee) : 'غير معيّن';
            $toName = $task->assigned_to ? (Employee::find($task->assigned_to)?->name ?? '#'.$task->assigned_to) : 'غير معيّن';
            $task->logEvent(
                'assignee_changed',
                'تغيّر المسؤول من «'.$fromName.'» إلى «'.$toName.'»',
                'assigned_to',
                $fromAssignee,
                $task->assigned_to
            );
        }

        $message = 'تم نقل المحتوى إلى مرحلة «'.(WorkTask::pipelineStages()[$stage] ?? $stage).'»';

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'pipeline_stage' => $stage,
                'task_id' => $task->id,
            ]);
        }

        return back()->with('success', $message);
    }

    public function reorder(Request $request, WorkActivity $work)
    {
        $this->authorizeActivity($request, $work);

        $validated = $request->validate([
            'pipeline_stage' => 'required|in:writing,design,ready_to_publish,published',
            'task_ids' => 'required|array|min:1',
            'task_ids.*' => 'integer',
        ]);

        $taskIds = array_values(array_unique(array_map('intval', $validated['task_ids'])));
        $stage = $validated['pipeline_stage'];

        $ownedIds = $work->tasks()
            ->whereIn('id', $taskIds)
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();

        if (count($ownedIds) !== count($taskIds)) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'بعض المهام غير موجودة'], 422);
            }

            return back()->with('error', 'بعض المهام غير موجودة');
        }

        foreach ($taskIds as $index => $taskId) {
            WorkTask::where('work_activity_id', $work->id)
                ->where('id', $taskId)
                ->update([
                    'order' => $index + 1,
                ]);
        }

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'تم حفظ الترتيب',
                'pipeline_stage' => $stage,
                'task_ids' => $taskIds,
            ]);
        }

        return back()->with('success', 'تم حفظ الترتيب');
    }

    public function uploadFile(Request $request, WorkActivity $work, WorkTask $task)
    {
        $this->authorizeActivity($request, $work);
        $this->authorizeTask($work, $task);

        $validated = $request->validate([
            'asset_kind' => 'required|in:image,video,pdf',
            'file' => 'required|file|max:102400', // 100MB
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

        // تحقق خفيف من المايم
        $mimeOk = match ($validated['asset_kind']) {
            'image' => str_starts_with($mime, 'image/'),
            'video' => str_starts_with($mime, 'video/') || in_array($mime, ['application/octet-stream'], true),
            'pdf' => in_array($mime, ['application/pdf', 'application/octet-stream'], true),
        };
        if (! $mimeOk) {
            return back()->with('error', 'نوع الملف غير مدعوم');
        }

        $path = $file->store('work-tasks/'.$task->id, 'public');

        WorkTaskFile::create([
            'work_task_id' => $task->id,
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'file_type' => $ext,
            'asset_kind' => $validated['asset_kind'],
            'file_size' => $file->getSize(),
            'uploaded_by' => $request->user()->id,
            'description' => $validated['description'] ?? null,
        ]);

        $kindLabel = WorkTask::designAssetKinds()[$validated['asset_kind']] ?? $validated['asset_kind'];
        $task->logEvent(
            'file_uploaded',
            'تم رفع ملف تصميم ('.$kindLabel.'): '.$file->getClientOriginalName(),
            'file',
            null,
            $file->getClientOriginalName(),
            ['asset_kind' => $validated['asset_kind']]
        );

        return redirect()
            ->route('work.tasks.show', [$work, $task])
            ->with('success', 'تم رفع ملف التصميم');
    }

    public function deleteFile(Request $request, WorkActivity $work, WorkTask $task, WorkTaskFile $file)
    {
        $this->authorizeActivity($request, $work);
        $this->authorizeTask($work, $task);
        abort_unless($file->work_task_id === $task->id, 404);

        if ($file->file_path && Storage::disk('public')->exists($file->file_path)) {
            Storage::disk('public')->delete($file->file_path);
        }
        $file->delete();

        return redirect()
            ->route('work.tasks.show', [$work, $task])
            ->with('success', 'تم حذف الملف');
    }

    public function downloadFile(Request $request, WorkActivity $work, WorkTask $task, WorkTaskFile $file)
    {
        $this->authorizeActivity($request, $work);
        $this->authorizeTask($work, $task);
        abort_unless($file->work_task_id === $task->id, 404);
        abort_unless(Storage::disk('public')->exists($file->file_path), 404);

        return Storage::disk('public')->download($file->file_path, $file->file_name);
    }

    public function update(Request $request, WorkActivity $work, WorkTask $task)
    {
        $this->authorizeActivity($request, $work);
        $this->authorizeTask($work, $task);

        $validated = $this->validateContentTask($request, true);
        if (! $request->has('platforms')) {
            $validated['platforms'] = [];
        }
        $validated = $this->normalizePlatforms($validated);
        $validated['publish_links'] = $this->normalizePublishLinks($request, $validated['platforms'] ?? []);

        $this->ensureOptionalEmployeesInOrg($request, $validated);

        $before = $task->only(['status', 'pipeline_stage', 'assigned_to', 'content_writer_id', 'designer_id', 'title']);
        $task->update($validated);
        $this->logTaskFieldChanges($task, $before, $task->fresh()->only(array_keys($before)));

        if ($request->boolean('return_to_detail')) {
            return redirect()->route('work.tasks.show', [$work, $task])->with('success', 'تم تحديث المهمة');
        }

        if ($request->boolean('return_to_edit')) {
            return redirect()->route('work.tasks.edit', [$work, $task])->with('success', 'تم حفظ التعديلات');
        }

        return redirect()->route('work.show', $work)->with('success', 'تم تحديث المهمة');
    }

    public function assign(Request $request, WorkActivity $work, WorkTask $task)
    {
        $this->authorizeActivity($request, $work);
        $this->authorizeTask($work, $task);

        $validated = $request->validate([
            'assigned_to' => 'nullable|exists:employees,id',
            'employee_id' => 'nullable|exists:employees,id',
            'pipeline_stage' => 'nullable|in:writing,design,ready_to_publish,published',
        ]);

        $employeeId = $validated['employee_id'] ?? $validated['assigned_to'] ?? null;
        if ($employeeId) {
            $this->ensureEmployeeInOrg($request, (int) $employeeId);
        }

        $stage = $validated['pipeline_stage'] ?? $task->pipeline_stage;
        $updates = ['assigned_to' => $employeeId];

        if ($stage === 'writing') {
            $updates['content_writer_id'] = $employeeId;
        } elseif ($stage === 'design') {
            $updates['designer_id'] = $employeeId;
        }

        $fromAssignee = $task->assigned_to;
        $fromWriter = $task->content_writer_id;
        $fromDesigner = $task->designer_id;

        $task->update($updates);
        $task->load(['contentWriter', 'designer', 'assignedEmployee']);

        $owner = $task->owner_for_current_stage;
        $message = 'تم تحديث الموظف المسؤول';

        $fromName = $fromAssignee ? (Employee::find($fromAssignee)?->name ?? '#'.$fromAssignee) : 'غير معيّن';
        $toName = $owner?->name ?? ($employeeId ? '#'.$employeeId : 'غير معيّن');
        if ((int) $fromAssignee !== (int) $task->assigned_to
            || (int) $fromWriter !== (int) $task->content_writer_id
            || (int) $fromDesigner !== (int) $task->designer_id) {
            $roleLabel = match ($stage) {
                'design' => 'المصمم',
                'writing' => 'كاتب المحتوى',
                default => 'المسؤول',
            };
            $task->logEvent(
                'assignee_changed',
                'تم تعيين '.$roleLabel.' «'.$toName.'»'.($fromName !== $toName ? ' (كان: '.$fromName.')' : ''),
                $stage === 'design' ? 'designer_id' : ($stage === 'writing' ? 'content_writer_id' : 'assigned_to'),
                $stage === 'design' ? $fromDesigner : ($stage === 'writing' ? $fromWriter : $fromAssignee),
                $stage === 'design' ? $task->designer_id : ($stage === 'writing' ? $task->content_writer_id : $task->assigned_to),
                ['pipeline_stage' => $stage]
            );
        }

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'employee_id' => $employeeId,
                'employee_name' => $owner?->name,
                'pipeline_stage' => $stage,
            ]);
        }

        return back()->with('success', $message);
    }

    public function updatePublishLinks(Request $request, WorkActivity $work, WorkTask $task)
    {
        $this->authorizeActivity($request, $work);
        $this->authorizeTask($work, $task);

        $platforms = $task->platforms ?? [];
        $links = $this->normalizePublishLinks($request, $platforms);
        $before = $task->publish_links ?? [];
        $task->update(['publish_links' => $links]);

        $filled = collect($links)->filter()->count();
        $task->logEvent(
            'publish_links_updated',
            'تم تحديث روابط النشر ('.$filled.' رابط'.($filled === 1 ? '' : 'ات').')',
            'publish_links',
            json_encode($before, JSON_UNESCAPED_UNICODE),
            json_encode($links, JSON_UNESCAPED_UNICODE)
        );

        return redirect()
            ->route('work.tasks.show', [$work, $task])
            ->with('success', 'تم حفظ روابط النشر');
    }

    public function destroy(Request $request, WorkActivity $work, WorkTask $task)
    {
        $this->authorizeActivity($request, $work);
        $this->authorizeTask($work, $task);

        $task->delete();

        return redirect()->route('work.show', $work)->with('success', 'تم حذف المهمة');
    }

    private function callDeepSeekSplitTasks(string $bulkText, string $activityTitle): array
    {
        $apiKey = config('services.deepseek.api_key');
        if (! $apiKey) {
            throw new \RuntimeException('DeepSeek API key غير موجود');
        }

        $platformKeys = implode(', ', array_keys(WorkTask::platforms()));
        $typeKeys = implode(', ', array_keys(WorkTask::contentTypes()));

        $prompt = <<<PROMPT
أنت محلل محتوى لفريق تسويق. مهمتك فقط تقسيم النص التالي إلى وحدات محتوى منفصلة (كل بوست/ريلز/كروسيل = تاسك واحد).

النشاط: {$activityTitle}

قواعد صارمة جداً:
1) لا تعيد صياغة أي نص مطلوب. انسخ الكابشن و TOV ومرجع التصميم والنصوص كما هي حرفياً من المصدر.
2) لا تضف أفكاراً أو جمل جديدة غير موجودة في النص.
3) لو معلومة مش موجودة في النص، ضع null.
4) designer_brief فقط هو الملخص المسموح: نقاط مختصرة جداً (2-4) بما يحتاجه المصمم، مستنتجة من النص دون تغيير المعنى.
5) أرجع JSON فقط بدون Markdown أو شرح.

صيغة JSON المطلوبة:
{
  "tasks": [
    {
      "title": "عنوان قصير للتاسك",
      "idea": "نص الفكرة كما هو أو null",
      "tov": "Tone of Voice كما هو أو null",
      "caption": "الكابشن كما هو حرفياً أو null",
      "content_type": "واحد من: {$typeKeys} أو null",
      "design_reference": "تعليمات/مرجع التصميم كما هو حرفياً أو null",
      "designer_brief": "ملخص نقاط للمصمم أو null",
      "platforms": ["من: {$platformKeys}"],
      "publish_date": "YYYY-MM-DD أو null",
      "due_date": "YYYY-MM-DD أو null"
    }
  ]
}

النص المصدر:
{$bulkText}
PROMPT;

        $response = Http::timeout(90)
            ->withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer '.$apiKey,
            ])
            ->post('https://api.deepseek.com/v1/chat/completions', [
                'model' => 'deepseek-chat',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You extract structured content tasks from text. Never rewrite required copy. Return JSON only.',
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt,
                    ],
                ],
                'temperature' => 0.1,
            ]);

        if (! $response->successful()) {
            Log::error('DeepSeek parseBulk API error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            throw new \RuntimeException('فشل الاتصال بـ DeepSeek');
        }

        $aiResponse = $response->json('choices.0.message.content') ?? '';
        $data = $this->extractJson($aiResponse);

        if (! is_array($data)) {
            throw new \RuntimeException('فشل قراءة استجابة DeepSeek');
        }

        $tasks = $data['tasks'] ?? $data;
        if (! is_array($tasks)) {
            throw new \RuntimeException('صيغة التاسكات غير صحيحة');
        }

        // لو المصفوفة associative بعدد مفاتيح tasks فقط تم التعامل؛ تأكد أنها قائمة عناصر
        if (isset($tasks['title'])) {
            $tasks = [$tasks];
        }

        return array_values(array_filter($tasks, 'is_array'));
    }

    private function callDeepSeekDesignerBrief(string $source, ?string $contentType): string
    {
        $apiKey = config('services.deepseek.api_key');
        if (! $apiKey) {
            throw new \RuntimeException('DeepSeek API key غير موجود');
        }

        $typeLabel = $contentType
            ? (WorkTask::contentTypes()[$contentType] ?? $contentType)
            : 'محتوى';

        $prompt = <<<PROMPT
لخّص للمصمم المطلوب منه في نقاط مختصرة (2 إلى 4 نقاط) بالعربية.
القواعد:
- لا تغيّر المعنى ولا تخترع تفاصيل غير موجودة
- ركّز على: نوع التصميم، العناصر البصرية، النصوص الظاهرة، المقاس/المنصة إن وُجدت
- أرجع نص الملخص فقط بدون مقدمة

نوع المحتوى: {$typeLabel}

المحتوى المرجعي:
{$source}
PROMPT;

        $response = Http::timeout(60)
            ->withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer '.$apiKey,
            ])
            ->post('https://api.deepseek.com/v1/chat/completions', [
                'model' => 'deepseek-chat',
                'messages' => [
                    ['role' => 'user', 'content' => $prompt],
                ],
                'temperature' => 0.2,
            ]);

        if (! $response->successful()) {
            Log::error('DeepSeek designer brief API error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            throw new \RuntimeException('فشل الاتصال بـ DeepSeek');
        }

        $brief = trim((string) ($response->json('choices.0.message.content') ?? ''));
        if ($brief === '') {
            throw new \RuntimeException('رد فارغ من DeepSeek');
        }

        return $brief;
    }

    private function extractJson(string $text): ?array
    {
        $text = trim($text);
        $decoded = json_decode($text, true);
        if (is_array($decoded)) {
            return $decoded;
        }

        if (preg_match('/\{[\s\S]*\}/', $text, $m)) {
            $decoded = json_decode($m[0], true);
            if (is_array($decoded)) {
                return $decoded;
            }
        }

        if (preg_match('/\[[\s\S]*\]/', $text, $m)) {
            $decoded = json_decode($m[0], true);
            if (is_array($decoded)) {
                return ['tasks' => $decoded];
            }
        }

        return null;
    }

    private function normalizePlatformList(mixed $platforms): array
    {
        if (! is_array($platforms)) {
            return [];
        }

        $allowed = array_keys(WorkTask::platforms());

        return array_values(array_unique(array_filter(
            $platforms,
            fn ($p) => is_string($p) && in_array($p, $allowed, true)
        )));
    }

    private function nullableString(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }
        $text = trim((string) $value);
        if ($text === '' || strtolower($text) === 'null') {
            return null;
        }

        return $text;
    }

    private function nullableDate(mixed $value): ?string
    {
        $text = $this->nullableString($value);
        if (! $text || ! preg_match('/^\d{4}-\d{2}-\d{2}$/', $text)) {
            return null;
        }

        return $text;
    }

    private function logTaskFieldChanges(WorkTask $task, array $before, array $after): void
    {
        if (($before['status'] ?? null) !== ($after['status'] ?? null)) {
            $task->logEvent(
                'status_changed',
                'تغيّرت الحالة من «'.(WorkTask::statuses()[$before['status']] ?? $before['status']).'» إلى «'.(WorkTask::statuses()[$after['status']] ?? $after['status']).'»',
                'status',
                $before['status'] ?? null,
                $after['status'] ?? null
            );
        }

        if (($before['pipeline_stage'] ?? null) !== ($after['pipeline_stage'] ?? null)) {
            $task->logEvent(
                'stage_changed',
                'نُقل من «'.(WorkTask::pipelineStages()[$before['pipeline_stage']] ?? $before['pipeline_stage']).'» إلى «'.(WorkTask::pipelineStages()[$after['pipeline_stage']] ?? $after['pipeline_stage']).'»',
                'pipeline_stage',
                $before['pipeline_stage'] ?? null,
                $after['pipeline_stage'] ?? null
            );
        }

        foreach (['assigned_to' => 'المسؤول', 'content_writer_id' => 'كاتب المحتوى', 'designer_id' => 'المصمم'] as $field => $label) {
            if ((int) ($before[$field] ?? 0) === (int) ($after[$field] ?? 0)) {
                continue;
            }
            $fromName = ! empty($before[$field]) ? (Employee::find($before[$field])?->name ?? '#'.$before[$field]) : 'غير معيّن';
            $toName = ! empty($after[$field]) ? (Employee::find($after[$field])?->name ?? '#'.$after[$field]) : 'غير معيّن';
            $task->logEvent(
                'assignee_changed',
                'تغيّر '.$label.' من «'.$fromName.'» إلى «'.$toName.'»',
                $field,
                $before[$field] ?? null,
                $after[$field] ?? null
            );
        }

        if (($before['title'] ?? null) !== ($after['title'] ?? null)) {
            $task->logEvent(
                'updated',
                'تم تعديل العنوان',
                'title',
                $before['title'] ?? null,
                $after['title'] ?? null
            );
        }
    }

    private function validateContentTask(Request $request, bool $forUpdate = false): array
    {
        $rules = [
            'title' => 'required|string|max:255',
            'idea' => 'nullable|string',
            'tov' => 'nullable|string',
            'caption' => 'nullable|string',
            'content_type' => 'nullable|in:post,reels,carousel',
            'design_reference' => 'nullable|string',
            'designer_brief' => 'nullable|string',
            'platforms' => 'nullable|array',
            'platforms.*' => 'in:facebook,instagram,linkedin,tiktok,twitter',
            'publish_links' => 'nullable|array',
            'publish_links.*' => 'nullable|string|max:1000',
            'notes' => 'nullable|string',
            'kind' => 'required|in:design,video,content,publish,other',
            'assigned_to' => 'nullable|exists:employees,id',
            'content_writer_id' => 'nullable|exists:employees,id',
            'designer_id' => 'nullable|exists:employees,id',
            'due_date' => 'nullable|date',
            'publish_date' => 'nullable|date',
        ];

        if ($forUpdate) {
            $rules['status'] = 'required|in:todo,in_progress,review,done';
        }

        return $request->validate($rules);
    }

    private function normalizePlatforms(array $validated): array
    {
        $validated['platforms'] = array_values(array_unique($validated['platforms'] ?? []));

        return $validated;
    }

    private function normalizePublishLinks(Request $request, array $platforms): array
    {
        $allowed = array_keys(WorkTask::platforms());
        $raw = $request->input('publish_links', []);
        if (! is_array($raw)) {
            return [];
        }

        $links = [];
        foreach ($raw as $platform => $url) {
            $platform = (string) $platform;
            if (! in_array($platform, $allowed, true)) {
                continue;
            }
            // لو فيه منصات محددة، احفظ روابطها فقط
            if ($platforms && ! in_array($platform, $platforms, true)) {
                continue;
            }
            $url = trim((string) $url);
            if ($url === '') {
                continue;
            }
            $links[$platform] = $url;
        }

        return $links;
    }

    private function ensureOptionalEmployeesInOrg(Request $request, array $validated): void
    {
        foreach (['assigned_to', 'content_writer_id', 'designer_id'] as $field) {
            if (! empty($validated[$field])) {
                $this->ensureEmployeeInOrg($request, (int) $validated[$field]);
            }
        }
    }

    private function suggestAssignee(Request $request, string $kind): ?int
    {
        return WorkTask::suggestAssigneeId($request->user()->organization_id, $kind);
    }

    private function ensureEmployeeInOrg(Request $request, int $employeeId): void
    {
        $ok = Employee::where('id', $employeeId)
            ->where('organization_id', $request->user()->organization_id)
            ->exists();
        abort_unless($ok, 403);
    }

    private function authorizeActivity(Request $request, WorkActivity $work): void
    {
        abort_unless($work->organization_id === $request->user()->organization_id, 403);
    }

    private function authorizeTask(WorkActivity $work, WorkTask $task): void
    {
        abort_unless($task->work_activity_id === $work->id, 404);
    }
}
