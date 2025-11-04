<div class="bg-white border-l-4 rounded-lg p-4 shadow-sm hover:shadow-md transition-shadow" style="border-left-color: {{ $task->color ?? '#6366f1' }};">
    <div class="flex items-start justify-between mb-2">
        <div class="flex-1">
            <h4 class="font-semibold text-gray-800 text-sm mb-1">{{ $task->title }}</h4>
            @if($task->description)
                <p class="text-xs text-gray-600 line-clamp-2">{{ $task->description }}</p>
            @endif
        </div>
    </div>

    @if($task->monthlyPlan)
        <div class="flex items-center text-xs text-gray-500 mb-2">
            <span class="material-icons text-xs ml-1">folder</span>
            <span>{{ $task->monthlyPlan->project->business_name ?? 'مشروع' }} - {{ $task->monthlyPlan->month }} {{ $task->monthlyPlan->year }}</span>
        </div>
    @endif

    @if($task->due_date)
        <div class="flex items-center text-xs text-gray-500 mb-2">
            <span class="material-icons text-xs ml-1">event</span>
            <span>{{ $task->due_date->format('Y-m-d') }}</span>
            @if($task->due_date->isPast() && $task->status !== 'done')
                <span class="text-red-500 mr-2">(متأخرة)</span>
            @endif
        </div>
    @endif

    <div class="flex items-center justify-between mt-3 pt-3 border-t border-gray-100">
        <div class="flex items-center space-x-2 space-x-reverse">
            <span class="px-2 py-1 text-xs rounded-full bg-{{ $task->status_color }}-100 text-{{ $task->status_color }}-800">
                {{ $task->status_badge }}
            </span>
            @php
                $isManagerOfThisTask = false;
                if (isset($hasManagerRole) && $hasManagerRole && isset($managedProjectIds) && $task->monthlyPlan) {
                    $isManagerOfThisTask = in_array($task->monthlyPlan->project_id, $managedProjectIds);
                }
            @endphp
            @if($isManagerOfThisTask)
                <span class="px-2 py-1 text-xs rounded-full bg-orange-100 text-orange-700 flex items-center">
                    <span class="material-icons text-xs ml-1">admin_panel_settings</span>
                    مدير
                </span>
            @endif
            @if($task->assignedEmployee)
                <span class="text-xs text-gray-500">{{ $task->assignedEmployee->name }}</span>
            @endif
        </div>
        <div class="flex items-center space-x-2 space-x-reverse">
            <a href="{{ route('employee.tasks.edit', $task->id) }}" 
               class="text-purple-500 hover:text-purple-700 text-xs flex items-center">
                <span class="material-icons text-xs ml-1">edit</span>
                تعديل
            </a>
            <a href="{{ route('employee.tasks.show', $task->id) }}" 
               class="text-blue-500 hover:text-blue-700 text-xs flex items-center">
                <span class="material-icons text-xs ml-1">visibility</span>
                عرض
            </a>
        </div>
    </div>
</div>

