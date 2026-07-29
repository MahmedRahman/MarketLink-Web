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
            <div class="inline-flex rounded-xl border-2 border-gray-200 p-1 bg-gray-50">
                <a href="{{ work_route('index', array_filter(['type' => $filterType, 'status' => $filterStatus, 'view' => 'title'])) }}"
                   class="px-3 py-1.5 rounded-lg text-sm font-semibold inline-flex items-center gap-1 transition-colors {{ ($viewMode ?? 'title') === 'title' ? 'bg-white text-primary shadow-sm' : 'text-gray-600 hover:text-gray-800' }}">
                    <span class="material-icons text-base">title</span>
                    حسب العنوان
                </a>
                <a href="{{ work_route('index', array_filter(['type' => $filterType, 'status' => $filterStatus, 'view' => 'month'])) }}"
                   class="px-3 py-1.5 rounded-lg text-sm font-semibold inline-flex items-center gap-1 transition-colors {{ ($viewMode ?? 'title') === 'month' ? 'bg-white text-primary shadow-sm' : 'text-gray-600 hover:text-gray-800' }}">
                    <span class="material-icons text-base">calendar_month</span>
                    حسب الشهر
                </a>
            </div>

            <form method="GET" class="flex flex-wrap items-center gap-2">
                @if(($viewMode ?? 'title') === 'month')
                    <input type="hidden" name="view" value="month">
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
                    <a href="{{ work_route('index', ($viewMode ?? 'title') === 'month' ? ['view' => 'month'] : []) }}" class="text-sm text-gray-500 hover:text-gray-700 px-2">مسح</a>
                @endif
            </form>
        </div>

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

        <form method="POST" action="{{ work_route('store') }}" class="space-y-4">
            @csrf
            <input type="hidden" name="idea_id" id="ideaIdField" value="">
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
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">وصف مختصر</label>
                <textarea name="description" rows="3" placeholder="تفاصيل النشاط والأفكار العامة..."
                          class="w-full px-4 py-2.5 rounded-xl border-2 border-gray-200 focus:border-primary focus:outline-none"></textarea>
                <p class="text-[11px] text-gray-400 mt-1.5">تاريخ النشاط بيتسجّل تلقائيًا بتاريخ الإنشاء</p>
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
        const box = document.getElementById('templateOption');
        const checkbox = document.getElementById('withTemplateCheckbox');

        // لو مفيش Type مختار (حالة الفكرة بدون type)، نخلي الفورم يطلب اختيار يدويًا
        if (!selected) {
            box.classList.add('hidden');
            if (checkbox) checkbox.checked = false;
            return;
        }

        const type = selected.value;
        const isLecture = type === 'live_lecture' || type === 'live_lecture_paid';
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
    });
</script>
@endsection
