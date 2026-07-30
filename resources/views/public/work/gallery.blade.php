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

@php
    $reviewData = [];
    foreach ($items as $itemIndex => $item) {
        $task = $item['task'];
        $files = ($item['type'] ?? 'single') === 'carousel'
            ? $item['files']
            : collect([$item['file']]);

        $reviewData[(string) $itemIndex] = [
            'title' => $task->title,
            'typeLabel' => $task->content_type_label,
            'designerName' => $task->designer?->name,
            'isCarousel' => ($item['type'] ?? 'single') === 'carousel',
            'caption' => $task->caption,
            'tov' => $task->tov,
            'idea' => $task->idea,
            'platforms' => $task->platform_labels ?? [],
            'publishDate' => $task->publish_date?->format('Y/m/d'),
            'taskUrl' => route('public.work.task', [$shareToken, $task]),
            'slides' => $files->map(function ($file) use ($shareToken, $task) {
                $fullUrl = route('public.work.file', [$shareToken, $task, $file]);
                $isImage = $file->isImage();

                return [
                    'url' => $isImage
                        ? route('public.work.file', [$shareToken, $task, $file, 'w' => 1100])
                        : $fullUrl,
                    'thumb' => $isImage
                        ? route('public.work.file', [$shareToken, $task, $file, 'w' => 420])
                        : $fullUrl,
                    'download' => route('public.work.file', [$shareToken, $task, $file, 'download' => 1]),
                    'name' => $file->file_name,
                    'kind' => $file->isVideo() ? 'video' : 'image',
                ];
            })->values()->all(),
        ];
    }
@endphp

