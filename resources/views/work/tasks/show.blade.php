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

    <a href="{{ route('work.show', $activity) }}" class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-primary">
        <span class="material-icons text-lg">arrow_forward</span>
        رجوع للنشاط
    </a>

    {{-- رأس التاسك --}}
    <div class="card rounded-2xl p-6">
        <div class="flex flex-col md:flex-row md:items-start justify-between gap-4">
            <div class="min-w-0">
                <div class="flex items-center gap-2 flex-wrap mb-3">
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
            <div class="flex items-center gap-2 shrink-0">
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
</script>
@endsection
