@extends('layouts.public')

@section('title', 'جاهز للنشر — '.$activity->title)

@section('header-actions')
<button type="button"
        data-share-url="{{ $pageUrl }}"
        onclick="window.copyShareText && window.copyShareText(this.dataset.shareUrl, this)"
        class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl bg-slate-900 text-white text-xs font-bold hover:bg-slate-800 transition-colors">
    <span class="material-icons text-sm">content_copy</span>
    نسخ الرابط
</button>
@endsection

@php
    $today = now()->toDateString();
    $tomorrow = now()->addDay()->toDateString();
    $dayAfter = now()->addDays(2)->toDateString();
    $reviewPayload = [];
    foreach ($tasks as $task) {
        $media = $task->files
            ->filter(fn ($f) => $f->isImage() || $f->isVideo())
            ->sortBy(fn ($f) => mb_strtolower((string) $f->file_name), SORT_NATURAL)
            ->values();
        $reviewPayload[(string) $task->id] = [
            'id' => $task->id,
            'title' => $task->title,
            'typeLabel' => $task->content_type_label,
            'designerName' => $task->designer?->name,
            'caption' => $task->caption,
            'tov' => $task->tov,
            'idea' => $task->idea,
            'platforms' => $task->platform_labels ?? [],
            'publish_date' => $task->publish_date?->format('Y-m-d'),
            'publish_time' => $task->publish_time_short,
            'schedule_label' => $task->publish_schedule_label,
            'taskUrl' => route('public.work.task', [$shareToken, $task]),
            'slides' => $media->map(function ($file) use ($shareToken, $task) {
                $isImage = $file->isImage();
                $full = route('public.work.file', [$shareToken, $task, $file]);

                return [
                    'url' => $isImage ? route('public.work.file', [$shareToken, $task, $file, 'w' => 1400, 'q' => 88]) : $full,
                    'thumb' => $isImage ? route('public.work.file', [$shareToken, $task, $file, 'w' => 240, 'q' => 70]) : $full,
                    'name' => $file->file_name,
                    'kind' => $file->isVideo() ? 'video' : 'image',
                ];
            })->values()->all(),
        ];
    }
    $scheduleUrlTpl = route('public.work.publish-schedule', [$shareToken, 'TASK_ID']);
@endphp

