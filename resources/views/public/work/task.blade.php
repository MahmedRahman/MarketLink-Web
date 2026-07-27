@extends('layouts.public')

@section('title', $task->title)

@section('content')
<div class="max-w-3xl mx-auto space-y-4">
    <a href="{{ route('public.work.show', $shareToken) }}" class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-indigo-600">
        <span class="material-icons text-lg">arrow_forward</span>
        رجوع للمحتوى
    </a>

    <div class="bg-white rounded-2xl border border-gray-200 p-6 space-y-4">
        <div class="flex flex-wrap gap-2">
            <span class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-gray-100 text-gray-700">{{ $task->pipeline_stage_label }}</span>
            @if($task->content_type_label)
                <span class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-indigo-50 text-indigo-700">{{ $task->content_type_label }}</span>
            @endif
        </div>
        <h1 class="text-2xl font-extrabold text-gray-900">{{ $task->title }}</h1>
        <p class="text-sm text-gray-500">{{ $activity->title }}</p>

        @include('partials.share-link', [
            'label' => 'رابط شير للكارت كامل',
            'hint' => 'انسخ وابعت لأي حد يشوف الكارت ده',
            'url' => $cardShareUrl,
            'inputId' => 'public-card-share',
        ])

        @if(!empty($task->platform_labels) || $task->publish_date)
            <div class="flex flex-wrap gap-2 pt-4 border-t border-gray-100 text-xs text-gray-600">
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
                    <div class="text-sm text-gray-700 leading-7">{!! linkify_text($task->design_reference) !!}</div>
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

    @if($task->files->count())
        <div id="files" class="bg-white rounded-2xl border border-gray-200 p-5 space-y-3">
            <h2 class="text-sm font-bold text-gray-800 flex items-center gap-1">
                <span class="material-icons text-base text-purple-600">folder</span>
                ملفات التصميم ({{ $task->files->count() }})
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                @foreach($task->files as $file)
                    @php
                        $fileShareUrl = route('public.work.file', [$shareToken, $task, $file]);
                    @endphp
                    <div class="rounded-xl border border-gray-200 bg-gray-50 p-3 space-y-2">
                        <div class="flex gap-3">
                            <div class="w-14 h-14 rounded-lg bg-white border border-gray-200 flex items-center justify-center overflow-hidden shrink-0">
                                @if($file->isImage())
                                    <img src="{{ $fileShareUrl }}" alt="{{ $file->file_name }}" class="w-full h-full object-cover">
                                @else
                                    <span class="material-icons text-2xl text-gray-400">{{ $file->file_icon }}</span>
                                @endif
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-medium text-gray-800 truncate">{{ $file->file_name }}</p>
                                <p class="text-[11px] text-gray-500 mt-0.5">{{ $file->asset_kind_label }} · {{ $file->formatted_file_size }}</p>
                                <a href="{{ $fileShareUrl }}" target="_blank" rel="noopener"
                                   class="text-xs text-indigo-600 hover:underline mt-1 inline-block">فتح الملف</a>
                            </div>
                        </div>
                        @include('partials.share-link', [
                            'label' => 'رابط شير للملف',
                            'url' => $fileShareUrl,
                            'inputId' => 'file-share-'.$file->id,
                        ])
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    @if(!empty($task->platforms))
        <div class="bg-white rounded-2xl border border-teal-100 p-5 space-y-3">
            <h2 class="text-sm font-bold text-teal-900 flex items-center gap-1">
                <span class="material-icons text-base">link</span>
                روابط النشر
            </h2>
            <div class="space-y-2">
                @foreach($task->platforms as $plat)
                    @php
                        $platLabel = \App\Models\WorkTask::platforms()[$plat] ?? $plat;
                        $link = $task->publishLinkFor($plat);
                    @endphp
                    <div class="flex items-center justify-between gap-3 rounded-xl bg-teal-50/60 border border-teal-100 px-3 py-2.5">
                        <span class="text-sm font-medium text-teal-900">{{ $platLabel }}</span>
                        @if($link)
                            <a href="{{ $link }}" target="_blank" rel="noopener" class="text-xs text-indigo-600 hover:underline truncate max-w-[60%]" dir="ltr">{{ $link }}</a>
                        @else
                            <span class="text-xs text-teal-500">لم يُضف رابط بعد</span>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection
