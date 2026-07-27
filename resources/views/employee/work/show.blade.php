@extends('layouts.employee')

@section('title', $task->title)
@section('page-title', 'تفاصيل المهمة')
@section('page-description', $task->activity->title ?? 'مساحة العمل')

@section('content')
@php
    $stageColor = $task->pipeline_stage_color ?? 'blue';
    $kindColors = [
        'design' => 'bg-purple-100 text-purple-700',
        'video' => 'bg-red-100 text-red-700',
        'content' => 'bg-blue-100 text-blue-700',
        'publish' => 'bg-teal-100 text-teal-700',
        'other' => 'bg-gray-100 text-gray-700',
    ];
    $stColors = [
        'todo' => 'bg-gray-100 text-gray-700',
        'in_progress' => 'bg-blue-100 text-blue-700',
        'review' => 'bg-yellow-100 text-yellow-700',
        'done' => 'bg-green-100 text-green-700',
    ];
    $stageBg = match($task->pipeline_stage) {
        'design' => 'from-purple-500/10 via-white to-white',
        'ready_to_publish' => 'from-teal-500/10 via-white to-white',
        'published' => 'from-green-500/10 via-white to-white',
        default => 'from-indigo-500/10 via-white to-white',
    };
@endphp

<div class="max-w-3xl mx-auto space-y-5">

    <a href="{{ $task->activity ? route('employee.work.activity', $task->activity) : route('employee.tasks.index') }}"
       class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-indigo-600 transition-colors">
        <span class="material-icons text-lg">arrow_forward</span>
        رجوع للنشاط
    </a>

    {{-- رأس المهمة --}}
    <div class="card rounded-2xl overflow-hidden">
        <div class="bg-gradient-to-b {{ $stageBg }} p-6 md:p-7">
            <div class="flex items-start justify-between gap-4 flex-wrap">
                <div class="min-w-0 flex-1">
                    @if($task->activity)
                        <p class="inline-flex items-center gap-1.5 text-xs font-medium text-indigo-600 mb-3">
                            <span class="material-icons text-sm">folder_open</span>
                            {{ $task->activity->title }}
                        </p>
                    @endif

                    <div class="flex items-center gap-2 flex-wrap mb-3">
                        @if($task->pipeline_stage_label ?? null)
                            <span class="role-badge role-{{ $stageColor }}">{{ $task->pipeline_stage_label }}</span>
                        @endif
                        <span class="px-2.5 py-1 text-xs font-semibold rounded-lg {{ $kindColors[$task->kind] ?? $kindColors['other'] }}">{{ $task->kind_label }}</span>
                        <span class="px-2.5 py-1 text-xs font-semibold rounded-lg {{ $stColors[$task->status] ?? $stColors['todo'] }}">{{ $task->status_label }}</span>
                        @if($task->content_type_label)
                            <span class="px-2.5 py-1 text-xs font-semibold rounded-lg bg-indigo-100 text-indigo-700">{{ $task->content_type_label }}</span>
                        @endif
                        @if($task->is_overdue)
                            <span class="px-2.5 py-1 text-xs font-semibold rounded-lg bg-red-100 text-red-700">متأخرة</span>
                        @endif
                    </div>

                    <h2 class="text-2xl md:text-3xl font-extrabold text-gray-900 leading-snug">{{ $task->title }}</h2>
                </div>
            </div>

            @if($task->due_date || $task->publish_date || !empty($task->platform_labels))
                <div class="mt-5 pt-4 border-t border-gray-200/70 flex flex-wrap gap-3">
                    @if($task->due_date)
                        <div class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-white/80 border border-gray-100 text-sm text-gray-700">
                            <span class="material-icons text-base text-indigo-500">event</span>
                            <span>التسليم <strong class="font-semibold">{{ $task->due_date->format('Y/m/d') }}</strong></span>
                        </div>
                    @endif
                    @if($task->publish_date)
                        <div class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-white/80 border border-gray-100 text-sm text-gray-700">
                            <span class="material-icons text-base text-teal-500">campaign</span>
                            <span>النشر <strong class="font-semibold">{{ $task->publish_date->format('Y/m/d') }}</strong></span>
                        </div>
                    @endif
                    @foreach($task->platform_labels ?? [] as $plat)
                        <span class="inline-flex items-center px-3 py-2 rounded-xl bg-white/80 border border-gray-100 text-xs font-medium text-gray-600">{{ $plat }}</span>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- المحتوى --}}
    <div class="space-y-4">
        @if($task->idea)
            <section class="card rounded-2xl p-5">
                <div class="flex items-center gap-2 mb-3">
                    <div class="w-9 h-9 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                        <span class="material-icons text-xl">lightbulb</span>
                    </div>
                    <h3 class="font-bold text-gray-800">الفكرة</h3>
                </div>
                <p class="text-sm md:text-base text-gray-700 leading-8 whitespace-pre-line">{{ $task->idea }}</p>
            </section>
        @endif

        @if($task->tov)
            <section class="rounded-2xl border border-violet-200 bg-violet-50/70 p-5">
                <div class="flex items-center gap-2 mb-3">
                    <div class="w-9 h-9 rounded-xl bg-violet-100 text-violet-700 flex items-center justify-center">
                        <span class="material-icons text-xl">record_voice_over</span>
                    </div>
                    <div>
                        <h3 class="font-bold text-violet-900">TOV</h3>
                        <p class="text-[11px] text-violet-600">Tone of Voice</p>
                    </div>
                </div>
                <p class="text-sm md:text-base text-violet-950 leading-8 whitespace-pre-line font-medium">{{ $task->tov }}</p>
            </section>
        @endif

        @if($task->caption)
            <section class="rounded-2xl border border-sky-200 bg-sky-50/70 p-5">
                <div class="flex items-center gap-2 mb-3">
                    <div class="w-9 h-9 rounded-xl bg-sky-100 text-sky-700 flex items-center justify-center">
                        <span class="material-icons text-xl">notes</span>
                    </div>
                    <div>
                        <h3 class="font-bold text-sky-900">Caption</h3>
                        <p class="text-[11px] text-sky-600">نص المنشور</p>
                    </div>
                </div>
                <p class="text-sm md:text-base text-sky-950 leading-8 whitespace-pre-line font-medium">{{ $task->caption }}</p>
            </section>
        @endif

        @if($task->design_reference || $task->designer_brief)
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @if($task->design_reference)
                    <section class="card rounded-2xl p-5">
                        <div class="flex items-center gap-2 mb-3">
                            <div class="w-9 h-9 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center">
                                <span class="material-icons text-xl">palette</span>
                            </div>
                            <h3 class="font-bold text-gray-800">مرجع التصميم</h3>
                        </div>
                        <p class="text-sm text-gray-700 leading-7 whitespace-pre-line">{{ $task->design_reference }}</p>
                    </section>
                @endif
                @if($task->designer_brief)
                    <section class="rounded-2xl border border-amber-200 bg-amber-50 p-5">
                        <div class="flex items-center gap-2 mb-3">
                            <div class="w-9 h-9 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center">
                                <span class="material-icons text-xl">tips_and_updates</span>
                            </div>
                            <h3 class="font-bold text-amber-900">مطلوب من المصمم</h3>
                        </div>
                        <p class="text-sm text-amber-950 leading-7 whitespace-pre-line">{{ $task->designer_brief }}</p>
                    </section>
                @endif
            </div>
        @endif
    </div>

    {{-- الفريق --}}
    @if($task->contentWriter || $task->designer || $task->assignedEmployee)
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            @if($task->contentWriter)
                <div class="card rounded-2xl p-4 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center font-bold text-sm">
                        {{ mb_substr($task->contentWriter->name, 0, 1) }}
                    </div>
                    <div class="min-w-0">
                        <p class="text-[11px] text-blue-600 font-medium">كاتب المحتوى</p>
                        <p class="text-sm font-semibold text-gray-800 truncate">{{ $task->contentWriter->name }}</p>
                    </div>
                </div>
            @endif
            @if($task->designer)
                <div class="card rounded-2xl p-4 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-purple-100 text-purple-700 flex items-center justify-center font-bold text-sm">
                        {{ mb_substr($task->designer->name, 0, 1) }}
                    </div>
                    <div class="min-w-0">
                        <p class="text-[11px] text-purple-600 font-medium">المصمم</p>
                        <p class="text-sm font-semibold text-gray-800 truncate">{{ $task->designer->name }}</p>
                    </div>
                </div>
            @endif
            @if($task->assignedEmployee)
                <div class="card rounded-2xl p-4 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-teal-100 text-teal-700 flex items-center justify-center font-bold text-sm">
                        {{ mb_substr($task->assignedEmployee->name, 0, 1) }}
                    </div>
                    <div class="min-w-0">
                        <p class="text-[11px] text-teal-600 font-medium">المسؤول</p>
                        <p class="text-sm font-semibold text-gray-800 truncate">{{ $task->assignedEmployee->name }}</p>
                    </div>
                </div>
            @endif
        </div>
    @endif

    {{-- ملفات التصميم --}}
    @if(($task->files ?? collect())->isNotEmpty())
        <section class="card rounded-2xl p-5">
            <div class="flex items-center gap-2 mb-4">
                <div class="w-9 h-9 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center">
                    <span class="material-icons text-xl">attach_file</span>
                </div>
                <h3 class="font-bold text-gray-800">ملفات التصميم</h3>
                <span class="px-2 py-0.5 rounded-lg bg-purple-50 text-purple-700 text-xs font-semibold">{{ $task->files->count() }}</span>
            </div>
            <div class="space-y-2">
                @foreach($task->files as $file)
                    <div class="flex items-center justify-between gap-3 rounded-xl border border-gray-100 bg-gray-50 px-3 py-2.5">
                        <div class="min-w-0 flex items-center gap-2">
                            <span class="material-icons text-gray-400 text-lg">
                                {{ ($file->asset_kind ?? '') === 'video' ? 'movie' : (($file->asset_kind ?? '') === 'pdf' ? 'picture_as_pdf' : 'image') }}
                            </span>
                            <span class="text-sm text-gray-800 truncate">{{ $file->file_name }}</span>
                        </div>
                        @if($file->description)
                            <span class="hidden sm:inline text-xs text-gray-400 truncate max-w-[140px]">{{ $file->description }}</span>
                        @endif
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    {{-- روابط النشر --}}
    @if(!empty($task->publish_links) && is_array($task->publish_links))
        <section class="card rounded-2xl p-5">
            <div class="flex items-center gap-2 mb-4">
                <div class="w-9 h-9 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center">
                    <span class="material-icons text-xl">link</span>
                </div>
                <h3 class="font-bold text-gray-800">روابط النشر</h3>
            </div>
            <div class="space-y-2">
                @foreach($task->publish_links as $platform => $url)
                    @if($url)
                        <a href="{{ $url }}" target="_blank" rel="noopener"
                           class="flex items-center justify-between gap-3 rounded-xl border border-teal-100 bg-teal-50/50 px-3 py-2.5 hover:bg-teal-50 transition-colors">
                            <span class="text-sm font-medium text-teal-800">{{ \App\Models\WorkTask::platforms()[$platform] ?? $platform }}</span>
                            <span class="material-icons text-teal-500 text-base">open_in_new</span>
                        </a>
                    @endif
                @endforeach
            </div>
        </section>
    @endif

    {{-- تحديث الحالة --}}
    <section class="card rounded-2xl p-6 border border-indigo-100 shadow-sm">
        <div class="flex items-center gap-2 mb-5">
            <div class="w-9 h-9 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                <span class="material-icons text-xl">update</span>
            </div>
            <div>
                <h3 class="font-bold text-gray-800">تحديث المهمة</h3>
                <p class="text-xs text-gray-500">غيّر الحالة وأضف ملاحظاتك</p>
            </div>
        </div>

        <form method="POST" action="{{ route('employee.work.status', $task) }}" class="space-y-4">
            @csrf @method('PATCH')
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">الحالة</label>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                    @foreach($statuses as $key => $label)
                        <label class="relative cursor-pointer">
                            <input type="radio" name="status" value="{{ $key }}" class="peer sr-only" @checked($task->status === $key)>
                            <span class="block text-center text-xs font-semibold px-2 py-2.5 rounded-xl border-2 border-gray-200 text-gray-600
                                         peer-checked:border-indigo-500 peer-checked:bg-indigo-50 peer-checked:text-indigo-700
                                         hover:border-gray-300 transition-colors">
                                {{ $label }}
                            </span>
                        </label>
                    @endforeach
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">ملاحظات</label>
                <textarea name="notes" rows="4" placeholder="اكتب أي ملاحظات أو تحديثات..."
                          class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-indigo-500 focus:outline-none text-sm leading-6">{{ $task->notes }}</textarea>
            </div>
            <button type="submit" class="btn-primary text-white w-full sm:w-auto px-8 py-3 rounded-xl font-semibold inline-flex items-center justify-center gap-2">
                <span class="material-icons text-base">save</span>
                حفظ التحديث
            </button>
        </form>
    </section>
</div>
@endsection
