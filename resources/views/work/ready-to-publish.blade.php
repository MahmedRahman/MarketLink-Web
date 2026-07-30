@extends($workLayout ?? 'layouts.dashboard')

@section('title', 'جاهز للنشر — '.$activity->title)
@section('page-title', 'جاهز للنشر')
@section('page-description', $activity->title)

@section('content')
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
            'caption' => $task->caption,
            'tov' => $task->tov,
            'idea' => $task->idea,
            'platforms' => $task->platform_labels ?? [],
            'publish_date' => $task->publish_date?->format('Y-m-d'),
            'publish_time' => $task->publish_time_short,
            'schedule_label' => $task->publish_schedule_label,
            'taskUrl' => work_route('tasks.show', [$activity, $task]),
            'slides' => $media->map(function ($file) {
                return [
                    'url' => $file->file_url,
                    'name' => $file->file_name,
                    'kind' => $file->isVideo() ? 'video' : 'image',
                ];
            })->values()->all(),
        ];
    }
@endphp

<div class="max-w-6xl mx-auto space-y-5">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <a href="{{ work_route('show', $activity) }}"
           class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-teal-700">
            <span class="material-icons text-lg">arrow_forward</span>
            رجوع للبايبلاين
        </a>
        <div class="flex items-center gap-2 flex-wrap">
            @php
                $activity->ensureShareToken();
                $publicReadyUrl = $activity->public_ready_to_publish_url;
            @endphp
            @if($publicReadyUrl)
                <a href="{{ $publicReadyUrl }}" target="_blank" rel="noopener"
                   class="inline-flex items-center gap-1 px-3 py-2 rounded-xl bg-teal-600 text-white text-xs font-bold hover:bg-teal-700">
                    <span class="material-icons text-sm">open_in_new</span>
                    فتح الرابط العام
                </a>
                <button type="button"
                        data-share-url="{{ $publicReadyUrl }}"
                        onclick="navigator.clipboard.writeText(this.dataset.shareUrl).then(() => { this.querySelector('span.label').textContent='تم النسخ'; setTimeout(() => this.querySelector('span.label').textContent='نسخ الرابط', 1500); })"
                        class="inline-flex items-center gap-1 px-3 py-2 rounded-xl bg-teal-50 text-teal-800 text-xs font-bold hover:bg-teal-100">
                    <span class="material-icons text-sm">content_copy</span>
                    <span class="label">نسخ الرابط</span>
                </button>
            @endif
            <span class="text-xs font-bold px-2.5 py-1 rounded-lg bg-teal-50 text-teal-800">
                {{ $tasks->count() }} بوست جاهز للنشر
            </span>
        </div>
    </div>

    <section class="card rounded-2xl overflow-hidden">
        <div class="bg-gradient-to-l from-teal-700 via-teal-600 to-cyan-700 px-5 py-6 text-white">
            <p class="text-xs font-bold text-teal-100 mb-1 inline-flex items-center gap-1">
                <span class="material-icons text-sm">schedule_send</span>
                جدولة النشر
            </p>
            <h1 class="text-2xl md:text-3xl font-extrabold">{{ $activity->title }}</h1>
            <p class="text-sm text-teal-50/90 mt-2">اضغط البوست لعرض المحتوى، وحدّد اليوم والوقت من الكارت مباشرة</p>
        </div>
    </section>

    @if($tasks->isEmpty())
        <section class="card rounded-2xl p-10 text-center">
            <span class="material-icons text-5xl text-gray-300">inbox</span>
            <h2 class="text-lg font-bold text-gray-700 mt-3">مفيش بوستات جاهزة للنشر</h2>
            <p class="text-sm text-gray-500 mt-1">لما التاسكات توصل لمرحلة جاهز للنشر هتظهر هنا</p>
        </section>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($tasks as $task)
                @php
                    $cover = $task->files
                        ->filter(fn ($f) => $f->isImage() || $f->isVideo())
                        ->sortBy(fn ($f) => mb_strtolower((string) $f->file_name), SORT_NATURAL)
                        ->first();
                    $coverUrl = $cover?->file_url;
                @endphp
                <article class="card rounded-2xl border border-teal-100 overflow-hidden flex flex-col"
                         data-task-card="{{ $task->id }}">
                    <button type="button"
                            class="text-start w-full"
                            data-open-review="{{ $task->id }}">
                        <div class="aspect-[16/10] bg-slate-100 relative">
                            @if($cover && $cover->isImage())
                                <img src="{{ $coverUrl }}" alt="" class="w-full h-full object-cover" loading="lazy">
                            @elseif($cover && $cover->isVideo())
                                <video src="{{ $coverUrl }}" class="w-full h-full object-cover" muted playsinline preload="metadata"></video>
                                <span class="absolute inset-0 flex items-center justify-center bg-black/20">
                                    <span class="material-icons text-white text-4xl">play_circle</span>
                                </span>
                            @else
                                <div class="w-full h-full flex items-center justify-center text-slate-300">
                                    <span class="material-icons text-5xl">image</span>
                                </div>
                            @endif
                            <span class="absolute top-3 start-3 text-[11px] font-bold px-2 py-1 rounded-lg bg-white/90 text-teal-800">
                                {{ $task->content_type_label ?: 'محتوى' }}
                            </span>
                        </div>
                        <div class="px-4 pt-3">
                            <h3 class="font-bold text-gray-900 leading-snug line-clamp-2">{{ $task->title }}</h3>
                            <p class="mt-1 text-xs text-teal-700 font-semibold schedule-label min-h-[1.25rem]">
                                {{ $task->publish_schedule_label ?: 'لم يُحدد موعد بعد' }}
                            </p>
                        </div>
                    </button>

                    <div class="px-4 pb-4 pt-3 space-y-3 border-t border-gray-100 mt-3"
                         data-schedule-box="{{ $task->id }}">
                        <div class="flex flex-wrap gap-1.5">
                            <button type="button" class="date-chip px-2.5 py-1 rounded-lg text-[11px] font-bold border border-teal-200 text-teal-800 bg-teal-50 hover:bg-teal-100" data-date="{{ $today }}">اليوم</button>
                            <button type="button" class="date-chip px-2.5 py-1 rounded-lg text-[11px] font-bold border border-gray-200 text-gray-700 bg-white hover:bg-gray-50" data-date="{{ $tomorrow }}">بكرة</button>
                            <button type="button" class="date-chip px-2.5 py-1 rounded-lg text-[11px] font-bold border border-gray-200 text-gray-700 bg-white hover:bg-gray-50" data-date="{{ $dayAfter }}">بعد بكرة</button>
                        </div>
                        <div class="grid grid-cols-2 gap-2">
                            <label class="block">
                                <span class="text-[10px] text-gray-400 mb-0.5 block">اليوم</span>
                                <input type="date" class="schedule-date w-full px-2.5 py-2 rounded-xl border border-gray-200 text-sm focus:border-teal-500 focus:outline-none"
                                       value="{{ $task->publish_date?->format('Y-m-d') }}">
                            </label>
                            <label class="block">
                                <span class="text-[10px] text-gray-400 mb-0.5 block">الوقت</span>
                                <input type="time" class="schedule-time w-full px-2.5 py-2 rounded-xl border border-gray-200 text-sm focus:border-teal-500 focus:outline-none"
                                       value="{{ $task->publish_time_short }}">
                            </label>
                        </div>
                        <div class="flex flex-wrap gap-1.5">
                            @foreach(['09:00','12:00','15:00','18:00','21:00'] as $slot)
                                <button type="button" class="time-chip px-2 py-1 rounded-lg text-[11px] font-bold border border-gray-200 text-gray-700 bg-white hover:bg-teal-50 hover:border-teal-300 hover:text-teal-800" data-time="{{ $slot }}">{{ $slot }}</button>
                            @endforeach
                        </div>
                        <div class="flex items-center justify-between gap-2">
                            <button type="button"
                                    data-open-review="{{ $task->id }}"
                                    class="inline-flex items-center gap-1 px-3 py-2 rounded-xl bg-indigo-50 text-indigo-700 text-xs font-bold hover:bg-indigo-100">
                                <span class="material-icons text-sm">visibility</span>
                                عرض المحتوى
                            </button>
                            <span class="schedule-status text-[11px] text-gray-400"></span>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    @endif
