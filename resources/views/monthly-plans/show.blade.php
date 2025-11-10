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
                    <div class="flex items-center gap-4">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-800">{{ $monthlyPlan->project->business_name }}</h2>
                            <p class="text-gray-600">الخطة الشهرية: {{ $monthlyPlan->month }} {{ $monthlyPlan->year }}</p>
                        </div>
                        <!-- Icons for Goals, Employees, and Status -->
                        <div class="flex items-center gap-3 mr-4">
                            <!-- Goals Icon -->
                            <button onclick="showGoalsModal()" class="flex items-center gap-2 bg-blue-50 px-3 py-2 rounded-lg hover:bg-blue-100 transition-colors cursor-pointer" title="الأهداف">
                                <span class="material-icons text-blue-600 text-lg">flag</span>
                                <span class="text-sm font-semibold text-blue-700">{{ $monthlyPlan->goals->count() }}</span>
                            </button>
                            <!-- Employees Icon -->
                            <button onclick="showEmployeesModal()" class="flex items-center gap-2 bg-green-50 px-3 py-2 rounded-lg hover:bg-green-100 transition-colors cursor-pointer" title="الموظفين">
                                <span class="material-icons text-green-600 text-lg">people</span>
                                <span class="text-sm font-semibold text-green-700">{{ $monthlyPlan->employees->count() }}</span>
                            </button>
                            <!-- Status Icon -->
                            <div class="flex items-center gap-2 bg-purple-50 px-3 py-2 rounded-lg" title="الحالة">
                                <span class="material-icons text-purple-600 text-lg">info</span>
                                <span class="text-sm font-semibold text-purple-700">{{ $monthlyPlan->status_badge }}</span>
                            </div>
                        </div>
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

        <!-- Tasks Section -->
        <div id="tasks-section" class="card rounded-2xl p-6">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-indigo-100 rounded-xl flex items-center justify-center icon-spacing ml-3">
                        <span class="material-icons text-indigo-600">view_module</span>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-800">متابعة الخطة</h3>
                </div>
                <div class="flex items-center space-x-2 rtl:space-x-reverse">
                    <a href="{{ route('monthly-plans.tasks.create', $monthlyPlan) }}" class="flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors">
                        <span class="material-icons text-sm ml-2">add_task</span>
                        إضافة مهمة جديدة
                    </a>
                    <button onclick="showAddTaskModal()" class="btn-primary text-white px-4 py-2 rounded-lg flex items-center">
                        <span class="material-icons text-sm ml-2">add</span>
                        إضافة مهمة سريعة
                    </button>
                </div>
            </div>

            <!-- Tasks Rows -->
            <div class="space-y-6">
                <!-- General Tasks Row -->
                @php
                    $generalTasks = $tasksByList->get('tasks', collect())->where('status', '!=', 'archived')->where('status', '!=', 'publish')->sortBy('order');
                    $readyTasks = $tasksByList->get('ready', collect())->where('status', '!=', 'archived')->where('status', '!=', 'publish')->sortBy('order');
                    $allGeneralTasks = $generalTasks->merge($readyTasks);
                @endphp
                
                @if($allGeneralTasks->count() > 0)
                    <div class="bg-gray-50 rounded-xl p-4">
                        <!-- General Tasks Header -->
                        <div class="flex items-center justify-between mb-4 pb-3 border-b border-gray-200">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-gradient-to-r from-gray-400 to-gray-600 rounded-full flex items-center justify-center">
                                    <span class="material-icons text-white text-sm">folder</span>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-800 text-lg">مهام عامة</h4>
                                    <p class="text-xs text-gray-500">مهام غير مخصصة لموظف محدد</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="px-3 py-1 text-sm rounded-full bg-gray-100 text-gray-700">
                                    {{ $allGeneralTasks->count() }} مهمة
                                </span>
                            </div>
                        </div>
                        
                        <!-- General Tasks Grid -->
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                            @foreach($allGeneralTasks as $task)
                                @include('monthly-plans.task-card', ['task' => $task])
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Publish Tasks Row -->
                @php
                    $publishTasks = $monthlyPlan->tasks()->where('status', 'publish')->orderBy('order')->get();
                @endphp
                
                @if($publishTasks->count() > 0)
                    <div class="bg-green-50 rounded-xl p-4">
                        <!-- Publish Tasks Header -->
                        <div class="flex items-center justify-between mb-4 pb-3 border-b border-gray-200">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-gradient-to-r from-green-400 to-green-600 rounded-full flex items-center justify-center">
                                    <span class="material-icons text-white text-sm">publish</span>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-800 text-lg">نشر</h4>
                                    <p class="text-xs text-gray-500">المهام المنشورة</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="px-3 py-1 text-sm rounded-full bg-green-100 text-green-700">
                                    {{ $publishTasks->count() }} مهمة
                                </span>
                            </div>
                        </div>
                        
                        <!-- Publish Tasks Grid -->
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                            @foreach($publishTasks as $task)
                                @include('monthly-plans.task-card', ['task' => $task])
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Archived Tasks Row -->
                @php
                    $archivedTasks = $monthlyPlan->tasks()->where('status', 'archived')->orderBy('order')->get();
                @endphp
                
                @if($archivedTasks->count() > 0)
                    <div class="bg-slate-50 rounded-xl p-4">
                        <!-- Archived Tasks Header -->
                        <div class="flex items-center justify-between mb-4 pb-3 border-b border-gray-200">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-gradient-to-r from-slate-400 to-slate-600 rounded-full flex items-center justify-center">
                                    <span class="material-icons text-white text-sm">archive</span>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-800 text-lg">أرشيف</h4>
                                    <p class="text-xs text-gray-500">المهام المؤرشفة</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="px-3 py-1 text-sm rounded-full bg-slate-100 text-slate-700">
                                    {{ $archivedTasks->count() }} مهمة
                                </span>
                            </div>
                        </div>
                        
                        <!-- Archived Tasks Grid -->
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                            @foreach($archivedTasks as $task)
                                @include('monthly-plans.task-card', ['task' => $task])
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Employees Rows -->
                @foreach($monthlyPlan->employees as $employee)
                    @php
                        $employeeTasksKey = 'employee_' . $employee->id;
                        // عرض جميع مهام الموظف بما فيها المنشورة (لكن بدون المؤرشفة)
                        $employeeTasks = $tasksByList->get($employeeTasksKey, collect())->where('status', '!=', 'archived')->sortBy('order');
                    @endphp
                    <div class="bg-gray-50 rounded-xl p-4">
                        <!-- Employee Header -->
                        <div class="flex items-center justify-between mb-4 pb-3 border-b border-gray-200">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-gradient-to-r from-green-400 to-green-600 rounded-full flex items-center justify-center">
                                    <span class="material-icons text-white text-sm">person</span>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-800 text-lg">{{ $employee->name }}</h4>
                                    <p class="text-xs text-gray-500">{{ $employee->email ?? '' }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="px-3 py-1 text-sm rounded-full bg-green-100 text-green-700">
                                    {{ $employeeTasks->count() }} مهمة
                                </span>
                            </div>
                        </div>
                        
                        <!-- Employee Tasks Grid -->
                        @if($employeeTasks->count() > 0)
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                                @foreach($employeeTasks as $task)
                                    @include('monthly-plans.task-card', ['task' => $task])
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8 text-gray-400">
                                <span class="material-icons text-4xl mb-2">task_alt</span>
                                <p class="text-sm">لا توجد مهام لهذا الموظف</p>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<!-- Goals Modal -->
<div id="goals-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
    <div class="bg-white rounded-2xl p-6 max-w-2xl w-full mx-4 max-h-[80vh] overflow-y-auto">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center">
                <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center ml-3">
                    <span class="material-icons text-blue-600">flag</span>
                </div>
                <h3 class="text-xl font-bold text-gray-800">الأهداف</h3>
            </div>
            <button onclick="hideGoalsModal()" class="text-gray-400 hover:text-gray-600">
                <span class="material-icons">close</span>
            </button>
        </div>
        <div class="space-y-3">
            @forelse($monthlyPlan->goals as $goal)
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-medium text-gray-800">{{ $goal->goal_name }}</span>
                        <span class="text-xs text-gray-500">{{ number_format($goal->progress_percentage, 0) }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-blue-600 h-2 rounded-full" style="width: {{ min(100, $goal->progress_percentage) }}%"></div>
                    </div>
                    <div class="text-xs text-gray-600 mt-1">
                        {{ $goal->achieved_value }} / {{ $goal->target_value }}
                    </div>
                </div>
            @empty
                <div class="text-center py-8 text-gray-500">
                    <span class="material-icons text-4xl mb-2">flag</span>
                    <p>لا توجد أهداف</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

<!-- Employees Modal -->
<div id="employees-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
    <div class="bg-white rounded-2xl p-6 max-w-2xl w-full mx-4 max-h-[80vh] overflow-y-auto">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center">
                <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center ml-3">
                    <span class="material-icons text-green-600">people</span>
                </div>
                <h3 class="text-xl font-bold text-gray-800">الموظفين</h3>
            </div>
            <button onclick="hideEmployeesModal()" class="text-gray-400 hover:text-gray-600">
                <span class="material-icons">close</span>
            </button>
        </div>
        <div class="space-y-2">
            @forelse($monthlyPlan->employees as $employee)
                <div class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                    <div class="w-10 h-10 bg-gradient-to-r from-green-400 to-green-600 rounded-full flex items-center justify-center ml-3">
                        <span class="material-icons text-white text-sm">person</span>
                    </div>
                    <span class="text-sm font-medium text-gray-800">{{ $employee->name }}</span>
                </div>
            @empty
                <div class="text-center py-8 text-gray-500">
                    <span class="material-icons text-4xl mb-2">people</span>
                    <p>لا يوجد موظفين</p>
                </div>
            @endforelse
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
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">اللون</label>
                <div class="flex items-center gap-3">
                    <input type="color" id="task-color" name="color" value="#6366f1"
                        class="w-16 h-10 border border-gray-300 rounded-lg cursor-pointer">
                    <input type="text" id="task-color-hex" value="#6366f1" placeholder="#6366f1"
                        class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary"
                        pattern="^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$">
                </div>
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

<!-- Quick Assign Employee Modal -->
<div id="quick-assign-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
    <div class="bg-white rounded-2xl p-6 max-w-md w-full mx-4">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center">
                <div class="w-10 h-10 bg-purple-100 rounded-xl flex items-center justify-center ml-3">
                    <span class="material-icons text-purple-600">person_add</span>
                </div>
                <h3 class="text-xl font-bold text-gray-800">تعديل الموظف المسؤول</h3>
            </div>
            <button onclick="hideQuickAssignModal()" class="text-gray-400 hover:text-gray-600">
                <span class="material-icons">close</span>
            </button>
        </div>
        <form id="quick-assign-form" class="space-y-4">
            @csrf
            <input type="hidden" id="quick-assign-task-id">
            <input type="hidden" id="quick-assign-task-status">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">اختر الموظف</label>
                <select id="quick-assign-employee" name="assigned_to"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
                    <option value="">مهام عامة (بدون موظف)</option>
                    @foreach($monthlyPlan->employees as $employee)
                        <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex justify-end space-x-3 rtl:space-x-reverse">
                <button type="button" id="publish-task-btn" onclick="publishTask()" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg flex items-center gap-2 hidden">
                    <span class="material-icons text-sm">publish</span>
                    نشر
                </button>
                <button type="button" onclick="archiveTask()" class="px-4 py-2 bg-slate-600 hover:bg-slate-700 text-white rounded-lg flex items-center gap-2">
                    <span class="material-icons text-sm">archive</span>
                    أرشيف
                </button>
                <button type="button" onclick="hideQuickAssignModal()" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                    إلغاء
                </button>
                <button type="submit" class="btn-primary text-white px-4 py-2 rounded-lg">
                    حفظ
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
                <label class="block text-sm font-medium text-gray-700 mb-2">اللون</label>
                <div class="flex items-center gap-3">
                    <input type="color" id="edit-task-color" name="color" value="#6366f1"
                        class="w-16 h-10 border border-gray-300 rounded-lg cursor-pointer">
                    <input type="text" id="edit-task-color-hex" value="#6366f1" placeholder="#6366f1"
                        class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary"
                        pattern="^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">الحالة</label>
                <select id="edit-task-status" name="status"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
                    <option value="todo">مهام</option>
                    <option value="in_progress">قيد التنفيذ</option>
                    <option value="review">قيد المراجعة</option>
                    <option value="done">مكتملة</option>
                    <option value="archived">أرشيف</option>
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
<style>
/* Fullscreen styles */
.kanban-section-fullscreen {
    position: fixed !important;
    top: 0 !important;
    left: 0 !important;
    right: 0 !important;
    bottom: 0 !important;
    width: 100vw !important;
    height: 100vh !important;
    max-width: 100% !important;
    max-height: 100% !important;
    z-index: 9999 !important;
    margin: 0 !important;
    border-radius: 0 !important;
    overflow: hidden !important;
    background: white !important;
}

.kanban-section-fullscreen .kanban-board-container {
    height: calc(100vh - 120px) !important;
    min-height: calc(100vh - 120px) !important;
}

.kanban-section-fullscreen .card {
    height: 100% !important;
    display: flex !important;
    flex-direction: column !important;
}

body.kanban-fullscreen-active {
    overflow: hidden !important;
}
</style>
<script>
const monthlyPlanId = {{ $monthlyPlan->id }};
let currentEditTaskId = null;
let isFullscreen = false;

// Toggle Fullscreen Function
function toggleFullscreen() {
    const kanbanSection = document.getElementById('kanban-section');
    const icon = document.getElementById('fullscreen-icon');
    const body = document.body;
    
    isFullscreen = !isFullscreen;
    
    if (isFullscreen) {
        kanbanSection.classList.add('kanban-section-fullscreen');
        body.classList.add('kanban-fullscreen-active');
        icon.textContent = 'fullscreen_exit';
    } else {
        kanbanSection.classList.remove('kanban-section-fullscreen');
        body.classList.remove('kanban-fullscreen-active');
        icon.textContent = 'fullscreen';
        // Scroll back to the section
        kanbanSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
}

// Exit fullscreen on ESC key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && isFullscreen) {
        toggleFullscreen();
    }
});

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

    // Initialize sortable for ready list
    const readyList = document.getElementById('ready-list');
    if (readyList) {
        new Sortable(readyList, {
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

    // Initialize sortable for publish list
    const publishList = document.getElementById('publish-list');
    if (publishList) {
        new Sortable(publishList, {
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
    let listType = toListType;
    let assignedTo = '';
    
    if (toListType === 'tasks' || toListType === 'ready' || toListType === 'publish') {
        listType = toListType;
        assignedTo = '';
    } else if (toListType === 'employee') {
        listType = 'employee';
        assignedTo = toEmployeeId;
    }

    fetch(`/monthly-plans/${monthlyPlanId}/tasks/${taskId}/move`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            list_type: listType,
            assigned_to: assignedTo || null,
            new_order: newIndex
        })
    })
    .then(response => {
        return response.json().then(data => {
            if (!response.ok) {
                throw new Error(data.error || data.message || 'حدث خطأ أثناء نقل المهمة');
            }
            return data;
        });
    })
    .then(data => {
        if (data.success) {
            updateTaskCounts();
        } else {
            alert(data.error || 'حدث خطأ أثناء نقل المهمة');
            location.reload();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert(error.message || 'حدث خطأ أثناء نقل المهمة');
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
    // Reset color to default
    document.getElementById('task-color').value = '#6366f1';
    document.getElementById('task-color-hex').value = '#6366f1';
}

function showEditTaskModal(taskId, taskData) {
    currentEditTaskId = taskId;
    document.getElementById('edit-task-id').value = taskId;
    document.getElementById('edit-task-title').value = taskData.title;
    document.getElementById('edit-task-description').value = taskData.description || '';
    document.getElementById('edit-task-assigned-to').value = taskData.assigned_to || '';
    document.getElementById('edit-task-due-date').value = taskData.due_date || '';
    document.getElementById('edit-task-status').value = taskData.status;
    const taskColor = taskData.color || '#6366f1';
    document.getElementById('edit-task-color').value = taskColor;
    document.getElementById('edit-task-color-hex').value = taskColor;
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
    
    // Add color to form data
    const color = document.getElementById('task-color').value || '#6366f1';
    formData.set('color', color);

    fetch(`/monthly-plans/${monthlyPlanId}/tasks`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(response => {
        return response.json().then(data => {
            if (!response.ok) {
                throw new Error(data.error || data.message || 'حدث خطأ أثناء إضافة المهمة');
            }
            return data;
        });
    })
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.error || 'حدث خطأ أثناء إضافة المهمة');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert(error.message || 'حدث خطأ أثناء إضافة المهمة');
    });
});

// Edit Task Form Submit
document.getElementById('edit-task-form').addEventListener('submit', function(e) {
    e.preventDefault();
    if (!currentEditTaskId) return;

    const formData = new FormData(this);
    
    // جمع البيانات من النموذج
    const data = {
        title: formData.get('title'),
        description: formData.get('description') || null,
        assigned_to: formData.get('assigned_to') || null,
        due_date: formData.get('due_date') || null,
        status: formData.get('status'),
        color: document.getElementById('edit-task-color').value || '#6366f1'
    };
    
    // تحديد list_type بناءً على assigned_to
    data.list_type = data.assigned_to ? 'employee' : 'tasks';
    
    fetch(`/monthly-plans/${monthlyPlanId}/tasks/${currentEditTaskId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(response => {
        return response.json().then(responseData => {
            if (!response.ok) {
                throw new Error(responseData.error || responseData.message || 'حدث خطأ أثناء تحديث المهمة');
            }
            return responseData;
        });
    })
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.error || 'حدث خطأ أثناء تحديث المهمة');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert(error.message || 'حدث خطأ أثناء تحديث المهمة');
    });
});

function deleteTask() {
    if (!currentEditTaskId) return;
    if (!confirm('هل أنت متأكد من حذف هذه المهمة؟')) return;

    fetch(`/monthly-plans/${monthlyPlanId}/tasks/${currentEditTaskId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.error || 'حدث خطأ أثناء حذف المهمة');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('حدث خطأ أثناء حذف المهمة');
    });
}

// حذف المهمة من الكارت
function deleteTaskCard(taskId, deleteUrl) {
    if (!confirm('هل أنت متأكد من حذف هذه المهمة؟')) return;

    // تحويل HTTP URLs إلى relative paths لتجنب مشاكل Mixed Content
    let url = deleteUrl;
    try {
        const urlObj = new URL(deleteUrl, window.location.origin);
        // إذا كان URL absolute، استخدم فقط المسار
        if (urlObj.protocol === 'http:' || urlObj.protocol === 'https:') {
            url = urlObj.pathname;
        }
    } catch (e) {
        // إذا كان URL بالفعل relative، استخدمه كما هو
    }

    fetch(url, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        }
    })
    .then(response => {
        return response.json().then(data => {
            if (!response.ok) {
                throw new Error(data.error || data.message || 'حدث خطأ أثناء حذف المهمة');
            }
            return data;
        });
    })
    .then(data => {
        if (data.success) {
            // إزالة الكارت من DOM
            const taskCard = document.querySelector(`[data-task-id="${taskId}"]`);
            if (taskCard) {
                taskCard.style.transition = 'opacity 0.3s';
                taskCard.style.opacity = '0';
                setTimeout(() => {
                    taskCard.remove();
                    updateTaskCounts();
                }, 300);
            } else {
                location.reload();
            }
        } else {
            alert(data.error || 'حدث خطأ أثناء حذف المهمة');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert(error.message || 'حدث خطأ أثناء حذف المهمة');
    });
}

// Update task assigned_to when selecting employee in add modal
document.getElementById('task-assigned-to').addEventListener('change', function() {
    document.getElementById('task-list-type').value = this.value ? 'employee' : 'tasks';
});

// Sync color picker with hex input in add modal
document.getElementById('task-color').addEventListener('input', function() {
    document.getElementById('task-color-hex').value = this.value;
});

document.getElementById('task-color-hex').addEventListener('input', function() {
    const hexValue = this.value;
    if (/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/.test(hexValue)) {
        document.getElementById('task-color').value = hexValue;
    }
});

// Sync color picker with hex input in edit modal
document.getElementById('edit-task-color').addEventListener('input', function() {
    document.getElementById('edit-task-color-hex').value = this.value;
});

document.getElementById('edit-task-color-hex').addEventListener('input', function() {
    const hexValue = this.value;
    if (/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/.test(hexValue)) {
        document.getElementById('edit-task-color').value = hexValue;
    }
});

// Goals Modal Functions
function showGoalsModal() {
    document.getElementById('goals-modal').classList.remove('hidden');
}

function hideGoalsModal() {
    document.getElementById('goals-modal').classList.add('hidden');
}

// Employees Modal Functions
function showEmployeesModal() {
    document.getElementById('employees-modal').classList.remove('hidden');
}

function hideEmployeesModal() {
    document.getElementById('employees-modal').classList.add('hidden');
}

// Close modals when clicking outside
document.getElementById('goals-modal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        hideGoalsModal();
    }
});

