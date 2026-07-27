@extends('layouts.employee')

@section('title', $task->title)
@section('page-title', 'مهمة من مساحة العمل')
@section('page-description', $task->activity->title)

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    <a href="{{ $task->activity ? route('employee.work.activity', $task->activity) : route('employee.tasks.index') }}" class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-purple-600">
        <span class="material-icons text-lg">arrow_forward</span>
        رجوع للنشاط
    </a>

    <div class="card p-6">
        {{-- النشاط --}}
        <div class="flex items-center gap-2 text-sm text-gray-500 mb-4">
            <span class="material-icons text-purple-500">dashboard_customize</span>
            <span>{{ $task->activity->title }}</span>
        </div>

        <div class="flex items-center gap-2 flex-wrap mb-3">
            @php
                $kindColors = ['design'=>'bg-purple-100 text-purple-700','video'=>'bg-red-100 text-red-700','content'=>'bg-blue-100 text-blue-700','publish'=>'bg-teal-100 text-teal-700','other'=>'bg-gray-100 text-gray-700'];
                $stColors = ['todo'=>'bg-gray-100 text-gray-700','in_progress'=>'bg-blue-100 text-blue-700','review'=>'bg-yellow-100 text-yellow-700','done'=>'bg-green-100 text-green-700'];
            @endphp
            <span class="px-3 py-1 text-xs rounded-full {{ $kindColors[$task->kind] ?? 'bg-gray-100 text-gray-700' }}">{{ $task->kind_label }}</span>
            <span class="px-3 py-1 text-xs rounded-full {{ $stColors[$task->status] ?? 'bg-gray-100 text-gray-700' }}">{{ $task->status_label }}</span>
            @if($task->content_type_label)
                <span class="px-3 py-1 text-xs rounded-full bg-indigo-100 text-indigo-700">{{ $task->content_type_label }}</span>
            @endif
            @if($task->is_overdue)
                <span class="px-3 py-1 text-xs rounded-full bg-red-100 text-red-700">متأخرة</span>
            @endif
        </div>

        <h2 class="text-xl font-bold text-gray-800">{{ $task->title }}</h2>

        <div class="flex flex-wrap gap-x-4 gap-y-1 mt-2 text-sm text-gray-500">
            @if($task->due_date)
                <span class="flex items-center gap-1">
                    <span class="material-icons text-sm">event</span>
                    التسليم: {{ $task->due_date->format('Y/m/d') }}
                </span>
            @endif
            @if($task->publish_date)
                <span class="flex items-center gap-1">
                    <span class="material-icons text-sm">campaign</span>
                    النشر: {{ $task->publish_date->format('Y/m/d') }}
                </span>
            @endif
        </div>

        @if(!empty($task->platform_labels))
            <div class="flex flex-wrap gap-1.5 mt-3">
                @foreach($task->platform_labels as $plat)
                    <span class="px-2.5 py-1 text-xs rounded-lg bg-gray-100 text-gray-600">{{ $plat }}</span>
                @endforeach
            </div>
        @endif

        @if($task->idea)
            <div class="mt-5">
                <h3 class="text-sm font-semibold text-gray-700 mb-1">الفكرة</h3>
                <p class="text-sm text-gray-600 bg-gray-50 rounded-xl p-3 whitespace-pre-line">{{ $task->idea }}</p>
            </div>
        @endif

        @if($task->tov)
            <div class="mt-4">
                <h3 class="text-sm font-semibold text-gray-700 mb-1">TOV</h3>
                <p class="text-sm text-gray-600 bg-gray-50 rounded-xl p-3 whitespace-pre-line">{{ $task->tov }}</p>
            </div>
        @endif

        @if($task->caption)
            <div class="mt-4">
                <h3 class="text-sm font-semibold text-gray-700 mb-1">Caption</h3>
                <p class="text-sm text-gray-600 bg-gray-50 rounded-xl p-3 whitespace-pre-line">{{ $task->caption }}</p>
            </div>
        @endif

        @if($task->design_reference)
            <div class="mt-4">
                <h3 class="text-sm font-semibold text-gray-700 mb-1">مرجع التصميم</h3>
                <p class="text-sm text-gray-600 bg-amber-50 border border-amber-100 rounded-xl p-3 whitespace-pre-line">{{ $task->design_reference }}</p>
            </div>
        @endif

        @if($task->designer_brief)
            <div class="mt-4">
                <h3 class="text-sm font-semibold text-amber-800 mb-1 flex items-center gap-1">
                    <span class="material-icons text-base">tips_and_updates</span>
                    ملخص المطلوب من المصمم
                </h3>
                <p class="text-sm text-gray-700 bg-amber-50 border border-amber-200 rounded-xl p-3 whitespace-pre-line">{{ $task->designer_brief }}</p>
            </div>
        @endif

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mt-5">
            @if($task->contentWriter)
                <div class="bg-blue-50 rounded-xl p-3">
                    <p class="text-xs text-blue-600 mb-0.5">كاتب المحتوى</p>
                    <p class="text-sm font-medium text-gray-800">{{ $task->contentWriter->name }}</p>
                </div>
            @endif
            @if($task->designer)
                <div class="bg-purple-50 rounded-xl p-3">
                    <p class="text-xs text-purple-600 mb-0.5">المصمم</p>
                    <p class="text-sm font-medium text-gray-800">{{ $task->designer->name }}</p>
                </div>
            @endif
        </div>
    </div>

    {{-- تحديث الحالة والملاحظات --}}
    <div class="card p-6">
        <h3 class="font-bold text-gray-800 mb-4">تحديث المهمة</h3>
        <form method="POST" action="{{ route('employee.work.status', $task) }}" class="space-y-4">
            @csrf @method('PATCH')
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">الحالة</label>
                <select name="status" class="w-full px-4 py-2.5 rounded-xl border-2 border-gray-200 focus:border-purple-500 focus:outline-none">
                    @foreach($statuses as $key => $label)
                        <option value="{{ $key }}" @selected($task->status === $key)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">ملاحظات</label>
                <textarea name="notes" rows="4" placeholder="اكتب أي ملاحظات أو تحديثات..."
                          class="w-full px-4 py-2.5 rounded-xl border-2 border-gray-200 focus:border-purple-500 focus:outline-none">{{ $task->notes }}</textarea>
            </div>
            <button type="submit" class="btn-primary text-white px-6 py-2.5 rounded-xl font-medium">حفظ</button>
        </form>
    </div>
</div>
@endsection