@push('head')
<style>
    .gallery-skel {
        background: linear-gradient(90deg, #e2e8f0 0%, #f1f5f9 45%, #e2e8f0 90%);
        background-size: 200% 100%;
        animation: sk 1.1s ease-in-out infinite;
    }
    @keyframes sk { 0%{background-position:100% 0} 100%{background-position:-100% 0} }
    .gallery-img { opacity: 0; transition: opacity .28s ease; }
    .gallery-img.is-loaded { opacity: 1; }
    .design-frame {
        position: relative;
        aspect-ratio: 4 / 5;
        background:
            radial-gradient(ellipse at 30% 20%, rgba(13,148,136,.12), transparent 55%),
            linear-gradient(160deg, #0f172a 0%, #1e293b 55%, #0f172a 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 1rem;
        overflow: hidden;
    }
    @media (min-width: 768px) {
        .design-frame { aspect-ratio: 1 / 1; padding: 1.25rem; }
    }
    .design-frame img,
    .design-frame video {
        max-width: 100%;
        max-height: 100%;
        width: auto;
        height: auto;
        object-fit: contain;
        border-radius: 0.85rem;
        box-shadow: 0 18px 40px rgba(0,0,0,.45);
        background: #fff;
    }
    .design-strip {
        display: flex;
        gap: 0.5rem;
        overflow-x: auto;
        padding: 0.75rem 1rem 0;
        scroll-snap-type: x mandatory;
        -webkit-overflow-scrolling: touch;
    }
    .design-strip::-webkit-scrollbar { height: 5px; }
    .design-strip::-webkit-scrollbar-thumb { background: rgba(15,23,42,.2); border-radius: 99px; }
    .design-strip-item {
        flex: 0 0 auto;
        width: 4.5rem;
        height: 4.5rem;
        border-radius: 0.75rem;
        overflow: hidden;
        border: 2px solid #e2e8f0;
        scroll-snap-align: start;
        background: #f1f5f9;
    }
    .design-strip-item img { width: 100%; height: 100%; object-fit: cover; }
    .review-modal {
        position: fixed; inset: 0; z-index: 80;
        background: rgba(15,23,42,.88); display: none;
        align-items: stretch; justify-content: center; padding: .75rem;
    }
    .review-modal.is-open { display: flex; }
    .review-shell {
        width: min(1100px, 100%); max-height: calc(100vh - 1.5rem);
        margin: auto; background: #fff; border-radius: 1.5rem;
        overflow: hidden; display: flex; flex-direction: column;
    }
    .review-body { display: grid; grid-template-columns: 1fr; flex: 1; min-height: 0; overflow: auto; }
    @media (min-width: 900px) {
        .review-body { grid-template-columns: 1.15fr .85fr; overflow: hidden; }
        .review-media, .review-content { overflow: auto; max-height: calc(100vh - 5.5rem); }
    }
    .review-media-track {
        display: flex; gap: .75rem; overflow-x: auto; padding: 1rem;
        min-height: 320px; align-items: center; justify-content: center;
        background: linear-gradient(180deg, #0f172a, #1e293b);
    }
    .review-slide img, .review-slide video {
        max-height: min(70vh, 720px); max-width: min(92vw, 520px);
        border-radius: 1rem; object-fit: contain;
        box-shadow: 0 20px 50px rgba(0,0,0,.4);
        background: #fff;
    }
</style>
<meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
<div class="space-y-5">
    <div class="flex items-center justify-between gap-3 flex-wrap">
        <a href="{{ route('public.work.show', $shareToken) }}"
           class="inline-flex items-center gap-1 text-sm text-slate-500 hover:text-teal-700">
            <span class="material-icons text-lg">arrow_forward</span>
            رجوع لمحتوى الحملة
        </a>
        <span class="text-xs font-bold px-2.5 py-1 rounded-lg bg-teal-50 text-teal-800">
            {{ $tasks->count() }} بوست جاهز للنشر
        </span>
    </div>

    <section class="share-panel rounded-3xl overflow-hidden">
        <div class="bg-gradient-to-l from-teal-700 via-teal-600 to-cyan-700 px-5 py-6 md:px-7 text-white">
            <p class="text-xs font-bold text-teal-100 mb-1 inline-flex items-center gap-1">
                <span class="material-icons text-sm">schedule_send</span>
                جدولة النشر · رابط عام
            </p>
            <h1 class="text-2xl md:text-4xl font-extrabold">{{ $activity->title }}</h1>
            <p class="text-sm text-teal-50/90 mt-2">اضغط البوست لعرض المحتوى، وحدّد اليوم والوقت بسهولة</p>
        </div>
    </section>

    @if($tasks->isEmpty())
        <section class="share-panel rounded-3xl p-10 text-center">
            <span class="material-icons text-5xl text-slate-300">inbox</span>
            <h2 class="text-lg font-bold text-slate-700 mt-3">مفيش بوستات جاهزة للنشر</h2>
        </section>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($tasks as $task)
                @php
                    $mediaFiles = $task->files
                        ->filter(fn ($f) => $f->isImage() || $f->isVideo())
                        ->sortBy(fn ($f) => mb_strtolower((string) $f->file_name), SORT_NATURAL)
                        ->values();
                    $cover = $mediaFiles->first();
                    $coverUrl = $cover
                        ? ($cover->isImage()
                            ? route('public.work.file', [$shareToken, $task, $cover, 'w' => 900, 'q' => 86])
                            : route('public.work.file', [$shareToken, $task, $cover]))
                        : null;
                @endphp
                <article class="share-panel rounded-2xl overflow-hidden flex flex-col border border-teal-100"
                         data-task-card="{{ $task->id }}">
                    <button type="button" class="text-start w-full" data-open-review="{{ $task->id }}">
                        <div class="design-frame gallery-skel">
                            @if($cover && $cover->isImage())
                                <img src="{{ $coverUrl }}" alt="{{ $task->title }}"
                                     class="gallery-img"
                                     loading="lazy" decoding="async"
                                     onload="this.classList.add('is-loaded'); this.parentElement.classList.remove('gallery-skel')">
                            @elseif($cover && $cover->isVideo())
                                <video src="{{ $coverUrl }}" muted playsinline preload="metadata"></video>
                                <span class="absolute inset-0 flex items-center justify-center pointer-events-none">
                                    <span class="material-icons text-white text-5xl drop-shadow">play_circle</span>
                                </span>
                            @else
                                <div class="text-slate-400 flex flex-col items-center gap-1">
                                    <span class="material-icons text-5xl">image</span>
                                    <span class="text-xs">مفيش تصميم</span>
                                </div>
                            @endif
                            @if($task->content_type_label)
                                <span class="absolute top-3 start-3 text-[11px] font-bold px-2 py-1 rounded-lg bg-white/95 text-teal-800 shadow-sm">
                                    {{ $task->content_type_label }}
                                </span>
                            @endif
                        </div>
                    </button>
                        @if($mediaFiles->count() > 1)
                            <div class="design-strip" dir="ltr">
                                @foreach($mediaFiles->take(8) as $mf)
                                    @php
                                        $mini = $mf->isImage()
                                            ? route('public.work.file', [$shareToken, $task, $mf, 'w' => 180, 'q' => 70])
                                            : route('public.work.file', [$shareToken, $task, $mf]);
                                    @endphp
                                    <button type="button" class="design-strip-item" data-open-review="{{ $task->id }}">
                                        @if($mf->isImage())
                                            <img src="{{ $mini }}" alt="" loading="lazy" decoding="async">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center bg-slate-800 text-white">
                                                <span class="material-icons text-base">movie</span>
                                            </div>
                                        @endif
                                    </button>
                                @endforeach
                            </div>
                        @endif
                        <button type="button" class="text-start w-full px-4 pt-3" data-open-review="{{ $task->id }}">
                            <h3 class="font-bold text-slate-900 leading-snug line-clamp-2">{{ $task->title }}</h3>
                            @if($task->designer?->name)
                                <p class="mt-1 text-[11px] text-slate-500">تصميم: <span class="font-semibold text-slate-700">{{ $task->designer->name }}</span></p>
                            @endif
                            <p class="mt-1 text-xs text-teal-700 font-semibold schedule-label min-h-[1.25rem]">
                                {{ $task->publish_schedule_label ?: 'لم يُحدد موعد بعد' }}
                            </p>
                        </button>

                    <div class="px-4 pb-4 pt-3 space-y-3 border-t border-slate-100 mt-3" data-schedule-box="{{ $task->id }}">
                        <div class="flex flex-wrap gap-1.5">
                            <button type="button" class="date-chip px-2.5 py-1 rounded-lg text-[11px] font-bold border border-teal-200 text-teal-800 bg-teal-50" data-date="{{ $today }}">اليوم</button>
                            <button type="button" class="date-chip px-2.5 py-1 rounded-lg text-[11px] font-bold border border-slate-200 text-slate-700 bg-white" data-date="{{ $tomorrow }}">بكرة</button>
                            <button type="button" class="date-chip px-2.5 py-1 rounded-lg text-[11px] font-bold border border-slate-200 text-slate-700 bg-white" data-date="{{ $dayAfter }}">بعد بكرة</button>
                        </div>
                        <div class="grid grid-cols-2 gap-2">
                            <label class="block">
                                <span class="text-[10px] text-slate-400 mb-0.5 block">اليوم</span>
                                <input type="date" class="schedule-date w-full px-2.5 py-2 rounded-xl border border-slate-200 text-sm"
                                       value="{{ $task->publish_date?->format('Y-m-d') }}">
                            </label>
                            <label class="block">
                                <span class="text-[10px] text-slate-400 mb-0.5 block">الوقت</span>
                                <input type="time" class="schedule-time w-full px-2.5 py-2 rounded-xl border border-slate-200 text-sm"
                                       value="{{ $task->publish_time_short }}">
                            </label>
                        </div>
                        <div class="flex flex-wrap gap-1.5">
                            @foreach(['09:00','12:00','15:00','18:00','21:00'] as $slot)
                                <button type="button" class="time-chip px-2 py-1 rounded-lg text-[11px] font-bold border border-slate-200 bg-white" data-time="{{ $slot }}">{{ $slot }}</button>
                            @endforeach
                        </div>
                        <div class="flex items-center justify-between gap-2">
                            <button type="button" data-open-review="{{ $task->id }}"
                                    class="inline-flex items-center gap-1 px-3 py-2 rounded-xl bg-indigo-50 text-indigo-700 text-xs font-bold">
                                <span class="material-icons text-sm">visibility</span>
                                عرض المحتوى
                            </button>
                            <span class="schedule-status text-[11px] text-slate-400"></span>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    @endif
</div>

<div id="readyReviewModal" class="review-modal" aria-hidden="true">
    <div class="review-shell">
        <div class="flex items-center justify-between gap-3 px-4 py-3 border-b">
            <div class="min-w-0">
                <p id="readyReviewTitle" class="font-extrabold text-slate-900 truncate"></p>
                <p id="readyReviewMeta" class="text-[11px] text-slate-500 mt-0.5"></p>
            </div>
            <button type="button" id="readyReviewClose" class="px-3 py-2 rounded-xl bg-slate-900 text-white text-xs font-bold">إغلاق</button>
        </div>
        <div class="review-body">
            <div class="review-media border-b md:border-b-0 md:border-l border-slate-200">
                <div id="readyReviewMedia" class="review-media-track" dir="ltr"></div>
            </div>
            <div class="review-content p-4 md:p-5 space-y-4 bg-slate-50">
                <section class="rounded-2xl bg-white border p-4">
                    <h3 class="text-[11px] font-bold text-slate-400 mb-2">المحتوى اللي هينزل</h3>
                    <p id="readyReviewCaption" class="text-sm leading-7 text-slate-800 whitespace-pre-line font-semibold"></p>
                </section>
                <section id="readyReviewTovBox" class="rounded-2xl bg-white border p-4 hidden">
                    <h3 class="text-[11px] font-bold text-slate-400 mb-2">Tone of Voice</h3>
                    <p id="readyReviewTov" class="text-sm leading-6 text-slate-700 whitespace-pre-line"></p>
                </section>
                <section class="rounded-2xl bg-teal-50 border border-teal-100 p-4">
                    <h3 class="text-[11px] font-bold text-teal-700 mb-2">موعد النشر</h3>
                    <p id="readyReviewSchedule" class="text-sm font-bold text-teal-900"></p>
                </section>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script type="application/json" id="readyReviewData">{!! json_encode($reviewPayload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
<script>
(function () {
    const payload = JSON.parse(document.getElementById('readyReviewData')?.textContent || '{}');
    const scheduleUrlTpl = @json($scheduleUrlTpl);
    const todayDate = @json($today);
    const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    function saveSchedule(taskId, box) {
        const dateInput = box.querySelector('.schedule-date');
        const timeInput = box.querySelector('.schedule-time');
        const status = box.querySelector('.schedule-status');
        const card = box.closest('[data-task-card]');
        const labelEl = card?.querySelector('.schedule-label');
        const body = new URLSearchParams();
        body.set('publish_date', dateInput.value || '');
        body.set('publish_time', timeInput.value || '');
        body.set('_token', csrf);
        status.textContent = 'جاري الحفظ...';
        status.className = 'schedule-status text-[11px] text-amber-600';

        return fetch(scheduleUrlTpl.replace('TASK_ID', taskId), {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrf,
                'Accept': 'application/json',
                'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
                'X-Requested-With': 'XMLHttpRequest',
            },
            credentials: 'same-origin',
            body: body.toString(),
        }).then(async function (res) {
            const data = await res.json().catch(function () { return {}; });
            if (!res.ok || !data.success) throw new Error(data.message || 'فشل الحفظ');
            status.textContent = 'تم الحفظ';
            status.className = 'schedule-status text-[11px] text-teal-700';
            if (labelEl) labelEl.textContent = data.label || 'لم يُحدد موعد بعد';
            if (payload[taskId]) {
                payload[taskId].publish_date = data.publish_date;
                payload[taskId].publish_time = data.publish_time;
                payload[taskId].schedule_label = data.label;
            }
            setTimeout(function () { if (status.textContent === 'تم الحفظ') status.textContent = ''; }, 1600);
        }).catch(function (err) {
            status.textContent = err.message || 'خطأ';
            status.className = 'schedule-status text-[11px] text-red-600';
        });
    }

    document.querySelectorAll('[data-schedule-box]').forEach(function (box) {
        const taskId = box.getAttribute('data-schedule-box');
        let timer = null;
        function queueSave() {
            clearTimeout(timer);
            timer = setTimeout(function () { saveSchedule(taskId, box); }, 350);
        }
        box.querySelector('.schedule-date')?.addEventListener('change', queueSave);
        box.querySelector('.schedule-time')?.addEventListener('change', queueSave);
        box.querySelectorAll('.date-chip').forEach(function (btn) {
            btn.addEventListener('click', function () {
                box.querySelector('.schedule-date').value = btn.dataset.date || '';
                if (!box.querySelector('.schedule-time').value) box.querySelector('.schedule-time').value = '12:00';
                queueSave();
            });
        });
        box.querySelectorAll('.time-chip').forEach(function (btn) {
            btn.addEventListener('click', function () {
                box.querySelector('.schedule-time').value = btn.dataset.time || '';
                if (!box.querySelector('.schedule-date').value) box.querySelector('.schedule-date').value = todayDate;
                queueSave();
            });
        });
    });

    const modal = document.getElementById('readyReviewModal');
    const mediaEl = document.getElementById('readyReviewMedia');
    function closeModal() {
        modal.classList.remove('is-open');
        mediaEl.innerHTML = '';
        document.body.style.overflow = '';
    }
    function openModal(taskId) {
        const item = payload[String(taskId)];
        if (!item) return;
        document.getElementById('readyReviewTitle').textContent = item.title || '';
        const bits = [];
        if (item.typeLabel) bits.push(item.typeLabel);
        if (item.designerName) bits.push('تصميم: ' + item.designerName);
        document.getElementById('readyReviewMeta').textContent = bits.join(' · ');
        document.getElementById('readyReviewCaption').textContent = item.caption || 'مفيش كابشن';
        const tovBox = document.getElementById('readyReviewTovBox');
        if (item.tov) { tovBox.classList.remove('hidden'); document.getElementById('readyReviewTov').textContent = item.tov; }
        else tovBox.classList.add('hidden');
        document.getElementById('readyReviewSchedule').textContent = item.schedule_label || 'لم يُحدد موعد بعد';
        mediaEl.innerHTML = '';
        (item.slides || []).forEach(function (slide) {
            const wrap = document.createElement('div');
            wrap.className = 'review-slide';
            if (slide.kind === 'video') {
                const v = document.createElement('video');
                v.src = slide.url; v.controls = true; v.playsInline = true;
                wrap.appendChild(v);
            } else {
                const img = document.createElement('img');
                img.src = slide.url; img.alt = slide.name || '';
                wrap.appendChild(img);
            }
            mediaEl.appendChild(wrap);
        });
        modal.classList.add('is-open');
        document.body.style.overflow = 'hidden';
    }
    document.querySelectorAll('[data-open-review]').forEach(function (btn) {
        btn.addEventListener('click', function () { openModal(btn.getAttribute('data-open-review')); });
    });
    document.getElementById('readyReviewClose')?.addEventListener('click', closeModal);
    modal?.addEventListener('click', function (e) { if (e.target === modal) closeModal(); });
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && modal.classList.contains('is-open')) closeModal();
    });
})();
</script>
@endpush
