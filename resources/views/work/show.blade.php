@extends('layouts.dashboard')

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

    <a href="{{ route('work.index') }}" class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-primary">
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
                <form method="POST" action="{{ route('work.update', $activity) }}" class="flex items-center gap-2">
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
                <button onclick="confirmDelete('{{ route('work.destroy', $activity) }}', 'حذف النشاط', 'سيتم حذف النشاط وكل مهامه.')"
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

        <div class="space-y-4">
            @foreach($pipelineStages as $stage)
                <section class="card rounded-2xl overflow-hidden">
                    <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between gap-3
                        {{ $stage['key'] === 'writing' ? 'bg-blue-50' : ($stage['key'] === 'design' ? 'bg-purple-50' : 'bg-teal-50') }}">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center
                                {{ $stage['key'] === 'writing' ? 'bg-blue-100 text-blue-700' : ($stage['key'] === 'design' ? 'bg-purple-100 text-purple-700' : 'bg-teal-100 text-teal-700') }}">
                                <span class="material-icons">{{ $stage['icon'] }}</span>
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-800">{{ $stage['label'] }}</h4>
                                <p class="text-xs text-gray-500">
                                    @if($stage['key'] === 'writing')
                                        عند كاتب المحتوى
                                    @elseif($stage['key'] === 'design')
                                        عند فريق التصميم
                                    @else
                                        جاهز / قيد النشر
                                    @endif
                                    · {{ $stage['count'] }} عنصر
                                </p>
                            </div>
                        </div>
                        <span class="px-2.5 py-1 rounded-lg text-xs font-bold
                            {{ $stage['key'] === 'writing' ? 'bg-blue-100 text-blue-700' : ($stage['key'] === 'design' ? 'bg-purple-100 text-purple-700' : 'bg-teal-100 text-teal-700') }}">
                            {{ $stage['count'] }}
                        </span>
                    </div>

                    <div class="p-4">
                        @if($stage['tasks']->count())
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                                @foreach($stage['tasks'] as $task)
                                    <a href="{{ route('work.tasks.show', [$activity, $task]) }}"
                                       class="group rounded-2xl border border-gray-200 bg-white p-4 min-h-[110px] flex flex-col justify-between hover:border-primary/50 hover:shadow-md transition-all {{ $task->is_overdue ? 'border-r-4 border-r-red-400' : '' }}">
                                        <div>
                                            @if($task->content_type_label)
                                                <span class="inline-block px-2 py-0.5 text-[10px] rounded-md bg-indigo-50 text-indigo-700 mb-2">{{ $task->content_type_label }}</span>
                                            @endif
                                            <h5 class="text-base font-bold text-gray-900 leading-snug line-clamp-3 group-hover:text-primary">
                                                {{ $task->title }}
                                            </h5>
                                        </div>
                                        <div class="mt-3 flex items-center justify-between text-[11px] text-gray-400">
                                            <span>{{ $task->owner_for_current_stage->name ?? '—' }}</span>
                                            <span class="inline-flex items-center gap-0.5 text-primary opacity-0 group-hover:opacity-100 transition-opacity">
                                                تفاصيل
                                                <span class="material-icons text-sm">chevron_left</span>
                                            </span>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8 text-sm text-gray-400">
                                لا يوجد محتوى في هذه المرحلة
                            </div>
                        @endif
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
                <form method="POST" action="{{ route('work.share.regenerate', $activity) }}" onsubmit="return confirm('تجديد الرابط؟ الرابط القديم هيتوقف.')">
                    @csrf
                    <button type="submit" class="px-4 py-2 rounded-xl bg-amber-50 text-amber-700 text-sm hover:bg-amber-100">تجديد الرابط</button>
                </form>
                <form method="POST" action="{{ route('work.share.disable', $activity) }}" onsubmit="return confirm('إيقاف الرابط العام؟')">
                    @csrf
                    <button type="submit" class="px-4 py-2 rounded-xl bg-red-50 text-red-600 text-sm hover:bg-red-100">إيقاف</button>
                </form>
            </div>
        @else
            <form method="POST" action="{{ route('work.share.enable', $activity) }}">
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
        <form method="POST" action="{{ route('work.tasks.parse-bulk', $activity) }}" id="parseBulkForm" class="space-y-3">
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
        <form method="POST" action="{{ route('work.tasks.store', $activity) }}" class="space-y-3">
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
        <form method="POST" action="{{ route('work.update', $activity) }}" class="space-y-4">
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

