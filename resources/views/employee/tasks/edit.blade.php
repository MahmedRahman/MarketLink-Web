@extends('layouts.employee')

@section('title', 'تعديل المهمة')
@section('page-title', 'تعديل المهمة')
@section('page-description', 'تعديل بيانات المهمة')

@section('content')
<div class="container mx-auto px-4">
    <div class="max-w-4xl mx-auto space-y-6">
        <!-- Header -->
        <div class="card p-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="w-12 h-12 logo-gradient rounded-xl flex items-center justify-center shadow-lg ml-3" style="background: {{ $task->color ?? '#6366f1' }};">
                        <span class="material-icons text-white text-xl">edit</span>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800">تعديل المهمة</h2>
                        <p class="text-gray-600">{{ $task->title }}</p>
                    </div>
                </div>
                <a href="{{ route('employee.dashboard') }}" class="flex items-center px-4 py-2 text-gray-600 hover:text-gray-800 hover:bg-gray-100 rounded-xl transition-colors">
                    <span class="material-icons text-sm ml-2">arrow_back</span>
                    العودة للوحة التحكم
                </a>
            </div>
        </div>

        <!-- Form Card -->
        <div class="card p-8">
            @if ($errors->any())
                <div class="mb-6 bg-red-50 border border-red-200 rounded-xl p-4">
                    <div class="flex items-center mb-2">
                        <span class="material-icons text-red-500 ml-2">error</span>
                        <h3 class="text-red-800 font-semibold">يرجى تصحيح الأخطاء التالية:</h3>
                    </div>
                    <ul class="text-red-700 text-sm space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>• {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('employee.tasks.update', $task->id) }}" class="space-y-6">
                @csrf
                @method('PUT')

                @if($isManager)
                    <!-- Title (Only for Managers) -->
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                            العنوان <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            id="title" 
                            name="title" 
                            value="{{ old('title', $task->title) }}"
                            required
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-colors"
                        />
                        @error('title')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                @else
                    <!-- Task Info -->
                    <div class="bg-gray-50 rounded-xl p-4 mb-6">
                        <h3 class="font-semibold text-gray-800 mb-3">معلومات المهمة</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-600">العنوان</p>
                                <p class="font-medium text-gray-800">{{ $task->title }}</p>
                            </div>
                            @if($task->monthlyPlan)
                                <div>
                                    <p class="text-sm text-gray-600">المشروع</p>
                                    <p class="font-medium text-gray-800">{{ $task->monthlyPlan->project->business_name ?? 'مشروع' }}</p>
                                </div>
                            @endif
                            @if($task->due_date)
                                <div>
                                    <p class="text-sm text-gray-600">تاريخ الانتهاء</p>
                                    <p class="font-medium text-gray-800">{{ $task->due_date->format('Y-m-d') }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- Status -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                        الحالة <span class="text-red-500">*</span>
                    </label>
                    <select 
                        id="status" 
                        name="status" 
                        required
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-colors"
                    >
                        <option value="todo" {{ old('status', $task->status) == 'todo' ? 'selected' : '' }}>مهام</option>
                        <option value="in_progress" {{ old('status', $task->status) == 'in_progress' ? 'selected' : '' }}>قيد التنفيذ</option>
                        <option value="review" {{ old('status', $task->status) == 'review' ? 'selected' : '' }}>قيد المراجعة</option>
                        <option value="done" {{ old('status', $task->status) == 'done' ? 'selected' : '' }}>مكتملة</option>
                    </select>
                    @error('status')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                        الوصف
                    </label>
                    <textarea 
                        id="description" 
                        name="description" 
                        rows="6"
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-colors"
                        placeholder="أدخل وصف المهمة (اختياري)"
                    >{{ old('description', $task->description) }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                @if($isManager)
                    <!-- Assigned To (Only for Managers) -->
                    <div>
                        <label for="assigned_to" class="block text-sm font-medium text-gray-700 mb-2">
                            المخصص إلى
                        </label>
                        <select 
                            id="assigned_to" 
                            name="assigned_to" 
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-colors"
                        >
                            <option value="">غير مخصص</option>
                            @foreach($employees as $employee)
                                <option value="{{ $employee->id }}" {{ old('assigned_to', $task->assigned_to) == $employee->id ? 'selected' : '' }}>
                                    {{ $employee->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('assigned_to')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Due Date (Only for Managers) -->
                    <div>
                        <label for="due_date" class="block text-sm font-medium text-gray-700 mb-2">
                            تاريخ الانتهاء
                        </label>
                        <input 
                            type="date" 
                            id="due_date" 
                            name="due_date" 
                            value="{{ old('due_date', $task->due_date ? $task->due_date->format('Y-m-d') : '') }}"
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-colors"
                        />
                        @error('due_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                @endif

                <!-- Actions -->
                <div class="flex items-center justify-center space-x-4 space-x-reverse pt-6 border-t border-gray-200">
                    <button type="submit" class="btn-primary text-white px-8 py-3 rounded-xl flex items-center font-medium">
                        <span class="material-icons text-sm ml-2">save</span>
                        حفظ التغييرات
                    </button>
                    @if($isManager)
                        <form method="POST" action="{{ route('employee.tasks.destroy', $task->id) }}" class="inline" onsubmit="return confirm('هل أنت متأكد من حذف هذه المهمة؟');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-8 py-3 bg-red-600 text-white rounded-xl hover:bg-red-700 transition-colors font-medium">
                                <span class="material-icons text-sm ml-2">delete</span>
                                حذف المهمة
                            </button>
                        </form>
                    @endif
                    <a href="{{ route('employee.dashboard') }}" class="px-8 py-3 border border-gray-300 rounded-xl text-gray-700 hover:bg-gray-50 transition-colors font-medium">
                        إلغاء
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