document.getElementById('employees-modal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        hideEmployeesModal();
    }
});

// Close modals on ESC key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        hideGoalsModal();
        hideEmployeesModal();
        hideQuickAssignModal();
    }
});

// Quick Assign Employee Modal Functions
let currentQuickAssignTaskId = null;
let currentQuickAssignTaskStatus = null;

function showQuickAssignModal(taskId, currentAssignedTo, currentStatus) {
    currentQuickAssignTaskId = taskId;
    currentQuickAssignTaskStatus = currentStatus || null;
    document.getElementById('quick-assign-task-id').value = taskId;
    document.getElementById('quick-assign-task-status').value = currentStatus || '';
    document.getElementById('quick-assign-employee').value = currentAssignedTo || '';
    
    // إظهار زر "نشر" فقط إذا كانت الحالة "archived"
    const publishBtn = document.getElementById('publish-task-btn');
    if (currentStatus === 'archived') {
        publishBtn.classList.remove('hidden');
    } else {
        publishBtn.classList.add('hidden');
    }
    
    document.getElementById('quick-assign-modal').classList.remove('hidden');
}

function hideQuickAssignModal() {
    document.getElementById('quick-assign-modal').classList.add('hidden');
    currentQuickAssignTaskId = null;
    currentQuickAssignTaskStatus = null;
    document.getElementById('quick-assign-form').reset();
    document.getElementById('publish-task-btn').classList.add('hidden');
}

