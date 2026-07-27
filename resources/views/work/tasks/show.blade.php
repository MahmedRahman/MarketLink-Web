@extends($workLayout ?? 'layouts.dashboard')

@section('title', $task->title)
@section('page-title', 'تفاصيل المحتوى')
@section('page-description', $activity->title)

@section('content')
<div class="max-w-4xl mx-auto space-y-5">

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-xl flex items-center">
            <span class="material-icons ml-2">check_circle</span>
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl flex items-center">
            <span class="material-icons ml-2">error</span>
            {{ session('error') }}
        </div>
    @endif
    @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl">
            <ul class="list-disc list-inside text-sm space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <a href="{{ work_route('show', $activity) }}" class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-primary">
        <span class="material-icons text-lg">arrow_forward</span>
        رجوع للنشاط
    </a>

    {{-- رأس التاسك --}}
    <div class="card rounded-2xl p-6">
        <div class="flex flex-col md:flex-row md:items-start justify-between gap-4">
            <div class="min-w-0">
                <div class="flex items-center gap-2 flex-wrap mb-3">
                    <span class="role-badge role-{{ $task->pipeline_stage_color }}">{{ $task->pipeline_stage_label }}</span>
                    @if($task->content_type_label)
                        <span class="role-badge role-indigo">{{ $task->content_type_label }}</span>
                    @endif
                    <span class="role-badge role-{{ $task->kind_color }}">{{ $task->kind_label }}</span>
                    <span class="role-badge role-{{ $task->status_color }}">{{ $task->status_label }}</span>
                    @if($task->is_overdue)
                        <span class="role-badge role-red">متأخرة</span>
                    @endif
                </div>
                <h2 class="text-2xl font-bold text-gray-900">{{ $task->title }}</h2>
                <p class="text-sm text-gray-500 mt-1">{{ $activity->title }}</p>
            </div>
            <div class="flex items-center gap-2 shrink-0 flex-wrap">
                @if($prev = \App\Models\WorkTask::previousPipelineStage($task->pipeline_stage))
                    <form method="POST" action="{{ work_route('tasks.move-stage', [$activity, $task]) }}">
                        @csrf
                        <input type="hidden" name="pipeline_stage" value="{{ $prev }}">
                        <button type="submit" class="px-3 py-2 rounded-xl bg-gray-100 text-gray-700 text-sm">← {{ \App\Models\WorkTask::pipelineStages()[$prev] }}</button>
                    </form>
                @endif
                @if($next = \App\Models\WorkTask::nextPipelineStage($task->pipeline_stage))
                    <form method="POST" action="{{ work_route('tasks.move-stage', [$activity, $task]) }}">
                        @csrf
                        <input type="hidden" name="pipeline_stage" value="{{ $next }}">
                        <button type="submit" class="px-3 py-2 rounded-xl bg-indigo-600 text-white text-sm">إلى {{ \App\Models\WorkTask::pipelineStages()[$next] }} →</button>
                    </form>
                @endif
                <button type="button" onclick="summarizeDesigner({{ $task->id }}, this)"
                        class="px-3 py-2 rounded-xl bg-amber-50 text-amber-700 hover:bg-amber-100 text-sm inline-flex items-center gap-1">
                    <span class="material-icons text-base">tips_and_updates</span>
                    ملخص المصمم
                </button>
                <a href="{{ work_route('tasks.edit', [$activity, $task]) }}"
                        class="px-3 py-2 rounded-xl bg-gray-100 text-gray-700 hover:bg-gray-200 text-sm inline-flex items-center gap-1">
                    <span class="material-icons text-base">edit</span>
                    تعديل
                </a>
            </div>
        </div>

        @if(!empty($task->platform_labels) || $task->publish_date || $task->due_date)
            <div class="flex flex-wrap items-center gap-2 mt-4 pt-4 border-t border-gray-100 text-sm text-gray-600">
                @foreach($task->platform_labels as $plat)
                    <span class="px-2.5 py-1 rounded-lg bg-gray-100 text-gray-700 text-xs">{{ $plat }}</span>
                @endforeach
                @if($task->publish_date)
                    <span class="flex items-center gap-1 text-xs"><span class="material-icons text-sm">campaign</span>نشر {{ $task->publish_date->format('Y/m/d') }}</span>
                @endif
                @if($task->due_date)
                    <span class="flex items-center gap-1 text-xs"><span class="material-icons text-sm">event</span>تسليم {{ $task->due_date->format('Y/m/d') }}</span>
                @endif
            </div>
        @endif
    </div>

    @if(!empty($cardShareUrl))
        @include('partials.share-link', [
            'label' => 'رابط شير للكارت كامل',
            'hint' => 'ابعت الرابط لأي حد — يشوف المحتوى والملفات بدون تسجيل دخول',
            'url' => $cardShareUrl,
            'inputId' => 'card-share-'.$task->id,
        ])
    @endif

    {{-- TOV و Caption واضحين --}}
    <div class="grid grid-cols-1 gap-4">
        <div class="rounded-2xl border-2 border-violet-200 bg-violet-50/60 p-5">
            <div class="flex items-center gap-2 mb-3">
                <span class="material-icons text-violet-600">record_voice_over</span>
                <h3 class="text-lg font-bold text-violet-900">TOV</h3>
                <span class="text-xs text-violet-600">Tone of Voice</span>
            </div>
            @if($task->tov)
                <p class="text-base leading-8 text-violet-950 whitespace-pre-line font-medium">{{ $task->tov }}</p>
            @else
                <p class="text-sm text-violet-400">لم يُحدد بعد</p>
            @endif
        </div>

        <div class="rounded-2xl border-2 border-sky-200 bg-sky-50/60 p-5">
            <div class="flex items-center gap-2 mb-3">
                <span class="material-icons text-sky-600">notes</span>
                <h3 class="text-lg font-bold text-sky-900">Caption</h3>
                <span class="text-xs text-sky-600">نص المنشور</span>
            </div>
            @if($task->caption)
                <p class="text-base leading-8 text-sky-950 whitespace-pre-line font-medium">{{ $task->caption }}</p>
            @else
                <p class="text-sm text-sky-400">لم يُحدد بعد</p>
            @endif
        </div>
    </div>

    @if($task->idea)
        <div class="card rounded-2xl p-5">
            <h3 class="text-sm font-bold text-gray-700 mb-2 flex items-center gap-1">
                <span class="material-icons text-base text-gray-500">lightbulb</span>
                الفكرة
            </h3>
            <p class="text-sm text-gray-700 whitespace-pre-line leading-7">{{ $task->idea }}</p>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="card rounded-2xl p-5">
            <h3 class="text-sm font-bold text-gray-700 mb-2 flex items-center gap-1">
                <span class="material-icons text-base text-amber-600">palette</span>
                مرجع التصميم
            </h3>
            @if($task->design_reference)
                <div class="text-sm text-gray-700 leading-7">{!! linkify_text($task->design_reference) !!}</div>
            @else
                <p class="text-sm text-gray-400">لا يوجد مرجع</p>
            @endif
        </div>
        <div class="rounded-2xl border border-amber-200 bg-amber-50 p-5">
            <h3 class="text-sm font-bold text-amber-800 mb-2 flex items-center gap-1">
                <span class="material-icons text-base">tips_and_updates</span>
                ملخص المطلوب من المصمم
            </h3>
            @if($task->designer_brief)
                <p id="designerBriefText" class="text-sm text-amber-950 whitespace-pre-line leading-7">{{ $task->designer_brief }}</p>
            @else
                <p id="designerBriefText" class="text-sm text-amber-500">اضغط «ملخص المصمم» للتوليد</p>
            @endif
        </div>
    </div>

    {{-- ملفات التصميم --}}
    <div class="card rounded-2xl p-5 space-y-4">
        <div class="flex items-start justify-between gap-3 flex-wrap">
            <div>
                <h3 class="text-sm font-bold text-gray-800 flex items-center gap-1">
                    <span class="material-icons text-base text-purple-600">cloud_upload</span>
                    ملفات التصميم
                </h3>
                <p class="text-xs text-gray-500 mt-1">
                    ارفع صورة أو أكثر، فيديو، أو PDF حسب نوع المحتوى — لو رفعت أكتر من ملف هيتجمعوا في فولدر واحد
                    @if($task->content_type_label)
                        (الحالي: {{ $task->content_type_label }} → مقترح: {{ $designAssetKinds[$suggestedAssetKind] ?? $suggestedAssetKind }})
                    @endif
                </p>
            </div>
        </div>

        <form method="POST" action="{{ work_route('tasks.files.upload', [$activity, $task]) }}"
              enctype="multipart/form-data" id="designUploadForm" class="space-y-3">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">نوع الملف</label>
                    <select name="asset_kind" id="assetKindSelect"
                            class="w-full px-3 py-2 rounded-xl border-2 border-gray-200 text-sm focus:border-primary focus:outline-none">
                        @foreach($designAssetKinds as $key => $label)
                            <option value="{{ $key }}" @selected($suggestedAssetKind === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-xs font-medium text-gray-600 mb-1">وصف مختصر (اختياري)</label>
                    <input type="text" name="description" maxlength="500" placeholder="مثلاً: نسخة نهائية للريلز"
                           class="w-full px-3 py-2 rounded-xl border-2 border-gray-200 text-sm focus:border-primary focus:outline-none">
                </div>
            </div>

            <div id="designDropZone"
                 class="relative rounded-2xl border-2 border-dashed border-purple-200 bg-purple-50/40 px-4 py-8 text-center transition-colors cursor-pointer hover:border-purple-400 hover:bg-purple-50">
                <input type="file" name="files[]" id="designFileInput" required multiple
                       class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                       accept=".jpg,.jpeg,.png,.gif,.webp">
                <span class="material-icons text-3xl text-purple-400 mb-2">upload_file</span>
                <p class="text-sm font-medium text-gray-700" id="designDropHint">اسحب ملف أو أكثر هنا أو اضغط للاختيار</p>
                <p class="text-xs text-gray-500 mt-1" id="designAcceptHint">صور: JPG, PNG, GIF, WEBP — حتى 100MB لكل ملف · أكتر من ملف = فولدر</p>
                <p class="text-xs text-purple-700 mt-2 font-medium hidden" id="designFileName"></p>
            </div>

            <button type="submit" id="designUploadBtn"
                    class="w-full sm:w-auto px-5 py-2.5 rounded-xl bg-purple-600 text-white text-sm font-medium hover:bg-purple-700 inline-flex items-center justify-center gap-1">
                <span class="material-icons text-base">cloud_upload</span>
                رفع الملفات
            </button>
        </form>

        @if($task->files->count())
            @php
                $fileGroups = $task->files->groupBy(fn ($f) => $f->upload_batch ?: 'single-'.$f->id);
            @endphp
            <div class="border-t border-gray-100 pt-4 space-y-4">
                <div class="flex items-center justify-between gap-2 flex-wrap">
                    <p class="text-xs font-semibold text-gray-500">ملفات التصميم ({{ $task->files->count() }})</p>
                </div>
                @if(!empty($cardShareUrl))
                    @include('partials.share-link', [
                        'label' => 'رابط شير لملفات التصميم كلها',
                        'hint' => 'رابط واحد يفتح كل الملفات ('.$task->files->count().') بدون تسجيل دخول',
                        'url' => $cardShareUrl.'#files',
                        'inputId' => 'files-share-'.$task->id,
                    ])
                @endif
                @foreach($fileGroups as $batchKey => $groupFiles)
                    @php
                        $folderName = optional($groupFiles->first())->nas_folder;
                        $isFolder = filled($folderName);
                    @endphp
                    <div class="space-y-2">
                        @if($isFolder)
                            <div class="flex items-center gap-1.5 text-xs font-semibold text-purple-800">
                                <span class="material-icons text-sm">folder</span>
                                <span dir="ltr">{{ $folderName }}</span>
                                <span class="font-normal text-gray-500">({{ $groupFiles->count() }} ملفات)</span>
                            </div>
                        @endif
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 {{ $isFolder ? 'ms-2 border-s-2 border-purple-100 ps-3' : '' }}">
                            @foreach($groupFiles as $file)
                                <div class="rounded-xl border border-gray-200 bg-gray-50 p-3 flex gap-3">
                                    <div class="w-16 h-16 rounded-lg bg-white border border-gray-200 flex items-center justify-center overflow-hidden shrink-0">
                                        @if($file->isImage())
                                            <img src="{{ $file->file_url }}" alt="{{ $file->file_name }}" class="w-full h-full object-cover">
                                        @else
                                            <span class="material-icons text-2xl text-gray-400">{{ $file->file_icon }}</span>
                                        @endif
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="text-sm font-medium text-gray-800 truncate" title="{{ $file->file_name }}">{{ $file->file_name }}</p>
                                        <p class="text-[11px] text-gray-500 mt-0.5">
                                            {{ $file->asset_kind_label }} · {{ $file->formatted_file_size }}
                                            @if($file->description)
                                                · {{ $file->description }}
                                            @endif
                                        </p>
                                        @if($file->nas_path)
                                            <p class="text-[11px] text-teal-700 mt-1 flex items-start gap-1" dir="ltr">
                                                <span class="material-icons text-sm text-teal-600 shrink-0">folder</span>
                                                <span class="break-all">{{ $file->nas_display_path }}</span>
                                            </p>
                                            @if($file->nas_public_url)
                                                <a href="{{ $file->nas_public_url }}" target="_blank" rel="noopener"
                                                   class="text-xs text-teal-700 hover:underline inline-flex items-center gap-0.5 mt-1">
                                                    <span class="material-icons text-sm">open_in_new</span>
                                                    فتح على سيرفر الملفات
                                                </a>
                                            @endif
                                        @endif
                                        <div class="flex items-center gap-2 mt-2 flex-wrap">
                                            <a href="{{ work_route('tasks.files.download', [$activity, $task, $file]) }}"
                                               class="text-xs text-primary hover:underline inline-flex items-center gap-0.5">
                                                <span class="material-icons text-sm">download</span>
                                                تحميل
                                            </a>
                                            @if($file->isImage() || $file->isPdf())
                                                <a href="{{ $file->file_url }}" target="_blank" rel="noopener"
                                                   class="text-xs text-gray-600 hover:underline inline-flex items-center gap-0.5">
                                                    <span class="material-icons text-sm">open_in_new</span>
                                                    عرض
                                                </a>
                                            @endif
                                            <form method="POST" action="{{ work_route('tasks.files.destroy', [$activity, $task, $file]) }}"
                                                  onsubmit="return confirm('نقل الملف لفولدر deleted بدل الحذف النهائي؟');" class="inline">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="text-xs text-red-600 hover:underline inline-flex items-center gap-0.5">
                                                    <span class="material-icons text-sm">delete</span>
                                                    حذف
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- روابط النشر حسب المنصات --}}
    <div class="rounded-2xl border border-teal-200 bg-teal-50/50 p-5 space-y-4">
        <div class="flex items-start justify-between gap-3 flex-wrap">
            <div>
                <h3 class="text-sm font-bold text-teal-900 flex items-center gap-1">
                    <span class="material-icons text-base">link</span>
                    روابط النشر
                </h3>
                <p class="text-xs text-teal-700 mt-1">حسب المنصات المختارة للمحتوى</p>
            </div>
            @if($task->pipeline_stage === 'ready_to_publish')
                <span class="px-2.5 py-1 rounded-lg bg-teal-100 text-teal-800 text-[11px] font-semibold">جاهز للنشر — أضف الروابط ثم انقل لـ تم النشر</span>
            @elseif($task->pipeline_stage === 'published')
                <span class="px-2.5 py-1 rounded-lg bg-green-100 text-green-800 text-[11px] font-semibold">تم النشر · الحالة اكتمال</span>
            @endif
        </div>

        @if(!empty($task->platforms))
            <form method="POST" action="{{ work_route('tasks.publish-links', [$activity, $task]) }}" class="space-y-3">
                @csrf
                <div class="space-y-3">
                    @foreach($task->platforms as $plat)
                        <div class="bg-white rounded-xl border border-teal-100 p-3">
                            <div class="flex items-center justify-between gap-2 mb-2">
                                <label class="text-xs font-semibold text-teal-900">{{ $platforms[$plat] ?? $plat }}</label>
                                @if($link = $task->publishLinkFor($plat))
                                    <a href="{{ $link }}" target="_blank" rel="noopener" class="text-xs text-indigo-600 hover:underline inline-flex items-center gap-0.5">
                                        <span class="material-icons text-sm">open_in_new</span>
                                        فتح
                                    </a>
                                @endif
                            </div>
                            <input type="url" name="publish_links[{{ $plat }}]" dir="ltr"
                                   value="{{ $task->publishLinkFor($plat) }}"
                                   placeholder="https:// رابط منشور {{ $platforms[$plat] ?? $plat }}"
                                   class="w-full px-3 py-2 rounded-xl border-2 border-teal-100 text-sm focus:border-teal-400 focus:outline-none">
                        </div>
                    @endforeach
                </div>
                <button type="submit" class="px-4 py-2.5 rounded-xl bg-teal-600 text-white text-sm font-medium hover:bg-teal-700 inline-flex items-center gap-1">
                    <span class="material-icons text-base">save</span>
                    حفظ روابط النشر
                </button>
            </form>
        @else
            <p class="text-sm text-teal-700">
                لا توجد منصات محددة لهذا المحتوى.
                <a href="{{ work_route('tasks.edit', [$activity, $task]) }}" class="underline font-medium">حدد المنصات من التعديل</a>
            </p>
        @endif
    </div>

    <div class="card rounded-2xl p-5">
        <h3 class="text-sm font-bold text-gray-700 mb-3">الفريق</h3>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div class="bg-blue-50 rounded-xl p-3">
                <p class="text-xs text-blue-600 mb-0.5">كاتب المحتوى</p>
                <p class="text-sm font-semibold text-gray-800">{{ $task->contentWriter->name ?? '—' }}</p>
            </div>
            <div class="bg-purple-50 rounded-xl p-3">
                <p class="text-xs text-purple-600 mb-0.5">المصمم</p>
                <p class="text-sm font-semibold text-gray-800">{{ $task->designer->name ?? '—' }}</p>
            </div>
            <div class="bg-gray-50 rounded-xl p-3">
                <p class="text-xs text-gray-500 mb-0.5">الموظف الحالي</p>
                <p class="text-sm font-semibold text-gray-800">{{ $task->assignedEmployee->name ?? '—' }}</p>
            </div>
        </div>
    </div>

    @if($task->notes)
        <div class="card rounded-2xl p-5">
            <h3 class="text-sm font-bold text-gray-700 mb-2">ملاحظات</h3>
            <p class="text-sm text-gray-700 whitespace-pre-line">{{ $task->notes }}</p>
        </div>
    @endif

    {{-- سجل التنقلات والحالات --}}
    <div id="task-log" class="card rounded-2xl p-5 space-y-4">
        <div class="flex items-center justify-between gap-3 flex-wrap">
            <h3 class="text-sm font-bold text-gray-800 flex items-center gap-1">
                <span class="material-icons text-base text-indigo-600">history</span>
                سجل التغييرات
            </h3>
            <span class="text-xs text-gray-400">{{ $task->logs->count() }} حدث</span>
        </div>

        @if($task->logs->count())
            <div class="relative space-y-0">
                <div class="absolute top-2 bottom-2 right-[15px] w-px bg-gray-200"></div>
                @foreach($task->logs as $log)
                    @php
                        $tone = $log->action_color;
                        $toneMap = [
                            'indigo' => ['bg' => 'bg-indigo-100', 'text' => 'text-indigo-700', 'ring' => 'ring-indigo-200'],
                            'amber' => ['bg' => 'bg-amber-100', 'text' => 'text-amber-800', 'ring' => 'ring-amber-200'],
                            'purple' => ['bg' => 'bg-purple-100', 'text' => 'text-purple-700', 'ring' => 'ring-purple-200'],
                            'teal' => ['bg' => 'bg-teal-100', 'text' => 'text-teal-700', 'ring' => 'ring-teal-200'],
                            'blue' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-700', 'ring' => 'ring-blue-200'],
                            'green' => ['bg' => 'bg-green-100', 'text' => 'text-green-700', 'ring' => 'ring-green-200'],
                            'gray' => ['bg' => 'bg-gray-100', 'text' => 'text-gray-600', 'ring' => 'ring-gray-200'],
                        ];
                        $c = $toneMap[$tone] ?? $toneMap['gray'];
                    @endphp
                    <div class="relative flex gap-3 pb-4 last:pb-0">
                        <div class="relative z-10 w-8 h-8 rounded-full {{ $c['bg'] }} {{ $c['text'] }} ring-4 ring-white flex items-center justify-center shrink-0">
                            <span class="material-icons text-base">{{ $log->action_icon }}</span>
                        </div>
                        <div class="flex-1 min-w-0 rounded-xl border border-gray-100 bg-gray-50/70 px-3 py-2.5">
                            <p class="text-sm text-gray-800 leading-6">{{ $log->message }}</p>
                            <div class="mt-1 flex flex-wrap items-center gap-x-3 gap-y-1 text-[11px] text-gray-500">
                                <span class="inline-flex items-center gap-0.5">
                                    <span class="material-icons text-sm">schedule</span>
                                    {{ $log->created_at?->timezone(config('app.timezone'))->format('Y/m/d h:i A') }}
                                </span>
                                @if($log->user)
                                    <span class="inline-flex items-center gap-0.5">
                                        <span class="material-icons text-sm">person</span>
                                        {{ $log->user->name }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-sm text-gray-400 text-center py-4">لا يوجد سجل بعد — أي تنقّل أو تغيير حالة هيتسجل هنا</p>
        @endif
    </div>
</div>

@endsection



@section('scripts')
<script>
async function summarizeDesigner(taskId, btn) {
    const original = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<span class="material-icons text-base animate-spin">progress_activity</span>';
    try {
        const url = "{{ work_route('tasks.summarize-designer', [$activity, $task]) }}";
        const res = await fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({}),
        });
        const data = await res.json();
        if (!data.success) {
            alert(data.error || 'فشل التلخيص');
            return;
        }
        const el = document.getElementById('designerBriefText');
        if (el) el.textContent = data.designer_brief || '';
    } catch (e) {
        alert('حدث خطأ أثناء التلخيص');
    } finally {
        btn.disabled = false;
        btn.innerHTML = original;
    }
}

