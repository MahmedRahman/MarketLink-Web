@extends('layouts.dashboard')

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

    <a href="{{ route('work.show', $activity) }}" class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-primary">
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
                    <form method="POST" action="{{ route('work.tasks.move-stage', [$activity, $task]) }}">
                        @csrf
                        <input type="hidden" name="pipeline_stage" value="{{ $prev }}">
                        <button type="submit" class="px-3 py-2 rounded-xl bg-gray-100 text-gray-700 text-sm">← {{ \App\Models\WorkTask::pipelineStages()[$prev] }}</button>
                    </form>
                @endif
                @if($next = \App\Models\WorkTask::nextPipelineStage($task->pipeline_stage))
                    <form method="POST" action="{{ route('work.tasks.move-stage', [$activity, $task]) }}">
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
                <button type="button" onclick="document.getElementById('editTaskModal').classList.remove('hidden')"
                        class="px-3 py-2 rounded-xl bg-gray-100 text-gray-700 hover:bg-gray-200 text-sm inline-flex items-center gap-1">
                    <span class="material-icons text-base">edit</span>
                    تعديل
                </button>
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
                <p class="text-sm text-gray-700 whitespace-pre-line leading-7">{{ $task->design_reference }}</p>
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
                    ارفع صورة، فيديو، أو PDF حسب نوع المحتوى
                    @if($task->content_type_label)
                        (الحالي: {{ $task->content_type_label }} → مقترح: {{ $designAssetKinds[$suggestedAssetKind] ?? $suggestedAssetKind }})
                    @endif
                </p>
            </div>
        </div>

        <form method="POST" action="{{ route('work.tasks.files.upload', [$activity, $task]) }}"
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
                <input type="file" name="file" id="designFileInput" required
                       class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                       accept=".jpg,.jpeg,.png,.gif,.webp">
                <span class="material-icons text-3xl text-purple-400 mb-2">upload_file</span>
                <p class="text-sm font-medium text-gray-700" id="designDropHint">اسحب الملف هنا أو اضغط للاختيار</p>
                <p class="text-xs text-gray-500 mt-1" id="designAcceptHint">صور: JPG, PNG, GIF, WEBP — حتى 100MB</p>
                <p class="text-xs text-purple-700 mt-2 font-medium hidden" id="designFileName"></p>
            </div>

            <button type="submit" id="designUploadBtn"
                    class="w-full sm:w-auto px-5 py-2.5 rounded-xl bg-purple-600 text-white text-sm font-medium hover:bg-purple-700 inline-flex items-center justify-center gap-1">
                <span class="material-icons text-base">cloud_upload</span>
                رفع الملف
            </button>
        </form>

        @if($task->files->count())
            <div class="border-t border-gray-100 pt-4 space-y-3">
                <p class="text-xs font-semibold text-gray-500">الملفات المرفوعة ({{ $task->files->count() }})</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @foreach($task->files as $file)
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
                                <div class="flex items-center gap-2 mt-2">
                                    <a href="{{ route('work.tasks.files.download', [$activity, $task, $file]) }}"
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
                                    <form method="POST" action="{{ route('work.tasks.files.destroy', [$activity, $task, $file]) }}"
                                          onsubmit="return confirm('حذف هذا الملف؟');" class="inline">
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
</div>