</div>

{{-- مودال المحتوى --}}
<div id="readyReviewModal" class="hidden fixed inset-0 z-50 bg-slate-900/80 p-3 md:p-6 overflow-y-auto">
    <div class="max-w-5xl mx-auto bg-white rounded-2xl overflow-hidden shadow-2xl my-4">
        <div class="flex items-center justify-between gap-3 px-4 py-3 border-b border-gray-200">
            <div class="min-w-0">
                <h2 id="readyReviewTitle" class="font-extrabold text-gray-900 truncate"></h2>
                <p id="readyReviewMeta" class="text-xs text-gray-500 mt-0.5"></p>
            </div>
            <button type="button" id="readyReviewClose" class="px-3 py-2 rounded-xl bg-slate-900 text-white text-xs font-bold">إغلاق</button>
        </div>
        <div class="grid md:grid-cols-2">
            <div id="readyReviewMedia" class="bg-slate-900 min-h-[240px] p-4 flex items-center justify-center gap-3 overflow-x-auto"></div>
            <div class="p-4 md:p-5 space-y-4 bg-slate-50">
                <section class="rounded-2xl bg-white border border-gray-200 p-4">
                    <h3 class="text-[11px] font-bold text-gray-400 mb-2">المحتوى اللي هينزل</h3>
                    <p id="readyReviewCaption" class="text-sm leading-7 text-gray-800 whitespace-pre-line font-semibold"></p>
                </section>
                <section id="readyReviewTovBox" class="rounded-2xl bg-white border border-gray-200 p-4 hidden">
                    <h3 class="text-[11px] font-bold text-gray-400 mb-2">Tone of Voice</h3>
                    <p id="readyReviewTov" class="text-sm leading-6 text-gray-700 whitespace-pre-line"></p>
                </section>
                <section id="readyReviewIdeaBox" class="rounded-2xl bg-white border border-gray-200 p-4 hidden">
                    <h3 class="text-[11px] font-bold text-gray-400 mb-2">الفكرة</h3>
                    <p id="readyReviewIdea" class="text-sm leading-6 text-gray-700 whitespace-pre-line"></p>
                </section>
                <section class="rounded-2xl bg-teal-50 border border-teal-100 p-4">
                    <h3 class="text-[11px] font-bold text-teal-700 mb-2">موعد النشر</h3>
                    <p id="readyReviewSchedule" class="text-sm font-bold text-teal-900"></p>
                </section>
                <a id="readyReviewTaskLink" href="#" class="inline-flex items-center gap-1 text-xs font-bold text-indigo-700 hover:text-indigo-900">
                    <span class="material-icons text-sm">open_in_new</span>
                    فتح صفحة التاسك
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
@php
    $scheduleUrlTpl = work_route('tasks.publish-schedule', [$activity, 'TASK_ID'], false);
