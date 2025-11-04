@extends('layouts.employee')

@section('title', 'تفاصيل المهمة')
@section('page-title', 'تفاصيل المهمة')
@section('page-description', 'عرض تفاصيل المهمة')

@section('content')
<div class="container mx-auto px-4">
    <div class="max-w-5xl mx-auto space-y-6">
        <!-- Header -->
        <div class="card p-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="w-12 h-12 logo-gradient rounded-xl flex items-center justify-center shadow-lg ml-3" style="background: {{ $task->color ?? '#6366f1' }};">
                        <span class="material-icons text-white text-xl">task</span>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800">{{ $task->title }}</h2>
                        <p class="text-gray-600">تفاصيل المهمة</p>
                    </div>
                </div>
                <div class="flex items-center space-x-3 space-x-reverse">
                    <a href="{{ route('employee.tasks.edit', $task->id) }}" class="flex items-center px-4 py-2 text-purple-600 hover:text-purple-800 hover:bg-purple-50 rounded-xl transition-colors">
                        <span class="material-icons text-sm ml-2">edit</span>
                        تعديل
                    </a>
                    <a href="{{ route('employee.dashboard') }}" class="flex items-center px-4 py-2 text-gray-600 hover:text-gray-800 hover:bg-gray-100 rounded-xl transition-colors">
                        <span class="material-icons text-sm ml-2">arrow_back</span>
                        العودة
                    </a>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg flex items-center">
                <span class="material-icons ml-2">check_circle</span>
                {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Task Details -->
                <div class="card p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">تفاصيل المهمة</h3>
                    
                    @if($task->description)
                        <div class="mb-6">
                            <h4 class="text-sm font-medium text-gray-700 mb-2">الوصف</h4>
                            <div class="text-gray-800 whitespace-pre-wrap">{{ $task->description }}</div>
                        </div>
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <h4 class="text-sm font-medium text-gray-700 mb-2">الحالة</h4>
                            <span class="px-3 py-1 text-sm rounded-full bg-{{ $task->status_color }}-100 text-{{ $task->status_color }}-800">
                                {{ $task->status_badge }}
                            </span>
                        </div>

                        @if($task->goal)
                            <div>
                                <h4 class="text-sm font-medium text-gray-700 mb-2">الهدف</h4>
                                <p class="text-gray-800">{{ $task->goal->goal_name }}</p>
                            </div>
                        @endif

                        @if($task->due_date)
                            <div>
                                <h4 class="text-sm font-medium text-gray-700 mb-2">تاريخ الانتهاء</h4>
                                <p class="text-gray-800 flex items-center">
                                    <span class="material-icons text-sm ml-1">event</span>
                                    {{ $task->due_date->format('Y-m-d') }}
                                    @if($task->due_date->isPast() && $task->status !== 'done')
                                        <span class="text-red-500 mr-2">(متأخرة)</span>
                                    @endif
                                </p>
                            </div>
                        @endif

                        @if($task->monthlyPlan)
                            <div>
                                <h4 class="text-sm font-medium text-gray-700 mb-2">الخطة الشهرية</h4>
                                <p class="text-gray-800">{{ $task->monthlyPlan->month }} {{ $task->monthlyPlan->year }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Files -->
                @if($task->files && $task->files->count() > 0)
                    <div class="card p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">المرفقات</h3>
                        <div class="space-y-2">
                            @foreach($task->files as $file)
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                    <div class="flex items-center">
                                        <span class="material-icons text-gray-500 ml-2">attach_file</span>
                                        <span class="text-sm text-gray-800">{{ $file->file_name }}</span>
                                    </div>
                                    <a href="{{ route('monthly-plans.tasks.files.download', [$task->monthly_plan_id, $task->id, $file->id]) }}" 
                                       class="text-blue-500 hover:text-blue-700 text-sm">
                                        تحميل
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Quick Actions -->
                <div class="card p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">إجراءات سريعة</h3>
                    <div class="space-y-3">
                        <a href="{{ route('employee.tasks.edit', $task->id) }}" 
                           class="w-full btn-primary text-white px-4 py-3 rounded-xl flex items-center justify-center">
                            <span class="material-icons text-sm ml-2">edit</span>
                            تعديل المهمة
                        </a>
                    </div>
                </div>

                <!-- Task Info -->
                <div class="card p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">معلومات إضافية</h3>
                    <div class="space-y-3 text-sm">
                        <div>
                            <span class="text-gray-600">تاريخ الإنشاء:</span>
                            <span class="text-gray-800 font-medium">{{ $task->created_at->format('Y-m-d') }}</span>
                        </div>
                        <div>
                            <span class="text-gray-600">آخر تحديث:</span>
                            <span class="text-gray-800 font-medium">{{ $task->updated_at->format('Y-m-d') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

