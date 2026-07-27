@extends('layouts.employee')

@section('title', $task->title)
@section('page-title', 'تفاصيل المهمة')
@section('page-description', $task->activity->title ?? 'مساحة العمل')

@section('content')
@php
    $stageColor = $task->pipeline_stage_color ?? 'blue';
    $kindColors = [
        'design' => 'bg-purple-100 text-purple-700',
        'video' => 'bg-red-100 text-red-700',
        'content' => 'bg-blue-100 text-blue-700',
        'publish' => 'bg-teal-100 text-teal-700',
        'other' => 'bg-gray-100 text-gray-700',
    ];
    $stColors = [
        'todo' => 'bg-gray-100 text-gray-700',
        'in_progress' => 'bg-blue-100 text-blue-700',
        'review' => 'bg-yellow-100 text-yellow-700',
        'done' => 'bg-green-100 text-green-700',
    ];
    $stageBg = match($task->pipeline_stage) {
        'design' => 'from-purple-500/10 via-white to-white',
        'ready_to_publish' => 'from-teal-500/10 via-white to-white',
        'published' => 'from-green-500/10 via-white to-white',
        default => 'from-indigo-500/10 via-white to-white',
    };
@endphp

<div class="max-w-3xl mx-auto space-y-5">

    <a href="{{ $task->activity ? route('employee.work.activity', $task->activity) : route('employee.tasks.index') }}"
       class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-indigo-600 transition-colors">
        <span class="material-icons text-lg">arrow_forward</span>
        رجوع للنشاط
    </a>

    {{-- رأس المهمة --}}
    <div class="card rounded-2xl overflow-hidden">
        <div class="bg-gradient-to-b {{ $stageBg }} p-6 md:p-7">
            <div class="flex items-start justify-between gap-4 flex-wrap">
                <div class="min-w-0 flex-1">
                    @if($task->activity)
                        <p class="inline-flex items-center gap-1.5 text-xs font-medium text-indigo-600 mb-3">
                            <span class="material-icons text-sm">folder_open</span>
                            {{ $task->activity->title }}
                        </p>
                    @endif

                    <div class="flex items-center gap-2 flex-wrap mb-3">
                        @if($task->pipeline_stage_label ?? null)
                            <span class="role-badge role-{{ $stageColor }}">{{ $task->pipeline_stage_label }}</span>
                        @endif
                        <span class="px-2.5 py-1 text-xs font-semibold rounded-lg {{ $kindColors[$task->kind] ?? $kindColors['other'] }}">{{ $task->kind_label }}</span>
                        <span class="px-2.5 py-1 text-xs font-semibold rounded-lg {{ $stColors[$task->status] ?? $stColors['todo'] }}">{{ $task->status_label }}</span>
                        @if($task->content_type_label)
                            <span class="px-2.5 py-1 text-xs font-semibold rounded-lg bg-indigo-100 text-indigo-700">{{ $task->content_type_label }}</span>
                        @endif
                        @if($task->is_overdue)
                            <span class="px-2.5 py-1 text-xs font-semibold rounded-lg bg-red-100 text-red-700">متأخرة</span>
                        @endif
                    </div>

                    <h2 class="text-2xl md:text-3xl font-extrabold text-gray-900 leading-snug">{{ $task->title }}</h2>
                </div>
            </div>

            @if($task->due_date || $task->publish_date || !empty($task->platform_labels))
                <div class="mt-5 pt-4 border-t border-gray-200/70 flex flex-wrap gap-3">
                    @if($task->due_date)
                        <div class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-white/80 border border-gray-100 text-sm text-gray-700">
                            <span class="material-icons text-base text-indigo-500">event</span>
                            <span>التسليم <strong class="font-semibold">{{ $task->due_date->format('Y/m/d') }}</strong></span>
                        </div>
                    @endif
                    @if($task->publish_date)
                        <div class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-white/80 border border-gray-100 text-sm text-gray-700">
                            <span class="material-icons text-base text-teal-500">campaign</span>
                            <span>النشر <strong class="font-semibold">{{ $task->publish_date->format('Y/m/d') }}</strong></span>
                        </div>
                    @endif
                    @foreach($task->platform_labels ?? [] as $plat)
                        <span class="inline-flex items-center px-3 py-2 rounded-xl bg-white/80 border border-gray-100 text-xs font-medium text-gray-600">{{ $plat }}</span>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- المحتوى --}}
    <div class="space-y-4">
        @if($task->idea)
            <section class="card rounded-2xl p-5">
                <div class="flex items-center gap-2 mb-3">
                    <div class="w-9 h-9 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                        <span class="material-icons text-xl">lightbulb</span>
                    </div>
                    <h3 class="font-bold text-gray-800">الفكرة</h3>
                </div>
                <p class="text-sm md:text-base text-gray-700 leading-8 whitespace-pre-line">{{ $task->idea }}</p>
            </section>
        @endif

        @if($task->tov)
            <section class="rounded-2xl border border-violet-200 bg-violet-50/70 p-5">
                <div class="flex items-center gap-2 mb-3">
                    <div class="w-9 h-9 rounded-xl bg-violet-100 text-violet-700 flex items-center justify-center">
                        <span class="material-icons text-xl">record_voice_over</span>
                    </div>
                    <div>
                        <h3 class="font-bold text-violet-900">TOV</h3>
                        <p class="text-[11px] text-violet-600">Tone of Voice</p>
                    </div>
                </div>
                <p class="text-sm md:text-base text-violet-950 leading-8 whitespace-pre-line font-medium">{{ $task->tov }}</p>
            </section>
        @endif

        @if($task->caption)
            <section class="rounded-2xl border border-sky-200 bg-sky-50/70 p-5">
                <div class="flex items-center gap-2 mb-3">
                    <div class="w-9 h-9 rounded-xl bg-sky-100 text-sky-700 flex items-center justify-center">
                        <span class="material-icons text-xl">notes</span>
                    </div>
                    <div>
                        <h3 class="font-bold text-sky-900">Caption</h3>
                        <p class="text-[11px] text-sky-600">نص المنشور</p>
                    </div>
                </div>
                <p class="text-sm md:text-base text-sky-950 leading-8 whitespace-pre-line font-medium">{{ $task->caption }}</p>
            </section>
        @endif

        @if($task->design_reference || $task->designer_brief)
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @if($task->design_reference)
                    <section class="card rounded-2xl p-5">
                        <div class="flex items-center gap-2 mb-3">
                            <div class="w-9 h-9 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center">
                                <span class="material-icons text-xl">palette</span>
                            </div>
                            <h3 class="font-bold text-gray-800">مرجع التصميم</h3>
                        </div>
                        <div class="text-sm text-gray-700 leading-7">{!! linkify_text($task->design_reference) !!}</div>
                    </section>
                @endif
                @if($task->designer_brief)
                    <section class="rounded-2xl border border-amber-200 bg-amber-50 p-5">
                        <div class="flex items-center gap-2 mb-3">
                            <div class="w-9 h-9 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center">
                                <span class="material-icons text-xl">tips_and_updates</span>
                            </div>
                            <h3 class="font-bold text-amber-900">مطلوب من المصمم</h3>
                        </div>
                        <p class="text-sm text-amber-950 leading-7 whitespace-pre-line">{{ $task->designer_brief }}</p>
                    </section>
                @endif
            </div>
        @endif
    </div>

    {{-- الفريق --}}
    @if($task->contentWriter || $task->designer || $task->assignedEmployee)
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            @if($task->contentWriter)
                <div class="card rounded-2xl p-4 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center font-bold text-sm">
                        {{ mb_substr($task->contentWriter->name, 0, 1) }}
                    </div>
                    <div class="min-w-0">
                        <p class="text-[11px] text-blue-600 font-medium">كاتب المحتوى</p>
                        <p class="text-sm font-semibold text-gray-800 truncate">{{ $task->contentWriter->name }}</p>
                    </div>
                </div>
            @endif
            @if($task->designer)
                <div class="card rounded-2xl p-4 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-purple-100 text-purple-700 flex items-center justify-center font-bold text-sm">
                        {{ mb_substr($task->designer->name, 0, 1) }}
                    </div>
                    <div class="min-w-0">
                        <p class="text-[11px] text-purple-600 font-medium">المصمم</p>
                        <p class="text-sm font-semibold text-gray-800 truncate">{{ $task->designer->name }}</p>
                    </div>
                </div>
            @endif
            @if($task->assignedEmployee)
                <div class="card rounded-2xl p-4 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-teal-100 text-teal-700 flex items-center justify-center font-bold text-sm">
                        {{ mb_substr($task->assignedEmployee->name, 0, 1) }}
                    </div>
                    <div class="min-w-0">
                        <p class="text-[11px] text-teal-600 font-medium">المسؤول</p>
                        <p class="text-sm font-semibold text-gray-800 truncate">{{ $task->assignedEmployee->name }}</p>
                    </div>
                </div>
            @endif
        </div>
    @endif

    {{-- ملفات التصميم --}}
    <section class="card rounded-2xl p-5 space-y-4">
        <div class="flex items-start justify-between gap-3 flex-wrap">
            <div class="flex items-center gap-2">
                <div class="w-9 h-9 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center">
                    <span class="material-icons text-xl">cloud_upload</span>
                </div>
                <div>
                    <h3 class="font-bold text-gray-800">رفع التصميم</h3>
                    <p class="text-xs text-gray-500 mt-0.5">
                        ارفع صورة أو أكثر، فيديو، أو PDF — لو رفعت أكتر من ملف هيتجمعوا في فولدر واحد
                        @if($task->content_type_label)
                            · الحالي: {{ $task->content_type_label }} → مقترح: {{ $designAssetKinds[$suggestedAssetKind] ?? $suggestedAssetKind }}
                        @endif
                    </p>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('employee.work.files.upload', $task) }}"
              enctype="multipart/form-data" id="designUploadForm" class="space-y-3">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">نوع الملف</label>
                    <select name="asset_kind" id="assetKindSelect"
                            class="w-full px-3 py-2.5 rounded-xl border-2 border-gray-200 text-sm focus:border-purple-500 focus:outline-none">
                        @foreach($designAssetKinds as $key => $label)
                            <option value="{{ $key }}" @selected($suggestedAssetKind === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-xs font-medium text-gray-600 mb-1">وصف مختصر (اختياري)</label>
                    <input type="text" name="description" maxlength="500" placeholder="مثلاً: نسخة نهائية للريلز"
                           class="w-full px-3 py-2.5 rounded-xl border-2 border-gray-200 text-sm focus:border-purple-500 focus:outline-none">
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
                    class="w-full sm:w-auto px-5 py-2.5 rounded-xl bg-purple-600 text-white text-sm font-semibold hover:bg-purple-700 inline-flex items-center justify-center gap-1.5">
                <span class="material-icons text-base">cloud_upload</span>
                رفع الملفات
            </button>
        </form>

        @if($task->files->count())
            @php
                $fileGroups = $task->files->groupBy(fn ($f) => $f->upload_batch ?: 'single-'.$f->id);
            @endphp
            <div class="border-t border-gray-100 pt-4 space-y-4">
                <p class="text-xs font-semibold text-gray-500">الملفات المرفوعة ({{ $task->files->count() }})</p>
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
                                            <a href="{{ route('employee.work.files.download', [$task, $file]) }}"
                                               class="text-xs text-indigo-600 hover:underline inline-flex items-center gap-0.5">
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
                                            <form method="POST" action="{{ route('employee.work.files.destroy', [$task, $file]) }}"
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
                @endforeach
            </div>
        @endif
    </section>

    {{-- روابط النشر --}}
    @if(!empty($task->publish_links) && is_array($task->publish_links))
        <section class="card rounded-2xl p-5">
            <div class="flex items-center gap-2 mb-4">
                <div class="w-9 h-9 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center">
                    <span class="material-icons text-xl">link</span>
                </div>
                <h3 class="font-bold text-gray-800">روابط النشر</h3>
            </div>
            <div class="space-y-2">
                @foreach($task->publish_links as $platform => $url)
                    @if($url)
                        <a href="{{ $url }}" target="_blank" rel="noopener"
                           class="flex items-center justify-between gap-3 rounded-xl border border-teal-100 bg-teal-50/50 px-3 py-2.5 hover:bg-teal-50 transition-colors">
                            <span class="text-sm font-medium text-teal-800">{{ \App\Models\WorkTask::platforms()[$platform] ?? $platform }}</span>
                            <span class="material-icons text-teal-500 text-base">open_in_new</span>
                        </a>
                    @endif
                @endforeach
            </div>
        </section>
    @endif

    {{-- تحديث الحالة --}}
    <section class="card rounded-2xl p-6 border border-indigo-100 shadow-sm">
        <div class="flex items-center gap-2 mb-5">
            <div class="w-9 h-9 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                <span class="material-icons text-xl">update</span>
            </div>
            <div>
                <h3 class="font-bold text-gray-800">تحديث المهمة</h3>
                <p class="text-xs text-gray-500">غيّر الحالة وأضف ملاحظاتك</p>
            </div>
        </div>

        <form method="POST" action="{{ route('employee.work.status', $task) }}" class="space-y-4">
            @csrf @method('PATCH')
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">الحالة</label>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                    @foreach($statuses as $key => $label)
                        <label class="relative cursor-pointer">
                            <input type="radio" name="status" value="{{ $key }}" class="peer sr-only" @checked($task->status === $key)>
                            <span class="block text-center text-xs font-semibold px-2 py-2.5 rounded-xl border-2 border-gray-200 text-gray-600
                                         peer-checked:border-indigo-500 peer-checked:bg-indigo-50 peer-checked:text-indigo-700
                                         hover:border-gray-300 transition-colors">
                                {{ $label }}
                            </span>
                        </label>
                    @endforeach
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">ملاحظات</label>
                <textarea name="notes" rows="4" placeholder="اكتب أي ملاحظات أو تحديثات..."
                          class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-indigo-500 focus:outline-none text-sm leading-6">{{ $task->notes }}</textarea>
            </div>
            <button type="submit" class="btn-primary text-white w-full sm:w-auto px-8 py-3 rounded-xl font-semibold inline-flex items-center justify-center gap-2">
                <span class="material-icons text-base">save</span>
                حفظ التحديث
            </button>
        </form>
    </section>
</div>
@endsection

@section('scripts')
<script>
    (function () {
        const kindSelect = document.getElementById('assetKindSelect');
        const fileInput = document.getElementById('designFileInput');
        const acceptHint = document.getElementById('designAcceptHint');
        const fileNameEl = document.getElementById('designFileName');
        const dropZone = document.getElementById('designDropZone');
        const form = document.getElementById('designUploadForm');
        const btn = document.getElementById('designUploadBtn');

        const kinds = {
            image: {
                accept: '.jpg,.jpeg,.png,.gif,.webp',
                hint: 'صور: JPG, PNG, GIF, WEBP — حتى 100MB لكل ملف · أكتر من ملف = فولدر',
            },
            video: {
                accept: '.mp4,.mov,.webm,.m4v',
                hint: 'فيديو: MP4, MOV, WEBM — حتى 100MB لكل ملف · أكتر من ملف = فولدر',
            },
            pdf: {
                accept: '.pdf',
                hint: 'PDF — حتى 100MB لكل ملف · أكتر من ملف = فولدر',
            },
        };

        function syncKind() {
            const meta = kinds[kindSelect?.value] || kinds.image;
            if (fileInput) fileInput.setAttribute('accept', meta.accept);
            if (acceptHint) acceptHint.textContent = meta.hint;
        }

        function updateSelectedLabel() {
            if (!fileNameEl || !fileInput) return;
            const list = fileInput.files ? Array.from(fileInput.files) : [];
            if (!list.length) {
                fileNameEl.classList.add('hidden');
                fileNameEl.textContent = '';
                return;
            }
            fileNameEl.textContent = list.length === 1
                ? list[0].name
                : ('تم اختيار ' + list.length + ' ملفات — هيتجمعوا في فولدر واحد');
            fileNameEl.classList.remove('hidden');
        }

        kindSelect?.addEventListener('change', function () {
            if (fileInput) fileInput.value = '';
            updateSelectedLabel();
            syncKind();
        });
        syncKind();

        fileInput?.addEventListener('change', updateSelectedLabel);

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
            if (!files?.length || !fileInput) return;
            try {
                const dt = new DataTransfer();
                Array.from(files).forEach(function (f) { dt.items.add(f); });
                fileInput.files = dt.files;
            } catch (err) {}
            fileInput.dispatchEvent(new Event('change'));
        });

        form?.addEventListener('submit', function () {
            if (!btn) return;
            // لا نعطّل الزر فورًا — بعض المتصفحات تلغي الإرسال لو الزر اتـ disable أثناء submit
            btn.innerHTML = '<span class="material-icons text-base animate-pulse">hourglass_top</span> جاري الرفع...';
            btn.setAttribute('aria-busy', 'true');
            setTimeout(function () { btn.disabled = true; }, 50);
        });
    })();
</script>
@endsection