(function initDesignUpload() {
    const kindSelect = document.getElementById('assetKindSelect');
    const fileInput = document.getElementById('designFileInput');
    const acceptHint = document.getElementById('designAcceptHint');
    const fileNameEl = document.getElementById('designFileName');
    const dropZone = document.getElementById('designDropZone');
    const form = document.getElementById('designUploadForm');
    if (!kindSelect || !fileInput) return;

    const acceptMap = {
        image: { accept: '.jpg,.jpeg,.png,.gif,.webp', hint: 'صور: JPG, PNG, GIF, WEBP — حتى 100MB لكل ملف · أكتر من ملف = فولدر' },
        video: { accept: '.mp4,.mov,.webm,.m4v', hint: 'فيديو: MP4, MOV, WEBM, M4V — حتى 100MB لكل ملف · أكتر من ملف = فولدر' },
        pdf: { accept: '.pdf', hint: 'ملف PDF — حتى 100MB لكل ملف · أكتر من ملف = فولدر' },
    };

    function syncAccept() {
        const meta = acceptMap[kindSelect.value] || acceptMap.image;
        fileInput.accept = meta.accept;
        if (acceptHint) acceptHint.textContent = meta.hint;
    }

    function updateSelectedLabel() {
        const list = fileInput.files ? Array.from(fileInput.files) : [];
        if (!fileNameEl) return;
        if (!list.length) {
            fileNameEl.textContent = '';
            fileNameEl.classList.add('hidden');
            return;
        }
        if (list.length === 1) {
            fileNameEl.textContent = 'تم اختيار: ' + list[0].name;
        } else {
            fileNameEl.textContent = 'تم اختيار ' + list.length + ' ملفات — هيتجمعوا في فولدر واحد';
        }
        fileNameEl.classList.remove('hidden');
    }

    kindSelect.addEventListener('change', function () {
        fileInput.value = '';
        updateSelectedLabel();
        syncAccept();
    });
    syncAccept();

    fileInput.addEventListener('change', updateSelectedLabel);

    ['dragenter', 'dragover'].forEach(function (evt) {
        dropZone?.addEventListener(evt, function (e) {
            e.preventDefault();
            e.stopPropagation();
            dropZone.classList.add('border-purple-500', 'bg-purple-100');
        });
    });
    ['dragleave', 'drop'].forEach(function (evt) {
        dropZone?.addEventListener(evt, function (e) {
            e.preventDefault();
            e.stopPropagation();
            dropZone.classList.remove('border-purple-500', 'bg-purple-100');
        });
    });
    dropZone?.addEventListener('drop', function (e) {
        const files = e.dataTransfer?.files;
        if (!files?.length) return;
        try {
            const dt = new DataTransfer();
            Array.from(files).forEach(function (f) { dt.items.add(f); });
            fileInput.files = dt.files;
        } catch (err) {
            // بعض المتصفحات تمنع التعيين المباشر
        }
        fileInput.dispatchEvent(new Event('change'));
    });

    form?.addEventListener('submit', function () {
        const btn = document.getElementById('designUploadBtn');
        if (!btn) return;
        // لا نعطّل الزر فورًا — بعض المتصفحات تلغي الإرسال لو الزر اتـ disable أثناء submit
        btn.innerHTML = '<span class="material-icons text-base animate-spin">progress_activity</span> جاري الرفع...';
        btn.setAttribute('aria-busy', 'true');
        setTimeout(function () { btn.disabled = true; }, 50);
    });
})();
</script>
@endsection
