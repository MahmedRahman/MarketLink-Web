@extends($workLayout ?? 'layouts.dashboard')

@section('title', 'مساحة العمل')
@section('page-title', 'مساحة العمل')
@section('page-description', 'أنشطة الأكاديمية: محاضرات لايف، راوندات مدفوعة، ومحتوى تعليمي')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-xl flex items-center">
            <span class="material-icons ml-2">check_circle</span>
            {{ session('success') }}
        </div>
    @endif

    {{-- شريط المتابعة العامة --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="card rounded-2xl p-4 flex items-center gap-3">
            <div class="w-11 h-11 rounded-xl bg-red-100 text-red-600 flex items-center justify-center">
                <span class="material-icons">warning_amber</span>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-800">{{ $follow['overdue']->count() }}</p>
                <p class="text-xs text-gray-500">متأخرة</p>
            </div>
        </div>
        <div class="card rounded-2xl p-4 flex items-center gap-3">
            <div class="w-11 h-11 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center">
                <span class="material-icons">bolt</span>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-800">{{ $follow['in_progress']->count() }}</p>
                <p class="text-xs text-gray-500">قيد التنفيذ</p>
            </div>
        </div>
        <div class="card rounded-2xl p-4 flex items-center gap-3">
            <div class="w-11 h-11 rounded-xl bg-yellow-100 text-yellow-600 flex items-center justify-center">
                <span class="material-icons">rate_review</span>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-800">{{ $follow['review']->count() }}</p>
                <p class="text-xs text-gray-500">بانتظار مراجعة</p>
            </div>
        </div>
        <div class="card rounded-2xl p-4 flex items-center gap-3">
            <div class="w-11 h-11 rounded-xl bg-gray-100 text-gray-600 flex items-center justify-center">
                <span class="material-icons">person_off</span>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-800">{{ $follow['unassigned']->count() }}</p>
                <p class="text-xs text-gray-500">غير معيّنة</p>
            </div>
        </div>
    </div>

    {{-- شريط الأدوات --}}
    <div class="card rounded-2xl p-4 flex flex-col md:flex-row md:items-center justify-between gap-3">
        <div class="flex flex-col sm:flex-row sm:items-center gap-3 flex-wrap">
            <div class="inline-flex rounded-xl border-2 border-gray-200 p-1 bg-gray-50 flex-wrap">
                <a href="{{ work_route('index', array_filter(['type' => $filterType, 'status' => $filterStatus, 'view' => 'title'])) }}"
                   class="px-3 py-1.5 rounded-lg text-sm font-semibold inline-flex items-center gap-1 transition-colors {{ ($viewMode ?? 'title') === 'title' ? 'bg-white text-primary shadow-sm' : 'text-gray-600 hover:text-gray-800' }}">
                    <span class="material-icons text-base">title</span>
                    حسب العنوان
                </a>
                @if($showMonthView ?? true)
                    <a href="{{ work_route('index', array_filter(['type' => $filterType, 'status' => $filterStatus, 'view' => 'month'])) }}"
                       class="px-3 py-1.5 rounded-lg text-sm font-semibold inline-flex items-center gap-1 transition-colors {{ ($viewMode ?? 'title') === 'month' ? 'bg-white text-primary shadow-sm' : 'text-gray-600 hover:text-gray-800' }}">
                        <span class="material-icons text-base">calendar_month</span>
                        حسب الشهر
                    </a>
                @endif
                <a href="{{ work_route('index', array_filter(['type' => $filterType, 'status' => $filterStatus, 'view' => 'folder'])) }}"
                   class="px-3 py-1.5 rounded-lg text-sm font-semibold inline-flex items-center gap-1 transition-colors {{ ($viewMode ?? 'title') === 'folder' ? 'bg-white text-primary shadow-sm' : 'text-gray-600 hover:text-gray-800' }}">
                    <span class="material-icons text-base">folder</span>
                    حسب الفولدر
                </a>
            </div>

            <form method="GET" class="flex flex-wrap items-center gap-2">
                @if(in_array(($viewMode ?? 'title'), ['month', 'folder'], true))
                    <input type="hidden" name="view" value="{{ $viewMode }}">
                @endif
                <select name="type" onchange="this.form.submit()" class="px-3 py-2 rounded-xl border-2 border-gray-200 text-sm focus:border-primary focus:outline-none">
                    <option value="">كل الأنواع</option>
                    @foreach($types as $key => $label)
                        <option value="{{ $key }}" @selected($filterType === $key)>{{ $label }}</option>
                    @endforeach
                </select>
                <select name="status" onchange="this.form.submit()" class="px-3 py-2 rounded-xl border-2 border-gray-200 text-sm focus:border-primary focus:outline-none">
                    <option value="">كل الحالات</option>
                    @foreach($statuses as $key => $label)
                        <option value="{{ $key }}" @selected($filterStatus === $key)>{{ $label }}</option>
                    @endforeach
                </select>
                @if($filterType || $filterStatus)
                    <a href="{{ work_route('index', in_array(($viewMode ?? 'title'), ['month', 'folder'], true) ? ['view' => $viewMode] : []) }}" class="text-sm text-gray-500 hover:text-gray-700 px-2">مسح</a>
                @endif
            </form>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            @if($canManageFolders ?? false)
                <button type="button" onclick="document.getElementById('newFolderModal').classList.remove('hidden')"
                        class="px-4 py-2.5 rounded-xl font-medium bg-amber-50 text-amber-800 border border-amber-200 hover:bg-amber-100 inline-flex items-center gap-2">
                    <span class="material-icons text-lg">create_new_folder</span>
                    فولدر جديد
                </button>
            @endif
            <button onclick="document.getElementById('newActivityModal').classList.remove('hidden')"
                    class="btn-primary text-white px-5 py-2.5 rounded-xl font-medium flex items-center justify-center gap-2">
                <span class="material-icons text-lg">add</span>
                نشاط جديد
            </button>
        </div>
    </div>

    {{-- كروت الأنشطة --}}
    @if($activities->isEmpty() && ($viewMode ?? 'title') !== 'folder')
        <div class="card rounded-2xl p-12 text-center">
            <span class="material-icons text-6xl text-gray-300">dashboard_customize</span>
            <h3 class="text-lg font-bold text-gray-700 mt-4">لا توجد أنشطة بعد</h3>
            <p class="text-gray-500 text-sm mt-1">ابدأ بإضافة أول نشاط للأكاديمية (محاضرة لايف، راوند، محتوى...)</p>
        </div>
    @elseif(($viewMode ?? 'title') === 'folder')
        @if($canManageFolders ?? false)
            <div class="rounded-2xl border border-dashed border-amber-300 bg-amber-50/60 px-4 py-3 text-sm text-amber-900 flex items-center gap-2">
                <span class="material-icons text-amber-700">swipe</span>
                اسحب الكارت من أيقونة السحب وأفلته داخل فولدر تاني عشان تنقله
            </div>
        @endif
        <div class="space-y-6" id="folderBoard" @if($canManageFolders ?? false) data-folder-dnd="1" @endif>
            @forelse($activitiesByFolder as $folderGroup)
                @php
                    $folder = $folderGroup['folder'];
                    $folderActivities = $folderGroup['activities'];
                    $folderKey = $folder ? (string) $folder->id : '';
                @endphp
                <section class="card rounded-2xl overflow-hidden folder-section" @if($folder) id="folder-{{ $folder->id }}" @endif data-folder-id="{{ $folderKey }}">
                    <div class="px-5 py-4 bg-gradient-to-l {{ $folder ? 'from-amber-50 to-white border-b border-amber-100' : 'from-slate-50 to-white border-b border-gray-100' }} flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <div class="w-10 h-10 rounded-xl {{ $folder ? 'bg-amber-600' : 'bg-slate-500' }} text-white flex items-center justify-center shrink-0">
                                <span class="material-icons">{{ $folder ? 'folder' : 'folder_off' }}</span>
                            </div>
                            <div class="min-w-0">
                                <h3 class="font-bold text-gray-800 text-lg leading-tight">{{ $folder?->title ?? 'بدون فولدر' }}</h3>
                                <p class="text-xs text-gray-500 mt-0.5">
                                    @if($folder?->description)
                                        {{ $folder->description }}
                                    @else
                                        {{ $folder ? 'مجموعة أنشطة' : 'أنشطة غير مضافة لأي فولدر — اسحب هنا للإزالة من فولدر' }}
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="folder-count shrink-0 text-xs font-bold {{ $folder ? 'text-amber-800 bg-amber-100' : 'text-slate-700 bg-slate-100' }} px-2.5 py-1 rounded-lg"
                                  data-count="{{ $folderActivities->count() }}">
                                {{ $folderActivities->count() }} نشاط
                            </span>
                            @if($folder && ($canManageFolders ?? false))
                                <button type="button"
                                        class="text-xs font-medium text-gray-600 hover:text-gray-900 px-2.5 py-1 rounded-lg bg-white border border-gray-200"
                                        onclick="openEditFolderModal({{ $folder->id }}, @js($folder->title), @js($folder->description))">
                                    تعديل
                                </button>
                                <form method="POST" action="{{ work_route('folders.destroy', $folder) }}"
                                      onsubmit="return confirm('حذف الفولدر؟ الأنشطة هترجع بدون فولدر.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs font-medium text-red-600 hover:text-red-800 px-2.5 py-1 rounded-lg bg-red-50 border border-red-100">
                                        حذف
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                    <div class="p-5 folder-drop-zone min-h-[140px] transition-colors rounded-b-2xl"
                         data-folder-id="{{ $folderKey }}">
                        <div class="folder-drop-grid grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5 {{ $folderActivities->isEmpty() ? 'hidden' : '' }}">
                            @foreach($folderActivities as $activity)
                                @include('work.partials.activity-card', ['activity' => $activity])
                            @endforeach
                        </div>
                        <p class="folder-drop-empty text-sm text-gray-400 text-center py-8 border-2 border-dashed border-gray-200 rounded-2xl {{ $folderActivities->isEmpty() ? '' : 'hidden' }}">
                            {{ ($canManageFolders ?? false) ? 'اسحب نشاط هنا' : 'لا توجد أنشطة في هذا الفولدر' }}
                        </p>
                    </div>
                </section>
            @empty
                <div class="card rounded-2xl p-12 text-center">
                    <span class="material-icons text-6xl text-gray-300">folder</span>
                    <h3 class="text-lg font-bold text-gray-700 mt-4">لا توجد فولدرات بعد</h3>
                    <p class="text-gray-500 text-sm mt-1">أنشئ فولدر كبير لتجميع أكتر من نشاط مع بعض</p>
                </div>
            @endforelse
        </div>
    @elseif(($viewMode ?? 'title') === 'month')
        <div class="space-y-6">
            @foreach($activitiesByMonth as $monthGroup)
                <section class="card rounded-2xl overflow-hidden">
                    <div class="px-5 py-4 bg-gradient-to-l from-teal-50 to-white border-b border-teal-100 flex items-center justify-between gap-3">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <div class="w-10 h-10 rounded-xl bg-teal-600 text-white flex items-center justify-center shrink-0">
                                <span class="material-icons">folder</span>
                            </div>
                            <div class="min-w-0">
                                <h3 class="font-bold text-gray-800 text-lg leading-tight">{{ $monthGroup['label'] }}</h3>
                                <p class="text-xs text-gray-500 mt-0.5">فولدرات المحتوى والتصميم لهذا الشهر</p>
                            </div>
                        </div>
                        <span class="shrink-0 text-xs font-bold text-teal-800 bg-teal-100 px-2.5 py-1 rounded-lg">
                            {{ $monthGroup['activities']->count() }} نشاط
                        </span>
                    </div>
                    <div class="p-5">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                            @foreach($monthGroup['activities'] as $activity)
                                @include('work.partials.activity-card', ['activity' => $activity])
                            @endforeach
                        </div>
                    </div>
                </section>
            @endforeach
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach($activities as $activity)
                @include('work.partials.activity-card', ['activity' => $activity])
            @endforeach
        </div>
    @endif
</div>

</div>

@if($canManageFolders ?? false)
{{-- مودال فولدر جديد --}}
<div id="newFolderModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40">
    <div class="bg-white rounded-2xl w-full max-w-md p-6 shadow-2xl">
        <div class="flex items-center justify-between mb-5">
            <h3 class="text-lg font-bold text-gray-800">فولدر جديد</h3>
            <button type="button" onclick="document.getElementById('newFolderModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <span class="material-icons">close</span>
            </button>
        </div>
        <form method="POST" action="{{ work_route('folders.store') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">اسم الفولدر</label>
                <input type="text" name="title" required placeholder="مثال: حملة يوليو"
                       class="w-full px-4 py-2.5 rounded-xl border-2 border-gray-200 focus:border-primary focus:outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">وصف (اختياري)</label>
                <textarea name="description" rows="2" class="w-full px-4 py-2.5 rounded-xl border-2 border-gray-200 focus:border-primary focus:outline-none"></textarea>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="btn-primary text-white px-5 py-2.5 rounded-xl font-medium flex-1">إنشاء</button>
                <button type="button" onclick="document.getElementById('newFolderModal').classList.add('hidden')"
                        class="px-5 py-2.5 rounded-xl font-medium bg-gray-100 text-gray-700 hover:bg-gray-200">إلغاء</button>
            </div>
        </form>
    </div>
</div>

{{-- مودال تعديل فولدر --}}
<div id="editFolderModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40">
    <div class="bg-white rounded-2xl w-full max-w-md p-6 shadow-2xl">
        <div class="flex items-center justify-between mb-5">
            <h3 class="text-lg font-bold text-gray-800">تعديل الفولدر</h3>
            <button type="button" onclick="document.getElementById('editFolderModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <span class="material-icons">close</span>
            </button>
        </div>
        <form method="POST" id="editFolderForm" action="#" class="space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">اسم الفولدر</label>
                <input type="text" name="title" id="editFolderTitle" required
                       class="w-full px-4 py-2.5 rounded-xl border-2 border-gray-200 focus:border-primary focus:outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">وصف (اختياري)</label>
                <textarea name="description" id="editFolderDescription" rows="2"
                          class="w-full px-4 py-2.5 rounded-xl border-2 border-gray-200 focus:border-primary focus:outline-none"></textarea>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="btn-primary text-white px-5 py-2.5 rounded-xl font-medium flex-1">حفظ</button>
                <button type="button" onclick="document.getElementById('editFolderModal').classList.add('hidden')"
                        class="px-5 py-2.5 rounded-xl font-medium bg-gray-100 text-gray-700 hover:bg-gray-200">إلغاء</button>
            </div>
        </form>
    </div>
</div>
@endif

{{-- مودال نشاط جديد --}}
<div id="newActivityModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40">
    <div class="bg-white rounded-2xl w-full max-w-xl p-6 shadow-2xl">
        <div class="flex items-center justify-between mb-5">
            <h3 class="text-lg font-bold text-gray-800">نشاط جديد</h3>
            <button onclick="document.getElementById('newActivityModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <span class="material-icons">close</span>
            </button>
        </div>

        @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl mb-4">
                <ul class="list-disc list-inside text-sm space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ work_route('store') }}" class="space-y-4 max-h-[80vh] overflow-y-auto pe-1">
            @csrf
            <input type="hidden" name="idea_id" id="ideaIdField" value="">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">العنوان</label>
                <input type="text" name="title" required placeholder="مثال: محاضرة لايف — أسرار كتابة المحتوى"
                       class="w-full px-4 py-2.5 rounded-xl border-2 border-gray-200 focus:border-primary focus:outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">النوع</label>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-2" id="newActivityTypeCards">
                    @php
                        $typeIcons = [
                            'live_lecture' => 'live_tv',
                            'live_lecture_paid' => 'paid',
                            'paid_round' => 'workspace_premium',
                            'educational' => 'menu_book',
                            'other' => 'category',
                        ];
                        $defaultType = 'other';
                    @endphp
                    @foreach($types as $key => $label)
                        <label class="relative cursor-pointer">
                            <input type="radio" name="type" value="{{ $key }}" class="peer sr-only activity-type-radio"
                                   @checked($key === $defaultType) onchange="toggleTemplateOption()">
                            <span class="flex flex-col items-center gap-1.5 rounded-2xl border-2 border-gray-200 bg-white px-2 py-3 text-center
                                         peer-checked:border-indigo-500 peer-checked:bg-indigo-50 peer-checked:shadow-sm
                                         hover:border-gray-300 transition-all min-h-[88px]">
                                <span class="material-icons text-xl text-gray-400 type-card-icon">{{ $typeIcons[$key] ?? 'category' }}</span>
                                <span class="text-xs font-semibold text-gray-700 leading-snug">{{ $label }}</span>
                            </span>
                        </label>
                    @endforeach
                </div>
            </div>

            {{-- بيانات المحاضرة المجانية --}}
            <div id="freeLectureFields" class="hidden space-y-3 rounded-2xl border border-teal-200 bg-teal-50/50 p-4">
                <p class="text-sm font-bold text-teal-900 flex items-center gap-1.5">
                    <span class="material-icons text-base">live_tv</span>
                    بيانات المحاضرة المجانية
                </p>
                <div>
                    <label class="block text-xs font-medium text-teal-900 mb-1">اسم المحاضر <span class="text-red-500">*</span></label>
                    <input type="text" name="lecturer_name" id="lecturerNameField" placeholder="مثال: مها الخضري"
                           class="w-full px-3 py-2.5 rounded-xl border-2 border-teal-100 bg-white text-sm focus:border-teal-500 focus:outline-none">
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-teal-900 mb-1">تاريخ المحاضرة <span class="text-red-500">*</span></label>
                        <input type="date" name="event_date" id="lectureDateField"
                               class="w-full px-3 py-2.5 rounded-xl border-2 border-teal-100 bg-white text-sm focus:border-teal-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-teal-900 mb-1">الساعة (اختياري)</label>
                        <input type="text" name="lecture_time" id="lectureTimeField" placeholder="مثال: 8:00 مساءً"
                               class="w-full px-3 py-2.5 rounded-xl border-2 border-teal-100 bg-white text-sm focus:border-teal-500 focus:outline-none">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-medium text-teal-900 mb-1">أهداف المحاضرة <span class="text-red-500">*</span></label>
                    <textarea name="lecture_goals" id="lectureGoalsField" rows="3"
                              placeholder="مثال: يفهم الحضور أساسيات كتابة كابشن احترافي ويقدروا يطبقوا 3 صيغ جاهزة بعد اللقاء"
                              class="w-full px-3 py-2.5 rounded-xl border-2 border-teal-100 bg-white text-sm focus:border-teal-500 focus:outline-none"></textarea>
                    <p class="text-[11px] text-teal-800/70 mt-1">الأهداف دي هتدخل في كابي الإعلان والتذكير والتاسكات تلقائيًا.</p>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">وصف مختصر</label>
                <textarea name="description" rows="2" placeholder="ملاحظات إضافية عن النشاط..."
                          class="w-full px-4 py-2.5 rounded-xl border-2 border-gray-200 focus:border-primary focus:outline-none"></textarea>
            </div>
            @if(($folders ?? collect())->isNotEmpty())
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">الفولدر (اختياري)</label>
                    <select name="folder_id" class="w-full px-4 py-2.5 rounded-xl border-2 border-gray-200 focus:border-primary focus:outline-none text-sm">
                        <option value="">بدون فولدر</option>
                        @foreach($folders as $folderOption)
                            <option value="{{ $folderOption->id }}">{{ $folderOption->title }}</option>
                        @endforeach
                    </select>
                </div>
            @endif
            {{-- قالب تاسكات المحاضرة القياسية (من دليل تنظيم ملفات المحاضرة) --}}
            <div id="templateOption" class="hidden bg-teal-50 border border-teal-200 rounded-xl p-3">
                <label class="flex items-start gap-2 cursor-pointer">
                    <input type="checkbox" name="with_template" value="1" id="withTemplateCheckbox"
                           class="mt-1 rounded border-gray-300 text-teal-600 focus:ring-teal-500">
                    <span class="text-sm text-gray-700">
                        <span class="font-semibold">إنشاء تاسكات المحاضرة القياسية تلقائيًا</span>
                        <span class="block text-xs text-gray-500 mt-0.5">
                            12 مهمة في «قيد التخطيط» بالترتيب: بنر الموقع، تيزر قبل، رفع على الموقع، إعلان، تذكير، كفر يوتيوب، مونتاج، يوتيوب، مقاطع، جدولة، آراء — بكابي احترافي من بيانات المحاضرة.
                        </span>
                    </span>
                </label>
            </div>
            <div class="flex gap-3 pt-2 sticky bottom-0 bg-white pb-1">
                <button type="submit" class="btn-primary text-white px-5 py-2.5 rounded-xl font-medium flex-1">إنشاء</button>
                <button type="button" onclick="document.getElementById('newActivityModal').classList.add('hidden')"
                        class="px-5 py-2.5 rounded-xl font-medium bg-gray-100 text-gray-700 hover:bg-gray-200">إلغاء</button>
            </div>
        </form>
    </div>
</div>

<script>
    @php($folderStoreBaseUrl = rtrim(work_route('folders.store', [], false), '/'))
    function openEditFolderModal(id, title, description) {
        const modal = document.getElementById('editFolderModal');
        const form = document.getElementById('editFolderForm');
        if (!modal || !form) return;
        form.action = @json($folderStoreBaseUrl) + '/' + id;
        document.getElementById('editFolderTitle').value = title || '';
        document.getElementById('editFolderDescription').value = description || '';
        modal.classList.remove('hidden');
    }

    function toggleTemplateOption() {
        const selected = document.querySelector('input[name="type"]:checked');
        const box = document.getElementById('templateOption');
        const checkbox = document.getElementById('withTemplateCheckbox');
        const lectureFields = document.getElementById('freeLectureFields');
        const lecturer = document.getElementById('lecturerNameField');
        const lectureDate = document.getElementById('lectureDateField');
        const lectureGoals = document.getElementById('lectureGoalsField');

        // لو مفيش Type مختار (حالة الفكرة بدون type)، نخلي الفورم يطلب اختيار يدويًا
        if (!selected) {
            box.classList.add('hidden');
            if (checkbox) checkbox.checked = false;
            if (lectureFields) lectureFields.classList.add('hidden');
            [lecturer, lectureDate, lectureGoals].forEach(function (el) {
                if (el) el.required = false;
            });
            return;
        }

        const type = selected.value;
        const isFreeLecture = type === 'live_lecture';
        const isLecture = isFreeLecture || type === 'live_lecture_paid';
        box.classList.toggle('hidden', !isLecture);
        if (!isLecture && checkbox) checkbox.checked = false;
        if (isLecture && checkbox) checkbox.checked = true;

        if (lectureFields) {
            lectureFields.classList.toggle('hidden', !isFreeLecture);
        }
        [lecturer, lectureDate, lectureGoals].forEach(function (el) {
            if (!el) return;
            el.required = isFreeLecture && !!(checkbox && checkbox.checked);
        });

        document.querySelectorAll('#newActivityTypeCards label').forEach(function (label) {
            const input = label.querySelector('input');
            const icon = label.querySelector('.type-card-icon');
            const on = input && input.checked;
            if (icon) {
                icon.classList.toggle('text-indigo-600', !!on);
                icon.classList.toggle('text-gray-400', !on);
            }
        });
    }

    function initFolderDragAndDrop() {
        const board = document.getElementById('folderBoard');
        if (!board || board.getAttribute('data-folder-dnd') !== '1') return;

        let dragged = null;
        let busy = false;

        function csrfToken() {
            return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        }

        function refreshZone(zone) {
            if (!zone) return;
            const grid = zone.querySelector('.folder-drop-grid');
            const empty = zone.querySelector('.folder-drop-empty');
            const section = zone.closest('.folder-section');
            const countEl = section ? section.querySelector('.folder-count') : null;
            const count = grid ? grid.querySelectorAll('.folder-dnd-item').length : 0;

            if (grid) grid.classList.toggle('hidden', count === 0);
            if (empty) empty.classList.toggle('hidden', count > 0);
            if (countEl) {
                countEl.dataset.count = String(count);
                countEl.textContent = count + ' نشاط';
            }
        }

        function clearDropStyles() {
            board.querySelectorAll('.folder-drop-zone').forEach(function (zone) {
                zone.classList.remove('bg-amber-50', 'ring-2', 'ring-amber-400', 'ring-inset');
            });
        }

        board.querySelectorAll('.folder-dnd-item').forEach(function (item) {
            item.addEventListener('dragstart', function (e) {
                if (busy) {
                    e.preventDefault();
                    return;
                }
                dragged = item;
                item.classList.add('opacity-50');
                e.dataTransfer.effectAllowed = 'move';
                e.dataTransfer.setData('text/plain', item.dataset.activityId || '');
            });

            item.addEventListener('dragend', function () {
                item.classList.remove('opacity-50');
                clearDropStyles();
                dragged = null;
            });
        });

        board.querySelectorAll('.folder-drop-zone').forEach(function (zone) {
            zone.addEventListener('dragover', function (e) {
                e.preventDefault();
                e.dataTransfer.dropEffect = 'move';
                clearDropStyles();
                zone.classList.add('bg-amber-50', 'ring-2', 'ring-amber-400', 'ring-inset');
            });

            zone.addEventListener('dragleave', function (e) {
                if (!zone.contains(e.relatedTarget)) {
                    zone.classList.remove('bg-amber-50', 'ring-2', 'ring-amber-400', 'ring-inset');
                }
            });

            zone.addEventListener('drop', async function (e) {
                e.preventDefault();
                clearDropStyles();
                if (!dragged || busy) return;

                const targetFolderId = zone.getAttribute('data-folder-id') || '';
                const currentFolderId = dragged.getAttribute('data-folder-id') || '';
                if (targetFolderId === currentFolderId) return;

                const moveUrl = dragged.getAttribute('data-move-url');
                if (!moveUrl) return;

                const sourceZone = dragged.closest('.folder-drop-zone');
                const targetGrid = zone.querySelector('.folder-drop-grid');
                if (!targetGrid) return;

                busy = true;
                dragged.classList.add('pointer-events-none');

                try {
                    const body = new FormData();
                    body.append('_token', csrfToken());
                    body.append('folder_id', targetFolderId);
                    body.append('return_view', 'folder');

                    const res = await fetch(moveUrl, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        body: body,
                        credentials: 'same-origin',
                    });

                    if (!res.ok) {
                        throw new Error('move failed');
                    }

                    targetGrid.appendChild(dragged);
                    dragged.setAttribute('data-folder-id', targetFolderId);
                    const select = dragged.querySelector('.folder-move-select');
                    if (select) select.value = targetFolderId;

                    refreshZone(sourceZone);
                    refreshZone(zone);
                } catch (err) {
                    alert('تعذر نقل النشاط. حاول مرة أخرى.');
                } finally {
                    dragged.classList.remove('pointer-events-none');
                    busy = false;
                }
            });
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        const params = new URLSearchParams(window.location.search);
        const openNew = params.get('open_new_activity');
        const ideaId = params.get('idea_id');

        if (openNew === '1' && ideaId) {
            const modal = document.getElementById('newActivityModal');
            if (modal) modal.classList.remove('hidden');

            const title = params.get('prefill_title') ?? '';
            const desc = params.get('prefill_description') ?? '';
            const prefillType = params.get('prefill_type') ?? '';
            const forceSelectType = params.get('prefill_force_select_type') === '1';

            const ideaIdField = document.getElementById('ideaIdField');
            if (ideaIdField) ideaIdField.value = ideaId;

            const titleInput = modal ? modal.querySelector('input[name="title"]') : null;
            const descInput = modal ? modal.querySelector('textarea[name="description"]') : null;
            if (titleInput) titleInput.value = title;
            if (descInput) descInput.value = desc;

            // Type handling
            const typeRadios = modal ? modal.querySelectorAll('#newActivityTypeCards input[name="type"]') : [];
            typeRadios.forEach(function (r) { r.checked = false; });

            if (prefillType && !forceSelectType) {
                const match = modal ? modal.querySelector('#newActivityTypeCards input[name="type"][value="' + prefillType + '"]') : null;
                if (match) match.checked = true;
            }
            // لو prefill_force_select_type=1 سيبقى كله unchecked (الأدمن يختار)

            // with_template
            const withTemplate = params.get('with_template') === '1';
            const checkbox = modal ? modal.querySelector('#withTemplateCheckbox') : null;
            if (checkbox) checkbox.checked = withTemplate;
        }

        toggleTemplateOption();
        initFolderDragAndDrop();

        const templateCb = document.getElementById('withTemplateCheckbox');
        if (templateCb) {
            templateCb.addEventListener('change', toggleTemplateOption);
        }

        @if($errors->any())
            document.getElementById('newActivityModal')?.classList.remove('hidden');
            toggleTemplateOption();
        @endif
    });
</script>
@endsection
