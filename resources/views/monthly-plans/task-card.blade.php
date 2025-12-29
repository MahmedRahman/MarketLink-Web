<div class="task-card bg-white rounded-lg p-4 border-l-4 hover:shadow-md transition-shadow cursor-move relative" 
     data-task-id="{{ $task->id }}" 
     draggable="false"
     style="border-left-color: {{ $task->color ?? '#6366f1' }};">
    <!-- Checkbox for bulk selection -->
    <div class="absolute top-2 right-2 z-10">
        <input type="checkbox" 
               class="task-select-checkbox w-5 h-5 rounded border-2 border-gray-300 text-primary focus:ring-2 focus:ring-primary cursor-pointer" 
               data-task-id="{{ $task->id }}"
               onclick="event.stopPropagation(); updateBulkAssignButton();">
    </div>
    <div class="flex items-start justify-between mb-2 ml-6">
        <a href="{{ route('monthly-plans.tasks.show', [$task->monthly_plan_id, $task->id]) }}" 
           class="font-semibold text-gray-800 text-sm flex-1 hover:text-primary cursor-pointer">
            {{ $task->title }}
        </a>
        <div class="flex items-center space-x-2 rtl:space-x-reverse">
            <button onclick="event.stopPropagation(); showQuickAssignModal({{ $task->id }}, {{ $task->assigned_to ?? 'null' }}, '{{ $task->status }}')" 
                    class="text-purple-400 hover:text-purple-600" 
                    title="تعديل الموظف المسؤول">
                <span class="material-icons text-sm">person_add</span>
            </button>
            <a href="{{ route('monthly-plans.tasks.show', [$task->monthly_plan_id, $task->id]) }}" 
               class="text-blue-400 hover:text-blue-600" 
               title="عرض المهمة"
               onclick="event.stopPropagation();">
                <span class="material-icons text-sm">visibility</span>
            </a>
            <a href="{{ route('monthly-plans.tasks.edit', [$task->monthly_plan_id, $task->id]) }}" 
               class="text-gray-400 hover:text-gray-600" 
               title="تعديل المهمة"
               onclick="event.stopPropagation();">
                <span class="material-icons text-sm">edit</span>
            </a>
            @php
                $deleteUrl = route('monthly-plans.tasks.destroy', [$task->monthly_plan_id, $task->id]);
                $deletePath = parse_url($deleteUrl, PHP_URL_PATH);
                $deletePath = $deletePath ?: $deleteUrl;
            @endphp
            <button onclick="event.stopPropagation(); deleteTaskCard({{ $task->id }}, '{{ $deletePath }}')" 
                    class="text-red-400 hover:text-red-600 hidden" 
                    title="حذف المهمة">
                <span class="material-icons text-sm">delete</span>
            </button>
        </div>
    </div>

    @if($task->due_date)
        <div class="flex items-center text-xs text-gray-500 mb-2">
            <span class="material-icons text-sm ml-1">event</span>
            {{ $task->due_date->format('Y-m-d') }}
            @if($task->due_date->isPast() && $task->status !== 'done')
                <span class="text-red-500 mr-2">(متأخرة)</span>
            @endif
        </div>
    @endif

    <div class="flex items-center justify-between mt-3 pt-3 border-t border-gray-100">
        <div class="flex items-center gap-2">
            <span class="px-2 py-1 text-xs rounded-full bg-{{ $task->status_color }}-100 text-{{ $task->status_color }}-800">
                {{ $task->status_badge }}
            </span>
            @if($task->status === 'archived' && in_array($task->list_type, ['publish', 'ready']))
                <span class="px-2 py-1 text-xs rounded-full bg-{{ $task->list_type_color }}-100 text-{{ $task->list_type_color }}-800">
                    {{ $task->list_type_badge }}
                </span>
            @endif
        </div>
        @if($task->assignedEmployee)
            <span class="text-xs text-gray-500">{{ $task->assignedEmployee->name }}</span>
        @endif
    </div>
</div>

