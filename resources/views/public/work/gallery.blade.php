@extends('layouts.public')

@section('title', 'معرض التصميم — '.$activity->title)

@section('header-actions')
<button type="button"
        data-share-url="{{ $galleryUrl }}"
        onclick="window.copyShareText && window.copyShareText(this.dataset.shareUrl, this)"
        class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl bg-slate-900 text-white text-xs font-bold hover:bg-slate-800 transition-colors">
    <span class="material-icons text-sm">content_copy</span>
    نسخ الرابط
</button>
@endsection

@section('content')
<div class="space-y-5 md:space-y-6">
    <div class="flex items-center justify-between gap-3 flex-wrap">
        <a href="{{ route('public.work.show', $shareToken) }}"
           class="inline-flex items-center gap-1 text-sm text-slate-500 hover:text-teal-700 transition-colors">
            <span class="material-icons text-lg">arrow_forward</span>
            رجوع لمحتوى الحملة
        </a>
        <p class="text-xs text-slate-500">
            {{ $imageCount }} صورة
            @if($videoCount > 0)
                · {{ $videoCount }} فيديو
            @endif
        </p>
    </div>

    <section class="share-panel rounded-3xl overflow-hidden">
        <div class="bg-gradient-to-l from-slate-900 via-slate-800 to-teal-900 px-5 py-6 md:px-7 md:py-8 text-white">
            <p class="text-xs font-bold text-teal-200/90 mb-2 inline-flex items-center gap-1">
                <span class="material-icons text-sm">photo_library</span>
                معرض تصميم الحملة
            </p>
            <h1 class="text-2xl md:text-4xl font-extrabold leading-tight tracking-tight">{{ $activity->title }}</h1>
            <p class="text-sm md:text-base text-slate-300 mt-2">كل صور وفيديوهات التصميم في مكان واحد</p>
        </div>
    </section>

    @if($items->isEmpty())
        <section class="share-panel rounded-3xl p-10 text-center">
            <span class="material-icons text-5xl text-slate-300">image_not_supported</span>
            <h2 class="text-lg font-bold text-slate-700 mt-3">مفيش ملفات تصميم لسه</h2>
            <p class="text-sm text-slate-500 mt-1">لما المصمم يرفع الصور هتظهر هنا تلقائيًا</p>
        </section>
    @else
        <section class="share-panel rounded-3xl p-4 md:p-6">
            <div class="grid grid-cols-2 md:grid-cols-3 gap-3 md:gap-4">
                @foreach($items as $item)
                    @php
                        $file = $item['file'];
                        $task = $item['task'];
                        $fileUrl = route('public.work.file', [$shareToken, $task, $file]);
                        $downloadUrl = route('public.work.file', [$shareToken, $task, $file, 'download' => 1]);
                        $taskUrl = route('public.work.task', [$shareToken, $task]);
                    @endphp
                    <article class="file-tile rounded-2xl overflow-hidden border border-slate-200 bg-white flex flex-col">
                        @if($file->isImage())
                            <a href="{{ $fileUrl }}" target="_blank" class="block aspect-square bg-slate-100 relative group">
                                <img src="{{ $fileUrl }}" alt="{{ $file->file_name }}"
                                     class="w-full h-full object-cover"
                                     loading="lazy">
                                <span class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition-colors flex items-center justify-center">
                                    <span class="opacity-0 group-hover:opacity-100 material-icons text-white text-3xl drop-shadow">zoom_in</span>
                                </span>
                            </a>
                        @else
                            <a href="{{ $fileUrl }}" target="_blank" class="block aspect-square bg-slate-900 relative">
                                <video src="{{ $fileUrl }}" class="w-full h-full object-cover" muted playsinline preload="metadata"></video>
                                <span class="absolute inset-0 flex items-center justify-center bg-black/25">
                                    <span class="material-icons text-white text-4xl">play_circle</span>
                                </span>
                            </a>
                        @endif
                        <div class="p-3 space-y-2">
                            <a href="{{ $taskUrl }}" class="block text-sm font-bold text-slate-800 leading-snug line-clamp-2 hover:text-teal-700">
                                {{ $task->title }}
                            </a>
                            <div class="flex items-center justify-between gap-2">
                                @if($task->content_type_label)
                                    <span class="text-[10px] font-semibold px-2 py-0.5 rounded-md bg-teal-50 text-teal-700">{{ $task->content_type_label }}</span>
                                @else
                                    <span class="text-[10px] text-slate-400">{{ $file->asset_kind_label }}</span>
                                @endif
                                <a href="{{ $downloadUrl }}"
                                   class="inline-flex items-center gap-0.5 text-[11px] font-semibold text-slate-500 hover:text-slate-800">
                                    <span class="material-icons text-sm">download</span>
                                    تحميل
                                </a>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        </section>
    @endif
</div>
@endsection