@push('head')
<style>
    .gallery-skel {
        background:
            linear-gradient(90deg, #e2e8f0 0%, #f1f5f9 45%, #e2e8f0 90%);
        background-size: 200% 100%;
        animation: galleryShimmer 1.1s ease-in-out infinite;
    }
    @keyframes galleryShimmer {
        0% { background-position: 100% 0; }
        100% { background-position: -100% 0; }
    }
    .gallery-img {
        opacity: 0;
        transition: opacity .25s ease;
    }
    .gallery-img.is-loaded { opacity: 1; }
    .gallery-filter-btn {
        background: #fff;
        color: #475569;
        border-color: #e2e8f0;
    }
    .gallery-filter-btn:hover {
        border-color: #c4b5fd;
        color: #6d28d9;
        background: #f5f3ff;
    }
    .gallery-filter-btn.is-active {
        background: #7c3aed;
        color: #fff;
        border-color: #7c3aed;
    }
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
    .review-modal {
        position: fixed;
        inset: 0;
        z-index: 80;
        background: rgba(15, 23, 42, 0.88);
        display: none;
        align-items: stretch;
        justify-content: center;
        padding: 0.75rem;
    }
    .review-modal.is-open { display: flex; }
    .review-shell {
        width: min(1100px, 100%);
        max-height: calc(100vh - 1.5rem);
        margin: auto;
        background: #fff;
        border-radius: 1.5rem;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        box-shadow: 0 30px 80px rgba(0,0,0,.35);
    }
    .review-body {
        display: grid;
        grid-template-columns: 1fr;
        min-height: 0;
        flex: 1;
        overflow: auto;
    }
    @media (min-width: 900px) {
        .review-body {
            grid-template-columns: 1.15fr 0.85fr;
            overflow: hidden;
        }
        .review-media, .review-content {
            overflow: auto;
            max-height: calc(100vh - 5.5rem);
        }
    }
    .review-media-track {
        display: flex;
        gap: 0.75rem;
        overflow-x: auto;
        scroll-snap-type: x mandatory;
        -webkit-overflow-scrolling: touch;
        padding: 1rem;
        align-items: center;
        min-height: 280px;
        background:
            radial-gradient(600px 280px at 20% 0%, rgba(13,148,136,.18), transparent 55%),
            linear-gradient(180deg, #0f172a 0%, #1e293b 100%);
    }
    .review-slide {
        scroll-snap-align: center;
        flex: 0 0 auto;
        max-width: min(88vw, 420px);
        max-height: min(62vh, 640px);
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .review-slide.is-single {
        width: 100%;
        max-width: none;
    }
    .review-slide img,
    .review-slide video {
        max-height: min(62vh, 640px);
        max-width: 100%;
        width: auto;
        height: auto;
        object-fit: contain;
        border-radius: 1rem;
        background: #020617;
        box-shadow: 0 18px 40px rgba(0,0,0,.35);
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
            <p class="text-sm md:text-base text-slate-300 mt-2">اضغط أي تصميم لمراجعة الصورة والمحتوى مع بعض</p>
        </div>
    </section>

    @if($items->isEmpty())
        <section class="share-panel rounded-3xl p-10 text-center">
            <span class="material-icons text-5xl text-slate-300">image_not_supported</span>
            <h2 class="text-lg font-bold text-slate-700 mt-3">مفيش ملفات تصميم لسه</h2>
            <p class="text-sm text-slate-500 mt-1">لما المصمم يرفع الصور هتظهر هنا تلقائيًا</p>
        </section>
    @else
        @php
            $designerFilters = $items
                ->map(function ($item) {
                    $designer = $item['task']->designer;

                    return $designer
                        ? ['id' => (string) $designer->id, 'name' => $designer->name]
                        : null;
                })
                ->filter()
                ->unique('id')
                ->sortBy('name', SORT_NATURAL)
                ->values();
            $unassignedCount = $items->filter(fn ($item) => ! $item['task']->designer_id)->count();
        @endphp

        @if($designerFilters->isNotEmpty() || $unassignedCount > 0)
            <section class="share-panel rounded-2xl p-3 md:p-4">
                <div class="flex items-center gap-2 mb-2.5">
                    <span class="material-icons text-base text-purple-600">palette</span>
                    <p class="text-xs font-bold text-slate-600">تصفية حسب المصمم</p>
                    <span id="galleryFilterCount" class="text-[11px] text-slate-400 ms-auto"></span>
                </div>
                <div id="galleryDesignerFilters" class="flex flex-wrap gap-1.5">
                    <button type="button"
                            class="gallery-filter-btn is-active px-3 py-1.5 rounded-xl text-[11px] font-bold border transition-colors"
                            data-designer="all">
                        الكل
                    </button>
                    @foreach($designerFilters as $designer)
                        <button type="button"
                                class="gallery-filter-btn px-3 py-1.5 rounded-xl text-[11px] font-bold border transition-colors"
                                data-designer="{{ $designer['id'] }}">
                            {{ $designer['name'] }}
                        </button>
                    @endforeach
                    @if($unassignedCount > 0)
                        <button type="button"
                                class="gallery-filter-btn px-3 py-1.5 rounded-xl text-[11px] font-bold border transition-colors"
                                data-designer="none">
                            بدون مصمم
                        </button>
                    @endif
                </div>
            </section>
        @endif

        <section class="share-panel rounded-3xl p-4 md:p-6">
            <div id="galleryEmptyFilter" class="hidden text-center py-10">
                <span class="material-icons text-4xl text-slate-300">filter_alt_off</span>
                <p class="text-sm font-bold text-slate-600 mt-2">مفيش تصاميم للمصمم ده</p>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-3 md:gap-4" id="galleryGrid">
                @foreach($items as $itemIndex => $item)
                    @php
                        $task = $item['task'];
                        $taskUrl = route('public.work.task', [$shareToken, $task]);
                        $designerId = $task->designer_id ? (string) $task->designer_id : 'none';
                    @endphp

                    @if(($item['type'] ?? 'single') === 'carousel')
                        @php $files = $item['files']; @endphp
                        <article class="file-tile gallery-item col-span-2 md:col-span-3 rounded-2xl overflow-hidden border border-slate-200 bg-white flex flex-col"
                                 data-designer="{{ $designerId }}">
                            <button type="button"
                                    class="text-start w-full p-3 pb-2"
                                    data-review-open="{{ $itemIndex }}">
                                <div class="carousel-strip" dir="ltr">
                                    @foreach($files as $file)
                                        @php
                                            $fileUrl = route('public.work.file', [$shareToken, $task, $file]);
                                            $thumbUrl = $file->isImage()
                                                ? route('public.work.file', [$shareToken, $task, $file, 'w' => 360])
                                                : $fileUrl;
                                        @endphp
                                        <div class="carousel-strip-item aspect-square rounded-xl overflow-hidden bg-slate-100 relative border border-slate-200/80 gallery-skel">
                                            @if($file->isImage())
                                                <img src="{{ $thumbUrl }}"
                                                     alt="{{ $file->file_name }}"
                                                     class="gallery-img w-full h-full object-cover"
                                                     loading="{{ $loop->iteration <= 2 ? 'eager' : 'lazy' }}"
                                                     decoding="async"
                                                     width="360" height="360"
                                                     onload="this.classList.add('is-loaded'); this.parentElement.classList.remove('gallery-skel')">
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
                                <button type="button" data-review-open="{{ $itemIndex }}"
                                        class="block w-full text-start text-sm font-bold text-slate-800 leading-snug line-clamp-2 hover:text-teal-700">
                                    {{ $task->title }}
                                </button>
                                @if($task->designer?->name)
                                    <p class="text-[11px] text-slate-500 inline-flex items-center gap-1">
                                        <span class="material-icons text-sm text-purple-500">palette</span>
                                        تصميم: <span class="font-semibold text-slate-700">{{ $task->designer->name }}</span>
                                    </p>
                                @endif
                                <div class="flex items-center justify-between gap-2">
                                    <span class="text-[10px] font-semibold px-2 py-0.5 rounded-md bg-orange-50 text-orange-700 inline-flex items-center gap-1">
                                        <span class="material-icons text-xs">{{ ($item['is_carousel_type'] ?? ($task->content_type === 'carousel')) ? 'view_carousel' : 'collections' }}</span>
                                        @if($item['is_carousel_type'] ?? ($task->content_type === 'carousel'))
                                            كروسيل · {{ $files->count() }} شرائح
                                        @else
                                            {{ $files->count() }} ملفات
                                        @endif
                                    </span>
                                    <button type="button"
                                            class="inline-flex items-center gap-0.5 text-[11px] font-semibold text-teal-700 hover:text-teal-900"
                                            data-review-open="{{ $itemIndex }}">
                                        <span class="material-icons text-sm">rate_review</span>
                                        مراجعة
                                    </button>
                                </div>
                            </div>
                        </article>
                    @else
                        @php
                            $file = $item['file'];
                            $fileUrl = route('public.work.file', [$shareToken, $task, $file]);
                            $thumbUrl = $file->isImage()
                                ? route('public.work.file', [$shareToken, $task, $file, 'w' => 480])
                                : $fileUrl;
                            $downloadUrl = route('public.work.file', [$shareToken, $task, $file, 'download' => 1]);
                        @endphp
                        <article class="file-tile gallery-item rounded-2xl overflow-hidden border border-slate-200 bg-white flex flex-col"
                                 data-designer="{{ $designerId }}">
                            <button type="button" data-review-open="{{ $itemIndex }}"
                                    class="block aspect-square bg-slate-100 relative group text-start w-full gallery-skel">
                                @if($file->isImage())
                                    <img src="{{ $thumbUrl }}" alt="{{ $file->file_name }}"
                                         class="gallery-img w-full h-full object-cover"
                                         loading="{{ $itemIndex < 4 ? 'eager' : 'lazy' }}"
                                         decoding="async"
                                         fetchpriority="{{ $itemIndex < 2 ? 'high' : 'auto' }}"
                                         width="480" height="480"
                                         onload="this.classList.add('is-loaded'); this.parentElement.classList.remove('gallery-skel')">
                                    <span class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition-colors flex items-center justify-center">
                                        <span class="opacity-0 group-hover:opacity-100 material-icons text-white text-3xl drop-shadow">rate_review</span>
                                    </span>
                                @else
                                    <video src="{{ $fileUrl }}" class="w-full h-full object-cover" muted playsinline preload="metadata"></video>
                                    <span class="absolute inset-0 flex items-center justify-center bg-black/25">
                                        <span class="material-icons text-white text-4xl">play_circle</span>
                                    </span>
                                @endif
                            </button>
                            <div class="p-3 space-y-2">
                                <button type="button" data-review-open="{{ $itemIndex }}"
                                        class="block w-full text-start text-sm font-bold text-slate-800 leading-snug line-clamp-2 hover:text-teal-700">
                                    {{ $task->title }}
                                </button>
                                @if($task->designer?->name)
                                    <p class="text-[11px] text-slate-500 inline-flex items-center gap-1">
                                        <span class="material-icons text-sm text-purple-500">palette</span>
                                        تصميم: <span class="font-semibold text-slate-700">{{ $task->designer->name }}</span>
                                    </p>
                                @endif
                                <div class="flex items-center justify-between gap-2">
                                    @if($task->content_type_label)
                                        <span class="text-[10px] font-semibold px-2 py-0.5 rounded-md bg-teal-50 text-teal-700">{{ $task->content_type_label }}</span>
                                    @else
                                        <span class="text-[10px] text-slate-400">{{ $file->asset_kind_label }}</span>
                                    @endif
                                    <a href="{{ $downloadUrl }}"
                                       class="inline-flex items-center gap-0.5 text-[11px] font-semibold text-slate-500 hover:text-slate-800"
                                       onclick="event.stopPropagation()">
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

{{-- مراجعة: تصميم + محتوى النشر --}}
<div id="reviewModal" class="review-modal" aria-hidden="true">
    <div class="review-shell" role="dialog" aria-modal="true" aria-labelledby="reviewTitle">
        <div class="flex items-center justify-between gap-3 px-4 py-3 border-b border-slate-200 bg-white">
            <div class="min-w-0">
                <p id="reviewTitle" class="text-sm md:text-base font-extrabold text-slate-900 truncate"></p>
                <p id="reviewMeta" class="text-[11px] text-slate-500 mt-0.5"></p>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <a id="reviewTaskLink" href="#"
                   class="hidden sm:inline-flex items-center gap-1 px-3 py-2 rounded-xl border border-slate-200 text-xs font-bold text-slate-700 hover:bg-slate-50">
                    <span class="material-icons text-sm">open_in_new</span>
                    التفاصيل
                </a>
                <button type="button" id="reviewClose"
                        class="inline-flex items-center gap-1 px-3 py-2 rounded-xl bg-slate-900 text-white text-xs font-bold hover:bg-slate-800">
                    <span class="material-icons text-sm">close</span>
                    إغلاق
                </button>
            </div>
        </div>

        <div class="review-body">
            <div class="review-media border-b md:border-b-0 md:border-l border-slate-200">
                <div id="reviewMediaTrack" class="review-media-track" dir="ltr"></div>
            </div>

            <div class="review-content p-4 md:p-5 space-y-4 bg-slate-50/70">
                <section class="rounded-2xl bg-white border border-slate-200 p-4">
                    <h3 class="text-[11px] font-bold uppercase tracking-wide text-slate-400 mb-2 flex items-center gap-1">
                        <span class="material-icons text-sm text-teal-600">notes</span>
                        المحتوى اللي هينزل (Caption)
                    </h3>
                    <p id="reviewCaption" class="text-sm md:text-base leading-7 text-slate-800 whitespace-pre-line font-semibold"></p>
                </section>

                <section id="reviewTovBox" class="rounded-2xl bg-white border border-slate-200 p-4 hidden">
                    <h3 class="text-[11px] font-bold uppercase tracking-wide text-slate-400 mb-2">Tone of Voice</h3>
                    <p id="reviewTov" class="text-sm leading-7 text-slate-700 whitespace-pre-line"></p>
                </section>

                <section id="reviewIdeaBox" class="rounded-2xl bg-white border border-slate-200 p-4 hidden">
                    <h3 class="text-[11px] font-bold uppercase tracking-wide text-slate-400 mb-2">الفكرة</h3>
                    <p id="reviewIdea" class="text-sm leading-7 text-slate-700 whitespace-pre-line"></p>
                </section>

                <section id="reviewMetaBox" class="rounded-2xl bg-white border border-slate-200 p-4 hidden">
                    <h3 class="text-[11px] font-bold uppercase tracking-wide text-slate-400 mb-2">بيانات النشر</h3>
                    <div id="reviewPlatforms" class="flex flex-wrap gap-1.5 mb-2"></div>
                    <p id="reviewPublishDate" class="text-xs text-slate-600"></p>
                </section>

                <div class="flex flex-wrap gap-2 pt-1">
                    <a id="reviewDownload" href="#"
                       class="inline-flex items-center gap-1 px-3.5 py-2.5 rounded-xl bg-slate-900 text-white text-xs font-bold hover:bg-slate-800">
                        <span class="material-icons text-sm">download</span>
                        تحميل التصميم
                    </a>
                    <a id="reviewTaskLinkMobile" href="#"
                       class="sm:hidden inline-flex items-center gap-1 px-3.5 py-2.5 rounded-xl border border-slate-200 bg-white text-xs font-bold text-slate-700">
                        <span class="material-icons text-sm">open_in_new</span>
                        صفحة التاسك
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="application/json" id="galleryReviewData">{!! json_encode($reviewData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
@endsection

@push('scripts')
<script>
(function () {
    const filterWrap = document.getElementById('galleryDesignerFilters');
    if (filterWrap) {
        const items = Array.from(document.querySelectorAll('.gallery-item'));
        const emptyEl = document.getElementById('galleryEmptyFilter');
        const countEl = document.getElementById('galleryFilterCount');
        const grid = document.getElementById('galleryGrid');

        function applyFilter(designerId) {
            let visible = 0;
            items.forEach(function (el) {
                const match = designerId === 'all' || el.dataset.designer === designerId;
                el.classList.toggle('hidden', !match);
                if (match) visible++;
            });
            if (emptyEl) emptyEl.classList.toggle('hidden', visible > 0);
            if (grid) grid.classList.toggle('hidden', visible === 0);
            if (countEl) {
                countEl.textContent = designerId === 'all'
                    ? (visible + ' عنصر')
                    : (visible + ' من ' + items.length);
            }
        }

        filterWrap.addEventListener('click', function (e) {
            const btn = e.target.closest('.gallery-filter-btn');
            if (!btn) return;
            filterWrap.querySelectorAll('.gallery-filter-btn').forEach(function (b) {
                b.classList.toggle('is-active', b === btn);
            });
            applyFilter(btn.dataset.designer || 'all');
        });

        applyFilter('all');
    }
})();

(function () {
    const modal = document.getElementById('reviewModal');
    const dataEl = document.getElementById('galleryReviewData');
    if (!modal || !dataEl) return;

    let reviewData = {};
    try { reviewData = JSON.parse(dataEl.textContent || '{}'); } catch (e) { reviewData = {}; }

    const titleEl = document.getElementById('reviewTitle');
    const metaEl = document.getElementById('reviewMeta');
    const track = document.getElementById('reviewMediaTrack');
    const captionEl = document.getElementById('reviewCaption');
    const tovBox = document.getElementById('reviewTovBox');
    const tovEl = document.getElementById('reviewTov');
    const ideaBox = document.getElementById('reviewIdeaBox');
    const ideaEl = document.getElementById('reviewIdea');
    const metaBox = document.getElementById('reviewMetaBox');
    const platformsEl = document.getElementById('reviewPlatforms');
    const publishDateEl = document.getElementById('reviewPublishDate');
    const taskLink = document.getElementById('reviewTaskLink');
    const taskLinkMobile = document.getElementById('reviewTaskLinkMobile');
    const downloadLink = document.getElementById('reviewDownload');
    const closeBtn = document.getElementById('reviewClose');

    function setTextOrPlaceholder(el, value, emptyText) {
        const text = (value || '').trim();
        el.textContent = text || emptyText;
        el.classList.toggle('text-slate-400', !text);
        el.classList.toggle('font-semibold', !!text);
        return !!text;
    }

    function closeReview() {
        modal.classList.remove('is-open');
        modal.setAttribute('aria-hidden', 'true');
        track.innerHTML = '';
        document.body.style.overflow = '';
    }

    function openReview(key) {
        const item = reviewData[String(key)];
        if (!item || !item.slides || !item.slides.length) return;

        titleEl.textContent = item.title || 'مراجعة التصميم';
        const bits = [];
        if (item.typeLabel) bits.push(item.typeLabel);
        if (item.designerName) bits.push('تصميم: ' + item.designerName);
        if (item.isCarousel) bits.push(item.slides.length + ' شرائح');
        bits.push('مراجعة قبل النشر');
        metaEl.textContent = bits.join(' · ');

        track.innerHTML = '';
        const single = item.slides.length === 1;
        item.slides.forEach(function (slide, index) {
            const wrap = document.createElement('div');
            wrap.className = 'review-slide' + (single ? ' is-single' : '');
            if (slide.kind === 'video') {
                const video = document.createElement('video');
                video.src = slide.url;
                video.controls = true;
                video.playsInline = true;
                video.setAttribute('aria-label', slide.name || ('slide-' + (index + 1)));
                wrap.appendChild(video);
            } else {
                const img = document.createElement('img');
                img.src = slide.url || slide.thumb;
                img.alt = slide.name || ('slide-' + (index + 1));
                img.decoding = 'async';
                img.loading = index === 0 ? 'eager' : 'lazy';
                wrap.appendChild(img);
            }
            track.appendChild(wrap);
        });
        track.scrollLeft = 0;

        setTextOrPlaceholder(captionEl, item.caption, 'مفيش كابشن مكتوب لسه');

        if (setTextOrPlaceholder(tovEl, item.tov, '')) {
            tovBox.classList.remove('hidden');
        } else {
            tovBox.classList.add('hidden');
        }

        if (setTextOrPlaceholder(ideaEl, item.idea, '')) {
            ideaBox.classList.remove('hidden');
        } else {
            ideaBox.classList.add('hidden');
        }

        platformsEl.innerHTML = '';
        const platforms = item.platforms || [];
        platforms.forEach(function (p) {
            const chip = document.createElement('span');
            chip.className = 'text-[11px] font-semibold px-2 py-1 rounded-lg bg-teal-50 text-teal-800';
            chip.textContent = p;
            platformsEl.appendChild(chip);
        });
        publishDateEl.textContent = item.publishDate ? ('موعد النشر: ' + item.publishDate) : '';
        if (platforms.length || item.publishDate) {
            metaBox.classList.remove('hidden');
        } else {
            metaBox.classList.add('hidden');
        }

        taskLink.href = item.taskUrl || '#';
        taskLinkMobile.href = item.taskUrl || '#';
        downloadLink.href = item.slides[0].download || item.slides[0].url;
        downloadLink.textContent = '';
        downloadLink.innerHTML = '<span class="material-icons text-sm">download</span> ' +
            (item.isCarousel ? 'تحميل أول شريحة' : 'تحميل التصميم');

        modal.classList.add('is-open');
        modal.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
    }

    document.querySelectorAll('[data-review-open]').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            openReview(btn.getAttribute('data-review-open'));
        });
    });

    closeBtn && closeBtn.addEventListener('click', closeReview);
    modal.addEventListener('click', function (e) {
        if (e.target === modal) closeReview();
    });
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && modal.classList.contains('is-open')) closeReview();
    });
})();
</script>
@endpush
