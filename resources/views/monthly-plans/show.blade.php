@extends('layouts.dashboard')

@section('title', 'تفاصيل الخطة الشهرية')
@section('page-title', 'تفاصيل الخطة الشهرية')
@section('page-description', 'متابعة وتنظيم الخطة الشهرية')

@section('content')
<div class="container mx-auto px-4">
    <div class="max-w-7xl mx-auto space-y-6">
        <!-- Header -->
        <div class="card page-header rounded-2xl p-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="w-12 h-12 logo-gradient rounded-2xl flex items-center justify-center shadow-lg icon-spacing ml-3">
                        <span class="material-icons text-white text-xl">calendar_month</span>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800">{{ $monthlyPlan->project->business_name }}</h2>
                        <p class="text-gray-600">الخطة الشهرية: {{ $monthlyPlan->month }} {{ $monthlyPlan->year }}</p>
                    </div>
                </div>
                <div class="flex items-center space-x-3 rtl:space-x-reverse">
                    <a href="{{ route('monthly-plans.edit', $monthlyPlan) }}" class="flex items-center px-4 py-2 text-blue-600 hover:text-blue-700 hover:bg-blue-50 rounded-xl transition-colors">
                        <span class="material-icons text-sm ml-2">edit</span>
                        تعديل
                    </a>
                    <a href="{{ route('monthly-plans.index') }}" class="flex items-center px-4 py-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-xl transition-colors">
                        العودة للقائمة
                    </a>
                </div>
            </div>
        </div>

        <!-- Plan Info Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Goals Card -->
            <div class="card rounded-2xl p-6">
                <div class="flex items-center mb-4">
                    <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center icon-spacing ml-3">
                        <span class="material-icons text-blue-600">flag</span>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-800">الأهداف</h3>
                </div>
                <div class="space-y-3">
                    @foreach($monthlyPlan->goals as $goal)
                        <div class="bg-gray-50 rounded-lg p-3">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm font-medium text-gray-800">{{ $goal->goal_name }}</span>
                                <span class="text-xs text-gray-500">{{ number_format($goal->progress_percentage, 0) }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-blue-600 h-2 rounded-full" style="width: {{ min(100, $goal->progress_percentage) }}%"></div>
                            </div>
                            <div class="text-xs text-gray-600 mt-1">
                                {{ $goal->achieved_value }} / {{ $goal->target_value }} {{ $goal->unit ?? '' }}
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Employees Card -->
            <div class="card rounded-2xl p-6">
                <div class="flex items-center mb-4">
                    <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center icon-spacing ml-3">
                        <span class="material-icons text-green-600">people</span>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-800">الموظفين</h3>
                </div>
                <div class="space-y-2">
                    @foreach($monthlyPlan->employees as $employee)
                        <div class="flex items-center p-2 bg-gray-50 rounded-lg">
                            <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center ml-3">
                                <span class="material-icons text-sm text-gray-600">person</span>
                            </div>
                            <span class="text-sm font-medium text-gray-800">{{ $employee->name }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Status Card -->
            <div class="card rounded-2xl p-6">
                <div class="flex items-center mb-4">
                    <div class="w-10 h-10 bg-purple-100 rounded-xl flex items-center justify-center icon-spacing ml-3">
                        <span class="material-icons text-purple-600">info</span>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-800">الحالة</h3>
                </div>
                <div class="space-y-3">
                    <div>
                        <span class="px-3 py-1 text-sm rounded-full bg-{{ $monthlyPlan->status_color }}-100 text-{{ $monthlyPlan->status_color }}-800">
                            {{ $monthlyPlan->status_badge }}
                        </span>
                    </div>
                    @if($monthlyPlan->description)
                        <p class="text-sm text-gray-600">{{ $monthlyPlan->description }}</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Trello Board Section -->
        <div class="card rounded-2xl p-6">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-indigo-100 rounded-xl flex items-center justify-center icon-spacing ml-3">
                        <span class="material-icons text-indigo-600">view_kanban</span>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-800">متابعة الخطة</h3>
                </div>
                <button onclick="showAddTaskModal()" class="btn-primary text-white px-4 py-2 rounded-lg flex items-center">
                    <span class="material-icons text-sm ml-2">add</span>
                    إضافة مهمة
                </button>
            </div>

            <!-- Board -->
            <div id="kanban-board" class="flex gap-4 overflow-x-auto pb-4" style="min-height: 500px;">
                <!-- Tasks List -->
                <div class="flex-shrink-0 w-80 bg-gray-50 rounded-xl p-4" data-list-type="tasks" data-employee-id="">
                    <div class="flex items-center justify-between mb-4">
                        <h4 class="font-semibold text-gray-800">مهام</h4>
                        <span class="bg-gray-200 text-gray-700 text-xs px-2 py-1 rounded-full tasks-count">
                            {{ $tasksByList->get('tasks', collect())->count() }}
                        </span>
                    </div>
                    <div class="sortable-list min-h-[400px] space-y-2" id="tasks-list" data-list-type="tasks" data-employee-id="">
                        @foreach($tasksByList->get('tasks', collect())->sortBy('order') as $task)
                            @include('monthly-plans.task-card', ['task' => $task])
                        @endforeach
                    </div>
                </div>

                <!-- Employee Lists -->
                @foreach($monthlyPlan->employees as $employee)
                    @php
                        $employeeTasksKey = 'employee_' . $employee->id;
                        $employeeTasks = $tasksByList->get($employeeTasksKey, collect())->sortBy('order');
                    @endphp
                    <div class="flex-shrink-0 w-80 bg-gray-50 rounded-xl p-4" data-list-type="employee" data-employee-id="{{ $employee->id }}">
                        <div class="flex items-center justify-between mb-4">
                            <h4 class="font-semibold text-gray-800">{{ $employee->name }}</h4>
                            <span class="bg-gray-200 text-gray-700 text-xs px-2 py-1 rounded-full employee-tasks-count employee-tasks-count-{{ $employee->id }}" data-employee-id="{{ $employee->id }}">
                                {{ $employeeTasks->count() }}
                            </span>
                        </div>
                        <div class="sortable-list min-h-[400px] space-y-2 employee-list employee-list-{{ $employee->id }}" data-list-type="employee" data-employee-id="{{ $employee->id }}">
                            @foreach($employeeTasks as $task)
                                @include('monthly-plans.task-card', ['task' => $task])
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<!-- Add Task Modal -->
<div id="add-task-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
    <div class="bg-white rounded-2xl p-6 max-w-md w-full mx-4">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-xl font-bold text-gray-800">إضافة مهمة جديدة</h3>
            <button onclick="hideAddTaskModal()" class="text-gray-400 hover:text-gray-600">
                <span class="material-icons">close</span>
            </button>
        </div>
        <form id="add-task-form" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">عنوان المهمة</label>
                <input type="text" id="task-title" name="title" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">الوصف</label>
                <textarea id="task-description" name="description" rows="3"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary"></textarea>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">تعيين إلى</label>
                <select id="task-assigned-to" name="assigned_to"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
                    <option value="">قائمة المهام</option>
                    @foreach($monthlyPlan->employees as $employee)
                        <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">تاريخ الانتهاء</label>
                <input type="date" id="task-due-date" name="due_date"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
            </div>
            <input type="hidden" id="task-list-type" name="list_type" value="tasks">
            <div class="flex justify-end space-x-3 rtl:space-x-reverse">
                <button type="button" onclick="hideAddTaskModal()" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                    إلغاء
                </button>
                <button type="submit" class="btn-primary text-white px-4 py-2 rounded-lg">
                    إضافة
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Task Modal -->
<div id="edit-task-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
    <div class="bg-white rounded-2xl p-6 max-w-md w-full mx-4">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-xl font-bold text-gray-800">تعديل المهمة</h3>
            <button onclick="hideEditTaskModal()" class="text-gray-400 hover:text-gray-600">
                <span class="material-icons">close</span>
            </button>
        </div>
        <form id="edit-task-form" class="space-y-4">
            @csrf
            @method('PUT')
            <input type="hidden" id="edit-task-id">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">عنوان المهمة</label>
                <input type="text" id="edit-task-title" name="title" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">الوصف</label>
                <textarea id="edit-task-description" name="description" rows="3"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary"></textarea>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">تعيين إلى</label>
                <select id="edit-task-assigned-to" name="assigned_to"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
                    <option value="">قائمة المهام</option>
                    @foreach($monthlyPlan->employees as $employee)
                        <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">تاريخ الانتهاء</label>
                <input type="date" id="edit-task-due-date" name="due_date"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">الحالة</label>
                <select id="edit-task-status" name="status"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
                    <option value="todo">مهام</option>
                    <option value="in_progress">قيد التنفيذ</option>
                    <option value="review">قيد المراجعة</option>
                    <option value="done">مكتملة</option>
                </select>
            </div>
            <div class="flex justify-end space-x-3 rtl:space-x-reverse">
                <button type="button" onclick="hideEditTaskModal()" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                    إلغاء
                </button>
                <button type="submit" class="btn-primary text-white px-4 py-2 rounded-lg">
                    حفظ
                </button>
                <button type="button" onclick="deleteTask()" class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600">
                    حذف
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@section('scripts')
<!-- SortableJS for Drag & Drop -->
<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
<script>
const monthlyPlanId = {{ $monthlyPlan->id }};
let currentEditTaskId = null;

// Initialize Sortable for all lists
document.addEventListener('DOMContentLoaded', function() {
    // Initialize sortable for tasks list
    const tasksList = document.getElementById('tasks-list');
    if (tasksList) {
        new Sortable(tasksList, {
            group: 'tasks',
            animation: 150,
            ghostClass: 'opacity-50',
            onEnd: function(evt) {
                const fromList = evt.from.closest('[data-list-type]');
                const toList = evt.to.closest('[data-list-type]');
                if (fromList && toList) {
                    handleTaskMove(
                        evt.item.dataset.taskId,
                        fromList.dataset.listType,
                        fromList.dataset.employeeId || '',
                        toList.dataset.listType,
                        toList.dataset.employeeId || '',
                        evt.newIndex
                    );
                }
            }
        });
    }

    // Initialize sortable for employee lists
    @foreach($monthlyPlan->employees as $employee)
        const employeeList{{ $employee->id }} = document.querySelector('.employee-list-{{ $employee->id }}');
        if (employeeList{{ $employee->id }}) {
            new Sortable(employeeList{{ $employee->id }}, {
                group: 'tasks',
                animation: 150,
                handle: '.task-card',
                ghostClass: 'opacity-50',
                onEnd: function(evt) {
                    const fromList = evt.from.closest('[data-list-type]');
                    const toList = evt.to.closest('[data-list-type]');
                    if (fromList && toList) {
                        handleTaskMove(
                            evt.item.dataset.taskId,
                            fromList.dataset.listType,
                            fromList.dataset.employeeId || '',
                            toList.dataset.listType,
                            toList.dataset.employeeId || '',
                            evt.newIndex
                        );
                    }
                }
            });
        }
    @endforeach
});

function handleTaskMove(taskId, fromListType, fromEmployeeId, toListType, toEmployeeId, newIndex) {
    const listType = toListType === 'tasks' ? 'tasks' : 'employee';
    const assignedTo = toListType === 'tasks' ? '' : toEmployeeId;

    fetch(`/monthly-plans/${monthlyPlanId}/tasks/${taskId}/move`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            list_type: listType,
            assigned_to: assignedTo,
            new_order: newIndex
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateTaskCounts();
        } else {
            alert('حدث خطأ أثناء نقل المهمة');
            location.reload();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('حدث خطأ أثناء نقل المهمة');
        location.reload();
    });
}

function updateTaskCounts() {
    // Update counts for all lists
    const lists = document.querySelectorAll('[data-list-type]');
    lists.forEach(list => {
        const sortableList = list.querySelector('.sortable-list');
        if (sortableList) {
            const count = sortableList.children.length;
            const countElement = list.querySelector('[class*="count"]');
            if (countElement) {
                countElement.textContent = count;
            }
        }
    });
}

function showAddTaskModal() {
    document.getElementById('add-task-modal').classList.remove('hidden');
}

function hideAddTaskModal() {
    document.getElementById('add-task-modal').classList.add('hidden');
    document.getElementById('add-task-form').reset();
}

function showEditTaskModal(taskId, taskData) {
    currentEditTaskId = taskId;
    document.getElementById('edit-task-id').value = taskId;
    document.getElementById('edit-task-title').value = taskData.title;
    document.getElementById('edit-task-description').value = taskData.description || '';
    document.getElementById('edit-task-assigned-to').value = taskData.assigned_to || '';
    document.getElementById('edit-task-due-date').value = taskData.due_date || '';
    document.getElementById('edit-task-status').value = taskData.status;
    document.getElementById('edit-task-modal').classList.remove('hidden');
}

function hideEditTaskModal() {
    document.getElementById('edit-task-modal').classList.add('hidden');
    currentEditTaskId = null;
}

// Add Task Form Submit
document.getElementById('add-task-form').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const assignedTo = formData.get('assigned_to');
    const listType = assignedTo ? 'employee' : 'tasks';
    formData.set('list_type', listType);

    fetch(`/monthly-plans/${monthlyPlanId}/tasks`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.error || 'حدث خطأ أثناء إضافة المهمة');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('حدث خطأ أثناء إضافة المهمة');
    });
});

// Edit Task Form Submit
document.getElementById('edit-task-form').addEventListener('submit', function(e) {
    e.preventDefault();
    if (!currentEditTaskId) return;

    const formData = new FormData(this);
    const assignedTo = formData.get('assigned_to');
    const listType = assignedTo ? 'employee' : 'tasks';
    formData.set('list_type', listType);
    
    fetch(`/monthly-plans/${monthlyPlanId}/tasks/${currentEditTaskId}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.error || 'حدث خطأ أثناء تحديث المهمة');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('حدث خطأ أثناء تحديث المهمة');
    });
});

function deleteTask() {
    if (!currentEditTaskId) return;
    if (!confirm('هل أنت متأكد من حذف هذه المهمة؟')) return;

    fetch(`/monthly-plans/${monthlyPlanId}/tasks/${currentEditTaskId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('حدث خطأ أثناء حذف المهمة');
        }
    });
}

// Update task assigned_to when selecting employee in add modal
document.getElementById('task-assigned-to').addEventListener('change', function() {
    document.getElementById('task-list-type').value = this.value ? 'employee' : 'tasks';
});
</script>
@endsection

