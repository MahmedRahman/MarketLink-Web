@extends('layouts.public')

@section('title', $task->title)

@section('header-actions')
<button type="button"
        data-share-url="{{ $cardShareUrl }}"
        onclick="window.copyShareText && window.copyShareText(this.dataset.shareUrl, this)"
        class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl bg-slate-900 text-white text-xs font-bold hover:bg-slate-800 transition-colors">
    <span class="material-icons text-sm">content_copy</span>
    نسخ الرابط
</button>
@endsection

@section('content')
@php
    $imageFiles = $task->files->filter(fn ($f) => $f->isImage())->values();
    $otherFiles = $task->files->reject(fn ($f) => $f->isImage())->values();
@endphp

<div class="space-y-5 md:space-y-6">
    <div class="flex items-center justify-between gap-3 flex-wrap">
        <a href="{{ route('public.work.show', $shareToken) }}"
           class="inline-flex items-center gap-1 text-sm text-slate-500 hover:text-teal-700 transition-colors">
            <span class="material-icons text-lg">arrow_forward</span>
            رجوع لكل محتوى «{{ $activity->title }}»
        </a>
        @if($task->files->count())
            <a href="#files"
               class="inline-flex items-center gap-1 text-sm font-semibold text-teal-700 hover:text-teal-800">
                <span class="material-icons text-base">photo_library</span>
                الملفات ({{ $task->files->count() }})
            </a>
        @endif
    </div>

    {{-- رأس الكارت --}}
    <section class="share-panel rounded-3xl overflow-hidden">
        <div class="bg-gradient-to-l from-slate-900 via-slate-800 to-teal-900 px-5 py-6 md:px-7 md:py-8 text-white">
            <div class="flex flex-wrap gap-2 mb-3">
                <span class="px-2.5 py-1 rounded-lg text-[11px] font-bold bg-white/15 backdrop-blur">{{ $task->pipeline_stage_label }}</span>
                @if($task->content_type_label)
                    <span class="px-2.5 py-1 rounded-lg text-[11px] font-bold bg-teal-400/25 text-teal-50">{{ $task->content_type_label }}</span>
                @endif
            </div>
            <h1 class="text-2xl md:text-4xl font-extrabold leading-tight tracking-tight">{{ $task->title }}</h1>
            <p class="text-sm md:text-base text-slate-300 mt-2">{{ $activity->title }}</p>

            @if(!empty($task->platform_labels) || $task->publish_date)
                <div class="flex flex-wrap gap-2 mt-5">
                    @foreach($task->platform_labels as $plat)
                        <span class="px-2.5 py-1 rounded-lg bg-white/10 text-xs text-slate-100">{{ $plat }}</span>
                    @endforeach
                    @if($task->publish_date)
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-white/10 text-xs text-slate-100">
                            <span class="material-icons text-sm">campaign</span>
                            نشر {{ $task->publish_date->format('Y/m/d') }}
                        </span>
                    @endif
                </div>
            @endif
        </div>
    </section>

    {{-- ملفات التصميم أولاً لأنها الغرض من الشير غالبًا --}}
    @if($task->files->count())
        <section id="files" class="share-panel rounded-3xl p-5 md:p-6 space-y-5">
            <div class="flex items-start justify-between gap-3 flex-wrap">
                <div>
                    <h2 class="text-lg md:text-xl font-extrabold text-slate-900 flex items-center gap-2">
                        <span class="w-9 h-9 rounded-xl bg-teal-50 text-teal-700 inline-flex items-center justify-center">
                            <span class="material-icons">photo_library</span>
                        </span>
                        ملفات التصميم
                    </h2>
                    <p class="text-sm text-slate-500 mt-1">{{ $task->files->count() }} ملفات · اضغط الصورة للعرض أو حَمّلها</p>
                </div>
                <div class="flex items-center gap-2 flex-wrap">
                    <a href="{{ route('public.work.files.download-all', [$shareToken, $task]) }}"
                       class="inline-flex items-center gap-1.5 px-3.5 py-2.5 rounded-xl bg-slate-900 text-white text-xs font-bold hover:bg-slate-800 transition-colors">
                        <span class="material-icons text-sm">download</span>
                        تحميل الكل (ZIP)
                    </a>
                    <button type="button"
                            data-share-url="{{ $cardShareUrl }}#files"
                            onclick="window.copyShareText && window.copyShareText(this.dataset.shareUrl, this)"
                            class="inline-flex items-center gap-1.5 px-3.5 py-2.5 rounded-xl border border-teal-200 bg-teal-50 text-teal-800 text-xs font-bold hover:bg-teal-100 transition-colors">
                        <span class="material-icons text-sm">ios_share</span>
                        نسخ الرابط
                    </button>
                </div>
            </div>

            @if($imageFiles->count())
                <div class="grid grid-cols-2 md:grid-cols-3 gap-3 md:gap-4">
                    @foreach($imageFiles as $file)
                        @php
                            $fileUrl = route('public.work.file', [$shareToken, $task, $file]);
                            $downloadUrl = route('public.work.file', [$shareToken, $task, $file, 'download' => 1]);
                        @endphp
                        <div class="file-tile group relative overflow-hidden rounded-2xl bg-slate-100 aspect-square border border-slate-200/80">
                            <a href="{{ $fileUrl }}" target="_blank" rel="noopener" class="absolute inset-0 block">
                                <img src="{{ $fileUrl }}" alt="{{ $file->file_name }}"
                                     class="absolute inset-0 w-full h-full object-cover">
                            </a>
                            <div class="absolute inset-x-0 bottom-0 p-2.5 bg-gradient-to-t from-slate-950/80 via-slate-950/35 to-transparent flex items-end justify-between gap-2 pointer-events-none">
                                <div class="min-w-0 hidden sm:block">
                                    <p class="text-[11px] text-white font-semibold truncate">{{ $file->file_name }}</p>
                                    <p class="text-[10px] text-slate-200">{{ $file->formatted_file_size }}</p>
                                </div>
                                <a href="{{ $downloadUrl }}"
                                   class="pointer-events-auto relative z-10 shrink-0 inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-white text-slate-900 text-[11px] font-bold shadow-md hover:bg-teal-50"
                                   title="تحميل {{ $file->file_name }}">
                                    <span class="material-icons text-sm">download</span>
                                    تحميل
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            @if($otherFiles->count())
                <div class="space-y-2 {{ $imageFiles->count() ? 'pt-2 border-t border-slate-100' : '' }}">
                    @foreach($otherFiles as $file)
                        @php
                            $fileUrl = route('public.work.file', [$shareToken, $task, $file]);
                            $downloadUrl = route('public.work.file', [$shareToken, $task, $file, 'download' => 1]);
                        @endphp
                        <div class="flex items-center gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-3.5 py-3">
                            <a href="{{ $fileUrl }}" target="_blank" rel="noopener"
                               class="w-11 h-11 rounded-xl bg-white border border-slate-200 text-slate-500 flex items-center justify-center shrink-0 hover:border-teal-300">
                                <span class="material-icons">{{ $file->file_icon }}</span>
                            </a>
                            <a href="{{ $fileUrl }}" target="_blank" rel="noopener" class="min-w-0 flex-1 hover:opacity-80">
                                <p class="text-sm font-bold text-slate-800 truncate">{{ $file->file_name }}</p>
                                <p class="text-[11px] text-slate-500">{{ $file->asset_kind_label }} · {{ $file->formatted_file_size }}</p>
                            </a>
                            <a href="{{ $downloadUrl }}"
                               class="inline-flex items-center gap-1 px-3 py-2 rounded-xl bg-slate-900 text-white text-xs font-bold hover:bg-slate-800">
                                <span class="material-icons text-sm">download</span>
                                تحميل
                            </a>
                        </div>
                    @endforeach
                </div>
            @endif
        </section>
    @endif

    {{-- المحتوى --}}
    <div class="grid grid-cols-1 gap-4">
        <section class="share-panel rounded-3xl p-5 md:p-6">
            <h2 class="text-xs font-bold uppercase tracking-wide text-slate-400 mb-2">Tone of Voice</h2>
            @if($task->tov)
                <p class="text-base md:text-lg leading-8 text-slate-800 whitespace-pre-line font-semibold">{{ $task->tov }}</p>
            @else
                <p class="text-sm text-slate-400">غير محدد</p>
            @endif
        </section>

        <section class="share-panel rounded-3xl p-5 md:p-6">
            <h2 class="text-xs font-bold uppercase tracking-wide text-slate-400 mb-2">Caption</h2>
            @if($task->caption)
                <p class="text-base md:text-lg leading-8 text-slate-800 whitespace-pre-line font-semibold">{{ $task->caption }}</p>
            @else
                <p class="text-sm text-slate-400">غير محدد</p>
            @endif
        </section>

        @if($task->idea)
            <section class="share-panel rounded-3xl p-5 md:p-6">
                <h2 class="text-xs font-bold uppercase tracking-wide text-slate-400 mb-2">الفكرة</h2>
                <p class="text-sm md:text-base leading-7 text-slate-700 whitespace-pre-line">{{ $task->idea }}</p>
            </section>
        @endif
    </div>

    @if($task->design_reference || $task->designer_brief)
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @if($task->design_reference)
                <section class="share-panel rounded-3xl p-5">
                    <h2 class="text-xs font-bold uppercase tracking-wide text-slate-400 mb-2">مرجع التصميم</h2>
                    <div class="text-sm text-slate-700 leading-7">{!! linkify_text($task->design_reference) !!}</div>
                </section>
            @endif
            @if($task->designer_brief)
                <section class="share-panel rounded-3xl p-5 border border-amber-100 bg-amber-50/60">
                    <h2 class="text-xs font-bold uppercase tracking-wide text-amber-700/70 mb-2">ملخص للمصمم</h2>
                    <p class="text-sm text-amber-950 whitespace-pre-line leading-7">{{ $task->designer_brief }}</p>
                </section>
            @endif
        </div>
    @endif

    @if(!empty($task->platforms))
        <section class="share-panel rounded-3xl p-5 md:p-6 space-y-3">
            <h2 class="text-sm font-extrabold text-slate-900 flex items-center gap-2">
                <span class="material-icons text-teal-600">link</span>
                روابط النشر
            </h2>
            <div class="space-y-2">
                @foreach($task->platforms as $plat)
                    @php
                        $platLabel = \App\Models\WorkTask::platforms()[$plat] ?? $plat;
                        $link = $task->publishLinkFor($plat);
                    @endphp
                    <div class="flex items-center justify-between gap-3 rounded-2xl bg-slate-50 border border-slate-100 px-3.5 py-3">
                        <span class="text-sm font-semibold text-slate-800">{{ $platLabel }}</span>
                        @if($link)
                            <a href="{{ $link }}" target="_blank" rel="noopener"
                               class="text-xs text-teal-700 font-semibold hover:underline truncate max-w-[60%]" dir="ltr">{{ $link }}</a>
                        @else
                            <span class="text-xs text-slate-400">لم يُضف بعد</span>
                        @endif
                    </div>
                @endforeach
            </div>
        </section>
    @endif
</div>
@endsection
