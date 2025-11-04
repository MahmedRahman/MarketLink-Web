@extends('layouts.employee')

@section('title', 'لوحة التحكم')
@section('page-title', 'لوحة التحكم')
@section('page-description', 'مرحباً بك ' . $employee->name)

@section('content')
<div class="space-y-6">
    <!-- Welcome Card -->
    <div class="card p-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-bold text-gray-800">مرحباً {{ $employee->name }}</h2>
                <p class="text-gray-600 mt-1">
                    @if($hasManagerRole)
                        إليك جميع المهام في المشاريع التي تديرها
                    @else
                        إليك المهام المخصصة لك
                    @endif
                </p>
                @if($hasManagerRole)
                    <div class="mt-3 flex items-center space-x-2 space-x-reverse">
                        <span class="px-3 py-1 text-xs rounded-full bg-orange-100 text-orange-700 flex items-center">
                            <span class="material-icons text-xs ml-1">admin_panel_settings</span>
                            لديك صلاحيات المدير على {{ $stats['managed_projects_count'] }} مشروع
                        </span>
                        <a href="{{ route('employee.monthly-plans.index') }}" class="text-sm text-purple-600 hover:text-purple-700 font-medium">
                            عرض الخطط الشهرية →
                        </a>
                    </div>
                @endif
            </div>
            <div class="w-16 h-16 logo-gradient rounded-xl flex items-center justify-center">
                <span class="material-icons text-white text-2xl">task</span>
            </div>
        </div>
    </div>

        <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-{{ $hasManagerRole ? '6' : '5' }} gap-4">
        <div class="card p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">إجمالي المهام</p>
                    <p class="text-2xl font-bold text-gray-800 mt-1">{{ $stats['total'] }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <span class="material-icons text-blue-600">list</span>
                </div>
            </div>
        </div>

        <div class="card p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">مهام جديدة</p>
                    <p class="text-2xl font-bold text-gray-800 mt-1">{{ $stats['todo'] }}</p>
                </div>
                <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center">
                    <span class="material-icons text-gray-600">assignment</span>
                </div>
            </div>
        </div>

        <div class="card p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">قيد التنفيذ</p>
                    <p class="text-2xl font-bold text-gray-800 mt-1">{{ $stats['in_progress'] }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <span class="material-icons text-blue-600">sync</span>
                </div>
            </div>
        </div>

        <div class="card p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">قيد المراجعة</p>
                    <p class="text-2xl font-bold text-gray-800 mt-1">{{ $stats['review'] }}</p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <span class="material-icons text-yellow-600">visibility</span>
                </div>
            </div>
        </div>

        <div class="card p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">مكتملة</p>
                    <p class="text-2xl font-bold text-gray-800 mt-1">{{ $stats['done'] }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <span class="material-icons text-green-600">check_circle</span>
                </div>
            </div>
        </div>

        @if($hasManagerRole)
            <div class="card p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">مهام مخصصة لي</p>
                        <p class="text-2xl font-bold text-gray-800 mt-1">{{ $stats['assigned_only'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                        <span class="material-icons text-purple-600">person</span>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Tasks by Status -->
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <!-- Todo Tasks -->
        <div class="card p-4">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-gray-800">مهام جديدة</h3>
                <span class="bg-gray-200 text-gray-700 text-xs px-2 py-1 rounded-full">{{ $stats['todo'] }}</span>
            </div>
            <div class="space-y-3 max-h-[600px] overflow-y-auto">
                @forelse($tasksByStatus['todo'] as $task)
                    @include('employee.partials.task-card', ['task' => $task, 'hasManagerRole' => $hasManagerRole, 'managedProjectIds' => $managedProjectIds ?? []])
                @empty
                    <p class="text-sm text-gray-500 text-center py-4">لا توجد مهام</p>
                @endforelse
            </div>
        </div>

        <!-- In Progress Tasks -->
        <div class="card p-4">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-gray-800">قيد التنفيذ</h3>
                <span class="bg-blue-200 text-blue-700 text-xs px-2 py-1 rounded-full">{{ $stats['in_progress'] }}</span>
            </div>
            <div class="space-y-3 max-h-[600px] overflow-y-auto">
                @forelse($tasksByStatus['in_progress'] as $task)
                    @include('employee.partials.task-card', ['task' => $task, 'hasManagerRole' => $hasManagerRole, 'managedProjectIds' => $managedProjectIds ?? []])
                @empty
                    <p class="text-sm text-gray-500 text-center py-4">لا توجد مهام</p>
                @endforelse
            </div>
        </div>

        <!-- Review Tasks -->
        <div class="card p-4">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-gray-800">قيد المراجعة</h3>
                <span class="bg-yellow-200 text-yellow-700 text-xs px-2 py-1 rounded-full">{{ $stats['review'] }}</span>
            </div>
            <div class="space-y-3 max-h-[600px] overflow-y-auto">
                @forelse($tasksByStatus['review'] as $task)
                    @include('employee.partials.task-card', ['task' => $task, 'hasManagerRole' => $hasManagerRole, 'managedProjectIds' => $managedProjectIds ?? []])
                @empty
                    <p class="text-sm text-gray-500 text-center py-4">لا توجد مهام</p>
                @endforelse
            </div>
        </div>

        <!-- Done Tasks -->
        <div class="card p-4">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-gray-800">مكتملة</h3>
                <span class="bg-green-200 text-green-700 text-xs px-2 py-1 rounded-full">{{ $stats['done'] }}</span>
            </div>
            <div class="space-y-3 max-h-[600px] overflow-y-auto">
                @forelse($tasksByStatus['done'] as $task)
                    @include('employee.partials.task-card', ['task' => $task, 'hasManagerRole' => $hasManagerRole, 'managedProjectIds' => $managedProjectIds ?? []])
                @empty
                    <p class="text-sm text-gray-500 text-center py-4">لا توجد مهام</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

