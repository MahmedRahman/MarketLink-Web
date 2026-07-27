@extends('layouts.public')

@section('title', $activity->title)

@section('content')
<div class="space-y-5 md:space-y-6">
    <section class="share-panel rounded-3xl overflow-hidden">
        <div class="bg-gradient-to-l from-slate-900 via-slate-800 to-teal-900 px-5 py-7 md:px-8 md:py-9 text-white">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 rounded-2xl bg-white/10 text-teal-200 flex items-center justify-center shrink-0">
                    <span class="material-icons text-2xl">{{ $activity->type_icon }}</span>
                </div>
                <div class="min-w-0">
                    <p class="text-xs text-teal-200/90 font-semibold mb-1">{{ $activity->type_label }}</p>
                    <h1 class="text-2xl md:text-4xl font-extrabold leading-tight">{{ $activity->title }}</h1>
                    <p class="text-sm text-slate-300 mt-2">
                        @if($activity->event_date)
                            {{ $activity->event_date->format('Y/m/d') }} ·
                        @endif
                        {{ $contentCounts['total'] }} محتوى
                    </p>
                </div>
            </div>
            <div class="flex flex-wrap gap-2 mt-5">
                <span class="px-2.5 py-1 rounded-lg bg-white/10 text-xs font-semibold">بوست {{ $contentCounts['post'] }}</span>
                <span class="px-2.5 py-1 rounded-lg bg-white/10 text-xs font-semibold">ريلز {{ $contentCounts['reels'] }}</span>
                <span class="px-2.5 py-1 rounded-lg bg-white/10 text-xs font-semibold">كروسيل {{ $contentCounts['carousel'] }}</span>
            </div>
        </div>
        @if($activity->description)
            <div class="px-5 py-4 md:px-8 text-sm text-slate-600 leading-7 whitespace-pre-line">{{ $activity->description }}</div>
        @endif
    </section>

    @foreach($pipelineStages as $stage)
        <section class="share-panel rounded-3xl overflow-hidden">
            <div class="px-4 py-3.5 border-b border-slate-100 flex items-center justify-between gap-3
                {{ $stage['key'] === 'writing' ? 'bg-sky-50' : ($stage['key'] === 'design' ? 'bg-violet-50' : ($stage['key'] === 'ready_to_publish' ? 'bg-teal-50' : 'bg-emerald-50')) }}">
                <div class="flex items-center gap-2.5">
                    <span class="material-icons text-slate-600">{{ $stage['icon'] }}</span>
                    <h2 class="font-extrabold text-slate-900">{{ $stage['label'] }}</h2>
                </div>
                <span class="text-xs font-bold text-slate-500 bg-white/80 px-2.5 py-1 rounded-lg">{{ $stage['count'] }}</span>
            </div>
            <div class="p-4">
                @if($stage['tasks']->count())
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                        @foreach($stage['tasks'] as $task)
                            <a href="{{ route('public.work.task', [$shareToken, $task]) }}"
                               class="group rounded-2xl border border-slate-200 bg-white p-4 min-h-[110px] hover:border-teal-300 hover:shadow-md transition-all flex flex-col justify-between">
                                <div>
                                    @if($task->content_type_label)
                                        <span class="inline-block px-2 py-0.5 text-[10px] rounded-md bg-teal-50 text-teal-700 mb-2 font-semibold">{{ $task->content_type_label }}</span>
                                    @endif
                                    <h3 class="font-bold text-slate-900 leading-snug line-clamp-3 group-hover:text-teal-700">{{ $task->title }}</h3>
                                </div>
                                <span class="mt-3 text-[11px] text-teal-700 font-semibold inline-flex items-center gap-0.5 opacity-80 group-hover:opacity-100">
                                    عرض التفاصيل
                                    <span class="material-icons text-sm">chevron_left</span>
                                </span>
                            </a>
                        @endforeach
                    </div>
                @else
                    <p class="text-center text-sm text-slate-400 py-8">لا يوجد محتوى في هذه المرحلة</p>
                @endif
            </div>
        </section>
    @endforeach
</div>
@endsection
