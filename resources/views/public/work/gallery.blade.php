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

@push('head')
<style>
    .carousel-strip {
        display: flex;
        gap: 0.5rem;
        overflow-x: auto;
        scroll-snap-type: x mandatory;
        -webkit-overflow-scrolling: touch;
        padding-bottom: 0.25rem;
    }
    .carousel-strip::-webkit-scrollbar { height: 6px; }
    .carousel-strip::-webkit-scrollbar-thumb {
        background: rgba(15, 23, 42, 0.2);
        border-radius: 999px;
    }
    .carousel-strip-item {
        scroll-snap-align: start;
        flex: 0 0 auto;
        width: min(42%, 11rem);
    }
    @media (min-width: 768px) {
        .carousel-strip-item { width: min(28%, 12rem); }
    }
    .carousel-lightbox {
        position: fixed;
        inset: 0;
        z-index: 80;
        background: rgba(15, 23, 42, 0.92);
        display: none;
        flex-direction: column;
    }
    .carousel-lightbox.is-open { display: flex; }
    .carousel-lightbox-track {
        display: flex;
        gap: 0.75rem;
        overflow-x: auto;
        scroll-snap-type: x mandatory;
        -webkit-overflow-scrolling: touch;
        padding: 1rem;
        flex: 1;
        align-items: center;
    }
    .carousel-lightbox-slide {
        scroll-snap-align: center;
        flex: 0 0 auto;
        height: min(78vh, 860px);
        max-width: min(86vw, 520px);
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .carousel-lightbox-slide img,
    .carousel-lightbox-slide video {
        max-height: 100%;
        max-width: 100%;
        width: auto;
        height: auto;
        object-fit: contain;
        border-radius: 1rem;
        background: #0f172a;
        box-shadow: 0 20px 50px rgba(0,0,0,.35);
    }
</style>
@endpush

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
                @foreach($items as $itemIndex => $item)
                    @php
                        $task = $item['task'];
                        $taskUrl = route('public.work.task', [$shareToken, $task]);
                    @endphp

                    @if(($item['type'] ?? 'single') === 'carousel')
                        @php
                            $files = $item['files'];
                            $slides = $files->map(function ($file) use ($shareToken, $task) {
                                return [
                                    'url' => route('public.work.file', [$shareToken, $task, $file]),
                                    'download' => route('public.work.file', [$shareToken, $task, $file, 'download' => 1]),
                                    'name' => $file->file_name,
                                    'kind' => $file->isVideo() ? 'video' : 'image',
                                ];
                            })->values();
                        @endphp
                        <article class="file-tile col-span-2 md:col-span-3 rounded-2xl overflow-hidden border border-slate-200 bg-white flex flex-col">
                            <button type="button"
                                    class="text-start w-full p-3 pb-2"
                                    data-carousel-open="{{ $itemIndex }}"
                                    data-carousel-slides='@json($slides)'
                                    data-carousel-title="{{ $task->title }}">
                                <div class="carousel-strip" dir="ltr">
                                    @foreach($files as $file)
                                        @php $fileUrl = route('public.work.file', [$shareToken, $task, $file]); @endphp
                                        <div class="carousel-strip-item aspect-square rounded-xl overflow-hidden bg-slate-100 relative border border-slate-200/80">
                                            @if($file->isImage())
                                                <img src="{{ $fileUrl }}" alt="{{ $file->file_name }}" class="w-full h-full object-cover" loading="lazy">
                                            @else
                                                <video src="{{ $fileUrl }}" class="w-full h-full object-cover" muted playsinline preload="metadata"></video>
                                                <span class="absolute inset-0 flex items-center justify-center bg-black/25">
                                                    <span class="material-icons text-white text-3xl">play_circle</span>
                                                </span>
                                            @endif
                                            <span class="absolute bottom-1.5 start-1.5 text-[10px] font-bold px-1.5 py-0.5 rounded-md bg-slate-900/70 text-white">
                                                {{ $loop->iteration }}/{{ $files->count() }}
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            </button>
                            <div class="px-3 pb-3 space-y-2">
                                <a href="{{ $taskUrl }}" class="block text-sm font-bold text-slate-800 leading-snug line-clamp-2 hover:text-teal-700">
                                    {{ $task->title }}
                                </a>
                                <div class="flex items-center justify-between gap-2">
                                    <span class="text-[10px] font-semibold px-2 py-0.5 rounded-md bg-orange-50 text-orange-700 inline-flex items-center gap-1">
                                        <span class="material-icons text-xs">view_carousel</span>
                                        كروسيل · {{ $files->count() }} شرائح
                                    </span>
                                    <button type="button"
                                            class="inline-flex items-center gap-0.5 text-[11px] font-semibold text-teal-700 hover:text-teal-900"
                                            data-carousel-open="{{ $itemIndex }}"
                                            data-carousel-slides='@json($slides)'
                                            data-carousel-title="{{ $task->title }}">
                                        <span class="material-icons text-sm">open_in_full</span>
                                        عرض الكل
                                    </button>
                                </div>
                            </div>
                        </article>
                    @else
                        @php
                            $file = $item['file'];
                            $fileUrl = route('public.work.file', [$shareToken, $task, $file]);
                            $downloadUrl = route('public.work.file', [$shareToken, $task, $file, 'download' => 1]);
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
                    @endif
                @endforeach
            </div>
        </section>
    @endif
</div>

{{-- Lightbox: كل شرائح الكروسيل جنب بعض --}}
<div id="carouselLightbox" class="carousel-lightbox" aria-hidden="true">
    <div class="flex items-center justify-between gap-3 px-4 py-3 text-white border-b border-white/10">
        <div class="min-w-0">
            <p id="carouselLightboxTitle" class="text-sm font-bold truncate"></p>
            <p id="carouselLightboxMeta" class="text-[11px] text-slate-300 mt-0.5"></p>
        </div>
        <button type="button" id="carouselLightboxClose"
                class="shrink-0 inline-flex items-center gap-1 px-3 py-2 rounded-xl bg-white/10 hover:bg-white/20 text-xs font-bold">
            <span class="material-icons text-sm">close</span>
            إغلاق
        </button>
    </div>
    <div id="carouselLightboxTrack" class="carousel-lightbox-track" dir="ltr"></div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    const lightbox = document.getElementById('carouselLightbox');
    const track = document.getElementById('carouselLightboxTrack');
    const titleEl = document.getElementById('carouselLightboxTitle');
    const metaEl = document.getElementById('carouselLightboxMeta');
    const closeBtn = document.getElementById('carouselLightboxClose');
    if (!lightbox || !track) return;

    function closeLightbox() {
        lightbox.classList.remove('is-open');
        lightbox.setAttribute('aria-hidden', 'true');
        track.innerHTML = '';
        document.body.style.overflow = '';
    }

    function openLightbox(slides, title) {
        track.innerHTML = '';
        titleEl.textContent = title || 'كروسيل';
        metaEl.textContent = (slides.length || 0) + ' شرائح · اسحب يمين/شمال';

        slides.forEach(function (slide, index) {
            const wrap = document.createElement('div');
            wrap.className = 'carousel-lightbox-slide';

            if (slide.kind === 'video') {
                const video = document.createElement('video');
                video.src = slide.url;
                video.controls = true;
                video.playsInline = true;
                video.setAttribute('aria-label', slide.name || ('slide-' + (index + 1)));
                wrap.appendChild(video);
            } else {
                const img = document.createElement('img');
                img.src = slide.url;
                img.alt = slide.name || ('slide-' + (index + 1));
                wrap.appendChild(img);
            }

            track.appendChild(wrap);
        });

        lightbox.classList.add('is-open');
        lightbox.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
        track.scrollLeft = 0;
    }

    document.querySelectorAll('[data-carousel-open]').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            let slides = [];
            try {
                slides = JSON.parse(btn.getAttribute('data-carousel-slides') || '[]');
            } catch (err) {
                slides = [];
            }
            if (!slides.length) return;
            openLightbox(slides, btn.getAttribute('data-carousel-title') || '');
        });
    });

    closeBtn && closeBtn.addEventListener('click', closeLightbox);
    lightbox.addEventListener('click', function (e) {
        if (e.target === lightbox) closeLightbox();
    });
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && lightbox.classList.contains('is-open')) {
            closeLightbox();
        }
    });
})();
</script>
@endpush
