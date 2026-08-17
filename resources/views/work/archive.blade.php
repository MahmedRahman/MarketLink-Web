@extends($workLayout ?? 'layouts.dashboard')

@section('title', 'الأرشيف')
@section('page-title', 'الأرشيف')
@section('page-description', 'الأنشطة والمهام المؤرشفة')

@section('content')
@php
    $employeeOnly = $employeeOnly ?? false;
    $activities = $activities ?? collect();
@endphp
<div class="max-w-6xl mx-auto space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h2 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                <span class="material-icons text-slate-600">inventory_2</span>
                الأرشيف
            </h2>
            <p class="text-sm text-gray-500 mt-1">
                {{ $activities->count() }} نشاط · {{ $tasks->count() }} تاسك
            </p>
        </div>
        @unless($employeeOnly)
            <a href="{{ work_route('index') }}" class="px-4 py-2 rounded-xl bg-gray-100 text-gray-700 text-sm font-medium hover:bg-gray-200 inline-flex items-center gap-1">
                <span class="material-icons text-base">dashboard_customize</span>
                مساحة العمل
            </a>
        @endunless
    </div>

    @if($activities->isNotEmpty())
        <section class="space-y-3">
            <h3 class="text-sm font-bold text-gray-700">الأنشطة المؤرشفة</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($activities as $activity)
                    <div class="card rounded-2xl p-5 space-y-3">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <h4 class="font-bold text-gray-800 truncate">{{ $activity->title }}</h4>
                                <p class="text-xs text-gray-500 mt-0.5">{{ $activity->type_label }} · {{ $activity->tasks_count }} مهمة</p>
                            </div>
                            <span class="role-badge role-gray shrink-0">أرشيف</span>
                        </div>
                        <div class="flex items-center gap-2 flex-wrap">
                            <a href="{{ work_route('show', $activity) }}"
                               class="px-3 py-1.5 rounded-xl bg-indigo-50 text-indigo-700 text-xs font-bold hover:bg-indigo-100">فتح</a>
                            <form method="POST" action="{{ work_route('unarchive-activity', $activity) }}">
                                @csrf
                                <button type="submit" class="px-3 py-1.5 rounded-xl bg-green-50 text-green-800 text-xs font-bold border border-green-100 hover:bg-green-100">
                                    إرجاع لمساحة العمل
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    @if($tasks->isEmpty() && $activities->isEmpty())
        <div class="card rounded-2xl p-12 text-center">
            <span class="material-icons text-6xl text-gray-300">inventory_2</span>
            <h3 class="text-lg font-bold text-gray-700 mt-4">الأرشيف فاضي</h3>
            <p class="text-gray-500 text-sm mt-1">من مساحة العمل اضغط «اذهب إلى الأرشيف» على كارت النشاط عشان ينتقل هنا</p>
        </div>
    @elseif($tasks->isNotEmpty())
        <div class="space-y-6">
            @foreach($groups as $activityId => $activityTasks)
                @php $activity = optional($activityTasks->first())->activity; @endphp
                <section class="card rounded-2xl overflow-hidden">
                    <div class="px-5 py-4 bg-gradient-to-l from-slate-50 to-white border-b border-slate-100 flex items-center justify-between gap-3">
                        <div class="min-w-0">
                            <h3 class="font-bold text-gray-800 truncate">{{ $activity?->title ?? 'نشاط محذوف' }}</h3>
                            <p class="text-xs text-gray-500 mt-0.5">{{ $activityTasks->count() }} تاسك</p>
                        </div>
                        @if($activity && ! $employeeOnly)
                            <a href="{{ work_route('show', [$activity, 'board' => 'archive']) }}"
                               class="text-xs font-semibold text-indigo-700 hover:underline shrink-0">فتح أرشيف النشاط</a>
                        @elseif($activity && $employeeOnly)
                            <a href="{{ route('employee.work.activity', [$activity, 'board' => 'archive']) }}"
                               class="text-xs font-semibold text-indigo-700 hover:underline shrink-0">فتح النشاط</a>
                        @endif
                    </div>
                    <div class="divide-y divide-slate-100">
                        @foreach($activityTasks as $task)
                            <article class="p-4 sm:p-5 flex flex-col sm:flex-row sm:items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <div class="flex flex-wrap items-center gap-2 mb-1.5">
                                        <span class="role-badge role-gray">أرشيف</span>
                                        @if($task->content_type_label)
                                            <span class="px-2 py-0.5 rounded-md bg-indigo-50 text-indigo-700 text-[11px] font-semibold">{{ $task->content_type_label }}</span>
                                        @endif
                                        @if($task->files->count())
                                            <span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-600 text-[11px]">{{ $task->files->count() }} ملف</span>
                                        @endif
                                    </div>
                                    <h4 class="font-bold text-gray-900 leading-snug">{{ $task->title }}</h4>
                                    <p class="text-xs text-gray-500 mt-1 flex flex-wrap items-center gap-x-3 gap-y-1">
                                        @if($task->designer)
                                            <span>تصميم: {{ $task->designer->name }}</span>
                                        @endif
                                        @if($task->assignedEmployee)
                                            <span>معيّن: {{ $task->assignedEmployee->name }}</span>
                                        @endif
                                        @if($task->publish_date)
                                            <span>نُشر {{ $task->publish_date->format('Y/m/d') }}</span>
                                        @endif
                                    </p>
                                </div>
                                <div class="flex items-center gap-2 flex-wrap shrink-0">
                                    @if($employeeOnly)
                                        <a href="{{ route('employee.work.show', $task) }}"
                                           class="px-3 py-2 rounded-xl bg-indigo-50 text-indigo-700 text-xs font-bold hover:bg-indigo-100">التفاصيل</a>
                                    @else
                                        @if($activity)
                                            <form method="POST" action="{{ work_route('tasks.move-stage', [$activity, $task]) }}">
                                                @csrf
                                                <input type="hidden" name="pipeline_stage" value="published">
                                                <button type="submit" class="px-3 py-2 rounded-xl bg-green-50 text-green-800 text-xs font-bold border border-green-100 hover:bg-green-100">
                                                    إرجاع لتم النشر
                                                </button>
                                            </form>
                                            <a href="{{ work_route('tasks.show', [$activity, $task]) }}"
                                               class="px-3 py-2 rounded-xl bg-indigo-50 text-indigo-700 text-xs font-bold hover:bg-indigo-100">التفاصيل</a>
                                        @endif
                                    @endif
                                </div>
                            </article>
                        @endforeach
                    </div>
                </section>
            @endforeach
        </div>
    @endif
</div>
@endsection
