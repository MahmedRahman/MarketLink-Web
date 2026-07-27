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
        <form method="GET" class="flex flex-wrap items-center gap-2">
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
                <a href="{{ work_route('index') }}" class="text-sm text-gray-500 hover:text-gray-700 px-2">مسح</a>
            @endif
        </form>

        <button onclick="document.getElementById('newActivityModal').classList.remove('hidden')"
                class="btn-primary text-white px-5 py-2.5 rounded-xl font-medium flex items-center justify-center gap-2">
            <span class="material-icons text-lg">add</span>
            نشاط جديد
        </button>
    </div>

    {{-- كروت الأنشطة --}}
    @if($activities->isEmpty())
        <div class="card rounded-2xl p-12 text-center">
            <span class="material-icons text-6xl text-gray-300">dashboard_customize</span>
            <h3 class="text-lg font-bold text-gray-700 mt-4">لا توجد أنشطة بعد</h3>
            <p class="text-gray-500 text-sm mt-1">ابدأ بإضافة أول نشاط للأكاديمية (محاضرة لايف، راوند، محتوى...)</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach($activities as $activity)
                <a href="{{ work_route('show', $activity) }}" class="card rounded-2xl p-5 block hover:no-underline">
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex items-center gap-3">
                            <div class="w-11 h-11 rounded-xl bg-indigo-50 text-primary flex items-center justify-center">
                                <span class="material-icons">{{ $activity->type_icon }}</span>
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-800 leading-tight">{{ $activity->title }}</h3>
                                <span class="text-xs text-gray-500">{{ $activity->type_label }}</span>
                            </div>
                        </div>
                        <span class="role-badge role-{{ $activity->status_color }}">{{ $activity->status_label }}</span>
                    </div>

                    @if($activity->event_date)
                        <p class="text-xs text-gray-500 flex items-center gap-1 mb-3">
                            <span class="material-icons text-sm">event</span>
                            {{ $activity->event_date->format('Y/m/d') }}
                        </p>
                    @endif

                    <div class="mt-2">
                        <div class="flex items-center justify-between text-xs text-gray-500 mb-1">
                            <span>{{ $activity->done_tasks_count }} / {{ $activity->tasks_count }} مهمة</span>
                            <span>{{ $activity->progress }}%</span>
                        </div>
                        <div class="w-full h-2 bg-gray-100 rounded-full overflow-hidden">
                            <div class="h-full bg-gradient-to-l from-primary to-secondary rounded-full" style="width: {{ $activity->progress }}%"></div>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    @endif
</div>

{{-- مودال نشاط جديد --}}
<div id="newActivityModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40">
    <div class="bg-white rounded-2xl w-full max-w-xl p-6 shadow-2xl">
        <div class="flex items-center justify-between mb-5">
            <h3 class="text-lg font-bold text-gray-800">نشاط جديد</h3>
            <button onclick="document.getElementById('newActivityModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <span class="material-icons">close</span>
            </button>
        </div>
        <form method="POST" action="{{ work_route('store') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">العنوان</label>
                <input type="text" name="title" required placeholder="مثال: حملة محتوى سوشيال — مارس"
                       class="w-full px-4 py-2.5 rounded-xl border-2 border-gray-200 focus:border-primary focus:outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">النوع</label>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-2" id="newActivityTypeCards">
                    @php
                        $typeIcons = [
                            'free_lecture' => 'smart_display',
                            'live_lecture' => 'live_tv',
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
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">تاريخ المحاضرة / النشاط</label>
                <input type="date" name="event_date"
                       class="w-full px-4 py-2.5 rounded-xl border-2 border-gray-200 focus:border-primary focus:outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">وصف مختصر</label>
                <textarea name="description" rows="3" placeholder="تفاصيل النشاط والأفكار العامة..."
                          class="w-full px-4 py-2.5 rounded-xl border-2 border-gray-200 focus:border-primary focus:outline-none"></textarea>
            </div>
            {{-- قالب تاسكات المحاضرة القياسية (من دليل تنظيم ملفات المحاضرة) --}}
            <div id="templateOption" class="hidden bg-teal-50 border border-teal-200 rounded-xl p-3">
                <label class="flex items-start gap-2 cursor-pointer">
                    <input type="checkbox" name="with_template" value="1" id="withTemplateCheckbox"
                           class="mt-1 rounded border-gray-300 text-teal-600 focus:ring-teal-500">
                    <span class="text-sm text-gray-700">
                        <span class="font-semibold">إنشاء تاسكات المحاضرة القياسية تلقائيًا</span>
                        <span class="block text-xs text-gray-500 mt-0.5">
                            11 مهمة موزّعة على الفريق حسب الدور: إعلان، تذكير، تيزر، مونتاج، كفر يوتيوب، رفع، مقاطع، ريلز، آراء، جدولة — بمواعيد نسبية لتاريخ المحاضرة.
                        </span>
                    </span>
                </label>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="btn-primary text-white px-5 py-2.5 rounded-xl font-medium flex-1">إنشاء</button>
                <button type="button" onclick="document.getElementById('newActivityModal').classList.add('hidden')"
                        class="px-5 py-2.5 rounded-xl font-medium bg-gray-100 text-gray-700 hover:bg-gray-200">إلغاء</button>
            </div>
        </form>
    </div>
</div>

<script>
    function toggleTemplateOption() {
        const selected = document.querySelector('input[name="type"]:checked');
        const type = selected ? selected.value : 'other';
        const box = document.getElementById('templateOption');
        const checkbox = document.getElementById('withTemplateCheckbox');
        const isLecture = type === 'free_lecture' || type === 'live_lecture';
        box.classList.toggle('hidden', !isLecture);
        if (!isLecture && checkbox) checkbox.checked = false;
        if (isLecture && checkbox) checkbox.checked = true;

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
    document.addEventListener('DOMContentLoaded', toggleTemplateOption);
</script>
@endsection
