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
            'stageLabel' => $task->pipeline_stage_label,
            'isCarousel' => ($item['type'] ?? 'single') === 'carousel',
            'caption' => $task->caption,
            'tov' => $task->tov,
            'idea' => $task->idea,
            'platforms' => $task->platform_labels ?? [],
            'publishDate' => $task->publish_schedule_label,
            'postNumber' => \App\Models\WorkTask::extractPostSequence($task->title),
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

@push('head')
<style>
    .share-shell { max-width: 72rem; }

    .gallery-skel {
        background: linear-gradient(90deg, #e2e8f0 0%, #f1f5f9 45%, #e2e8f0 90%);
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
        border-color: #99f6e4;
        color: #0f766e;
        background: #f0fdfa;
    }
    .gallery-filter-btn.is-active {
        background: #0d9488;
        color: #fff;
        border-color: #0d9488;
    }

    .post-card {
        background: #fff;
        border: 1px solid rgba(15, 23, 42, 0.08);
        border-radius: 1.25rem;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        height: 100%;
        transition: transform .18s ease, box-shadow .18s ease;
    }
    .post-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 16px 36px rgba(15, 23, 42, 0.1);
    }
    .post-card-media {
        position: relative;
        aspect-ratio: 1 / 1;
        background: #f1f5f9;
        overflow: hidden;
    }
    .post-card-body {
        display: flex;
        flex-direction: column;
        gap: 0.65rem;
        padding: 0.9rem 1rem 1rem;
        flex: 1;
    }
    .post-meta-row {
        display: flex;
        flex-wrap: wrap;
        gap: 0.35rem;
    }
    .post-chip {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        font-size: 0.65rem;
        font-weight: 700;
        line-height: 1.2;
        padding: 0.28rem 0.5rem;
        border-radius: 0.55rem;
        background: #f8fafc;
        color: #475569;
        border: 1px solid #e2e8f0;
    }
    .post-chip-teal { background: #f0fdfa; color: #0f766e; border-color: #99f6e4; }
    .post-chip-amber { background: #fffbeb; color: #b45309; border-color: #fde68a; }
    .post-chip-slate { background: #f1f5f9; color: #334155; border-color: #e2e8f0; }
    .post-chip-rose { background: #fff1f2; color: #be123c; border-color: #fecdd3; }
    .post-caption {
        font-size: 0.75rem;
        line-height: 1.55;
        color: #64748b;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .post-thumb-stack {
        position: absolute;
        inset-inline-end: 0.6rem;
        bottom: 0.6rem;
        display: flex;
        align-items: center;
    }
    .post-thumb-stack span {
        width: 1.65rem;
        height: 1.65rem;
        border-radius: 0.45rem;
        border: 2px solid #fff;
        background: #cbd5e1;
        margin-inline-start: -0.45rem;
        box-shadow: 0 2px 6px rgba(15,23,42,.15);
        overflow: hidden;
    }
    .post-thumb-stack span img {
        width: 100%;
        height: 100%;
        object-fit: cover;
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
        <p class="text-xs font-semibold text-slate-500 bg-white/70 border border-slate-200 px-2.5 py-1 rounded-lg">
            {{ $items->count() }} بوست
            · {{ $imageCount }} صورة
            @if($videoCount > 0)
                · {{ $videoCount }} فيديو
            @endif
        </p>
    </div>

    <section class="share-panel rounded-3xl overflow-hidden">
        <div class="bg-gradient-to-l from-slate-900 via-slate-800 to-teal-900 px-5 py-6 md:px-8 md:py-8 text-white">
            <p class="text-xs font-bold text-teal-200/90 mb-2 inline-flex items-center gap-1">
                <span class="material-icons text-sm">photo_library</span>
                معرض تصميم الحملة
            </p>
            <h1 class="text-2xl md:text-4xl font-extrabold leading-tight tracking-tight">{{ $activity->title }}</h1>
            <p class="text-sm md:text-base text-slate-300 mt-2 max-w-2xl">
                كل بوست في كارت منظم: التصميم + النوع + المصمم + موعد النشر + مقتطف المحتوى
            </p>
        </div>
    </section>

    @if($items->isEmpty())
        <section class="share-panel rounded-3xl p-10 text-center">
            <span class="material-icons text-5xl text-slate-300">image_not_supported</span>
            <h2 class="text-lg font-bold text-slate-700 mt-3">مفيش ملفات تصميم لسه</h2>
            <p class="text-sm text-slate-500 mt-1">لما المصمم يرفع الصور هتظهر هنا تلقائيًا</p>
        </section>
    @else
        @if($designerFilters->isNotEmpty() || $unassignedCount > 0)
            <section class="share-panel rounded-2xl p-3.5 md:p-4">
                <div class="flex items-center gap-2 mb-2.5">
                    <span class="material-icons text-base text-teal-700">palette</span>
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

        <div id="galleryEmptyFilter" class="hidden share-panel rounded-3xl p-10 text-center">
            <span class="material-icons text-4xl text-slate-300">filter_alt_off</span>
            <p class="text-sm font-bold text-slate-600 mt-2">مفيش تصاميم للمصمم ده</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4 md:gap-5" id="galleryGrid">
            @foreach($items as $itemIndex => $item)
                @php
                    $task = $item['task'];
                    $isCarousel = ($item['type'] ?? 'single') === 'carousel';
                    $files = $isCarousel ? $item['files'] : collect([$item['file']]);
                    $cover = $files->first();
                    $designerId = $task->designer_id ? (string) $task->designer_id : 'none';
                    $postNumber = \App\Models\WorkTask::extractPostSequence($task->title);
                    $coverUrl = $cover && $cover->isImage()
                        ? route('public.work.file', [$shareToken, $task, $cover, 'w' => 640])
                        : ($cover ? route('public.work.file', [$shareToken, $task, $cover]) : null);
                    $downloadUrl = $cover
                        ? route('public.work.file', [$shareToken, $task, $cover, 'download' => 1])
                        : null;
                    $typeIcon = match ($task->content_type) {
                        'reels' => 'movie',
                        'carousel' => 'view_carousel',
                        'post' => 'article',
                        default => $isCarousel ? 'collections' : 'image',
                    };
                @endphp

                <article class="post-card gallery-item" data-designer="{{ $designerId }}">
                    <button type="button"
                            class="post-card-media gallery-skel group text-start w-full"
                            data-review-open="{{ $itemIndex }}">
                        @if($cover && $cover->isImage())
                            <img src="{{ $coverUrl }}"
                                 alt="{{ $task->title }}"
                                 class="gallery-img absolute inset-0 w-full h-full object-cover"
                                 loading="{{ $itemIndex < 3 ? 'eager' : 'lazy' }}"
                                 decoding="async"
                                 width="640" height="640"
                                 onload="this.classList.add('is-loaded'); this.parentElement.classList.remove('gallery-skel')">
                        @elseif($cover)
                            <video src="{{ $coverUrl }}" class="absolute inset-0 w-full h-full object-cover" muted playsinline preload="metadata"></video>
                            <span class="absolute inset-0 flex items-center justify-center bg-black/25">
                                <span class="material-icons text-white text-4xl">play_circle</span>
                            </span>
                        @endif

                        <span class="absolute inset-0 bg-gradient-to-t from-slate-900/55 via-transparent to-transparent opacity-80"></span>

                        @if($postNumber)
                            <span class="absolute top-3 start-3 text-[11px] font-extrabold px-2.5 py-1 rounded-lg bg-white/95 text-slate-900 shadow-sm">
                                بوست {{ $postNumber }}
                            </span>
                        @endif

                        @if($files->count() > 1)
                            <span class="absolute top-3 end-3 text-[10px] font-bold px-2 py-1 rounded-lg bg-slate-900/75 text-white inline-flex items-center gap-1">
                                <span class="material-icons text-xs">{{ $typeIcon }}</span>
                                {{ $files->count() }} شرائح
                            </span>
                            <div class="post-thumb-stack" dir="ltr">
                                @foreach($files->take(3) as $thumbFile)
                                    @php
                                        $mini = $thumbFile->isImage()
                                            ? route('public.work.file', [$shareToken, $task, $thumbFile, 'w' => 80])
                                            : null;
                                    @endphp
                                    <span>
                                        @if($mini)
                                            <img src="{{ $mini }}" alt="" loading="lazy">
                                        @endif
                                    </span>
                                @endforeach
                            </div>
                        @endif

                        <span class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                            <span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl bg-white/95 text-slate-900 text-xs font-bold shadow">
                                <span class="material-icons text-sm">rate_review</span>
                                مراجعة
                            </span>
                        </span>
                    </button>

                    <div class="post-card-body">
                        <div class="post-meta-row">
                            @if($task->content_type_label)
                                <span class="post-chip post-chip-teal">
                                    <span class="material-icons text-xs">{{ $typeIcon }}</span>
                                    {{ $task->content_type_label }}
                                </span>
                            @elseif($isCarousel)
                                <span class="post-chip post-chip-amber">
                                    <span class="material-icons text-xs">collections</span>
                                    {{ $files->count() }} ملفات
                                </span>
                            @endif

                            <span class="post-chip post-chip-slate">
                                <span class="material-icons text-xs">{{ $task->pipeline_stage_icon }}</span>
                                {{ $task->pipeline_stage_label }}
                            </span>

                            @if($task->is_overdue)
                                <span class="post-chip post-chip-rose">متأخرة</span>
                            @endif
                        </div>

                        <button type="button"
                                data-review-open="{{ $itemIndex }}"
                                class="text-start font-extrabold text-slate-900 leading-snug text-[0.95rem] line-clamp-2 hover:text-teal-700">
                            {{ $task->title }}
                        </button>

                        @if(filled($task->caption))
                            <p class="post-caption">{{ $task->caption }}</p>
                        @endif

                        <div class="space-y-1.5 pt-1 mt-auto border-t border-slate-100">
                            @if($task->designer?->name)
                                <p class="text-[11px] text-slate-600 inline-flex items-center gap-1.5">
                                    <span class="material-icons text-sm text-teal-600">palette</span>
                                    <span class="text-slate-400">المصمم</span>
                                    <span class="font-bold text-slate-800">{{ $task->designer->name }}</span>
                                </p>
                            @endif

                            @if($task->publish_schedule_label)
                                <p class="text-[11px] text-slate-600 inline-flex items-center gap-1.5">
                                    <span class="material-icons text-sm text-teal-600">event</span>
                                    <span class="text-slate-400">موعد النشر</span>
                                    <span class="font-bold text-slate-800">{{ $task->publish_schedule_label }}</span>
                                </p>
                            @endif

                            @if(!empty($task->platform_labels))
                                <div class="flex flex-wrap gap-1 pt-0.5">
                                    @foreach($task->platform_labels as $platform)
                                        <span class="post-chip">{{ $platform }}</span>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        <div class="flex items-center justify-between gap-2 pt-1">
                            <button type="button"
                                    class="inline-flex items-center gap-1 text-[11px] font-bold text-teal-700 hover:text-teal-900"
                                    data-review-open="{{ $itemIndex }}">
                                <span class="material-icons text-sm">visibility</span>
                                فتح المراجعة
                            </button>
                            @if($downloadUrl)
                                <a href="{{ $downloadUrl }}"
                                   class="inline-flex items-center gap-0.5 text-[11px] font-semibold text-slate-500 hover:text-slate-800"
                                   onclick="event.stopPropagation()">
                                    <span class="material-icons text-sm">download</span>
                                    تحميل
                                </a>
                            @endif
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
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
                    ? (visible + ' بوست')
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

    function openReview(index) {
        const item = reviewData[String(index)];
        if (!item) return;

        const metaBits = [];
        if (item.postNumber) metaBits.push('بوست ' + item.postNumber);
        if (item.typeLabel) metaBits.push(item.typeLabel);
        if (item.designerName) metaBits.push('تصميم: ' + item.designerName);
        if (item.stageLabel) metaBits.push(item.stageLabel);
        if (item.isCarousel && item.slides) metaBits.push(item.slides.length + ' شرائح');

        titleEl.textContent = item.title || '';
        metaEl.textContent = metaBits.join(' · ');

        setTextOrPlaceholder(captionEl, item.caption, 'مفيش كابشن مكتوب لسه');

        const hasTov = setTextOrPlaceholder(tovEl, item.tov, '');
        tovBox.classList.toggle('hidden', !hasTov);

        const hasIdea = setTextOrPlaceholder(ideaEl, item.idea, '');
        ideaBox.classList.toggle('hidden', !hasIdea);

        platformsEl.innerHTML = '';
        (item.platforms || []).forEach(function (p) {
            const chip = document.createElement('span');
            chip.className = 'inline-flex px-2 py-0.5 rounded-md bg-teal-50 text-teal-800 text-[11px] font-bold border border-teal-100';
            chip.textContent = p;
            platformsEl.appendChild(chip);
        });
        publishDateEl.textContent = item.publishDate ? ('موعد النشر: ' + item.publishDate) : '';
        const hasMeta = (item.platforms || []).length > 0 || !!item.publishDate;
        metaBox.classList.toggle('hidden', !hasMeta);

        if (item.taskUrl) {
            taskLink.href = item.taskUrl;
            taskLinkMobile.href = item.taskUrl;
        }

        track.innerHTML = '';
        const slides = item.slides || [];
        slides.forEach(function (slide, i) {
            const wrap = document.createElement('div');
            wrap.className = 'review-slide' + (slides.length === 1 ? ' is-single' : '');
            if (slide.kind === 'video') {
                const video = document.createElement('video');
                video.src = slide.url;
                video.controls = true;
                video.playsInline = true;
                wrap.appendChild(video);
            } else {
                const img = document.createElement('img');
                img.src = slide.url;
                img.alt = slide.name || '';
                wrap.appendChild(img);
            }
            track.appendChild(wrap);
            if (i === 0 && downloadLink) {
                downloadLink.href = slide.download || '#';
            }
        });

        modal.classList.add('is-open');
        modal.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
    }

    document.addEventListener('click', function (e) {
        const btn = e.target.closest('[data-review-open]');
        if (!btn) return;
        openReview(btn.getAttribute('data-review-open'));
    });

    closeBtn?.addEventListener('click', closeReview);
    modal.addEventListener('click', function (e) {
        if (e.target === modal) closeReview();
    });
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && modal.classList.contains('is-open')) closeReview();
    });
})();
</script>
@endpush
