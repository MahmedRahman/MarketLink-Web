<div class="task-card bg-white rounded-lg p-4 border border-gray-200 hover:shadow-md transition-shadow cursor-move" data-task-id="{{ $task->id }}" draggable="false">
    <div class="flex items-start justify-between mb-2">
        <h5 class="font-semibold text-gray-800 text-sm flex-1">{{ $task->title }}</h5>
        <button onclick="showEditTaskModal({{ $task->id }}, {
            title: '{{ addslashes($task->title) }}',
            description: '{{ addslashes($task->description ?? '') }}',
            assigned_to: '{{ $task->assigned_to ?? '' }}',
            due_date: '{{ $task->due_date ? $task->due_date->format('Y-m-d') : '' }}',
            status: '{{ $task->status }}'
        })" class="text-gray-400 hover:text-gray-600 ml-2">
            <span class="material-icons text-sm">edit</span>
        </button>
    </div>
    
    @if($task->description)
        <p class="text-xs text-gray-600 mb-2 line-clamp-2">{{ $task->description }}</p>
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

