@extends('layouts.dashboard')

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
        <a href="{{ route('work.show', $activity) }}" class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-primary">
            <span class="material-icons text-lg">arrow_forward</span>
            رجوع للنشاط
        </a>
        <a href="{{ route('work.tasks.show', [$activity, $task]) }}" class="inline-flex items-center gap-1 text-sm text-indigo-600 hover:text-indigo-800">
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

        <form method="POST" action="{{ route('work.tasks.update', [$activity, $task]) }}" class="space-y-4">
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

            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">المنصات</label>
                <div class="flex flex-wrap gap-2">
                    @foreach($platforms as $key => $label)
                        <label class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg border border-gray-200 text-xs cursor-pointer hover:border-primary">
                            <input type="checkbox" name="platforms[]" value="{{ $key }}"
                                   @checked(in_array($key, old('platforms', $task->platforms ?? []), true))
                                   class="rounded border-gray-300 text-primary focus:ring-primary">
                            {{ $label }}
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">نوع الشغل</label>
                    <select name="kind" class="w-full px-3 py-2.5 rounded-xl border-2 border-gray-200 text-sm focus:border-primary focus:outline-none">
                        @foreach($kinds as $key => $label)
                            <option value="{{ $key }}" @selected(old('kind', $task->kind) === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">الحالة</label>
                    <select name="status" class="w-full px-3 py-2.5 rounded-xl border-2 border-gray-200 text-sm focus:border-primary focus:outline-none">
                        @foreach($taskStatuses as $key => $label)
                            <option value="{{ $key }}" @selected(old('status', $task->status) === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
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

            <div class="flex flex-col sm:flex-row gap-3 pt-2">
                <button type="submit" class="btn-primary text-white px-5 py-2.5 rounded-xl font-medium flex-1">حفظ والرجوع للنشاط</button>
                <button type="submit" name="return_to_detail" value="1"
                        class="px-5 py-2.5 rounded-xl font-medium bg-indigo-50 text-indigo-700 hover:bg-indigo-100">حفظ وعرض التفاصيل</button>
                <a href="{{ route('work.show', $activity) }}"
                   class="px-5 py-2.5 rounded-xl font-medium bg-gray-100 text-gray-700 hover:bg-gray-200 text-center">إلغاء</a>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
async function summarizeDesigner() {
    const btn = document.getElementById('editSummarizeBtn');
    const original = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<span class="material-icons text-sm animate-spin">progress_activity</span> جاري...';
    try {
        const res = await fetch("{{ route('work.tasks.summarize-designer', [$activity, $task], false) }}", {
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
