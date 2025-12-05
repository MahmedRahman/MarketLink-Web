@extends('layouts.dashboard')

@section('title', 'تعديل الاجتماع')
@section('page-title', 'تعديل الاجتماع')
@section('page-description', 'تعديل بيانات الاجتماع: ' . $meeting->title)

@section('content')
<div class="container mx-auto px-4">
    <div class="max-w-4xl mx-auto space-y-6">
        <!-- Header -->
        <div class="card page-header rounded-2xl p-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="w-12 h-12 logo-gradient rounded-2xl flex items-center justify-center shadow-lg icon-spacing ml-3">
                        <i class="fas fa-edit text-white text-xl"></i>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800">تعديل الاجتماع</h2>
                        <p class="text-gray-600">تعديل بيانات الاجتماع: {{ $meeting->title }}</p>
                    </div>
                </div>
                <a href="{{ route('meetings.index') }}" class="flex items-center px-4 py-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-xl transition-colors icon-spacing">
                    العودة للقائمة
                </a>
            </div>
        </div>

        <!-- Form Card -->
        <div class="card rounded-2xl p-8">
            @if ($errors->any())
                <div class="mb-6 bg-red-50 border border-red-200 rounded-xl p-4">
                    <div class="flex items-center mb-2">
                        <i class="fas fa-exclamation-triangle text-red-500 ml-2"></i>
                        <h3 class="text-red-800 font-semibold">يرجى تصحيح الأخطاء التالية:</h3>
                    </div>
                    <ul class="text-red-700 text-sm space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>• {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('meetings.update', $meeting) }}" class="space-y-8">
                @csrf
                @method('PUT')
                
                <!-- Basic Information Section -->
                <div class="form-section space-y-6">
                    <h3 class="text-lg font-semibold text-gray-800">المعلومات الأساسية</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Meeting Date -->
                        <div>
                            <label for="meeting_date" class="block text-sm font-medium text-gray-700 mb-2">
                                تاريخ الاجتماع <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="date"
                                id="meeting_date"
                                name="meeting_date"
                                value="{{ old('meeting_date', $meeting->meeting_date->format('Y-m-d')) }}"
                                required
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-colors"
                            />
                            @error('meeting_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Meeting Time -->
                        <div>
                            <label for="meeting_time" class="block text-sm font-medium text-gray-700 mb-2">
                                معاد الاجتماع <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="time"
                                id="meeting_time"
                                name="meeting_time"
                                value="{{ old('meeting_time', \Carbon\Carbon::parse($meeting->meeting_time)->format('H:i')) }}"
                                required
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-colors"
                            />
                            @error('meeting_time')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Title -->
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                            عنوان الاجتماع <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="text"
                            id="title"
                            name="title"
                            value="{{ old('title', $meeting->title) }}"
                            required
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-colors"
                            placeholder="أدخل عنوان الاجتماع"
                        />
                        @error('title')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Objective -->
                    <div>
                        <label for="objective" class="block text-sm font-medium text-gray-700 mb-2">
                            الهدف من الاجتماع
                        </label>
                        <textarea
                            id="objective"
                            name="objective"
                            rows="4"
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-colors"
                            placeholder="أدخل الهدف من الاجتماع"
                        >{{ old('objective', $meeting->objective) }}</textarea>
                        @error('objective')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Responsible Employee -->
                    <div>
                        <label for="responsible_employee_id" class="block text-sm font-medium text-gray-700 mb-2">
                            الموظف المسؤول <span class="text-red-500">*</span>
                        </label>
                        <select
                            id="responsible_employee_id"
                            name="responsible_employee_id"
                            required
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-colors select2"
                        >
                            <option value="">اختر الموظف المسؤول</option>
                            @foreach($employees as $employee)
                                <option value="{{ $employee->id }}" {{ old('responsible_employee_id', $meeting->responsible_employee_id) == $employee->id ? 'selected' : '' }}>
                                    {{ $employee->name }} @if($employee->email) - {{ $employee->email }} @endif
                                </option>
                            @endforeach
                        </select>
                        @error('responsible_employee_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Project and Link Section -->
                <div class="form-section space-y-6">
                    <h3 class="text-lg font-semibold text-gray-800">المشروع ورابط الاجتماع</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Project -->
                        <div>
                            <label for="project_id" class="block text-sm font-medium text-gray-700 mb-2">
                                المشروع (اختياري)
                            </label>
                            <select
                                id="project_id"
                                name="project_id"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-colors select2"
                            >
                                <option value="">اختر المشروع</option>
                                @foreach($projects as $project)
                                    <option value="{{ $project->id }}" {{ old('project_id', $meeting->project_id) == $project->id ? 'selected' : '' }}>
                                        {{ $project->business_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('project_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Meeting Link -->
                        <div>
                            <label for="meeting_link" class="block text-sm font-medium text-gray-700 mb-2">
                                رابط الاجتماع
                            </label>
                            <input
                                type="url"
                                id="meeting_link"
                                name="meeting_link"
                                value="{{ old('meeting_link', $meeting->meeting_link) }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-colors"
                                placeholder="https://meet.google.com/..."
                            />
                            @error('meeting_link')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Attendees Section -->
                <div class="form-section space-y-6">
                    <h3 class="text-lg font-semibold text-gray-800">الحضور</h3>
                    <p class="text-sm text-gray-600">اختر الأشخاص المطلوب حضورهم للاجتماع</p>
                    
                    @php
                        $selectedAttendees = old('attendees', $meeting->attendees ?? []);
                        if (!is_array($selectedAttendees)) {
                            $selectedAttendees = [];
                        }
                    @endphp
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($employees as $employee)
                            <label class="flex items-center cursor-pointer p-3 border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors">
                                <input
                                    type="checkbox"
                                    name="attendees[]"
                                    value="{{ $employee->id }}"
                                    {{ in_array($employee->id, $selectedAttendees) ? 'checked' : '' }}
                                    class="w-4 h-4 text-primary border-gray-300 rounded focus:ring-primary focus:ring-2"
                                />
                                <span class="mr-2 text-sm text-gray-700">{{ $employee->name }}</span>
                            </label>
                        @endforeach
                    </div>
                    @error('attendees')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Actions -->
                <div class="flex items-center justify-center rtl-spacing pt-8 border-t border-gray-200">
                    <button type="submit" class="action-button btn-primary text-white px-8 py-4 rounded-2xl flex items-center font-medium text-lg min-w-[160px] justify-center">
                        <i class="fas fa-save text-lg ml-3"></i>
                        حفظ التغييرات
                    </button>   
                    
                    <a href="{{ route('meetings.index') }}" class="action-button cancel-button flex items-center px-8 py-4 rounded-2xl font-medium text-lg min-w-[140px] justify-center">
                        إلغاء
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Initialize Select2
    $('.select2').select2({
        placeholder: 'اختر من القائمة',
        allowClear: true,
        dir: 'rtl',
        width: '100%',
        language: {
            noResults: function() {
                return 'لا توجد نتائج';
            },
            searching: function() {
                return 'جاري البحث...';
            }
        }
    });
});
</script>
@endsection

