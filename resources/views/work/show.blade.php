@extends($workLayout ?? 'layouts.dashboard')

@section('title', $activity->title)
@section('page-title', 'مساحة العمل')
@section('page-description', $activity->title)

@section('content')
@php
    $kindRoleMap = $kindRoleMap ?? [];
    // موظف مقترح لكل دور لعرض التلميح
    $roleEmployee = $employees->groupBy('role')->map(fn($g) => $g->first());
@endphp
<div class="max-w-6xl mx-auto space-y-6">

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

    <a href="{{ work_route('index') }}" class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-primary">
        <span class="material-icons text-lg">arrow_forward</span>
        رجوع لمساحة العمل
    </a>

    {{-- رأس النشاط --}}
    <div class="card rounded-2xl p-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl bg-indigo-50 text-primary flex items-center justify-center">
                    <span class="material-icons text-3xl">{{ $activity->type_icon }}</span>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-gray-800">{{ $activity->title }}</h2>
                    <div class="flex items-center gap-2 mt-1 text-sm text-gray-500">
                        <span>{{ $activity->type_label }}</span>
                        @if($activity->event_date)
                            <span>·</span>
                            <span class="flex items-center gap-1"><span class="material-icons text-sm">event</span>{{ $activity->event_date->format('Y/m/d') }}</span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-2 flex-wrap">
                <button type="button" onclick="document.getElementById('shareModal').classList.remove('hidden')"
                        class="px-3 py-2 rounded-xl bg-indigo-50 text-indigo-700 hover:bg-indigo-100 text-sm inline-flex items-center gap-1" title="رابط عام">
                    <span class="material-icons text-lg">share</span>
                    رابط عام
                </button>
                {{-- تغيير الحالة سريعًا --}}
                <form method="POST" action="{{ work_route('update', $activity) }}" class="flex items-center gap-2">
                    @csrf @method('PUT')
                    <input type="hidden" name="title" value="{{ $activity->title }}">
                    <input type="hidden" name="type" value="{{ $activity->type }}">
                    <input type="hidden" name="description" value="{{ $activity->description }}">
                    <input type="hidden" name="event_date" value="{{ optional($activity->event_date)->format('Y-m-d') }}">
                    <select name="status" onchange="this.form.submit()" class="px-3 py-2 rounded-xl border-2 border-gray-200 text-sm focus:border-primary focus:outline-none">
                        @foreach($activityStatuses as $key => $label)
                            <option value="{{ $key }}" @selected($activity->status === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                </form>
                <button onclick="document.getElementById('editActivityModal').classList.remove('hidden')"
                        class="p-2.5 rounded-xl bg-gray-100 text-gray-600 hover:bg-gray-200" title="تعديل">
                    <span class="material-icons text-lg">edit</span>
                </button>
                <button onclick="confirmDelete('{{ work_route('destroy', $activity, false) }}', 'حذف النشاط', 'سيتم حذف النشاط وكل مهامه.')"
                        class="p-2.5 rounded-xl bg-red-50 text-red-600 hover:bg-red-100" title="حذف">
                    <span class="material-icons text-lg">delete</span>
                </button>
            </div>
        </div>

        @if($activity->description)
            <p class="text-sm text-gray-600 mt-4 bg-gray-50 rounded-xl p-3 whitespace-pre-line">{{ $activity->description }}</p>
        @endif

        <div class="mt-4">
            <div class="flex items-center justify-between text-xs text-gray-500 mb-1">
                <span>التقدّم</span>
                <span>{{ $activity->progress }}%</span>
            </div>
            <div class="w-full h-2.5 bg-gray-100 rounded-full overflow-hidden">
                <div class="h-full bg-gradient-to-l from-primary to-secondary rounded-full" style="width: {{ $activity->progress }}%"></div>
            </div>
        </div>
    </div>

    {{-- تنظيم ملفات المحاضرة (حسب دليل تنظيم ملفات المحاضرة) --}}
    @if($activity->is_lecture)
    <details class="card rounded-2xl overflow-hidden">
        <summary class="p-5 cursor-pointer flex items-center justify-between select-none">
            <span class="font-bold text-gray-800 flex items-center gap-2">
                <span class="material-icons text-teal-600">folder_open</span>
                تنظيم ملفات المحاضرة
            </span>
            <span class="text-xs text-gray-500">اضغط للعرض</span>
        </summary>
        <div class="px-5 pb-5 space-y-4">
            <div class="bg-teal-50 border border-teal-200 rounded-xl p-3">
                <p class="text-xs text-gray-500 mb-1">فولدر المحاضرة:</p>
                <code class="text-sm font-semibold text-teal-800" dir="ltr">{{ $activity->suggested_folder }}</code>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-3 text-sm">
                <div class="border border-gray-200 rounded-xl p-3">
                    <p class="font-semibold text-gray-800 flex items-center gap-1 mb-2">
                        <span class="material-icons text-base text-indigo-500">movie</span>
                        Final_Lecture
                    </p>
                    <ul class="text-xs text-gray-600 space-y-1" dir="ltr">
                        <li><code>Final_YouTube.mp4</code></li>
                        <li><code>Youtube_Cover.png</code></li>
                        <li><code>youtube_link.txt</code></li>
                    </ul>
                    <p class="text-xs text-gray-400 mt-2">النسخة النهائية المرفوعة يوتيوب</p>
                </div>
                <div class="border border-gray-200 rounded-xl p-3">
                    <p class="font-semibold text-gray-800 flex items-center gap-1 mb-2">
                        <span class="material-icons text-base text-red-500">video_library</span>
                        Marketing_Clips
                    </p>
                    <ul class="text-xs text-gray-600 space-y-1" dir="ltr">
                        <li><code>Lecture_Clips/</code></li>
                        <li><code>Teasers/Teaser_Before.mp4</code></li>
                        <li><code>Teasers/Teaser_After.mp4</code></li>
                    </ul>
                    <p class="text-xs text-gray-400 mt-2">مقاطع من المحاضرة + تيزرات</p>
                </div>
                <div class="border border-gray-200 rounded-xl p-3">
                    <p class="font-semibold text-gray-800 flex items-center gap-1 mb-2">
                        <span class="material-icons text-base text-purple-500">palette</span>
                        Marketing_Graphics
                    </p>
                    <ul class="text-xs text-gray-600 space-y-1" dir="ltr">
                        <li><code>Announcement/</code></li>
                        <li><code>Reminder_1Hour/</code></li>
                        <li><code>Testimonials/</code></li>
                        <li><code>Reels_Design/</code></li>
                    </ul>
                    <p class="text-xs text-gray-400 mt-2">إعلان + تذكير + آراء + ريلز</p>
                </div>
            </div>

            <p class="text-xs text-gray-500 bg-gray-50 rounded-xl p-3">
                <span class="font-semibold">القاعدة الذهبية:</span>
                كل حاجة تخص المحاضرة دي مكانها الأساسي جوه فولدرها — أي نسخة في <code dir="ltr">03_Social_Content</code> مؤقتة لجدول النشر بس. التسجيل الخام وقت اللايف يروح <code dir="ltr">05_Live_Recordings</code> مؤقتًا لحد المونتاج.
            </p>
        </div>
    </details>
    @endif

    <div class="space-y-4">
        <div class="flex items-center justify-between gap-3 flex-wrap">
            <div>
                <h3 class="font-bold text-gray-800">المهام ({{ $contentCounts['total'] }})</h3>
                <div class="flex flex-wrap items-center gap-2 mt-2">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-blue-50 text-blue-700 text-xs font-semibold border border-blue-100">
                        <span class="material-icons text-sm">article</span>
                        بوست {{ $contentCounts['post'] }}
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-rose-50 text-rose-700 text-xs font-semibold border border-rose-100">
                        <span class="material-icons text-sm">movie</span>
                        ريلز {{ $contentCounts['reels'] }}
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-amber-50 text-amber-700 text-xs font-semibold border border-amber-100">
                        <span class="material-icons text-sm">view_carousel</span>
                        كروسيل {{ $contentCounts['carousel'] }}
                    </span>
                    @if($contentCounts['other'] > 0)
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-gray-50 text-gray-600 text-xs font-semibold border border-gray-200">
                            أخرى {{ $contentCounts['other'] }}
                        </span>
                    @endif
                </div>
            </div>
            <div class="flex items-center gap-2 flex-wrap">
                <button type="button"
                        onclick="openParseBulkModal()"
                        class="px-4 py-2 rounded-xl font-medium text-sm inline-flex items-center gap-1.5 bg-indigo-50 text-indigo-700 border border-indigo-100 hover:bg-indigo-100">
                    <span class="material-icons text-base">auto_awesome</span>
                    لصق المحتوى وتقسيم التاسكات
                </button>
                <button type="button"
                        onclick="openAddTaskModal()"
                        class="btn-primary text-white px-4 py-2 rounded-xl font-medium text-sm inline-flex items-center gap-1.5">
                    <span class="material-icons text-base">add_task</span>
                    إضافة تاسك محتوى
                </button>
            </div>
        </div>

        <div class="space-y-4" id="pipelineBoard">
            <p class="text-xs text-gray-500">اسحب الكارت لترتيب داخل المرحلة، أو أفلت على مرحلة تانية للنقل.</p>
            @foreach($pipelineStages as $stage)
                <section class="card rounded-2xl overflow-hidden pipeline-stage"
                         data-stage="{{ $stage['key'] }}">
                    <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between gap-3
                        @if($stage['key'] === 'writing') bg-blue-50
                        @elseif($stage['key'] === 'design') bg-purple-50
                        @elseif($stage['key'] === 'ready_to_publish') bg-teal-50
                        @else bg-green-50
                        @endif">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center
                                @if($stage['key'] === 'writing') bg-blue-100 text-blue-700
                                @elseif($stage['key'] === 'design') bg-purple-100 text-purple-700
                                @elseif($stage['key'] === 'ready_to_publish') bg-teal-100 text-teal-700
                                @else bg-green-100 text-green-700
                                @endif">
                                <span class="material-icons">{{ $stage['icon'] }}</span>
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-800">{{ $stage['label'] }}</h4>
                                <p class="text-xs text-gray-500">
                                    @if($stage['key'] === 'writing')
                                        عند كاتب المحتوى
                                    @elseif($stage['key'] === 'design')
                                        عند فريق التصميم — ارفع صورة / فيديو / PDF من صفحة التفاصيل
                                    @elseif($stage['key'] === 'ready_to_publish')
                                        جاهز للنشر — أضف روابط النشر من التفاصيل
                                    @else
                                        تم النشر — الحالة تتحول لاكتمال
                                    @endif
                                    · <span class="stage-count">{{ $stage['count'] }}</span> عنصر
                                </p>
                            </div>
                        </div>
                        <span class="stage-count-badge px-2.5 py-1 rounded-lg text-xs font-bold
                            @if($stage['key'] === 'writing') bg-blue-100 text-blue-700
                            @elseif($stage['key'] === 'design') bg-purple-100 text-purple-700
                            @elseif($stage['key'] === 'ready_to_publish') bg-teal-100 text-teal-700
                            @else bg-green-100 text-green-700
                            @endif">
                            {{ $stage['count'] }}
                        </span>
                    </div>

                    <div class="p-4 stage-dropzone min-h-[120px] transition-colors"
                         data-stage="{{ $stage['key'] }}">
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 stage-cards">
                            @foreach($stage['tasks'] as $task)
                                @php
                                    $stageOwnerId = match($stage['key']) {
                                        'design' => $task->designer_id ?? $task->assigned_to,
                                        'ready_to_publish', 'published' => $task->assigned_to,
                                        default => $task->content_writer_id ?? $task->assigned_to,
                                    };
                                    $assigneePool = match($stage['key']) {
                                        'design' => ($designers->isNotEmpty() ? $designers : $employees),
                                        'writing' => (($contentWriters ?? collect())->isNotEmpty() ? $contentWriters : $employees),
                                        'ready_to_publish', 'published' => (($publishers ?? collect())->isNotEmpty() ? $publishers : $employees),
                                        default => $employees,
                                    };
                                    // لو المسؤول الحالي مش في القائمة المختصرة، أضفه
                                    if ($stageOwnerId && ! $assigneePool->contains('id', $stageOwnerId)) {
                                        $current = $employees->firstWhere('id', $stageOwnerId);
                                        if ($current) {
                                            $assigneePool = $assigneePool->push($current)->unique('id')->values();
                                        }
                                    }
                                    $chipActive = match($stage['key']) {
                                        'design' => 'bg-purple-600 text-white border-purple-600 shadow-sm',
                                        'ready_to_publish' => 'bg-teal-600 text-white border-teal-600 shadow-sm',
                                        'published' => 'bg-green-600 text-white border-green-600 shadow-sm',
                                        default => 'bg-blue-600 text-white border-blue-600 shadow-sm',
                                    };
                                    $chipIdle = match($stage['key']) {
                                        'design' => 'bg-purple-50 text-purple-800 border-purple-200 hover:border-purple-400',
                                        'ready_to_publish' => 'bg-teal-50 text-teal-800 border-teal-200 hover:border-teal-400',
                                        'published' => 'bg-green-50 text-green-800 border-green-200 hover:border-green-400',
                                        default => 'bg-blue-50 text-blue-800 border-blue-200 hover:border-blue-400',
                                    };
                                @endphp
                                <div role="link" tabindex="0"
                                   draggable="true"
                                   data-task-id="{{ $task->id }}"
                                   data-stage="{{ $stage['key'] }}"
                                   data-href="{{ work_route('tasks.show', [$activity, $task], false) }}"
                                   class="pipeline-card group rounded-2xl border border-gray-200 bg-white p-4 min-h-[110px] flex flex-col justify-between hover:border-primary/50 hover:shadow-md transition-all cursor-grab active:cursor-grabbing {{ $task->is_overdue ? 'border-r-4 border-r-red-400' : '' }} {{ $stage['key'] === 'design' ? 'ring-1 ring-purple-100' : '' }}">
                                    <div>
                                        @if($task->content_type_label)
                                            <span class="inline-block px-2 py-0.5 text-[10px] rounded-md bg-indigo-50 text-indigo-700 mb-2">{{ $task->content_type_label }}</span>
                                        @endif
                                        <h5 class="text-base font-bold text-gray-900 leading-snug line-clamp-3 group-hover:text-primary">
                                            {{ $task->title }}
                                        </h5>
                                    </div>
                                    <div class="mt-3 space-y-2 card-controls" data-no-nav>
                                        <label class="block text-[10px] text-gray-400 mb-0.5">
                                            @if($stage['key'] === 'design') اختَر المصمم
                                            @elseif($stage['key'] === 'writing') اختَر كاتب المحتوى
                                            @elseif($stage['key'] === 'ready_to_publish') اختَر مسؤول النشر
                                            @else اختَر الناشر
                                            @endif
                                        </label>
                                        <div class="card-assignee-group flex flex-wrap gap-1.5"
                                             data-task-id="{{ $task->id }}"
                                             data-stage="{{ $stage['key'] }}"
                                             data-active-class="{{ $chipActive }}"
                                             data-idle-class="{{ $chipIdle }}">
                                            @forelse($assigneePool as $emp)
                                                @php $isSelected = (int) $stageOwnerId === (int) $emp->id; @endphp
                                                <button type="button"
                                                        class="card-assignee-chip px-2.5 py-1 rounded-lg border text-[11px] font-semibold transition-all {{ $isSelected ? $chipActive : $chipIdle }}"
                                                        data-employee-id="{{ $emp->id }}"
                                                        data-selected="{{ $isSelected ? '1' : '0' }}"
                                                        draggable="false"
                                                        title="{{ $emp->role_badge }}">
                                                    {{ $emp->name }}
                                                </button>
                                            @empty
                                                <span class="text-[11px] text-gray-400">لا يوجد موظفون لهذا الدور</span>
                                            @endforelse
                                        </div>
                                        <div class="flex items-center gap-1.5">
                                            <a href="{{ work_route('tasks.edit', [$activity, $task]) }}"
                                               class="card-edit-btn flex-1 inline-flex items-center justify-center gap-1 px-2 py-1.5 rounded-lg bg-gray-100 text-gray-700 text-xs font-medium hover:bg-gray-200"
                                               draggable="false">
                                                <span class="material-icons text-sm">edit</span>
                                                تعديل
                                            </a>
                                            <a href="{{ work_route('tasks.show', [$activity, $task]) }}"
                                               class="card-detail-btn flex-1 inline-flex items-center justify-center gap-1 px-2 py-1.5 rounded-lg bg-indigo-50 text-indigo-700 text-xs font-medium hover:bg-indigo-100"
                                               draggable="false">
                                                <span class="material-icons text-sm">visibility</span>
                                                تفاصيل
                                            </a>
                                            <a href="{{ work_route('tasks.show', [$activity, $task]) }}#task-log"
                                               class="card-detail-btn inline-flex items-center justify-center px-2 py-1.5 rounded-lg bg-amber-50 text-amber-700 text-xs font-medium hover:bg-amber-100"
                                               draggable="false" title="سجل التغييرات">
                                                <span class="material-icons text-sm">history</span>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="stage-empty text-center py-8 text-sm text-gray-400 {{ $stage['tasks']->count() ? 'hidden' : '' }}">
                            اسحب كارت هنا أو لا يوجد محتوى
                        </div>
                    </div>
                </section>
            @endforeach
        </div>
    </div>
</div>

{{-- مودال الرابط العام --}}
<div id="shareModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40">
    <div class="bg-white rounded-2xl w-full max-w-lg p-6 shadow-2xl">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                <span class="material-icons text-indigo-600">share</span>
                رابط عام للمحتوى
            </h3>
            <button type="button" onclick="document.getElementById('shareModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <span class="material-icons">close</span>
            </button>
        </div>
        <p class="text-sm text-gray-500 mb-4">
            أي شخص معه الرابط يقدر يشوف المحتوى بدون تسجيل دخول (عرض فقط).
        </p>

        @if($activity->share_token)
            <label class="block text-xs font-medium text-gray-600 mb-1">الرابط</label>
            <div class="flex gap-2 mb-4">
                <input type="text" id="publicShareUrl" readonly
                       value="{{ $activity->public_share_url }}"
                       class="flex-1 px-3 py-2.5 rounded-xl border-2 border-indigo-100 bg-indigo-50/40 text-sm focus:outline-none" dir="ltr">
                <button type="button" onclick="copyShareUrl()"
                        class="px-4 py-2.5 rounded-xl bg-indigo-600 text-white text-sm font-medium hover:bg-indigo-700 shrink-0">
                    نسخ
                </button>
            </div>
            <p id="copyShareHint" class="text-xs text-teal-600 mb-4 hidden">تم نسخ الرابط</p>
            <div class="flex flex-wrap gap-2">
                <a href="{{ $activity->public_share_url }}" target="_blank"
                   class="px-4 py-2 rounded-xl bg-gray-100 text-gray-700 text-sm hover:bg-gray-200 inline-flex items-center gap-1">
                    <span class="material-icons text-base">open_in_new</span>
                    فتح
                </a>
                <form method="POST" action="{{ work_route('share.regenerate', $activity) }}" onsubmit="return confirm('تجديد الرابط؟ الرابط القديم هيتوقف.')">
                    @csrf
                    <button type="submit" class="px-4 py-2 rounded-xl bg-amber-50 text-amber-700 text-sm hover:bg-amber-100">تجديد الرابط</button>
                </form>
                <form method="POST" action="{{ work_route('share.disable', $activity) }}" onsubmit="return confirm('إيقاف الرابط العام؟')">
                    @csrf
                    <button type="submit" class="px-4 py-2 rounded-xl bg-red-50 text-red-600 text-sm hover:bg-red-100">إيقاف</button>
                </form>
            </div>
        @else
            <form method="POST" action="{{ work_route('share.enable', $activity) }}">
                @csrf
                <button type="submit" class="btn-primary text-white w-full py-2.5 rounded-xl font-medium inline-flex items-center justify-center gap-2">
                    <span class="material-icons text-base">link</span>
                    تفعيل رابط عام
                </button>
            </form>
        @endif
    </div>
</div>

{{-- مودال لصق المحتوى وتقسيمه --}}
<div id="parseBulkModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40">
    <div class="bg-white rounded-2xl w-full max-w-2xl p-6 shadow-2xl max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between mb-2">
            <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                <span class="material-icons text-indigo-600">auto_awesome</span>
                لصق المحتوى وتقسيم التاسكات
            </h3>
            <button type="button" onclick="closeParseBulkModal()" class="text-gray-400 hover:text-gray-600">
                <span class="material-icons">close</span>
            </button>
        </div>
        <p class="text-xs text-gray-500 mb-4">
            الصق النص كامل مرة واحدة — DeepSeek يقسّمه لتاسكات بدون ما يغيّر الكابشن أو المطلوب، ويلخّص المطلوب من المصمم.
        </p>
        <form method="POST" action="{{ work_route('tasks.parse-bulk', $activity) }}" id="parseBulkForm" class="space-y-3">
            @csrf
            <textarea name="bulk_text" id="bulkText" rows="8" required minlength="20"
                      placeholder="الصق هنا كل المحتوى مرة واحدة: البوستات، الكابشن، TOV، مرجع التصميم، المنصات، مواعيد النشر..."
                      class="w-full px-4 py-3 rounded-xl border-2 border-indigo-100 text-sm focus:border-indigo-400 focus:outline-none bg-white">{{ old('bulk_text') }}</textarea>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">كاتب المحتوى (اختياري)</label>
                    <select name="content_writer_id" class="w-full px-3 py-2 rounded-xl border-2 border-gray-200 text-sm focus:border-primary focus:outline-none">
                        <option value="">اقتراح تلقائي</option>
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}" @selected(old('content_writer_id') == $emp->id)>{{ $emp->name }} — {{ $emp->role_badge }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">المصمم (اختياري)</label>
                    <select name="designer_id" class="w-full px-3 py-2 rounded-xl border-2 border-gray-200 text-sm focus:border-primary focus:outline-none">
                        <option value="">اقتراح تلقائي</option>
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}" @selected(old('designer_id') == $emp->id)>{{ $emp->name }} — {{ $emp->role_badge }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" id="parseBulkBtn"
                        class="btn-primary text-white flex-1 py-2.5 rounded-xl font-medium inline-flex items-center justify-center gap-2">
                    <span class="material-icons text-base">psychology</span>
                    <span id="parseBulkBtnLabel">حلّل وقسّم التاسكات</span>
                </button>
                <button type="button" onclick="closeParseBulkModal()"
                        class="px-5 py-2.5 rounded-xl font-medium bg-gray-100 text-gray-700 hover:bg-gray-200">إلغاء</button>
            </div>
        </form>
    </div>
