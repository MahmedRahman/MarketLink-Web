@extends('layouts.employee')

@section('title', $activity->title)
@section('page-title', 'مساحة العمل')
@section('page-description', $activity->title)

@section('content')
<div class="max-w-6xl mx-auto space-y-6">

    <a href="{{ route('employee.tasks.index') }}" class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-primary">
        <span class="material-icons text-lg">arrow_forward</span>
        رجوع لمساحة العمل
    </a>

    {{-- رأس النشاط --}}
    <div class="card rounded-2xl p-6">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-indigo-50 text-primary flex items-center justify-center">
                <span class="material-icons text-3xl">{{ $activity->type_icon }}</span>
            </div>
            <div>
                <h2 class="text-xl font-bold text-gray-800">{{ $activity->title }}</h2>
                <div class="flex items-center gap-2 mt-1 text-sm text-gray-500 flex-wrap">
                    <span>{{ $activity->type_label }}</span>
                    @if($activity->event_date)
                        <span>·</span>
                        <span class="flex items-center gap-1"><span class="material-icons text-sm">event</span>{{ $activity->event_date->format('Y/m/d') }}</span>
                    @endif
                    <span>·</span>
                    <span class="role-badge role-{{ $activity->status_color }}">{{ $activity->status_label }}</span>
                </div>
            </div>
        </div>

        @if($activity->description)
            <p class="text-sm text-gray-600 mt-4 bg-gray-50 rounded-xl p-3 whitespace-pre-line">{{ $activity->description }}</p>
        @endif

        <div class="mt-4">
            <div class="flex items-center justify-between text-xs text-gray-500 mb-1">
                <span>تقدّم مهامك ({{ $doneCount }} / {{ $contentCounts['total'] }})</span>
                <span>{{ $progress }}%</span>
            </div>
            <div class="w-full h-2.5 bg-gray-100 rounded-full overflow-hidden">
                <div class="h-full bg-gradient-to-l from-indigo-500 to-purple-500 rounded-full" style="width: {{ $progress }}%"></div>
            </div>
        </div>
    </div>

    <div class="space-y-4">
        <div>
            <h3 class="font-bold text-gray-800">مهامك في النشاط ({{ $contentCounts['total'] }})</h3>
            <p class="text-xs text-gray-500 mt-1">بتظهر هنا بس التاسكات اللي مطلوب منك تشتغل عليها</p>
            <div class="flex flex-wrap items-center gap-2 mt-2">
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-blue-50 text-blue-700 text-xs font-semibold border border-blue-100">
                    <span class="material-icons text-sm">article</span>
                    بوست {{ $contentCounts['post'] }}
                </span>
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-rose-50 text-rose-700 text-xs font-semibold border border-rose-100">
                    <span class="material-icons text-sm">movie</span>
                    ريلز {{ $contentCounts['reels'] }}
                </span>
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-amber-50 text-amber-700 text-xs font-semibold border border-amber-100">
                    <span class="material-icons text-sm">view_carousel</span>
                    كروسيل {{ $contentCounts['carousel'] }}
                </span>
                @if($contentCounts['other'] > 0)
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-gray-50 text-gray-600 text-xs font-semibold border border-gray-200">
                        أخرى {{ $contentCounts['other'] }}
                    </span>
                @endif
            </div>
        </div>

        <div class="space-y-4">
            @foreach($pipelineStages as $stage)
                @if($stage['count'] === 0 && ! in_array($stage['key'], ['ready_to_publish'], true))
                    @continue
                @endif
                <section class="card rounded-2xl overflow-hidden">
                    <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between gap-3
                        @if($stage['key'] === 'writing') bg-blue-50
                        @elseif($stage['key'] === 'design') bg-purple-50
                        @elseif($stage['key'] === 'ready_to_publish') bg-teal-50
                        @else bg-green-50
                        @endif">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center
                                @if($stage['key'] === 'writing') bg-blue-100 text-blue-700
                                @elseif($stage['key'] === 'design') bg-purple-100 text-purple-700
                                @elseif($stage['key'] === 'ready_to_publish') bg-teal-100 text-teal-700
                                @else bg-green-100 text-green-700
                                @endif">
                                <span class="material-icons">{{ $stage['icon'] }}</span>
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-800">{{ $stage['label'] }}</h4>
                                <p class="text-xs text-gray-500">
                                    @if($stage['key'] === 'ready_to_publish')
                                        بعد اكتمال التصميم البوست بيظهر هنا جاهز للنشر
                                    @else
                                        {{ $stage['count'] }} مهمة لك
                                    @endif
                                </p>
                            </div>
                        </div>
                        <span class="px-2.5 py-1 rounded-lg text-xs font-bold
                            @if($stage['key'] === 'writing') bg-blue-100 text-blue-700
                            @elseif($stage['key'] === 'design') bg-purple-100 text-purple-700
                            @elseif($stage['key'] === 'ready_to_publish') bg-teal-100 text-teal-700
                            @else bg-green-100 text-green-700
                            @endif">
                            {{ $stage['count'] }}
                        </span>
                    </div>

                    <div class="p-4">
                        @if($stage['count'] === 0)
                            <div class="rounded-xl border border-dashed border-teal-200 bg-teal-50/40 px-4 py-6 text-center">
                                <span class="material-icons text-2xl text-teal-400 mb-1">schedule_send</span>
                                <p class="text-sm text-teal-800 font-medium">مفيش بوستات جاهزة للنشر لسه</p>
                                <p class="text-xs text-teal-600 mt-1">لما تضغط «اكتمال» على التصميم، البوست هيظهر هنا</p>
                            </div>
                        @else
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                                @foreach($stage['tasks'] as $task)
                                    @php
                                        $stColors = [
                                            'todo' => 'bg-gray-100 text-gray-700',
                                            'in_progress' => 'bg-blue-100 text-blue-700',
                                            'review' => 'bg-yellow-100 text-yellow-700',
                                            'done' => 'bg-green-100 text-green-700',
                                        ];
                                    @endphp
                                    <a href="{{ route('employee.work.show', $task) }}"
                                       class="rounded-2xl border border-gray-200 bg-white p-4 min-h-[110px] flex flex-col justify-between hover:border-indigo-300 hover:shadow-md transition-all {{ $task->is_overdue ? 'border-r-4 border-r-red-400' : '' }}">
                                        <div>
                                            @if($task->content_type_label)
                                                <span class="inline-block px-2 py-0.5 text-[10px] rounded-md bg-indigo-50 text-indigo-700 mb-2">{{ $task->content_type_label }}</span>
                                            @endif
                                            <h5 class="text-base font-bold text-gray-900 leading-snug line-clamp-3">
                                                {{ $task->title }}
                                            </h5>
                                        </div>
                                        <div class="mt-3 flex items-center justify-between gap-2">
                                            <span class="px-2 py-0.5 text-[11px] rounded-lg {{ $stColors[$task->status] ?? $stColors['todo'] }}">
                                                {{ $task->status_label }}
                                            </span>
                                            @if($task->due_date)
                                                <span class="text-[11px] text-gray-500 flex items-center gap-0.5">
                                                    <span class="material-icons text-sm">event</span>
                                                    {{ $task->due_date->format('m/d') }}
                                                </span>
                                            @endif
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </section>
            @endforeach
        </div>
    </div>
</div>
@endsection
