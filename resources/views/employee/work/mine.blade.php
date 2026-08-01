@extends('layouts.employee')

@section('title', 'مهامي')
@section('page-title', 'مهامي')
@section('page-description', 'المهام الحالية المسندة لك في مساحة العمل')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">

    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="card rounded-2xl p-4 flex items-center gap-3">
            <div class="w-11 h-11 rounded-xl bg-indigo-100 text-indigo-600 flex items-center justify-center">
                <span class="material-icons">assignment</span>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-800">{{ $stats['total'] }}</p>
                <p class="text-xs text-gray-500">مهام ظاهرة</p>
            </div>
        </div>
        <div class="card rounded-2xl p-4 flex items-center gap-3">
            <div class="w-11 h-11 rounded-xl bg-red-100 text-red-600 flex items-center justify-center">
                <span class="material-icons">warning_amber</span>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-800">{{ $stats['overdue'] }}</p>
                <p class="text-xs text-gray-500">متأخرة</p>
            </div>
        </div>
        <div class="card rounded-2xl p-4 flex items-center gap-3">
            <div class="w-11 h-11 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center">
                <span class="material-icons">bolt</span>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-800">{{ $stats['in_progress'] }}</p>
                <p class="text-xs text-gray-500">قيد التنفيذ</p>
            </div>
        </div>
        <div class="card rounded-2xl p-4 flex items-center gap-3">
            <div class="w-11 h-11 rounded-xl bg-gray-100 text-gray-600 flex items-center justify-center">
                <span class="material-icons">pending_actions</span>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-800">{{ $stats['todo'] }}</p>
                <p class="text-xs text-gray-500">لم تبدأ</p>
            </div>
        </div>
    </div>

    <form method="GET" action="{{ route('employee.mine') }}" class="card rounded-2xl p-4">
        <div class="flex flex-wrap items-center gap-3">
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">الحالة</label>
                <select name="state" onchange="this.form.submit()" class="px-3 py-2 rounded-xl border-2 border-gray-200 text-sm focus:border-primary focus:outline-none">
                    <option value="active" @selected(($filters['state'] ?? '') === 'active')>نشطة فقط</option>
                    <option value="overdue" @selected(($filters['state'] ?? '') === 'overdue')>متأخرة فقط</option>
                    <option value="done" @selected(($filters['state'] ?? '') === 'done')>منجزة</option>
                    <option value="" @selected(($filters['state'] ?? null) === null && request()->has('state'))>الكل</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">المرحلة</label>
                <select name="stage" onchange="this.form.submit()" class="px-3 py-2 rounded-xl border-2 border-gray-200 text-sm focus:border-primary focus:outline-none">
                    <option value="">كل المراحل</option>
                    @foreach($stages as $key => $label)
                        <option value="{{ $key }}" @selected(($filters['stage'] ?? '') === $key)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            @if(request()->hasAny(['state', 'stage']))
                <a href="{{ route('employee.mine') }}" class="self-end text-sm text-gray-500 hover:text-gray-700 px-2 py-2">مسح الفلاتر</a>
            @endif
            @if($employee->isWorkHubAdmin())
                <a href="{{ route('employee.hub.index') }}" class="self-end mr-auto text-sm font-medium text-indigo-700 hover:text-indigo-900 px-3 py-2 rounded-xl bg-indigo-50">
                    مساحة العمل الكاملة ←
                </a>
            @else
                <a href="{{ route('employee.tasks.index') }}" class="self-end mr-auto text-sm font-medium text-indigo-700 hover:text-indigo-900 px-3 py-2 rounded-xl bg-indigo-50">
                    الأنشطة ←
                </a>
            @endif
        </div>
    </form>

    @if($tasks->isEmpty())
        <div class="card rounded-2xl p-12 text-center">
            <span class="material-icons text-6xl text-gray-300">task_alt</span>
            <h3 class="text-lg font-bold text-gray-700 mt-4">مفيش مهام حالية</h3>
            <p class="text-gray-500 text-sm mt-1">لما يتسند لك تاسك في مساحة العمل هيظهر هنا</p>
        </div>
    @else
        <div class="space-y-3">
            @foreach($tasks as $task)
                @php
                    $activity = $task->activity;
                    $kindColors = [
                        'design' => 'bg-purple-100 text-purple-700',
                        'video' => 'bg-red-100 text-red-700',
                        'content' => 'bg-blue-100 text-blue-700',
                        'publish' => 'bg-teal-100 text-teal-700',
                        'other' => 'bg-gray-100 text-gray-700',
                    ];
                    $stColors = [
                        'todo' => 'bg-gray-100 text-gray-700',
                        'in_progress' => 'bg-blue-100 text-blue-700',
                        'review' => 'bg-yellow-100 text-yellow-700',
                        'done' => 'bg-green-100 text-green-700',
                        'executed' => 'bg-emerald-100 text-emerald-700',
                    ];
                @endphp
                <a href="{{ route('employee.work.show', $task) }}"
                   class="card rounded-2xl p-4 block hover:shadow-md transition-shadow {{ $task->is_overdue ? 'ring-1 ring-red-200' : '' }}">
                    <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-3">
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="px-2 py-0.5 text-xs rounded-full font-semibold {{ $kindColors[$task->kind] ?? 'bg-gray-100 text-gray-700' }}">
                                    {{ $task->content_type_label ?: $task->kind_label }}
                                </span>
                                <span class="px-2 py-0.5 text-xs rounded-full font-semibold {{ $stColors[$task->status] ?? 'bg-gray-100 text-gray-700' }}">
                                    {{ $task->status_label }}
                                </span>
                                <span class="role-badge role-{{ $task->pipeline_stage_color }} inline-flex items-center gap-1">
                                    <span class="material-icons text-sm">{{ $task->pipeline_stage_icon }}</span>
                                    {{ $task->pipeline_stage_label }}
                                </span>
                                @if($task->is_overdue)
                                    <span class="px-2 py-0.5 text-xs rounded-full bg-red-600 text-white font-bold">متأخرة</span>
                                @endif
                            </div>
                            <h3 class="font-bold text-gray-900 mt-2 leading-snug">{{ $task->title }}</h3>
                            @if($activity)
                                <p class="text-sm text-indigo-700 mt-1 truncate inline-flex items-center gap-1">
                                    <span class="material-icons text-sm">folder_open</span>
                                    {{ $activity->title }}
                                </p>
                            @endif
                            <div class="flex flex-wrap items-center gap-3 mt-2 text-xs text-gray-500">
                                @if($task->due_date)
                                    <span class="inline-flex items-center gap-1 {{ $task->is_overdue ? 'text-red-600 font-semibold' : '' }}">
                                        <span class="material-icons text-sm">event</span>
                                        تسليم: {{ $task->due_date->format('Y/m/d') }}
                                    </span>
                                @endif
                                @if($task->publish_date)
                                    <span class="inline-flex items-center gap-1">
                                        <span class="material-icons text-sm">publish</span>
                                        نشر: {{ $task->publish_date->format('Y/m/d') }}
                                    </span>
                                @endif
                            </div>
                        </div>
                        <span class="inline-flex items-center gap-1 text-sm font-medium text-primary shrink-0">
                            فتح التاسك
                            <span class="material-icons text-base">arrow_back</span>
                        </span>
                    </div>
                </a>
            @endforeach
        </div>
    @endif
</div>
@endsection
