@extends($workLayout ?? 'layouts.dashboard')

@section('title', 'تعديل: '.$task->title)
@section('page-title', 'تعديل المحتوى')
@section('page-description', $activity->title)

@section('content')
<div class="max-w-3xl mx-auto space-y-5">

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-xl flex items-center">
            <span class="material-icons ml-2">check_circle</span>
            {{ session('success') }}
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

    <div class="flex flex-wrap items-center justify-between gap-3">
        <a href="{{ work_route('show', $activity) }}" class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-primary">
            <span class="material-icons text-lg">arrow_forward</span>
            رجوع للنشاط
        </a>
        <a href="{{ work_route('tasks.show', [$activity, $task]) }}" class="inline-flex items-center gap-1 text-sm text-indigo-600 hover:text-indigo-800">
            <span class="material-icons text-base">visibility</span>
            عرض التفاصيل
        </a>
    </div>

    <div class="card rounded-2xl p-6">
        <div class="flex items-center gap-2 flex-wrap mb-5">
            <h2 class="text-xl font-bold text-gray-900">تعديل المحتوى</h2>
            <span class="role-badge role-{{ $task->pipeline_stage_color }}">{{ $task->pipeline_stage_label }}</span>
            @if($task->content_type_label)
                <span class="role-badge role-indigo">{{ $task->content_type_label }}</span>
            @endif
        </div>

        <form method="POST" action="{{ work_route('tasks.update', [$activity, $task]) }}" class="space-y-4">
            @csrf @method('PUT')

            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">العنوان</label>
                <input type="text" name="title" value="{{ old('title', $task->title) }}" required
                       class="w-full px-3 py-2.5 rounded-xl border-2 border-gray-200 text-sm focus:border-primary focus:outline-none">
            </div>

            <div>
                <label class="block text-xs font-medium text-violet-700 mb-1">TOV</label>
                <textarea name="tov" rows="3"
                          class="w-full px-3 py-2.5 rounded-xl border-2 border-violet-200 bg-violet-50/40 text-sm focus:border-violet-400 focus:outline-none">{{ old('tov', $task->tov) }}</textarea>
            </div>

            <div>
                <label class="block text-xs font-medium text-sky-700 mb-1">Caption</label>
                <textarea name="caption" rows="4"
                          class="w-full px-3 py-2.5 rounded-xl border-2 border-sky-200 bg-sky-50/40 text-sm focus:border-sky-400 focus:outline-none">{{ old('caption', $task->caption) }}</textarea>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">الفكرة</label>
                <textarea name="idea" rows="2"
                          class="w-full px-3 py-2.5 rounded-xl border-2 border-gray-200 text-sm focus:border-primary focus:outline-none">{{ old('idea', $task->idea) }}</textarea>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">نوع المحتوى</label>
                <select name="content_type" class="w-full px-3 py-2.5 rounded-xl border-2 border-gray-200 text-sm focus:border-primary focus:outline-none">
                    <option value="">— اختر —</option>
                    @foreach($contentTypes as $key => $label)
                        <option value="{{ $key }}" @selected(old('content_type', $task->content_type) === $key)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">مرجع التصميم</label>
                <textarea name="design_reference" rows="3"
                          class="w-full px-3 py-2.5 rounded-xl border-2 border-gray-200 text-sm focus:border-primary focus:outline-none">{{ old('design_reference', $task->design_reference) }}</textarea>
            </div>

            <div>
                <div class="flex items-center justify-between mb-1">
                    <label class="block text-xs font-medium text-gray-600">ملخص للمصمم</label>
                    <button type="button" id="editSummarizeBtn" onclick="summarizeDesigner()"
                            class="text-xs text-amber-700 hover:text-amber-900 flex items-center gap-1">
                        <span class="material-icons text-sm">auto_awesome</span>
                        ولّد الملخص
                    </button>
                </div>
                <textarea name="designer_brief" id="designerBriefField" rows="3"
                          class="w-full px-3 py-2.5 rounded-xl border-2 border-amber-100 bg-amber-50/40 text-sm focus:border-amber-400 focus:outline-none">{{ old('designer_brief', $task->designer_brief) }}</textarea>
            </div>

            <div class="rounded-2xl border border-gray-100 bg-gray-50/60 p-4 space-y-3">
                <label class="block text-sm font-bold text-gray-800">المنصات</label>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                    @foreach($platforms as $key => $label)
                        @php $platformChecked = in_array($key, old('platforms', $task->platforms ?? []), true); @endphp
                        <label class="choice-card relative cursor-pointer rounded-xl border-2 p-3 text-center transition-all
                            {{ $platformChecked ? 'border-indigo-500 bg-indigo-50 text-indigo-800 shadow-sm' : 'border-gray-200 bg-white text-gray-700 hover:border-indigo-300' }}">
                            <input type="checkbox" name="platforms[]" value="{{ $key }}" class="sr-only peer platform-toggle"
                                   data-platform="{{ $key }}"
                                   @checked($platformChecked)
                                   onchange="this.closest('label').classList.toggle('border-indigo-500', this.checked); this.closest('label').classList.toggle('bg-indigo-50', this.checked); this.closest('label').classList.toggle('text-indigo-800', this.checked); this.closest('label').classList.toggle('shadow-sm', this.checked); this.closest('label').classList.toggle('border-gray-200', !this.checked); this.closest('label').classList.toggle('bg-white', !this.checked); this.closest('label').classList.toggle('text-gray-700', !this.checked); syncPublishLinkFields();">
                            <span class="material-icons text-xl mb-1 block
                                {{ $key === 'facebook' ? 'text-blue-600' : ($key === 'instagram' ? 'text-pink-500' : ($key === 'linkedin' ? 'text-sky-700' : ($key === 'tiktok' ? 'text-gray-900' : 'text-slate-700'))) }}">
                                {{ $key === 'facebook' ? 'facebook' : ($key === 'instagram' ? 'photo_camera' : ($key === 'linkedin' ? 'work' : ($key === 'tiktok' ? 'smart_display' : 'tag'))) }}
                            </span>
                            <span class="text-xs font-semibold">{{ $label }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <div id="publishLinksSection" class="rounded-2xl border border-teal-100 bg-teal-50/40 p-4 space-y-3">
                <div>
                    <label class="block text-sm font-bold text-teal-900">روابط النشر</label>
                    <p class="text-xs text-teal-700 mt-0.5">ظهر حسب المنصات المختارة — الصق رابط المنشور بعد النشر</p>
                </div>
                <div class="space-y-2">
                    @foreach($platforms as $key => $label)
                        @php $showLink = in_array($key, old('platforms', $task->platforms ?? []), true); @endphp
                        <div class="publish-link-row {{ $showLink ? '' : 'hidden' }}" data-platform="{{ $key }}">
                            <label class="block text-xs font-medium text-teal-800 mb-1">{{ $label }}</label>
                            <input type="url" name="publish_links[{{ $key }}]" dir="ltr"
                                   value="{{ old('publish_links.'.$key, $task->publishLinkFor($key)) }}"
                                   placeholder="https://..."
                                   class="w-full px-3 py-2.5 rounded-xl border-2 border-teal-100 bg-white text-sm focus:border-teal-400 focus:outline-none">
                        </div>
                    @endforeach
                </div>
                <p id="publishLinksEmpty" class="text-xs text-teal-600 {{ !empty(old('platforms', $task->platforms ?? [])) ? 'hidden' : '' }}">
                    اختَر منصة أولاً عشان تظهر خانات الروابط
                </p>
            </div>

            <div class="rounded-2xl border border-gray-100 bg-gray-50/60 p-4 space-y-3">
                <label class="block text-sm font-bold text-gray-800">نوع الشغل</label>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                    @foreach($kinds as $key => $label)
                        @php $kindSelected = old('kind', $task->kind) === $key; @endphp
                        <label class="relative cursor-pointer rounded-xl border-2 p-3 text-center transition-all
                            {{ $kindSelected ? 'border-teal-500 bg-teal-50 text-teal-900 shadow-sm' : 'border-gray-200 bg-white text-gray-700 hover:border-teal-300' }}">
                            <input type="radio" name="kind" value="{{ $key }}" class="sr-only"
                                   @checked($kindSelected)
                                   onchange="document.querySelectorAll('input[name=kind]').forEach(function(r){ var l=r.closest('label'); var on=r.checked; l.classList.toggle('border-teal-500', on); l.classList.toggle('bg-teal-50', on); l.classList.toggle('text-teal-900', on); l.classList.toggle('shadow-sm', on); l.classList.toggle('border-gray-200', !on); l.classList.toggle('bg-white', !on); l.classList.toggle('text-gray-700', !on); });">
                            <span class="material-icons text-xl mb-1 block text-teal-600">
                                {{ $key === 'design' ? 'palette' : ($key === 'video' ? 'movie' : ($key === 'content' ? 'edit_note' : ($key === 'publish' ? 'campaign' : 'more_horiz'))) }}
                            </span>
                            <span class="text-xs font-semibold">{{ $label }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="rounded-2xl border border-gray-100 bg-gray-50/60 p-4 space-y-3">
                <label class="block text-sm font-bold text-gray-800">الحالة</label>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                    @foreach($taskStatuses as $key => $label)
                        @php
                            $statusSelected = old('status', $task->status) === $key;
                            $statusTone = match($key) {
                                'todo' => ['border' => 'border-gray-500', 'bg' => 'bg-gray-100', 'text' => 'text-gray-800', 'icon' => 'radio_button_unchecked', 'hover' => 'hover:border-gray-400'],
                                'in_progress' => ['border' => 'border-blue-500', 'bg' => 'bg-blue-50', 'text' => 'text-blue-900', 'icon' => 'autorenew', 'hover' => 'hover:border-blue-300'],
                                'review' => ['border' => 'border-amber-500', 'bg' => 'bg-amber-50', 'text' => 'text-amber-900', 'icon' => 'rate_review', 'hover' => 'hover:border-amber-300'],
                                'done' => ['border' => 'border-green-500', 'bg' => 'bg-green-50', 'text' => 'text-green-900', 'icon' => 'check_circle', 'hover' => 'hover:border-green-300'],
                                default => ['border' => 'border-indigo-500', 'bg' => 'bg-indigo-50', 'text' => 'text-indigo-900', 'icon' => 'flag', 'hover' => 'hover:border-indigo-300'],
                            };
                        @endphp
                        <label class="relative cursor-pointer rounded-xl border-2 p-3 text-center transition-all
                            {{ $statusSelected ? $statusTone['border'].' '.$statusTone['bg'].' '.$statusTone['text'].' shadow-sm' : 'border-gray-200 bg-white text-gray-700 '.$statusTone['hover'] }}"
                               data-status-card="{{ $key }}">
                            <input type="radio" name="status" value="{{ $key }}" class="sr-only"
                                   @checked($statusSelected)
                                   onchange="syncStatusCards()">
                            <span class="material-icons text-xl mb-1 block">{{ $statusTone['icon'] }}</span>
                            <span class="text-xs font-semibold">{{ $label }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">كاتب المحتوى</label>
                    <select name="content_writer_id" class="w-full px-3 py-2.5 rounded-xl border-2 border-gray-200 text-sm focus:border-primary focus:outline-none">
                        <option value="">— غير معيّن —</option>
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}" @selected((string) old('content_writer_id', $task->content_writer_id) === (string) $emp->id)>
                                {{ $emp->name }} — {{ $emp->role_badge }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-purple-700 mb-1">المصمم</label>
                    <select name="designer_id" class="w-full px-3 py-2.5 rounded-xl border-2 border-purple-200 bg-purple-50/30 text-sm focus:border-purple-400 focus:outline-none">
                        <option value="">— غير معيّن —</option>
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}" @selected((string) old('designer_id', $task->designer_id) === (string) $emp->id)>
                                {{ $emp->name }} — {{ $emp->role_badge }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">الموظف الحالي</label>
                    <select name="assigned_to" class="w-full px-3 py-2.5 rounded-xl border-2 border-gray-200 text-sm focus:border-primary focus:outline-none">
                        <option value="">— غير معيّن —</option>
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}" @selected((string) old('assigned_to', $task->assigned_to) === (string) $emp->id)>
                                {{ $emp->name }} — {{ $emp->role_badge }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">تاريخ التسليم</label>
                    <input type="date" name="due_date" value="{{ old('due_date', optional($task->due_date)->format('Y-m-d')) }}"
                           class="w-full px-3 py-2.5 rounded-xl border-2 border-gray-200 text-sm focus:border-primary focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">موعد النشر</label>
                    <input type="date" name="publish_date" value="{{ old('publish_date', optional($task->publish_date)->format('Y-m-d')) }}"
                           class="w-full px-3 py-2.5 rounded-xl border-2 border-gray-200 text-sm focus:border-primary focus:outline-none">
                </div>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">ملاحظات</label>
                <textarea name="notes" rows="2"
                          class="w-full px-3 py-2.5 rounded-xl border-2 border-gray-200 text-sm focus:border-primary focus:outline-none">{{ old('notes', $task->notes) }}</textarea>
            </div>

            <div class="flex flex-col sm:flex-row flex-wrap gap-3 pt-2">
                <button type="submit" class="btn-primary text-white px-5 py-2.5 rounded-xl font-medium flex-1">حفظ والرجوع للنشاط</button>
                <button type="submit" name="return_to_detail" value="1"
                        class="px-5 py-2.5 rounded-xl font-medium bg-indigo-50 text-indigo-700 hover:bg-indigo-100">حفظ وعرض التفاصيل</button>
                @if(!empty($canMoveDesignFolders))
                    <button type="submit" name="move_design_folder" value="1"
                            onclick="return confirm('هتعمل إعادة رفع/تنظيم لملفات التصميم حسب العنوان الجديد على NAS (لو متفعل). المتابعة قد تستغرق دقيقة.');"
                            class="px-5 py-2.5 rounded-xl font-medium bg-amber-50 text-amber-700 hover:bg-amber-100">
                        حفظ وانقل ملفات التصميم للفولدر الجديد
                    </button>
                @endif
                <a href="{{ work_route('show', $activity) }}"
                   class="px-5 py-2.5 rounded-xl font-medium bg-gray-100 text-gray-700 hover:bg-gray-200 text-center">إلغاء</a>
            </div>
        </form>
    </div>

    @if(!empty($canMoveToActivity) && ($activities ?? collect())->isNotEmpty())
        <div class="card rounded-2xl p-6 border border-amber-100 bg-amber-50/40">
            <h3 class="text-base font-bold text-amber-950 flex items-center gap-2 mb-1">
                <span class="material-icons text-amber-700">drive_file_move</span>
                نقل التاسك لنشاط تاني
            </h3>
            <p class="text-xs text-amber-800/80 mb-4">اختار النشاط (فولدر الحملة) اللي عايز تنقل التاسك له. الملفات والتعليقات هتفضل معاه.</p>
            <form method="POST" action="{{ work_route('tasks.move-activity', [$activity, $task]) }}" class="space-y-3"
                  onsubmit="return confirm('نقل التاسك للنشاط المختار؟');">
                @csrf
                <div>
                    <label class="block text-xs font-medium text-amber-900 mb-1">النشاط الهدف</label>
                    <select name="target_activity_id" required
                            class="w-full px-3 py-2.5 rounded-xl border-2 border-amber-200 bg-white text-sm focus:border-amber-500 focus:outline-none">
                        <option value="">— اختر نشاط —</option>
                        @foreach($activities as $targetActivity)
                            @if((int) $targetActivity->id === (int) $activity->id)
                                @continue
                            @endif
                            <option value="{{ $targetActivity->id }}">
                                @if($targetActivity->folder)
                                    {{ $targetActivity->folder->title }} —
                                @endif
                                {{ $targetActivity->title }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <button type="submit"
                        class="inline-flex items-center gap-1.5 px-5 py-2.5 rounded-xl font-medium bg-amber-600 text-white hover:bg-amber-700">
                    <span class="material-icons text-sm">swap_horiz</span>
                    نقل التاسك
                </button>
            </form>
        </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
function syncPublishLinkFields() {
    let any = false;
    document.querySelectorAll('.platform-toggle').forEach(function (cb) {
        const row = document.querySelector('.publish-link-row[data-platform="' + cb.value + '"]');
        if (!row) return;
        row.classList.toggle('hidden', !cb.checked);
        if (cb.checked) any = true;
    });
    const empty = document.getElementById('publishLinksEmpty');
    if (empty) empty.classList.toggle('hidden', any);
}

const statusCardStyles = {
    todo: { on: ['border-gray-500', 'bg-gray-100', 'text-gray-800', 'shadow-sm'], off: ['border-gray-200', 'bg-white', 'text-gray-700', 'hover:border-gray-400'] },
    in_progress: { on: ['border-blue-500', 'bg-blue-50', 'text-blue-900', 'shadow-sm'], off: ['border-gray-200', 'bg-white', 'text-gray-700', 'hover:border-blue-300'] },
    review: { on: ['border-amber-500', 'bg-amber-50', 'text-amber-900', 'shadow-sm'], off: ['border-gray-200', 'bg-white', 'text-gray-700', 'hover:border-amber-300'] },
    done: { on: ['border-green-500', 'bg-green-50', 'text-green-900', 'shadow-sm'], off: ['border-gray-200', 'bg-white', 'text-gray-700', 'hover:border-green-300'] },
};

function syncStatusCards() {
    document.querySelectorAll('[data-status-card]').forEach(function (label) {
        const key = label.dataset.statusCard;
        const input = label.querySelector('input[type="radio"]');
        const styles = statusCardStyles[key] || statusCardStyles.todo;
        const all = styles.on.concat(styles.off);
        all.forEach(function (c) { label.classList.remove(c); });
        (input.checked ? styles.on : styles.off).forEach(function (c) { label.classList.add(c); });
    });
}

async function summarizeDesigner() {
    const btn = document.getElementById('editSummarizeBtn');
    const original = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<span class="material-icons text-sm animate-spin">progress_activity</span> جاري...';
    try {
        const res = await fetch("{{ work_route('tasks.summarize-designer', [$activity, $task], false) }}", {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            credentials: 'same-origin',
            body: JSON.stringify({}),
        });
        const data = await res.json();
        if (!data.success) {
            alert(data.error || 'فشل التلخيص');
            return;
        }
        document.getElementById('designerBriefField').value = data.designer_brief || '';
    } catch (e) {
        alert('حدث خطأ أثناء التلخيص');
    } finally {
        btn.disabled = false;
        btn.innerHTML = original;
    }
}
</script>
@endsection
