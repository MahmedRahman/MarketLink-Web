@extends('layouts.public')

@section('title', $activity->title)

@section('content')
<div class="space-y-5">
    <div class="bg-white rounded-2xl border border-gray-200 p-6">
        <div class="flex items-start gap-4">
            <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
                <span class="material-icons text-2xl">{{ $activity->type_icon }}</span>
            </div>
            <div class="min-w-0">
                <h1 class="text-2xl font-extrabold text-gray-900">{{ $activity->title }}</h1>
                <p class="text-sm text-gray-500 mt-1">
                    {{ $activity->type_label }}
                    @if($activity->event_date)
                        · {{ $activity->event_date->format('Y/m/d') }}
                    @endif
                    · {{ $contentCounts['total'] }} محتوى
                </p>
                <div class="flex flex-wrap gap-2 mt-3">
                    <span class="px-2.5 py-1 rounded-lg bg-blue-50 text-blue-700 text-xs font-semibold">بوست {{ $contentCounts['post'] }}</span>
                    <span class="px-2.5 py-1 rounded-lg bg-rose-50 text-rose-700 text-xs font-semibold">ريلز {{ $contentCounts['reels'] }}</span>
                    <span class="px-2.5 py-1 rounded-lg bg-amber-50 text-amber-700 text-xs font-semibold">كروسيل {{ $contentCounts['carousel'] }}</span>
                </div>
            </div>
        </div>
        @if($activity->description)
            <p class="text-sm text-gray-600 mt-4 whitespace-pre-line leading-7">{{ $activity->description }}</p>
        @endif
    </div>

    @foreach($pipelineStages as $stage)
        <section class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between
                {{ $stage['key'] === 'writing' ? 'bg-blue-50' : ($stage['key'] === 'design' ? 'bg-purple-50' : 'bg-teal-50') }}">
                <div class="flex items-center gap-2">
                    <span class="material-icons text-gray-600">{{ $stage['icon'] }}</span>
                    <h2 class="font-bold text-gray-800">{{ $stage['label'] }}</h2>
                </div>
                <span class="text-xs font-bold text-gray-500">{{ $stage['count'] }}</span>
            </div>
            <div class="p-4">
                @if($stage['tasks']->count())
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                        @foreach($stage['tasks'] as $task)
                            <a href="{{ route('public.work.task', [$shareToken, $task]) }}"
                               class="group rounded-2xl border border-gray-200 p-4 min-h-[100px] hover:border-indigo-300 hover:shadow-sm transition-all flex flex-col justify-between">
                                <div>
                                    @if($task->content_type_label)
                                        <span class="inline-block px-2 py-0.5 text-[10px] rounded-md bg-indigo-50 text-indigo-700 mb-2">{{ $task->content_type_label }}</span>
                                    @endif
                                    <h3 class="font-bold text-gray-900 leading-snug line-clamp-3 group-hover:text-indigo-700">{{ $task->title }}</h3>
                                </div>
                                <span class="mt-3 text-[11px] text-indigo-600 inline-flex items-center gap-0.5 opacity-70 group-hover:opacity-100">
                                    عرض التفاصيل
                                    <span class="material-icons text-sm">chevron_left</span>
                                </span>
                            </a>
                        @endforeach
                    </div>
                @else
                    <p class="text-center text-sm text-gray-400 py-6">لا يوجد محتوى في هذه المرحلة</p>
                @endif
            </div>
        </section>
    @endforeach
</div>
@endsection