{{-- مودال تعديل مهمة --}}
<div id="editTaskModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40">
    <div class="bg-white rounded-2xl w-full max-w-2xl p-6 shadow-2xl max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between mb-5">
            <h3 class="text-lg font-bold text-gray-800">تعديل تاسك المحتوى</h3>
            <button onclick="document.getElementById('editTaskModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <span class="material-icons">close</span>
            </button>
        </div>
        <form method="POST" id="editTaskForm" class="space-y-3">
            @csrf @method('PUT')
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">العنوان</label>
                <input type="text" name="title" id="editTaskTitle" required
                       class="w-full px-3 py-2 rounded-xl border-2 border-gray-200 text-sm focus:border-primary focus:outline-none">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">الفكرة</label>
                <textarea name="idea" id="editTaskIdea" rows="2"
                          class="w-full px-3 py-2 rounded-xl border-2 border-gray-200 text-sm focus:border-primary focus:outline-none"></textarea>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">TOV</label>
                    <textarea name="tov" id="editTaskTov" rows="2"
                              class="w-full px-3 py-2 rounded-xl border-2 border-gray-200 text-sm focus:border-primary focus:outline-none"></textarea>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Caption</label>
                    <textarea name="caption" id="editTaskCaption" rows="2"
                              class="w-full px-3 py-2 rounded-xl border-2 border-gray-200 text-sm focus:border-primary focus:outline-none"></textarea>
                </div>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">نوع المحتوى</label>
                <select name="content_type" id="editTaskContentType" class="w-full px-3 py-2 rounded-xl border-2 border-gray-200 text-sm focus:border-primary focus:outline-none">
                    <option value="">— اختر —</option>
                    @foreach($contentTypes as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">مرجع التصميم</label>
                <textarea name="design_reference" id="editTaskDesignRef" rows="2"
                          class="w-full px-3 py-2 rounded-xl border-2 border-gray-200 text-sm focus:border-primary focus:outline-none"></textarea>
            </div>
            <div>
                <div class="flex items-center justify-between mb-1">
                    <label class="block text-xs font-medium text-gray-600">ملخص للمصمم (مساعد)</label>
                    <button type="button" id="editSummarizeBtn" onclick="summarizeDesignerFromModal()"
                            class="text-xs text-amber-700 hover:text-amber-900 flex items-center gap-1">
                        <span class="material-icons text-sm">auto_awesome</span>
                        ولّد الملخص
                    </button>
                </div>
                <textarea name="designer_brief" id="editTaskDesignerBrief" rows="3"
                          placeholder="نقاط مختصرة بما يحتاجه المصمم..."
                          class="w-full px-3 py-2 rounded-xl border-2 border-amber-100 bg-amber-50/40 text-sm focus:border-amber-400 focus:outline-none"></textarea>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">المنصات</label>
                <div class="flex flex-wrap gap-2">
                    @foreach($platforms as $key => $label)
                        <label class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg border border-gray-200 text-xs cursor-pointer hover:border-primary">
                            <input type="checkbox" name="platforms[]" value="{{ $key }}" id="editPlatform_{{ $key }}" class="edit-platform rounded border-gray-300 text-primary focus:ring-primary">
                            {{ $label }}
                        </label>
                    @endforeach
                </div>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">ملاحظات</label>
                <textarea name="notes" id="editTaskNotes" rows="2"
                          class="w-full px-3 py-2 rounded-xl border-2 border-gray-200 text-sm focus:border-primary focus:outline-none"></textarea>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">نوع الشغل</label>
                    <select name="kind" id="editTaskKind" class="w-full px-3 py-2 rounded-xl border-2 border-gray-200 text-sm focus:border-primary focus:outline-none">
                        @foreach($kinds as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">الحالة</label>
                    <select name="status" id="editTaskStatus" class="w-full px-3 py-2 rounded-xl border-2 border-gray-200 text-sm focus:border-primary focus:outline-none">
                        @foreach($taskStatuses as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">كاتب المحتوى</label>
                    <select name="content_writer_id" id="editTaskWriter" class="w-full px-3 py-2 rounded-xl border-2 border-gray-200 text-sm focus:border-primary focus:outline-none">
                        <option value="">— غير معيّن —</option>
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}">{{ $emp->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">المصمم</label>
                    <select name="designer_id" id="editTaskDesigner" class="w-full px-3 py-2 rounded-xl border-2 border-gray-200 text-sm focus:border-primary focus:outline-none">
                        <option value="">— غير معيّن —</option>
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}">{{ $emp->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">الموظف الحالي</label>
                    <select name="assigned_to" id="editTaskAssignee" class="w-full px-3 py-2 rounded-xl border-2 border-gray-200 text-sm focus:border-primary focus:outline-none">
                        <option value="">— غير معيّن —</option>
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}">{{ $emp->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">تاريخ التسليم</label>
                    <input type="date" name="due_date" id="editTaskDue"
                           class="w-full px-3 py-2 rounded-xl border-2 border-gray-200 text-sm focus:border-primary focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">موعد النشر</label>
                    <input type="date" name="publish_date" id="editTaskPublish"
                           class="w-full px-3 py-2 rounded-xl border-2 border-gray-200 text-sm focus:border-primary focus:outline-none">
                </div>
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
    const roleLabels = {
        content_writer: 'كاتب محتوى', ad_manager: 'إدارة إعلانات', designer: 'مصمم',
        video_editor: 'مصمم فيديوهات', page_manager: 'إدارة الصفحة', account_manager: 'أكونت منجر',
        monitor: 'مونتير', media_buyer: 'ميديا بايرز'
    };
    const kindRoleMap = @json($kindRoleMap);
    const roleEmployeeName = @json($roleEmployee->map(fn($e) => $e->name));
    let currentEditTaskId = null;

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

    function openTaskEdit(task) {
        currentEditTaskId = task.id;
        const baseUrl = "{{ route('work.tasks.update', [$activity, 'TASK_ID']) }}".replace('TASK_ID', task.id);
        const form = document.getElementById('editTaskForm');
        form.action = baseUrl;
        document.getElementById('editTaskTitle').value = task.title || '';
        document.getElementById('editTaskIdea').value = task.idea || '';
        document.getElementById('editTaskTov').value = task.tov || '';
        document.getElementById('editTaskCaption').value = task.caption || '';
        document.getElementById('editTaskContentType').value = task.content_type || '';
        document.getElementById('editTaskDesignRef').value = task.design_reference || '';
        document.getElementById('editTaskDesignerBrief').value = task.designer_brief || '';
        document.getElementById('editTaskNotes').value = task.notes || '';
        document.getElementById('editTaskKind').value = task.kind || 'other';
        document.getElementById('editTaskStatus').value = task.status || 'todo';
        document.getElementById('editTaskWriter').value = task.content_writer_id || '';
        document.getElementById('editTaskDesigner').value = task.designer_id || '';
        document.getElementById('editTaskAssignee').value = task.assigned_to || '';
        document.getElementById('editTaskDue').value = dateValue(task.due_date);
        document.getElementById('editTaskPublish').value = dateValue(task.publish_date);

        const selected = Array.isArray(task.platforms) ? task.platforms : [];
        document.querySelectorAll('.edit-platform').forEach(function (cb) {
            cb.checked = selected.includes(cb.value);
        });

        document.getElementById('editTaskModal').classList.remove('hidden');
    }

    async function summarizeDesigner(taskId, btn) {
        if (!taskId) return;
        const original = btn ? btn.innerHTML : '';
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<span class="material-icons text-base animate-spin">progress_activity</span>';
        }
        try {
            const url = "{{ route('work.tasks.summarize-designer', [$activity, 'TASK_ID']) }}".replace('TASK_ID', taskId);
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
            window.location.reload();
        } catch (e) {
            alert('حدث خطأ أثناء التلخيص');
        } finally {
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = original;
            }
        }
    }

    async function summarizeDesignerFromModal() {
        if (!currentEditTaskId) return;
        const btn = document.getElementById('editSummarizeBtn');
        const original = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<span class="material-icons text-sm animate-spin">progress_activity</span> جاري...';
        try {
            const url = "{{ route('work.tasks.summarize-designer', [$activity, 'TASK_ID']) }}".replace('TASK_ID', currentEditTaskId);
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
            document.getElementById('editTaskDesignerBrief').value = data.designer_brief || '';
        } catch (e) {
            alert('حدث خطأ أثناء التلخيص');
        } finally {
            btn.disabled = false;
            btn.innerHTML = original;
        }
    }

    document.getElementById('parseBulkForm')?.addEventListener('submit', function () {
        const btn = document.getElementById('parseBulkBtn');
        const label = document.getElementById('parseBulkBtnLabel');
        btn.disabled = true;
        label.textContent = 'جاري التحليل والتقسيم...';
    });

    @if(old('bulk_text'))
    document.addEventListener('DOMContentLoaded', openParseBulkModal);
    @endif
</script>
@endsection