// Archive Task Function
function archiveTask() {
    if (!currentQuickAssignTaskId) return;
    
    if (!confirm('هل أنت متأكد من نقل هذه المهمة إلى الأرشيف؟')) {
        return;
    }

    // Update task status to archived and remove assigned employee
    fetch(`/monthly-plans/${monthlyPlanId}/tasks/${currentQuickAssignTaskId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            status: 'archived',
            assigned_to: null,
            _method: 'PUT'
        })
    })
    .then(response => {
        return response.json().then(data => {
            if (!response.ok) {
                throw new Error(data.error || data.message || 'حدث خطأ أثناء نقل المهمة إلى الأرشيف');
            }
            return data;
        });
    })
    .then(data => {
        if (data.success) {
            hideQuickAssignModal();
            location.reload();
        } else {
            alert(data.error || 'حدث خطأ أثناء نقل المهمة إلى الأرشيف');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert(error.message || 'حدث خطأ أثناء نقل المهمة إلى الأرشيف');
    });
}

// Publish Task Function (from archive)
function publishTask() {
    if (!currentQuickAssignTaskId) return;
    
    if (!confirm('هل أنت متأكد من نقل هذه المهمة من الأرشيف إلى النشر؟')) {
        return;
    }

    // Update task status to publish
    fetch(`/monthly-plans/${monthlyPlanId}/tasks/${currentQuickAssignTaskId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            status: 'publish',
            _method: 'PUT'
        })
    })
    .then(response => {
        return response.json().then(data => {
            if (!response.ok) {
                throw new Error(data.error || data.message || 'حدث خطأ أثناء نقل المهمة إلى النشر');
            }
            return data;
        });
    })
    .then(data => {
        if (data.success) {
            hideQuickAssignModal();
            location.reload();
        } else {
            alert(data.error || 'حدث خطأ أثناء نقل المهمة إلى النشر');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert(error.message || 'حدث خطأ أثناء نقل المهمة إلى النشر');
    });
}

// Quick Assign Form Submit
document.getElementById('quick-assign-form').addEventListener('submit', function(e) {
    e.preventDefault();
    if (!currentQuickAssignTaskId) return;

    const assignedTo = document.getElementById('quick-assign-employee').value || null;
    const listType = assignedTo ? 'employee' : 'tasks';

    fetch(`/monthly-plans/${monthlyPlanId}/tasks/${currentQuickAssignTaskId}/quick-assign`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            assigned_to: assignedTo,
            list_type: listType
        })
    })
    .then(response => {
        return response.json().then(data => {
            if (!response.ok) {
                throw new Error(data.error || data.message || 'حدث خطأ أثناء تحديث الموظف');
            }
            return data;
        });
    })
    .then(data => {
        if (data.success) {
            hideQuickAssignModal();
            location.reload();
        } else {
            alert(data.error || 'حدث خطأ أثناء تحديث الموظف');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert(error.message || 'حدث خطأ أثناء تحديث الموظف');
    });
});

// Close quick assign modal when clicking outside
document.getElementById('quick-assign-modal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        hideQuickAssignModal();
    }
});
</script>
@endsection