</div>

{{-- مودال إضافة تاسك محتوى --}}
<div id="addTaskModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40">
    <div class="bg-white rounded-2xl w-full max-w-2xl p-6 shadow-2xl max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between mb-5">
            <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                <span class="material-icons text-primary">add_task</span>
                إضافة تاسك محتوى
            </h3>
            <button type="button" onclick="closeAddTaskModal()" class="text-gray-400 hover:text-gray-600">
                <span class="material-icons">close</span>
            </button>
        </div>
        <form method="POST" action="{{ work_route('tasks.store', $activity) }}" class="space-y-3">
            @csrf
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">العنوان</label>
                <input type="text" name="title" required placeholder="مثال: بوست إعلان المحاضرة"
                       class="w-full px-3 py-2 rounded-xl border-2 border-gray-200 text-sm focus:border-primary focus:outline-none">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">الفكرة</label>
                <textarea name="idea" rows="2" placeholder="الفكرة باختصار..."
                          class="w-full px-3 py-2 rounded-xl border-2 border-gray-200 text-sm focus:border-primary focus:outline-none"></textarea>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">TOV</label>
                    <textarea name="tov" rows="2" placeholder="Tone of Voice للمحتوى..."
                              class="w-full px-3 py-2 rounded-xl border-2 border-gray-200 text-sm focus:border-primary focus:outline-none"></textarea>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Caption</label>
                    <textarea name="caption" rows="2" placeholder="نص الكابشن..."
                              class="w-full px-3 py-2 rounded-xl border-2 border-gray-200 text-sm focus:border-primary focus:outline-none"></textarea>
                </div>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">نوع المحتوى</label>
                <select name="content_type" class="w-full px-3 py-2 rounded-xl border-2 border-gray-200 text-sm focus:border-primary focus:outline-none">
                    <option value="">— اختر —</option>
                    @foreach($contentTypes as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">مرجع التصميم</label>
                <textarea name="design_reference" rows="2" placeholder="تعليمات/روابط للمصمم..."
                          class="w-full px-3 py-2 rounded-xl border-2 border-gray-200 text-sm focus:border-primary focus:outline-none"></textarea>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">المنصات</label>
                <div class="flex flex-wrap gap-2">
                    @foreach($platforms as $key => $label)
                        <label class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg border border-gray-200 text-xs cursor-pointer hover:border-primary">
                            <input type="checkbox" name="platforms[]" value="{{ $key }}" class="rounded border-gray-300 text-primary focus:ring-primary">
                            {{ $label }}
                        </label>
                    @endforeach
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">كاتب المحتوى</label>
                    <select name="content_writer_id" class="w-full px-3 py-2 rounded-xl border-2 border-gray-200 text-sm focus:border-primary focus:outline-none">
                        <option value="">اقتراح تلقائي</option>
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}">{{ $emp->name }} — {{ $emp->role_badge }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">المصمم</label>
                    <select name="designer_id" class="w-full px-3 py-2 rounded-xl border-2 border-gray-200 text-sm focus:border-primary focus:outline-none">
                        <option value="">اقتراح تلقائي</option>
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}">{{ $emp->name }} — {{ $emp->role_badge }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">نوع الشغل</label>
                    <select name="kind" id="addKind" onchange="updateSuggestion()" class="w-full px-3 py-2 rounded-xl border-2 border-gray-200 text-sm focus:border-primary focus:outline-none">
                        @foreach($kinds as $key => $label)
                            <option value="{{ $key }}" @selected($key === 'content')>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">الموظف الحالي</label>
                    <select name="assigned_to" id="addAssignee" onchange="updateSuggestion()" class="w-full px-3 py-2 rounded-xl border-2 border-gray-200 text-sm focus:border-primary focus:outline-none">
                        <option value="">اقتراح تلقائي حسب الدور</option>
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}" data-role="{{ $emp->role }}">{{ $emp->name }} — {{ $emp->role_badge }}</option>
                        @endforeach
                    </select>
                    <p id="suggestionHint" class="text-xs text-teal-600 mt-1"></p>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">التسليم</label>
                    <input type="date" name="due_date"
                           class="w-full px-3 py-2 rounded-xl border-2 border-gray-200 text-sm focus:border-primary focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">موعد النشر</label>
                    <input type="date" name="publish_date"
                           class="w-full px-3 py-2 rounded-xl border-2 border-gray-200 text-sm focus:border-primary focus:outline-none">
                </div>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="btn-primary text-white px-5 py-2.5 rounded-xl font-medium flex-1">إضافة</button>
                <button type="button" onclick="closeAddTaskModal()"
                        class="px-5 py-2.5 rounded-xl font-medium bg-gray-100 text-gray-700 hover:bg-gray-200">إلغاء</button>
            </div>
        </form>
    </div>
