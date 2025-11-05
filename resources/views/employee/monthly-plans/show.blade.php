@extends('layouts.employee')

@section('title', 'تفاصيل الخطة الشهرية')
@section('page-title', 'تفاصيل الخطة الشهرية')
@section('page-description', 'متابعة وتنظيم الخطة الشهرية')

@section('content')
<div class="container mx-auto px-4">
    <div class="max-w-7xl mx-auto space-y-6">
        <!-- Header -->
        <div class="card p-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="w-12 h-12 logo-gradient rounded-xl flex items-center justify-center shadow-lg ml-3">
                        <span class="material-icons text-white text-xl">calendar_month</span>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800">{{ $monthlyPlan->project->business_name }}</h2>
                        <p class="text-gray-600">الخطة الشهرية: {{ $monthlyPlan->month }} {{ $monthlyPlan->year }}</p>
                    </div>
                </div>
                <a href="{{ route('employee.monthly-plans.index') }}" class="flex items-center px-4 py-2 text-gray-600 hover:text-gray-800 hover:bg-gray-100 rounded-xl transition-colors">
                    <span class="material-icons text-sm ml-2">arrow_back</span>
                    العودة للقائمة
                </a>
            </div>
        </div>

        <!-- Plan Info Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Goals Card -->
            <div class="card p-6">
                <div class="flex items-center mb-4">
                    <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center ml-3">
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
                                {{ $goal->achieved_value }} / {{ $goal->target_value }}
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Employees Card -->
            <div class="card p-6">
                <div class="flex items-center mb-4">
                    <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center ml-3">
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
            <div class="card p-6">
                <div class="flex items-center mb-4">
                    <div class="w-10 h-10 bg-purple-100 rounded-xl flex items-center justify-center ml-3">
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

        <!-- Kanban Board Section -->
        <div id="kanban-section" class="card p-6">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-indigo-100 rounded-xl flex items-center justify-center ml-3">
                        <span class="material-icons text-indigo-600">view_kanban</span>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-800">متابعة الخطة</h3>
                </div>
            </div>

            <!-- Board -->
            <div id="kanban-board" class="flex gap-4 overflow-x-auto pb-4" style="min-height: 500px;">
                <!-- Tasks List -->
                <div class="flex-shrink-0 w-80 bg-gray-50 rounded-xl p-4">
                    <div class="flex items-center justify-between mb-4">
                        <h4 class="font-semibold text-gray-800">مهام</h4>
                        <span class="bg-gray-200 text-gray-700 text-xs px-2 py-1 rounded-full">
                            {{ $tasksByList->get('tasks', collect())->count() }}
                        </span>
                    </div>
                    <div class="min-h-[400px] space-y-2">
                        @foreach($tasksByList->get('tasks', collect())->sortBy('order') as $task)
                            @include('employee.partials.task-card-manager', ['task' => $task])
                        @endforeach
                    </div>
                </div>

                <!-- Employee Lists -->
                @foreach($monthlyPlan->employees as $employee)
                    @php
                        $employeeTasksKey = 'employee_' . $employee->id;
                        $employeeTasks = $tasksByList->get($employeeTasksKey, collect())->sortBy('order');
                    @endphp
                    <div class="flex-shrink-0 w-80 bg-gray-50 rounded-xl p-4">
                        <div class="flex items-center justify-between mb-4">
                            <h4 class="font-semibold text-gray-800">{{ $employee->name }}</h4>
                            <span class="bg-gray-200 text-gray-700 text-xs px-2 py-1 rounded-full">
                                {{ $employeeTasks->count() }}
                            </span>
                        </div>
                        <div class="min-h-[400px] space-y-2">
                            @foreach($employeeTasks as $task)
                                @include('employee.partials.task-card-manager', ['task' => $task])
                            @endforeach
                        </div>
                    </div>
                @endforeach

                <!-- Ready List -->
                <div class="flex-shrink-0 w-80 bg-orange-50 rounded-xl p-4">
                    <div class="flex items-center justify-between mb-4">
                        <h4 class="font-semibold text-gray-800">Ready</h4>
                        <span class="bg-orange-200 text-orange-700 text-xs px-2 py-1 rounded-full">
                            {{ $tasksByList->get('ready', collect())->count() }}
                        </span>
                    </div>
                    <div class="min-h-[400px] space-y-2">
                        @foreach($tasksByList->get('ready', collect())->sortBy('order') as $task)
                            @include('employee.partials.task-card-manager', ['task' => $task])
                        @endforeach
                    </div>
                </div>

                <!-- Publish List -->
                <div class="flex-shrink-0 w-80 bg-green-50 rounded-xl p-4">
                    <div class="flex items-center justify-between mb-4">
                        <h4 class="font-semibold text-gray-800">Publish</h4>
                        <span class="bg-green-200 text-green-700 text-xs px-2 py-1 rounded-full">
                            {{ $tasksByList->get('publish', collect())->count() }}
                        </span>
                    </div>
                    <div class="min-h-[400px] space-y-2">
                        @foreach($tasksByList->get('publish', collect())->sortBy('order') as $task)
                            @include('employee.partials.task-card-manager', ['task' => $task])
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

