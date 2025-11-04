<div class="task-card bg-white rounded-lg p-4 border-l-4 hover:shadow-md transition-shadow cursor-move" 
     data-task-id="{{ $task->id }}" 
     draggable="false"
     style="border-left-color: {{ $task->color ?? '#6366f1' }};">
    <div class="flex items-start justify-between mb-2">
        <a href="{{ route('employee.tasks.show', $task->id) }}" 
           class="font-semibold text-gray-800 text-sm flex-1 hover:text-primary cursor-pointer">
            {{ $task->title }}
        </a>
        <div class="flex items-center space-x-2 rtl:space-x-reverse">
            <a href="{{ route('employee.tasks.show', $task->id) }}" 
               class="text-blue-400 hover:text-blue-600" 
               title="عرض المهمة"
               onclick="event.stopPropagation();">
                <span class="material-icons text-sm">visibility</span>
            </a>
            <a href="{{ route('employee.tasks.edit', $task->id) }}" 
               class="text-gray-400 hover:text-gray-600" 
               title="تعديل المهمة"
               onclick="event.stopPropagation();">
                <span class="material-icons text-sm">edit</span>
            </a>
        </div>
    </div>

    @if($task->description)
        <p class="text-xs text-gray-600 mb-2 line-clamp-2">{{ Str::limit($task->description, 80) }}</p>
    @endif

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
        <span class="px-2 py-1 text-xs rounded-full bg-{{ $task->status_color }}-100 text-{{ $task->status_color }}-800">
            {{ $task->status_badge }}
        </span>
        @if($task->assignedEmployee)
            <span class="text-xs text-gray-500">{{ $task->assignedEmployee->name }}</span>
        @endif
    </div>
</div>

