@extends('layouts.employee')

@section('title', $activity->title)
@section('page-title', 'مساحة العمل')
@section('page-description', $activity->title)

@section('content')
<div class="max-w-6xl mx-auto space-y-6">

    <a href="{{ route('employee.tasks.index') }}" class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-primary">
        <span class="material-icons text-lg">arrow_forward</span>
        رجوع لمساحة العمل
    </a>

    {{-- رأس النشاط --}}
    <div class="card rounded-2xl p-6">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-indigo-50 text-primary flex items-center justify-center">
                <span class="material-icons text-3xl">{{ $activity->type_icon }}</span>
            </div>
            <div>
                <h2 class="text-xl font-bold text-gray-800">{{ $activity->title }}</h2>
                <div class="flex items-center gap-2 mt-1 text-sm text-gray-500 flex-wrap">
                    <span>{{ $activity->type_label }}</span>
                    @if($activity->event_date)
                        <span>·</span>
                        <span class="flex items-center gap-1"><span class="material-icons text-sm">event</span>{{ $activity->event_date->format('Y/m/d') }}</span>
                    @endif
                    <span>·</span>
                    <span class="role-badge role-{{ $activity->status_color }}">{{ $activity->status_label }}</span>
                </div>
            </div>
        </div>

        @if($activity->description)
            <p class="text-sm text-gray-600 mt-4 bg-gray-50 rounded-xl p-3 whitespace-pre-line">{{ $activity->description }}</p>
        @endif

        <div class="mt-4">
            <div class="flex items-center justify-between text-xs text-gray-500 mb-1">
                <span>تقدّم مهامك ({{ $doneCount }} / {{ $contentCounts['total'] }})</span>
                <span>{{ $progress }}%</span>
            </div>
            <div class="w-full h-2.5 bg-gray-100 rounded-full overflow-hidden">
                <div class="h-full bg-gradient-to-l from-indigo-500 to-purple-500 rounded-full" style="width: {{ $progress }}%"></div>
            </div>
        </div>
    </div>

    <div class="space-y-4">
        <div>
            <h3 class="font-bold text-gray-800">مهامك في النشاط ({{ $contentCounts['total'] }})</h3>
            <p class="text-xs text-gray-500 mt-1">اسحب الكارت لترتيب داخل المرحلة، أو أفلت على مرحلة تانية للنقل</p>
            @if($contentCounts['total'] === 0)
                <div class="mt-3 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
                    مفيش مهام مطلوبة منك في المرحلة الحالية — ممكن تكون اتنقّلت لمرحلة تانية أو لزميل تاني.
                </div>
            @endif
            <div class="flex flex-wrap items-center gap-2 mt-2">
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-blue-50 text-blue-700 text-xs font-semibold border border-blue-100">
                    <span class="material-icons text-sm">article</span>
                    بوست {{ $contentCounts['post'] }}
                </span>
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-rose-50 text-rose-700 text-xs font-semibold border border-rose-100">
                    <span class="material-icons text-sm">movie</span>
                    ريلز {{ $contentCounts['reels'] }}
                </span>
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-amber-50 text-amber-700 text-xs font-semibold border border-amber-100">
                    <span class="material-icons text-sm">view_carousel</span>
                    كروسيل {{ $contentCounts['carousel'] }}
                </span>
                @if($contentCounts['other'] > 0)
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-gray-50 text-gray-600 text-xs font-semibold border border-gray-200">
                        أخرى {{ $contentCounts['other'] }}
                    </span>
                @endif
            </div>
        </div>

        <div class="inline-flex items-center rounded-xl border border-gray-200 bg-gray-50 p-1 mb-2">
            <a href="{{ route('employee.work.activity', [$activity, 'board' => 'pipeline']) }}"
               class="px-3 py-1.5 rounded-lg text-xs font-bold inline-flex items-center gap-1 {{ ($boardView ?? 'pipeline') === 'pipeline' ? 'bg-white text-indigo-700 shadow-sm' : 'text-gray-600' }}">
                <span class="material-icons text-sm">view_kanban</span>
                البايبلاين
            </a>
            <a href="{{ route('employee.work.activity', [$activity, 'board' => 'archive']) }}"
               class="px-3 py-1.5 rounded-lg text-xs font-bold inline-flex items-center gap-1 {{ ($boardView ?? 'pipeline') === 'archive' ? 'bg-white text-slate-800 shadow-sm' : 'text-gray-600' }}">
                <span class="material-icons text-sm">inventory_2</span>
                الأرشيف
                <span class="px-1.5 py-0.5 rounded-md bg-slate-200 text-slate-700">{{ $contentCounts['archived'] ?? 0 }}</span>
            </a>
        </div>

        @if(($boardView ?? 'pipeline') === 'archive')
            <div class="space-y-3">
                @forelse(($archivedTasks ?? collect()) as $task)
                    <a href="{{ route('employee.work.show', $task) }}"
                       class="card rounded-2xl p-4 block hover:border-slate-300 border border-slate-200">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <span class="inline-block px-2 py-0.5 text-[10px] rounded-md bg-slate-100 text-slate-700 mb-1.5">أرشيف</span>
                                <h5 class="font-bold text-gray-900">{{ $task->title }}</h5>
                                @if($task->content_type_label)
                                    <p class="text-xs text-gray-500 mt-1">{{ $task->content_type_label }}</p>
                                @endif
                            </div>
                            <span class="material-icons text-slate-400">chevron_left</span>
                        </div>
                    </a>
                @empty
                    <div class="card rounded-2xl p-10 text-center text-sm text-slate-500">
                        مفيش مهام مؤرشفة لك هنا
                    </div>
                @endforelse
            </div>
        @else
        <div class="space-y-4" id="pipelineBoard">
            @foreach($pipelineStages as $stage)
                <section class="card rounded-2xl overflow-hidden pipeline-stage" data-stage="{{ $stage['key'] }}">
                    <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between gap-3
                        @if($stage['key'] === 'planning') bg-amber-50
                        @elseif($stage['key'] === 'writing') bg-blue-50
                        @elseif($stage['key'] === 'design') bg-purple-50
                        @elseif($stage['key'] === 'ready_to_publish') bg-teal-50
                        @else bg-green-50
                        @endif">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center
                                @if($stage['key'] === 'planning') bg-amber-100 text-amber-700
                                @elseif($stage['key'] === 'writing') bg-blue-100 text-blue-700
                                @elseif($stage['key'] === 'design') bg-purple-100 text-purple-700
                                @elseif($stage['key'] === 'ready_to_publish') bg-teal-100 text-teal-700
                                @else bg-green-100 text-green-700
                                @endif">
                                <span class="material-icons">{{ $stage['icon'] }}</span>
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-800">{{ $stage['label'] }}</h4>
                                <p class="text-xs text-gray-500">
                                    @if($stage['key'] === 'planning') تخطيط المحتوى قبل الكتابة
                                    @elseif($stage['key'] === 'writing') عند كاتب المحتوى
                                    @elseif($stage['key'] === 'design') عند فريق التصميم
                                    @elseif($stage['key'] === 'ready_to_publish') جاهز للنشر — اسحب البوست هنا بعد التصميم
                                    @else تم النشر
                                    @endif
                                    · <span class="stage-count">{{ $stage['count'] }}</span> مهمة لك
                                </p>
                            </div>
                        </div>
                        <span class="stage-count-badge px-2.5 py-1 rounded-lg text-xs font-bold
                            @if($stage['key'] === 'planning') bg-amber-100 text-amber-700
                            @elseif($stage['key'] === 'writing') bg-blue-100 text-blue-700
                            @elseif($stage['key'] === 'design') bg-purple-100 text-purple-700
                            @elseif($stage['key'] === 'ready_to_publish') bg-teal-100 text-teal-700
                            @else bg-green-100 text-green-700
                            @endif">
                            {{ $stage['count'] }}
                        </span>
                    </div>

                    <div class="p-4 stage-dropzone min-h-[120px] transition-colors" data-stage="{{ $stage['key'] }}">
                        <div class="stage-empty rounded-xl border border-dashed px-4 py-6 text-center mb-3
                            @if($stage['key'] === 'ready_to_publish') border-teal-200 bg-teal-50/40
                            @elseif($stage['key'] === 'design') border-purple-200 bg-purple-50/40
                            @elseif($stage['key'] === 'published') border-green-200 bg-green-50/40
                            @elseif($stage['key'] === 'planning') border-amber-200 bg-amber-50/40
                            @else border-blue-200 bg-blue-50/40
                            @endif
                            {{ $stage['count'] > 0 ? 'hidden' : '' }}">
                            <span class="material-icons text-2xl mb-1
                                @if($stage['key'] === 'ready_to_publish') text-teal-400
                                @elseif($stage['key'] === 'design') text-purple-400
                                @elseif($stage['key'] === 'published') text-green-400
                                @elseif($stage['key'] === 'planning') text-amber-400
                                @else text-blue-400
                                @endif">{{ $stage['icon'] }}</span>
                            <p class="text-sm font-medium text-gray-700">مفيش مهام هنا حاليًا</p>
                            <p class="text-xs text-gray-500 mt-1">اسحب كارت وأفلته هنا للنقل</p>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 stage-cards">
                            @foreach($stage['tasks'] as $task)
                                @php
                                    $stColors = [
                                        'todo' => 'bg-gray-100 text-gray-700',
                                        'in_progress' => 'bg-blue-100 text-blue-700',
                                        'executed' => 'bg-indigo-100 text-indigo-800',
                                        'review' => 'bg-yellow-100 text-yellow-700',
                                        'done' => 'bg-green-100 text-green-700',
                                    ];
                                @endphp
                                <div role="link" tabindex="0"
                                     draggable="true"
                                     data-task-id="{{ $task->id }}"
                                     data-stage="{{ $stage['key'] }}"
                                     data-href="{{ route('employee.work.show', $task) }}"
                                     class="pipeline-card rounded-2xl border border-gray-200 bg-white p-4 min-h-[110px] flex flex-col justify-between hover:border-indigo-300 hover:shadow-md transition-all cursor-grab active:cursor-grabbing {{ $task->is_overdue ? 'border-r-4 border-r-red-400' : '' }}">
                                    <div>
                                        @if($task->content_type_label)
                                            <span class="inline-block px-2 py-0.5 text-[10px] rounded-md bg-indigo-50 text-indigo-700 mb-2">{{ $task->content_type_label }}</span>
                                        @endif
                                        <h5 class="text-base font-bold text-gray-900 leading-snug line-clamp-3">
                                            {{ $task->title }}
                                        </h5>
                                    </div>
                                    <div class="mt-3 flex items-center justify-between gap-2">
                                        <span class="px-2 py-0.5 text-[11px] rounded-lg {{ $stColors[$task->status] ?? $stColors['todo'] }}">
                                            {{ $task->status_label }}
                                        </span>
                                        <div class="flex items-center gap-1">
                                            @if($activity->share_token)
                                                <button type="button"
                                                        class="card-share-btn p-1.5 rounded-lg bg-indigo-50 text-indigo-700 hover:bg-indigo-100"
                                                        data-share-url="{{ $activity->publicTaskUrl($task) }}"
                                                        title="نسخ رابط شير الكارت"
                                                        draggable="false">
                                                    <span class="material-icons text-sm">share</span>
                                                </button>
                                            @endif
                                            @if($task->due_date)
                                                <span class="text-[11px] text-gray-500 flex items-center gap-0.5">
                                                    <span class="material-icons text-sm">event</span>
                                                    {{ $task->due_date->format('m/d') }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </section>
            @endforeach
        </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script>
(function initPipelineDragDrop() {
    const board = document.getElementById('pipelineBoard');
    if (!board) return;

    @php
        $employeeMoveUrlTpl = str_replace(
            '999999',
            'TASK_ID',
            route('employee.work.move-stage', [$activity, 999999])
        );
        $employeeReorderUrl = route('employee.work.reorder', $activity);
    @endphp
    const moveUrlTpl = @json($employeeMoveUrlTpl);
    const reorderUrl = @json($employeeReorderUrl);
    let dragTaskId = null;
    let dragFromStage = null;
    let dragCard = null;
    let dragFromIndex = null;
    let didDrag = false;

    function csrfToken() {
        return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    }

    function refreshStageCounts() {
        board.querySelectorAll('.pipeline-stage').forEach(function (section) {
            const zone = section.querySelector('.stage-dropzone');
            const count = zone.querySelectorAll('.pipeline-card').length;
            section.querySelectorAll('.stage-count').forEach(function (el) { el.textContent = count; });
            section.querySelectorAll('.stage-count-badge').forEach(function (el) { el.textContent = count; });
            const empty = zone.querySelector('.stage-empty');
            if (empty) empty.classList.toggle('hidden', count > 0);
        });
    }

    function stageTaskIds(stage) {
        const zone = board.querySelector('.stage-dropzone[data-stage="' + stage + '"]');
        if (!zone) return [];
        return Array.from(zone.querySelectorAll('.pipeline-card')).map(function (card) {
            return card.dataset.taskId;
        });
    }

    function placeCard(cardsWrap, card, clientX, clientY) {
        const others = Array.from(cardsWrap.querySelectorAll('.pipeline-card')).filter(function (el) {
            return el !== card;
        });
        if (!others.length) {
            cardsWrap.appendChild(card);
            return;
        }

        let closest = null;
        let closestOffset = Number.NEGATIVE_INFINITY;
        others.forEach(function (child) {
            const box = child.getBoundingClientRect();
            const offset = clientY - (box.top + box.height / 2);
            if (offset < 0 && offset > closestOffset) {
                closestOffset = offset;
                closest = child;
            }
        });

        if (closest) {
            cardsWrap.insertBefore(card, closest);
        } else {
            cardsWrap.appendChild(card);
        }
    }

    async function postForm(url, fields) {
        const body = new URLSearchParams();
        Object.keys(fields).forEach(function (key) {
            const value = fields[key];
            if (Array.isArray(value)) {
                value.forEach(function (item) { body.append(key + '[]', item); });
            } else {
                body.set(key, value);
            }
        });
        body.set('_token', csrfToken());
        const res = await fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken(),
                'Accept': 'application/json',
                'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
                'X-Requested-With': 'XMLHttpRequest',
            },
            credentials: 'same-origin',
            body: body.toString(),
        });
        let data = {};
        try { data = await res.json(); } catch (_) {}
        if (!res.ok || data.success === false) {
            throw new Error(data.message || data.error || ('فشل الطلب (' + res.status + ')'));
        }
        return data;
    }

    board.addEventListener('dragstart', function (e) {
        const card = e.target.closest('.pipeline-card');
        if (!card) return;
        dragTaskId = card.dataset.taskId;
        dragFromStage = card.dataset.stage;
        dragCard = card;
        dragFromIndex = Array.from(card.parentElement?.children || []).indexOf(card);
        didDrag = true;
        card.classList.add('opacity-50');
        e.dataTransfer.effectAllowed = 'move';
        e.dataTransfer.setData('text/plain', String(dragTaskId));
    });

    board.addEventListener('dragend', function (e) {
        const card = e.target.closest('.pipeline-card');
        if (card) card.classList.remove('opacity-50');
        board.querySelectorAll('.stage-dropzone').forEach(function (z) {
            z.classList.remove('bg-indigo-50', 'ring-2', 'ring-indigo-300', 'ring-inset');
        });
        setTimeout(function () {
            dragTaskId = null;
            dragFromStage = null;
            dragCard = null;
            dragFromIndex = null;
            didDrag = false;
        }, 50);
    });

    board.addEventListener('dragover', function (e) {
        const zone = e.target.closest('.stage-dropzone');
        if (!zone || !dragTaskId || !dragCard) return;
        e.preventDefault();
        e.dataTransfer.dropEffect = 'move';
        board.querySelectorAll('.stage-dropzone').forEach(function (z) {
            z.classList.toggle('bg-indigo-50', z === zone);
            z.classList.toggle('ring-2', z === zone);
            z.classList.toggle('ring-indigo-300', z === zone);
            z.classList.toggle('ring-inset', z === zone);
        });

        const cardsWrap = zone.querySelector('.stage-cards');
        if (cardsWrap) placeCard(cardsWrap, dragCard, e.clientX, e.clientY);
        refreshStageCounts();
    });

    board.addEventListener('dragleave', function (e) {
        const zone = e.target.closest('.stage-dropzone');
        if (!zone) return;
        if (zone.contains(e.relatedTarget)) return;
        zone.classList.remove('bg-indigo-50', 'ring-2', 'ring-indigo-300', 'ring-inset');
    });

    board.addEventListener('drop', async function (e) {
        e.preventDefault();
        e.stopPropagation();

        const zone = e.target.closest('.stage-dropzone');
        if (!zone || !dragTaskId || !dragCard) return;
        zone.classList.remove('bg-indigo-50', 'ring-2', 'ring-indigo-300', 'ring-inset');

        const toStage = zone.dataset.stage;
        const fromStage = dragFromStage;
        const taskId = dragTaskId;
        const card = dragCard;
        const fromIndex = dragFromIndex;
        const fromZone = board.querySelector('.stage-dropzone[data-stage="' + fromStage + '"]');
        const cardsWrap = zone.querySelector('.stage-cards');
        if (!cardsWrap) return;

        placeCard(cardsWrap, card, e.clientX, e.clientY);
        card.dataset.stage = toStage;
        refreshStageCounts();

        const newIds = stageTaskIds(toStage);
        const newIndex = newIds.indexOf(String(taskId));
        const sameStage = toStage === fromStage;
        const samePosition = sameStage && newIndex === fromIndex;
        if (samePosition) return;

        try {
            let moveData = null;
            if (!sameStage) {
                moveData = await postForm(moveUrlTpl.replace('TASK_ID', taskId), {
                    pipeline_stage: toStage,
                });
            }

            if (moveData && moveData.removed) {
                card.remove();
                refreshStageCounts();
                return;
            }

            const reorderIds = stageTaskIds(toStage);
            if (reorderIds.length) {
                await postForm(reorderUrl, {
                    pipeline_stage: toStage,
                    task_ids: reorderIds,
                });
            }
        } catch (err) {
            if (fromZone) {
                const fromWrap = fromZone.querySelector('.stage-cards');
                if (fromWrap) {
                    const children = Array.from(fromWrap.children);
                    if (fromIndex != null && fromIndex < children.length) {
                        fromWrap.insertBefore(card, children[fromIndex]);
                    } else {
                        fromWrap.appendChild(card);
                    }
                }
                card.dataset.stage = fromStage;
                refreshStageCounts();
            }
            alert(err.message || 'حدث خطأ أثناء ترتيب الكارت');
        }
    });

    board.addEventListener('click', function (e) {
        if (e.target.closest('.card-share-btn')) {
            e.preventDefault();
            e.stopPropagation();
            return;
        }
        const card = e.target.closest('.pipeline-card');
        if (!card) return;
        if (didDrag) {
            e.preventDefault();
            e.stopPropagation();
            didDrag = false;
            return;
        }
        const href = card.dataset.href;
        if (href) window.location.href = href;
    });

    board.addEventListener('keydown', function (e) {
        if (e.key !== 'Enter' && e.key !== ' ') return;
        const card = e.target.closest('.pipeline-card');
        if (!card?.dataset.href) return;
        e.preventDefault();
        window.location.href = card.dataset.href;
    });
})();
</script>
@endsection