</div>

{{-- مودال تعديل النشاط --}}
<div id="editActivityModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40">
    <div class="bg-white rounded-2xl w-full max-w-lg p-6 shadow-2xl">
        <div class="flex items-center justify-between mb-5">
            <h3 class="text-lg font-bold text-gray-800">تعديل النشاط</h3>
            <button onclick="document.getElementById('editActivityModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <span class="material-icons">close</span>
            </button>
        </div>
        <form method="POST" action="{{ work_route('update', $activity) }}" class="space-y-4">
            @csrf @method('PUT')
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">العنوان</label>
                <input type="text" name="title" value="{{ $activity->title }}" required
                       class="w-full px-4 py-2.5 rounded-xl border-2 border-gray-200 focus:border-primary focus:outline-none">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">النوع</label>
                    <select name="type" class="w-full px-4 py-2.5 rounded-xl border-2 border-gray-200 focus:border-primary focus:outline-none">
                        @foreach(\App\Models\WorkActivity::types() as $key => $label)
                            <option value="{{ $key }}" @selected($activity->type === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">الحالة</label>
                    <select name="status" class="w-full px-4 py-2.5 rounded-xl border-2 border-gray-200 focus:border-primary focus:outline-none">
                        @foreach($activityStatuses as $key => $label)
                            <option value="{{ $key }}" @selected($activity->status === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">التاريخ</label>
                <input type="date" name="event_date" value="{{ optional($activity->event_date)->format('Y-m-d') }}"
                       class="w-full px-4 py-2.5 rounded-xl border-2 border-gray-200 focus:border-primary focus:outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">الوصف</label>
                <textarea name="description" rows="3"
                          class="w-full px-4 py-2.5 rounded-xl border-2 border-gray-200 focus:border-primary focus:outline-none">{{ $activity->description }}</textarea>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="btn-primary text-white px-5 py-2.5 rounded-xl font-medium flex-1">حفظ</button>
                <button type="button" onclick="document.getElementById('editActivityModal').classList.add('hidden')"
                        class="px-5 py-2.5 rounded-xl font-medium bg-gray-100 text-gray-700 hover:bg-gray-200">إلغاء</button>
            </div>
        </form>
    </div>
</div>

</div>
@endsection

@section('scripts')
<script>
    const roleLabels = {
        content_writer: 'كاتب محتوى', ad_manager: 'إدارة إعلانات', designer: 'مصمم',
        video_editor: 'مصمم فيديوهات', page_manager: 'إدارة الصفحة', account_manager: 'أكونت منجر',
        monitor: 'مونتير', media_buyer: 'ميديا بايرز'
    };
    const kindRoleMap = @json($kindRoleMap);
    const roleEmployeeName = @json($roleEmployee->map(fn($e) => $e->name));

    function dateValue(value) {
        if (!value) return '';
        return String(value).substring(0, 10);
    }

    function openParseBulkModal() {
        document.getElementById('parseBulkModal').classList.remove('hidden');
        document.getElementById('bulkText')?.focus();
    }

    function closeParseBulkModal() {
        document.getElementById('parseBulkModal').classList.add('hidden');
    }

    function copyShareUrl() {
        const input = document.getElementById('publicShareUrl');
        if (!input) return;
        navigator.clipboard.writeText(input.value).then(function () {
            const hint = document.getElementById('copyShareHint');
            if (hint) {
                hint.classList.remove('hidden');
                setTimeout(function () { hint.classList.add('hidden'); }, 2000);
            }
        }).catch(function () {
            input.select();
            document.execCommand('copy');
        });
    }

    function openAddTaskModal() {
        document.getElementById('addTaskModal').classList.remove('hidden');
        updateSuggestion();
    }

    function closeAddTaskModal() {
        document.getElementById('addTaskModal').classList.add('hidden');
    }

    function updateSuggestion() {
        const kindEl = document.getElementById('addKind');
        const assigneeEl = document.getElementById('addAssignee');
        const hint = document.getElementById('suggestionHint');
        if (!kindEl || !assigneeEl || !hint) return;
        const kind = kindEl.value;
        const assignee = assigneeEl.value;
        if (assignee) { hint.textContent = ''; return; }
        const role = kindRoleMap[kind];
        if (role && roleEmployeeName[role]) {
            hint.textContent = 'سيُعيَّن تلقائيًا إلى: ' + roleEmployeeName[role];
        } else if (role) {
            hint.textContent = 'لا يوجد موظف بدور «' + (roleLabels[role] || role) + '» — سيبقى غير معيّن';
        } else {
            hint.textContent = '';
        }
    }

    document.getElementById('parseBulkForm')?.addEventListener('submit', function () {
        const btn = document.getElementById('parseBulkBtn');
        const label = document.getElementById('parseBulkBtnLabel');
        btn.disabled = true;
        label.textContent = 'جاري التحليل والتقسيم...';
    });

    // —— تغيير المسؤول من على الكارت (أسماء جنب بعض) ——
    (function initCardAssignee() {
        const board = document.getElementById('pipelineBoard');
        if (!board) return;
        const assignUrlTpl = "{{ work_route('tasks.assign', [$activity, 'TASK_ID'], false) }}";

        function csrfToken() {
            return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        }

        function applyChipSelection(group, selectedId) {
            const active = (group.dataset.activeClass || '').split(/\s+/).filter(Boolean);
            const idle = (group.dataset.idleClass || '').split(/\s+/).filter(Boolean);
            group.querySelectorAll('.card-assignee-chip').forEach(function (chip) {
                const on = String(chip.dataset.employeeId) === String(selectedId);
                [...active, ...idle].forEach(function (c) { chip.classList.remove(c); });
                (on ? active : idle).forEach(function (c) { chip.classList.add(c); });
                chip.dataset.selected = on ? '1' : '0';
            });
        }

        board.addEventListener('click', async function (e) {
            const chip = e.target.closest('.card-assignee-chip');
            if (!chip) return;
            e.preventDefault();
            e.stopPropagation();

            const group = chip.closest('.card-assignee-group');
            if (!group) return;
            const taskId = group.dataset.taskId;
            const stage = group.dataset.stage;
            const employeeId = chip.dataset.employeeId || '';
            if (chip.dataset.selected === '1') return;

            const previousId = Array.from(group.querySelectorAll('.card-assignee-chip'))
                .find(function (c) { return c.dataset.selected === '1'; })?.dataset.employeeId || '';

            applyChipSelection(group, employeeId);
            group.querySelectorAll('.card-assignee-chip').forEach(function (c) { c.disabled = true; });

            try {
                const body = new URLSearchParams();
                body.set('employee_id', employeeId);
                body.set('pipeline_stage', stage);
                body.set('_token', csrfToken());
                const res = await fetch(assignUrlTpl.replace('TASK_ID', taskId), {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken(),
                        'Accept': 'application/json',
                        'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    credentials: 'same-origin',
                    body: body.toString(),
                });
                const data = await res.json().catch(function () { return {}; });
                if (!res.ok || !data.success) {
                    throw new Error(data.message || data.error || 'فشل تحديث المسؤول');
                }
            } catch (err) {
                applyChipSelection(group, previousId);
                alert(err.message || 'حدث خطأ');
            } finally {
                group.querySelectorAll('.card-assignee-chip').forEach(function (c) { c.disabled = false; });
            }
        });
    })();

    // —— Drag & drop: ترتيب داخل المرحلة + نقل بين المراحل ——
    (function initPipelineDragDrop() {
        const board = document.getElementById('pipelineBoard');
        if (!board) return;

        const moveUrlTpl = "{{ work_route('tasks.move-stage', [$activity, 'TASK_ID'], false) }}";
        const reorderUrl = "{{ work_route('tasks.reorder', $activity, false) }}";
        let dragTaskId = null;
        let dragFromStage = null;
        let dragCard = null;
        let dragFromIndex = null;
        let didDrag = false;

        function csrfToken() {
            return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        }

        function refreshStageCounts() {
            board.querySelectorAll('.pipeline-stage').forEach(function (section) {
                const zone = section.querySelector('.stage-dropzone');
                const count = zone.querySelectorAll('.pipeline-card').length;
                section.querySelectorAll('.stage-count').forEach(function (el) { el.textContent = count; });
                section.querySelectorAll('.stage-count-badge').forEach(function (el) { el.textContent = count; });
                const empty = zone.querySelector('.stage-empty');
                if (empty) empty.classList.toggle('hidden', count > 0);
            });
        }

        function stageTaskIds(stage) {
            const zone = board.querySelector('.stage-dropzone[data-stage="' + stage + '"]');
            if (!zone) return [];
            return Array.from(zone.querySelectorAll('.pipeline-card')).map(function (card) {
                return card.dataset.taskId;
            });
        }

        function placeCard(cardsWrap, card, clientX, clientY) {
            const others = Array.from(cardsWrap.querySelectorAll('.pipeline-card')).filter(function (el) {
                return el !== card;
            });
            if (!others.length) {
                cardsWrap.appendChild(card);
                return;
            }

            let closest = null;
            let closestOffset = Number.NEGATIVE_INFINITY;
            others.forEach(function (child) {
                const box = child.getBoundingClientRect();
                const offset = clientY - (box.top + box.height / 2);
                if (offset < 0 && offset > closestOffset) {
                    closestOffset = offset;
                    closest = child;
                }
            });

            if (closest) {
                cardsWrap.insertBefore(card, closest);
            } else {
                // لو تحت كل الكروت أو على يمين/يسار آخر كارت: حط في الآخر
                const last = others[others.length - 1];
                const box = last.getBoundingClientRect();
                if (clientX > box.left + box.width / 2 && clientY < box.bottom) {
                    // في شبكة RTL/LTR: لو الماوس على الجانب التاني من آخر عنصر ظاهر في نفس الصف
                    let insertBeforeEl = null;
                    for (let i = 0; i < others.length; i++) {
                        const b = others[i].getBoundingClientRect();
                        if (clientY >= b.top - 8 && clientY <= b.bottom + 8 && clientX > b.left + b.width / 2) {
                            insertBeforeEl = others[i];
                            break;
                        }
                    }
                    if (insertBeforeEl) cardsWrap.insertBefore(card, insertBeforeEl);
                    else cardsWrap.appendChild(card);
                } else {
                    cardsWrap.appendChild(card);
                }
            }
        }

        async function postForm(url, fields) {
            const body = new URLSearchParams();
            Object.keys(fields).forEach(function (key) {
                const value = fields[key];
                if (Array.isArray(value)) {
                    value.forEach(function (item) { body.append(key + '[]', item); });
                } else {
                    body.set(key, value);
                }
            });
            body.set('_token', csrfToken());
            const res = await fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken(),
                    'Accept': 'application/json',
                    'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                credentials: 'same-origin',
                body: body.toString(),
            });
            let data = {};
            try { data = await res.json(); } catch (_) {}
            if (!res.ok || data.success === false) {
                throw new Error(data.message || data.error || ('فشل الطلب (' + res.status + ')'));
            }
            return data;
        }

        board.addEventListener('mousedown', function (e) {
            if (e.target.closest('.card-controls, .card-assignee-group, .card-assignee-chip, .card-edit-btn, .card-detail-btn, select, a, button')) {
                const card = e.target.closest('.pipeline-card');
                if (card) card.setAttribute('draggable', 'false');
            }
        });
        board.addEventListener('mouseup', function () {
            board.querySelectorAll('.pipeline-card').forEach(function (card) {
                card.setAttribute('draggable', 'true');
            });
        });

        board.addEventListener('dragstart', function (e) {
            if (e.target.closest('.card-controls, .card-assignee-chip, select, a, button')) {
                e.preventDefault();
                return;
            }
            const card = e.target.closest('.pipeline-card');
            if (!card) return;
            dragTaskId = card.dataset.taskId;
            dragFromStage = card.dataset.stage;
            dragCard = card;
            dragFromIndex = Array.from(card.parentElement?.children || []).indexOf(card);
            didDrag = true;
            card.classList.add('opacity-50');
            e.dataTransfer.effectAllowed = 'move';
            e.dataTransfer.setData('text/plain', String(dragTaskId));
        });

        board.addEventListener('dragend', function (e) {
            const card = e.target.closest('.pipeline-card');
            if (card) card.classList.remove('opacity-50');
            board.querySelectorAll('.stage-dropzone').forEach(function (z) {
                z.classList.remove('bg-indigo-50', 'ring-2', 'ring-indigo-300', 'ring-inset');
            });
            setTimeout(function () {
                dragTaskId = null;
                dragFromStage = null;
                dragCard = null;
                dragFromIndex = null;
                didDrag = false;
            }, 50);
        });

        board.addEventListener('dragover', function (e) {
            const zone = e.target.closest('.stage-dropzone');
            if (!zone || !dragTaskId || !dragCard) return;
            e.preventDefault();
            e.dataTransfer.dropEffect = 'move';
            board.querySelectorAll('.stage-dropzone').forEach(function (z) {
                z.classList.toggle('bg-indigo-50', z === zone);
                z.classList.toggle('ring-2', z === zone);
                z.classList.toggle('ring-indigo-300', z === zone);
                z.classList.toggle('ring-inset', z === zone);
            });

            const cardsWrap = zone.querySelector('.stage-cards');
            if (cardsWrap) placeCard(cardsWrap, dragCard, e.clientX, e.clientY);
        });

        board.addEventListener('dragleave', function (e) {
            const zone = e.target.closest('.stage-dropzone');
            if (!zone) return;
            if (zone.contains(e.relatedTarget)) return;
            zone.classList.remove('bg-indigo-50', 'ring-2', 'ring-indigo-300', 'ring-inset');
        });

        board.addEventListener('drop', async function (e) {
            e.preventDefault();
            e.stopPropagation();

            const zone = e.target.closest('.stage-dropzone');
            if (!zone || !dragTaskId || !dragCard) return;
            zone.classList.remove('bg-indigo-50', 'ring-2', 'ring-indigo-300', 'ring-inset');

            const toStage = zone.dataset.stage;
            const fromStage = dragFromStage;
            const taskId = dragTaskId;
            const card = dragCard;
            const fromIndex = dragFromIndex;
            const fromZone = board.querySelector('.stage-dropzone[data-stage="' + fromStage + '"]');
            const cardsWrap = zone.querySelector('.stage-cards');
            if (!cardsWrap) return;

            placeCard(cardsWrap, card, e.clientX, e.clientY);
            card.dataset.stage = toStage;
            refreshStageCounts();

            const newIds = stageTaskIds(toStage);
            const newIndex = newIds.indexOf(String(taskId));
            const sameStage = toStage === fromStage;
            const samePosition = sameStage && newIndex === fromIndex;
            if (samePosition) return;

            try {
                if (!sameStage) {
                    await postForm(moveUrlTpl.replace('TASK_ID', taskId), {
                        pipeline_stage: toStage,
                    });
                }
                await postForm(reorderUrl, {
                    pipeline_stage: toStage,
                    task_ids: newIds,
                });
            } catch (err) {
                // رجّع للكارت مكانه القديم تقريبًا
                if (fromZone) {
                    const fromWrap = fromZone.querySelector('.stage-cards');
                    if (fromWrap) {
                        const children = Array.from(fromWrap.children);
                        if (fromIndex != null && fromIndex < children.length) {
                            fromWrap.insertBefore(card, children[fromIndex]);
                        } else {
                            fromWrap.appendChild(card);
                        }
                    }
                    card.dataset.stage = fromStage;
                    refreshStageCounts();
                }
                alert(err.message || 'حدث خطأ أثناء ترتيب الكارت');
            }
        });

        board.addEventListener('click', function (e) {
            if (e.target.closest('.card-controls, .card-assignee-group, .card-assignee-chip, .card-edit-btn, .card-detail-btn, select, a, button')) {
                return;
            }
            const card = e.target.closest('.pipeline-card');
            if (!card) return;
            if (didDrag) {
                e.preventDefault();
                e.stopPropagation();
                didDrag = false;
                return;
            }
            const href = card.dataset.href;
            if (href) window.location.href = href;
        });

        board.addEventListener('keydown', function (e) {
            if (e.key !== 'Enter' && e.key !== ' ') return;
            if (e.target.closest('select, a, button')) return;
            const card = e.target.closest('.pipeline-card');
            if (!card?.dataset.href) return;
            e.preventDefault();
            window.location.href = card.dataset.href;
        });
    })();

    @if(old('bulk_text'))
    document.addEventListener('DOMContentLoaded', openParseBulkModal);
    @endif
</script>
@endsection
