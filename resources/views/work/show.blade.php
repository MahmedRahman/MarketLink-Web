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

            <div class="flex items-center gap-2">
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

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- إضافة مهمة سريعة --}}
        <div class="lg:col-span-1">
            <div class="card rounded-2xl p-5 sticky top-4">
                <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <span class="material-icons text-primary">add_task</span>
                    إضافة مهمة / فكرة
                </h3>
                <form method="POST" action="{{ route('work.tasks.store', $activity) }}" class="space-y-3">
                    @csrf
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">العنوان</label>
                        <input type="text" name="title" required placeholder="مثال: تصميم بوستر الإعلان"
                               class="w-full px-3 py-2 rounded-xl border-2 border-gray-200 text-sm focus:border-primary focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">الفكرة</label>
                        <textarea name="idea" rows="2" placeholder="اكتب الفكرة بسرعة..."
                                  class="w-full px-3 py-2 rounded-xl border-2 border-gray-200 text-sm focus:border-primary focus:outline-none"></textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">نوع الشغل</label>
                        <select name="kind" id="addKind" onchange="updateSuggestion()" class="w-full px-3 py-2 rounded-xl border-2 border-gray-200 text-sm focus:border-primary focus:outline-none">
                            @foreach($kinds as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">الموظف</label>
                        <select name="assigned_to" id="addAssignee" class="w-full px-3 py-2 rounded-xl border-2 border-gray-200 text-sm focus:border-primary focus:outline-none">
                            <option value="">اقتراح تلقائي حسب الدور</option>
                            @foreach($employees as $emp)
                                <option value="{{ $emp->id }}" data-role="{{ $emp->role }}">{{ $emp->name }} — {{ $emp->role_badge }}</option>
                            @endforeach
                        </select>
                        <p id="suggestionHint" class="text-xs text-teal-600 mt-1"></p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">تاريخ التسليم</label>
                        <input type="date" name="due_date"
                               class="w-full px-3 py-2 rounded-xl border-2 border-gray-200 text-sm focus:border-primary focus:outline-none">
                    </div>
                    <button type="submit" class="btn-primary text-white w-full py-2.5 rounded-xl font-medium">إضافة</button>
                </form>
            </div>
        </div>

        {{-- قائمة المهام --}}
        <div class="lg:col-span-2 space-y-3">
            <div class="flex items-center justify-between">
                <h3 class="font-bold text-gray-800">المهام ({{ $activity->tasks->count() }})</h3>
            </div>

            @forelse($activity->tasks as $task)
                <div class="card rounded-2xl p-4 {{ $task->is_overdue ? 'border-r-4 border-red-400' : '' }}">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="role-badge role-{{ $task->kind_color }}">{{ $task->kind_label }}</span>
                                <span class="role-badge role-{{ $task->status_color }}">{{ $task->status_label }}</span>
                                @if($task->is_overdue)
                                    <span class="role-badge role-red flex items-center gap-1"><span class="material-icons text-xs">schedule</span>متأخرة</span>
                                @endif
                            </div>
                            <h4 class="font-semibold text-gray-800 mt-2">{{ $task->title }}</h4>
                            @if($task->idea)
                                <p class="text-sm text-gray-500 mt-1 line-clamp-2">{{ $task->idea }}</p>
                            @endif
                            <div class="flex items-center gap-3 mt-2 text-xs text-gray-500">
                                @if($task->due_date)
                                    <span class="flex items-center gap-1"><span class="material-icons text-sm">event</span>{{ $task->due_date->format('Y/m/d') }}</span>
                                @endif
                            </div>
                        </div>

                        <div class="flex flex-col items-end gap-2 shrink-0">
                            <div class="flex items-center gap-1">
                                <button type="button"
                                        onclick='openTaskEdit(@json($task))'
                                        class="p-2 rounded-lg bg-gray-100 text-gray-600 hover:bg-gray-200" title="تعديل">
                                    <span class="material-icons text-base">edit</span>
                                </button>
                                <button type="button"
                                        onclick="confirmDelete('{{ route('work.tasks.destroy', [$activity, $task]) }}', 'حذف المهمة', 'هل تريد حذف هذه المهمة؟')"
                                        class="p-2 rounded-lg bg-red-50 text-red-600 hover:bg-red-100" title="حذف">
                                    <span class="material-icons text-base">delete</span>
                                </button>
                            </div>
                            {{-- تعيين سريع --}}
                            <form method="POST" action="{{ route('work.tasks.assign', [$activity, $task]) }}">
                                @csrf
                                <select name="assigned_to" onchange="this.form.submit()"
                                        class="px-2 py-1.5 rounded-lg border-2 border-gray-200 text-xs focus:border-primary focus:outline-none max-w-[160px]">
                                    <option value="">— غير معيّن —</option>
                                    @foreach($employees as $emp)
                                        <option value="{{ $emp->id }}" @selected($task->assigned_to === $emp->id)>{{ $emp->name }}</option>
                                    @endforeach
                                </select>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="card rounded-2xl p-10 text-center">
                    <span class="material-icons text-5xl text-gray-300">checklist</span>
                    <p class="text-gray-500 text-sm mt-3">لا توجد مهام بعد — أضف أول مهمة من اليمين</p>
                </div>
            @endforelse
        </div>
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
    <div class="bg-white rounded-2xl w-full max-w-lg p-6 shadow-2xl">
        <div class="flex items-center justify-between mb-5">
            <h3 class="text-lg font-bold text-gray-800">تعديل المهمة</h3>
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
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">الموظف</label>
                    <select name="assigned_to" id="editTaskAssignee" class="w-full px-3 py-2 rounded-xl border-2 border-gray-200 text-sm focus:border-primary focus:outline-none">
                        <option value="">— غير معيّن —</option>
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}">{{ $emp->name }} — {{ $emp->role_badge }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">تاريخ التسليم</label>
                    <input type="date" name="due_date" id="editTaskDue"
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

    function updateSuggestion() {
        const kind = document.getElementById('addKind').value;
        const assignee = document.getElementById('addAssignee').value;
        const hint = document.getElementById('suggestionHint');
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
        const baseUrl = "{{ route('work.tasks.update', [$activity, 'TASK_ID']) }}".replace('TASK_ID', task.id);
        const form = document.getElementById('editTaskForm');
        form.action = baseUrl;
        document.getElementById('editTaskTitle').value = task.title || '';
        document.getElementById('editTaskIdea').value = task.idea || '';
        document.getElementById('editTaskNotes').value = task.notes || '';
        document.getElementById('editTaskKind').value = task.kind || 'other';
        document.getElementById('editTaskStatus').value = task.status || 'todo';
        document.getElementById('editTaskAssignee').value = task.assigned_to || '';
        document.getElementById('editTaskDue').value = task.due_date ? task.due_date.substring(0, 10) : '';
        document.getElementById('editTaskModal').classList.remove('hidden');
    }

    document.addEventListener('DOMContentLoaded', updateSuggestion);
</script>
@endsection