@endphp
<script type="application/json" id="readyReviewData">{!! json_encode($reviewPayload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
<script>
(function () {
    const payload = JSON.parse(document.getElementById('readyReviewData')?.textContent || '{}');
    const scheduleUrlTpl = @json($scheduleUrlTpl);
    const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    const todayDate = @json($today);

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
            setTimeout(function () {
                if (status.textContent === 'تم الحفظ') status.textContent = '';
            }, 1800);
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
                const input = box.querySelector('.schedule-date');
                if (!input) return;
                input.value = btn.dataset.date || '';
                if (!box.querySelector('.schedule-time').value) {
                    box.querySelector('.schedule-time').value = '12:00';
                }
                queueSave();
            });
        });
        box.querySelectorAll('.time-chip').forEach(function (btn) {
            btn.addEventListener('click', function () {
                const input = box.querySelector('.schedule-time');
                const dateInput = box.querySelector('.schedule-date');
                if (!input) return;
                input.value = btn.dataset.time || '';
                if (!dateInput.value) dateInput.value = todayDate;
                queueSave();
            });
        });
    });

    const modal = document.getElementById('readyReviewModal');
    const mediaEl = document.getElementById('readyReviewMedia');
    function closeModal() {
        modal.classList.add('hidden');
        document.body.style.overflow = '';
        mediaEl.innerHTML = '';
    }
    function openModal(taskId) {
        const item = payload[String(taskId)];
        if (!item) return;
        document.getElementById('readyReviewTitle').textContent = item.title || '';
        document.getElementById('readyReviewMeta').textContent = item.typeLabel || '';
        document.getElementById('readyReviewCaption').textContent = item.caption || 'مفيش كابشن';
        const tovBox = document.getElementById('readyReviewTovBox');
        const ideaBox = document.getElementById('readyReviewIdeaBox');
        if (item.tov) {
            tovBox.classList.remove('hidden');
            document.getElementById('readyReviewTov').textContent = item.tov;
        } else tovBox.classList.add('hidden');
        if (item.idea) {
            ideaBox.classList.remove('hidden');
            document.getElementById('readyReviewIdea').textContent = item.idea;
        } else ideaBox.classList.add('hidden');
        document.getElementById('readyReviewSchedule').textContent = item.schedule_label || 'لم يُحدد موعد بعد';
        const link = document.getElementById('readyReviewTaskLink');
        link.href = item.taskUrl || '#';
        mediaEl.innerHTML = '';
        (item.slides || []).forEach(function (slide) {
            if (slide.kind === 'video') {
                const v = document.createElement('video');
                v.src = slide.url;
                v.controls = true;
                v.className = 'max-h-[420px] rounded-xl';
                mediaEl.appendChild(v);
            } else {
                const img = document.createElement('img');
                img.src = slide.url;
                img.alt = slide.name || '';
                img.className = 'max-h-[420px] rounded-xl object-contain';
                mediaEl.appendChild(img);
            }
        });
        if (!(item.slides || []).length) {
            mediaEl.innerHTML = '<p class="text-slate-400 text-sm">مفيش ملفات تصميم</p>';
        }
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    document.querySelectorAll('[data-open-review]').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            openModal(btn.getAttribute('data-open-review'));
        });
    });
    document.getElementById('readyReviewClose')?.addEventListener('click', closeModal);
    modal?.addEventListener('click', function (e) {
        if (e.target === modal) closeModal();
    });
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && !modal.classList.contains('hidden')) closeModal();
    });
})();
</script>
@endsection
