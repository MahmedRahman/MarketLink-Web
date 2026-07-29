@php
    $canManageFolders = $canManageFolders ?? false;
    $folders = $folders ?? collect();
    $viewMode = $viewMode ?? 'title';
    $dndEnabled = $canManageFolders && ($viewMode === 'folder');
@endphp
<div class="space-y-2 {{ $dndEnabled ? 'folder-dnd-item' : '' }}"
     @if($dndEnabled)
         draggable="true"
         data-activity-id="{{ $activity->id }}"
         data-move-url="{{ work_route('move-folder', $activity) }}"
         data-folder-id="{{ $activity->folder_id ?: '' }}"
     @endif>
    <a href="{{ work_route('show', $activity) }}" class="card rounded-2xl p-5 block hover:no-underline relative">
        @if($dndEnabled)
            <span class="absolute top-3 start-3 w-8 h-8 rounded-lg bg-slate-100 text-slate-500 inline-flex items-center justify-center cursor-grab active:cursor-grabbing"
                  title="اسحب لنقل الفولدر"
                  aria-hidden="true">
                <span class="material-icons text-base">drag_indicator</span>
            </span>
        @endif
        <div class="flex items-start justify-between mb-3 {{ $dndEnabled ? 'ps-9' : '' }}">
            <div class="flex items-center gap-3 min-w-0">
                <div class="w-11 h-11 rounded-xl bg-indigo-50 text-primary flex items-center justify-center shrink-0">
                    <span class="material-icons">{{ $activity->type_icon }}</span>
                </div>
                <div class="min-w-0">
                    <h3 class="font-bold text-gray-800 leading-tight truncate">{{ $activity->title }}</h3>
                    <span class="text-xs text-gray-500">{{ $activity->type_label }}</span>
                </div>
            </div>
            <div class="flex flex-col items-end gap-1.5 shrink-0">
                <span class="role-badge role-{{ $activity->status_color }}">{{ $activity->status_label }}</span>
                @if($activity->month_label)
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg bg-teal-50 text-teal-800 text-[11px] font-bold border border-teal-100">
                        <span class="material-icons text-sm">calendar_month</span>
                        {{ $activity->month_label }}
                    </span>
                @endif
            </div>
        </div>

        @if($activity->event_date)
            <p class="text-xs text-gray-500 flex items-center gap-1 mb-3">
                <span class="material-icons text-sm">event</span>
                {{ $activity->event_date->format('Y/m/d') }}
            </p>
        @endif

        <div class="mt-2">
            <div class="flex items-center justify-between text-xs text-gray-500 mb-1 gap-2">
                <span>{{ $activity->done_tasks_count }} / {{ $activity->tasks_count }} مهمة</span>
                <span>{{ $activity->progress }}%</span>
            </div>
            <div class="w-full h-2 bg-gray-100 rounded-full overflow-hidden">
                <div class="h-full bg-gradient-to-l from-primary to-secondary rounded-full" style="width: {{ $activity->progress }}%"></div>
            </div>
            @if(($activity->ready_to_publish_count ?? 0) > 0)
                <p class="mt-2.5 inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-teal-50 text-teal-800 text-[11px] font-bold border border-teal-100">
                    <span class="material-icons text-sm">schedule_send</span>
                    {{ $activity->ready_to_publish_count }} جاهز للنشر
                </p>
            @endif
        </div>
    </a>

    @if($canManageFolders)
        <form method="POST" action="{{ work_route('move-folder', $activity) }}" class="px-1 folder-move-form" onclick="event.stopPropagation()">
            @csrf
            <input type="hidden" name="return_view" value="{{ $viewMode }}">
            @if(!empty($filterType))
                <input type="hidden" name="type" value="{{ $filterType }}">
            @endif
            @if(!empty($filterStatus))
                <input type="hidden" name="status" value="{{ $filterStatus }}">
            @endif
            <label class="sr-only">نقل لفولدر</label>
            <select name="folder_id" onchange="this.form.submit()"
                    class="folder-move-select w-full px-3 py-1.5 rounded-xl border border-gray-200 text-xs text-gray-700 bg-white focus:border-primary focus:outline-none">
                <option value="">بدون فولدر</option>
                @foreach($folders as $folderOption)
                    <option value="{{ $folderOption->id }}" @selected((int) $activity->folder_id === (int) $folderOption->id)>
                        {{ $folderOption->title }}
                    </option>
                @endforeach
            </select>
        </form>
    @endif
</div>
