@extends('layouts.public')

@section('title', $task->title)

@section('content')
<div class="max-w-3xl mx-auto space-y-4">
    <a href="{{ route('public.work.show', $shareToken) }}" class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-indigo-600">
        <span class="material-icons text-lg">arrow_forward</span>
        رجوع للمحتوى
    </a>

    <div class="bg-white rounded-2xl border border-gray-200 p-6">
        <div class="flex flex-wrap gap-2 mb-3">
            <span class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-gray-100 text-gray-700">{{ $task->pipeline_stage_label }}</span>
            @if($task->content_type_label)
                <span class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-indigo-50 text-indigo-700">{{ $task->content_type_label }}</span>
            @endif
        </div>
        <h1 class="text-2xl font-extrabold text-gray-900">{{ $task->title }}</h1>
        <p class="text-sm text-gray-500 mt-1">{{ $activity->title }}</p>

        @if(!empty($task->platform_labels) || $task->publish_date)
            <div class="flex flex-wrap gap-2 mt-4 pt-4 border-t border-gray-100 text-xs text-gray-600">
                @foreach($task->platform_labels as $plat)
                    <span class="px-2 py-1 rounded-lg bg-gray-100">{{ $plat }}</span>
                @endforeach
                @if($task->publish_date)
                    <span class="flex items-center gap-1"><span class="material-icons text-sm">campaign</span>نشر {{ $task->publish_date->format('Y/m/d') }}</span>
                @endif
            </div>
        @endif
    </div>

    <div class="rounded-2xl border-2 border-violet-200 bg-violet-50/70 p-5">
        <h2 class="text-lg font-bold text-violet-900 mb-2 flex items-center gap-2">
            <span class="material-icons">record_voice_over</span>
            TOV
        </h2>
        @if($task->tov)
            <p class="text-base leading-8 text-violet-950 whitespace-pre-line font-medium">{{ $task->tov }}</p>
        @else
            <p class="text-sm text-violet-400">غير محدد</p>
        @endif
    </div>

    <div class="rounded-2xl border-2 border-sky-200 bg-sky-50/70 p-5">
        <h2 class="text-lg font-bold text-sky-900 mb-2 flex items-center gap-2">
            <span class="material-icons">notes</span>
            Caption
        </h2>
        @if($task->caption)
            <p class="text-base leading-8 text-sky-950 whitespace-pre-line font-medium">{{ $task->caption }}</p>
        @else
            <p class="text-sm text-sky-400">غير محدد</p>
        @endif
    </div>

    @if($task->idea)
        <div class="bg-white rounded-2xl border border-gray-200 p-5">
            <h2 class="text-sm font-bold text-gray-700 mb-2">الفكرة</h2>
            <p class="text-sm text-gray-700 whitespace-pre-line leading-7">{{ $task->idea }}</p>
        </div>
    @endif

    @if($task->design_reference || $task->designer_brief)
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @if($task->design_reference)
                <div class="bg-white rounded-2xl border border-gray-200 p-5">
                    <h2 class="text-sm font-bold text-gray-700 mb-2">مرجع التصميم</h2>
                    <p class="text-sm text-gray-700 whitespace-pre-line leading-7">{{ $task->design_reference }}</p>
                </div>
            @endif
            @if($task->designer_brief)
                <div class="rounded-2xl border border-amber-200 bg-amber-50 p-5">
                    <h2 class="text-sm font-bold text-amber-800 mb-2">ملخص للمصمم</h2>
                    <p class="text-sm text-amber-950 whitespace-pre-line leading-7">{{ $task->designer_brief }}</p>
                </div>
            @endif
        </div>
    @endif
</div>
@endsection
