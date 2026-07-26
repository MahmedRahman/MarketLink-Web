<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\WorkActivity;
use App\Models\WorkTask;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WorkTaskController extends Controller
{
    public function show(Request $request, WorkActivity $work, WorkTask $task)
    {
        $this->authorizeActivity($request, $work);
        $this->authorizeTask($work, $task);

        $task->load(['contentWriter', 'designer', 'assignedEmployee']);

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
        $validated['order'] = ($work->tasks()->max('order') ?? 0) + 1;

        WorkTask::create($validated);

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

            WorkTask::create([
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
                'assigned_to' => $writerId,
                'content_writer_id' => $writerId,
                'designer_id' => $designerId,
                'publish_date' => $this->nullableDate($item['publish_date'] ?? null),
                'due_date' => $this->nullableDate($item['due_date'] ?? null),
                'order' => ++$order,
            ]);
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

    public function update(Request $request, WorkActivity $work, WorkTask $task)
    {
        $this->authorizeActivity($request, $work);
        $this->authorizeTask($work, $task);

        $validated = $this->validateContentTask($request, true);
        if (! $request->has('platforms')) {
            $validated['platforms'] = [];
        }
        $validated = $this->normalizePlatforms($validated);

        $this->ensureOptionalEmployeesInOrg($request, $validated);

        $task->update($validated);

        if ($request->boolean('return_to_detail')) {
            return redirect()->route('work.tasks.show', [$work, $task])->with('success', 'تم تحديث المهمة');
        }

        return redirect()->route('work.show', $work)->with('success', 'تم تحديث المهمة');
    }

    public function assign(Request $request, WorkActivity $work, WorkTask $task)
    {
        $this->authorizeActivity($request, $work);
        $this->authorizeTask($work, $task);

        $validated = $request->validate([
            'assigned_to' => 'nullable|exists:employees,id',
        ]);

        if (! empty($validated['assigned_to'])) {
            $this->ensureEmployeeInOrg($request, (int) $validated['assigned_to']);
        }

        $task->update(['assigned_to' => $validated['assigned_to'] ?? null]);

        return back()->with('success', 'تم تحديث التعيين');
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
