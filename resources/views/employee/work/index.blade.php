@extends('layouts.employee')

@section('title', 'المهام')
@section('page-title', 'المهام')
@section('page-description', 'كل المهام المعيّنة لك')

@section('content')
@php
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
        'publish' => 'bg-teal-100 text-teal-700',
        'archived' => 'bg-gray-100 text-gray-500',
    ];
@endphp

<div class="space-y-6">
    {{-- إحصائيات سريعة --}}
    <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
        <div class="card p-4">
            <p class="text-xs text-gray-500">الإجمالي</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ $stats['total'] }}</p>
        </div>
        <div class="card p-4">
            <p class="text-xs text-gray-500">لم تبدأ</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ $stats['todo'] }}</p>
        </div>
        <div class="card p-4">
            <p class="text-xs text-gray-500">قيد التنفيذ</p>
            <p class="text-2xl font-bold text-blue-600 mt-1">{{ $stats['in_progress'] }}</p>
        </div>
        <div class="card p-4">
            <p class="text-xs text-gray-500">قيد المراجعة</p>
            <p class="text-2xl font-bold text-yellow-600 mt-1">{{ $stats['review'] }}</p>
        </div>
        <div class="card p-4">
            <p class="text-xs text-gray-500">متأخرة</p>
            <p class="text-2xl font-bold text-red-600 mt-1">{{ $stats['overdue'] }}</p>
        </div>
    </div>

    {{-- فلتر الحالة --}}
    <div class="card p-3 flex flex-wrap items-center gap-2">
        <a href="{{ route('employee.tasks.index') }}"
           class="px-3 py-1.5 rounded-lg text-sm {{ !$filterStatus ? 'bg-purple-100 text-purple-700 font-medium' : 'text-gray-600 hover:bg-gray-100' }}">
            الكل
        </a>
        @foreach($statuses as $key => $label)
            <a href="{{ route('employee.tasks.index', ['status' => $key]) }}"
               class="px-3 py-1.5 rounded-lg text-sm {{ $filterStatus === $key ? 'bg-purple-100 text-purple-700 font-medium' : 'text-gray-600 hover:bg-gray-100' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    {{-- مهام مساحة العمل --}}
    <div>
        <h3 class="font-bold text-gray-800 mb-3 flex items-center gap-2">
            <span class="material-icons text-purple-600">dashboard_customize</span>
            مهام مساحة العمل
            <span class="px-2 py-0.5 text-xs rounded-full bg-purple-100 text-purple-700">{{ $workTasks->count() }}</span>
        </h3>

        @forelse($workTasks as $wt)
            <a href="{{ route('employee.work.show', $wt) }}"
               class="card p-4 mb-3 flex items-center justify-between gap-3 hover:shadow-md transition-shadow {{ $wt->is_overdue ? 'border-r-4 border-red-400' : '' }}">
                <div class="min-w-0 flex-1">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="px-2 py-0.5 text-xs rounded-full {{ $kindColors[$wt->kind] ?? $kindColors['other'] }}">{{ $wt->kind_label }}</span>
                        <span class="px-2 py-0.5 text-xs rounded-full {{ $stColors[$wt->status] ?? $stColors['todo'] }}">{{ $wt->status_label }}</span>
                        @if($wt->is_overdue)
                            <span class="px-2 py-0.5 text-xs rounded-full bg-red-100 text-red-700">متأخرة</span>
                        @endif
                    </div>
                    <p class="font-semibold text-gray-800 mt-2">{{ $wt->title }}</p>
                    <p class="text-xs text-gray-500 mt-0.5 truncate">{{ $wt->activity->title ?? '—' }} · {{ $wt->activity->type_label ?? '' }}</p>
                    @if($wt->idea)
                        <p class="text-sm text-gray-500 mt-1 line-clamp-1">{{ $wt->idea }}</p>
                    @endif
                </div>
                <div class="text-left shrink-0 flex flex-col items-end gap-1">
                    @if($wt->due_date)
                        <span class="text-xs text-gray-500 flex items-center gap-1">
                            <span class="material-icons text-sm">event</span>
                            {{ $wt->due_date->format('Y/m/d') }}
                        </span>
                    @endif
                    <span class="material-icons text-gray-400">chevron_left</span>
                </div>
            </a>
        @empty
            <div class="card p-8 text-center">
                <span class="material-icons text-5xl text-gray-300">task_alt</span>
                <p class="text-gray-500 text-sm mt-3">
                    @if($filterStatus)
                        لا توجد مهام بهذه الحالة
                    @else
                        لا توجد مهام معيّنة لك من مساحة العمل حاليًا
                    @endif
                </p>
            </div>
        @endforelse
    </div>

    {{-- مهام الخطط الشهرية (لو موجودة) --}}
    @if($planTasks->count() > 0)
    <div>
        <h3 class="font-bold text-gray-800 mb-3 flex items-center gap-2">
            <span class="material-icons text-indigo-600">calendar_month</span>
            مهام الخطط الشهرية
            <span class="px-2 py-0.5 text-xs rounded-full bg-indigo-100 text-indigo-700">{{ $planTasks->count() }}</span>
        </h3>

        @foreach($planTasks as $pt)
            <a href="{{ route('employee.tasks.show', $pt) }}"
               class="card p-4 mb-3 flex items-center justify-between gap-3 hover:shadow-md transition-shadow">
                <div class="min-w-0 flex-1">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="px-2 py-0.5 text-xs rounded-full {{ $stColors[$pt->status] ?? $stColors['todo'] }}">
                            {{ $pt->status_badge ?? $pt->status }}
                        </span>
                    </div>
                    <p class="font-semibold text-gray-800 mt-2">{{ $pt->title }}</p>
                    <p class="text-xs text-gray-500 mt-0.5 truncate">
                        {{ $pt->monthlyPlan->project->business_name ?? '—' }}
                        @if($pt->monthlyPlan)
                            · {{ $pt->monthlyPlan->month }} {{ $pt->monthlyPlan->year }}
                        @endif
                    </p>
                </div>
                <span class="material-icons text-gray-400 shrink-0">chevron_left</span>
            </a>
        @endforeach
    </div>
    @endif
</div>
@endsection