{{-- مودال تعديل سريع --}}
<div id="editTaskModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40">
    <div class="bg-white rounded-2xl w-full max-w-2xl p-6 shadow-2xl max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between mb-5">
            <h3 class="text-lg font-bold text-gray-800">تعديل المحتوى</h3>
            <button type="button" onclick="document.getElementById('editTaskModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <span class="material-icons">close</span>
            </button>
        </div>
        <form method="POST" action="{{ route('work.tasks.update', [$activity, $task]) }}" class="space-y-3">
            @csrf @method('PUT')
            <input type="hidden" name="return_to_detail" value="1">
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">العنوان</label>
                <input type="text" name="title" value="{{ $task->title }}" required
                       class="w-full px-3 py-2 rounded-xl border-2 border-gray-200 text-sm focus:border-primary focus:outline-none">
            </div>
            <div>
                <label class="block text-xs font-medium text-violet-700 mb-1">TOV</label>
                <textarea name="tov" rows="3"
                          class="w-full px-3 py-2 rounded-xl border-2 border-violet-200 bg-violet-50/40 text-sm focus:border-violet-400 focus:outline-none">{{ $task->tov }}</textarea>
            </div>
            <div>
                <label class="block text-xs font-medium text-sky-700 mb-1">Caption</label>
                <textarea name="caption" rows="4"
                          class="w-full px-3 py-2 rounded-xl border-2 border-sky-200 bg-sky-50/40 text-sm focus:border-sky-400 focus:outline-none">{{ $task->caption }}</textarea>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">الفكرة</label>
                <textarea name="idea" rows="2"
                          class="w-full px-3 py-2 rounded-xl border-2 border-gray-200 text-sm focus:border-primary focus:outline-none">{{ $task->idea }}</textarea>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">نوع المحتوى</label>
                <select name="content_type" class="w-full px-3 py-2 rounded-xl border-2 border-gray-200 text-sm focus:border-primary focus:outline-none">
                    <option value="">— اختر —</option>
                    @foreach($contentTypes as $key => $label)
                        <option value="{{ $key }}" @selected($task->content_type === $key)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">مرجع التصميم</label>
                <textarea name="design_reference" rows="2"
                          class="w-full px-3 py-2 rounded-xl border-2 border-gray-200 text-sm focus:border-primary focus:outline-none">{{ $task->design_reference }}</textarea>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">ملخص المصمم</label>
                <textarea name="designer_brief" rows="2"
                          class="w-full px-3 py-2 rounded-xl border-2 border-amber-100 bg-amber-50/40 text-sm focus:border-amber-400 focus:outline-none">{{ $task->designer_brief }}</textarea>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">المنصات</label>
                <div class="flex flex-wrap gap-2">
                    @foreach($platforms as $key => $label)
                        <label class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg border border-gray-200 text-xs cursor-pointer">
                            <input type="checkbox" name="platforms[]" value="{{ $key }}"
                                   @checked(in_array($key, $task->platforms ?? [], true))
                                   class="rounded border-gray-300 text-primary focus:ring-primary">
                            {{ $label }}
                        </label>
                    @endforeach
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">نوع الشغل</label>
                    <select name="kind" class="w-full px-3 py-2 rounded-xl border-2 border-gray-200 text-sm focus:border-primary focus:outline-none">
                        @foreach($kinds as $key => $label)
                            <option value="{{ $key }}" @selected($task->kind === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">الحالة</label>
                    <select name="status" class="w-full px-3 py-2 rounded-xl border-2 border-gray-200 text-sm focus:border-primary focus:outline-none">
                        @foreach($taskStatuses as $key => $label)
                            <option value="{{ $key }}" @selected($task->status === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">كاتب المحتوى</label>
                    <select name="content_writer_id" class="w-full px-3 py-2 rounded-xl border-2 border-gray-200 text-sm focus:border-primary focus:outline-none">
                        <option value="">—</option>
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}" @selected($task->content_writer_id === $emp->id)>{{ $emp->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">المصمم</label>
                    <select name="designer_id" class="w-full px-3 py-2 rounded-xl border-2 border-gray-200 text-sm focus:border-primary focus:outline-none">
                        <option value="">—</option>
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}" @selected($task->designer_id === $emp->id)>{{ $emp->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">الموظف الحالي</label>
                    <select name="assigned_to" class="w-full px-3 py-2 rounded-xl border-2 border-gray-200 text-sm focus:border-primary focus:outline-none">
                        <option value="">—</option>
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}" @selected($task->assigned_to === $emp->id)>{{ $emp->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">التسليم</label>
                    <input type="date" name="due_date" value="{{ optional($task->due_date)->format('Y-m-d') }}"
                           class="w-full px-3 py-2 rounded-xl border-2 border-gray-200 text-sm focus:border-primary focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">موعد النشر</label>
                    <input type="date" name="publish_date" value="{{ optional($task->publish_date)->format('Y-m-d') }}"
                           class="w-full px-3 py-2 rounded-xl border-2 border-gray-200 text-sm focus:border-primary focus:outline-none">
                </div>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">ملاحظات</label>
                <textarea name="notes" rows="2"
                          class="w-full px-3 py-2 rounded-xl border-2 border-gray-200 text-sm focus:border-primary focus:outline-none">{{ $task->notes }}</textarea>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="btn-primary text-white px-5 py-2.5 rounded-xl font-medium flex-1">حفظ</button>
                <button type="button" onclick="document.getElementById('editTaskModal').classList.add('hidden')"
                        class="px-5 py-2.5 rounded-xl font-medium bg-gray-100 text-gray-700 hover:bg-gray-200">إلغاء</button>
            </div>
        </form>
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
        const url = "{{ route('work.tasks.summarize-designer', [$activity, $task]) }}";
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
        image: { accept: '.jpg,.jpeg,.png,.gif,.webp', hint: 'صور: JPG, PNG, GIF, WEBP — حتى 100MB' },
        video: { accept: '.mp4,.mov,.webm,.m4v', hint: 'فيديو: MP4, MOV, WEBM, M4V — حتى 100MB' },
        pdf: { accept: '.pdf', hint: 'ملف PDF فقط — حتى 100MB' },
    };

    function syncAccept() {
        const meta = acceptMap[kindSelect.value] || acceptMap.image;
        fileInput.accept = meta.accept;
        if (acceptHint) acceptHint.textContent = meta.hint;
    }

    kindSelect.addEventListener('change', function () {
        fileInput.value = '';
        if (fileNameEl) {
            fileNameEl.textContent = '';
            fileNameEl.classList.add('hidden');
        }
        syncAccept();
    });
    syncAccept();

    fileInput.addEventListener('change', function () {
        const name = fileInput.files?.[0]?.name || '';
        if (fileNameEl) {
            fileNameEl.textContent = name ? 'تم اختيار: ' + name : '';
            fileNameEl.classList.toggle('hidden', !name);
        }
    });

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
            dt.items.add(files[0]);
            fileInput.files = dt.files;
        } catch (err) {
            // بعض المتصفحات تمنع التعيين المباشر
        }
        fileInput.dispatchEvent(new Event('change'));
    });

    form?.addEventListener('submit', function () {
        const btn = document.getElementById('designUploadBtn');
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<span class="material-icons text-base animate-spin">progress_activity</span> جاري الرفع...';
        }
    });
})();
</script>
@endsection
