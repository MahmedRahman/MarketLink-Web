@extends('layouts.dashboard')

@section('title', 'إضافة خطة شهرية جديدة')
@section('page-title', 'إضافة خطة شهرية جديدة')
@section('page-description', 'إنشاء خطة شهرية جديدة لمشروع')

@section('content')
<div class="container mx-auto px-4">
    <div class="max-w-5xl mx-auto space-y-6">
        <!-- Header -->
        <div class="card page-header rounded-2xl p-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="w-12 h-12 logo-gradient rounded-2xl flex items-center justify-center shadow-lg icon-spacing ml-3">
                        <span class="material-icons text-white text-xl">calendar_month</span>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800">إضافة خطة شهرية جديدة</h2>
                        <p class="text-gray-600">املأ البيانات التالية لإنشاء خطة شهرية جديدة</p>
                    </div>
                </div>
                <a href="{{ route('monthly-plans.index') }}" class="flex items-center px-4 py-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-xl transition-colors icon-spacing">
                    العودة للقائمة
                </a>
            </div>
        </div>

        <!-- Form Card -->
        <div class="card rounded-2xl p-8">
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

            @if (session('error'))
                <div class="mb-6 bg-red-50 border border-red-200 rounded-xl p-4">
                    <div class="flex items-center">
                        <span class="material-icons text-red-500 ml-2">error</span>
                        <span class="text-red-800">{{ session('error') }}</span>
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('monthly-plans.store') }}" class="space-y-8" id="monthly-plan-form">
                @csrf
                
                <!-- Basic Information Section -->
                <div class="form-section space-y-6">
                    <h3 class="text-lg font-semibold text-gray-800">المعلومات الأساسية</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Project -->
                        <div>
                            <label for="project_id" class="block text-sm font-medium text-gray-700 mb-2">
                                المشروع <span class="text-red-500">*</span>
                            </label>
                            <select
                                id="project_id"
                                name="project_id"
                                required
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-colors"
                            >
                                <option value="">اختر المشروع</option>
                                @foreach($projects as $project)
                                    <option value="{{ $project->id }}" {{ old('project_id') == $project->id ? 'selected' : '' }}>
                                        {{ $project->business_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('project_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Month -->
                        <div>
                            <label for="month" class="block text-sm font-medium text-gray-700 mb-2">
                                الشهر <span class="text-red-500">*</span>
                            </label>
                            <select
                                id="month"
                                name="month"
                                required
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-colors"
                            >
                                <option value="">اختر الشهر</option>
                                @php
                                    $months = [
                                        1 => 'يناير', 2 => 'فبراير', 3 => 'مارس', 4 => 'أبريل',
                                        5 => 'مايو', 6 => 'يونيو', 7 => 'يوليو', 8 => 'أغسطس',
                                        9 => 'سبتمبر', 10 => 'أكتوبر', 11 => 'نوفمبر', 12 => 'ديسمبر'
                                    ];
                                @endphp
                                @foreach($months as $num => $name)
                                    <option value="{{ $name }}" data-month="{{ $num }}" {{ old('month') == $name ? 'selected' : '' }}>
                                        {{ $name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('month')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Year -->
                        <div>
                            <label for="year" class="block text-sm font-medium text-gray-700 mb-2">
                                السنة <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="number"
                                id="year"
                                name="year"
                                value="{{ old('year', date('Y')) }}"
                                min="2020"
                                max="2100"
                                required
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-colors"
                            />
                            <input type="hidden" id="month_number" name="month_number" value="{{ old('month_number') }}">
                            @error('year')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                            الوصف
                        </label>
                        <textarea
                            id="description"
                            name="description"
                            rows="3"
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-colors"
                            placeholder="أدخل وصف للخطة الشهرية (اختياري)"
                        >{{ old('description') }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Goals Section -->
                <div class="form-section space-y-6">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-800">أهداف الخطة</h3>
                        <button type="button" id="add-goal" class="btn-primary text-white px-4 py-2 rounded-lg flex items-center">
                            <span class="material-icons text-sm ml-2">add</span>
                            إضافة هدف
                        </button>
                    </div>
                    
                    <div id="goals-container" class="space-y-4">
                        <div class="goal-item p-4 border border-gray-200 rounded-xl">
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">نوع الهدف</label>
                                    <select
                                        name="goals[0][goal_type]"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
                                    >
                                        <option value="posts">نشر بوستات</option>
                                        <option value="designs">عمل تصميمات</option>
                                        <option value="ads_budget">ميزانية إعلانات</option>
                                        <option value="custom">هدف مخصص</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">اسم الهدف</label>
                                    <input
                                        type="text"
                                        name="goals[0][goal_name]"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
                                        placeholder="مثال: نشر 12 بوست"
                                        required
                                    />
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">القيمة المستهدفة</label>
                                    <input
                                        type="number"
                                        name="goals[0][target_value]"
                                        min="0"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
                                        placeholder="12"
                                        required
                                    />
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">الوحدة</label>
                                    <input
                                        type="text"
                                        name="goals[0][unit]"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
                                        placeholder="بوست، تصميم، جنيه"
                                    />
                                </div>
                            </div>
                            <div class="mt-3">
                                <label class="block text-sm font-medium text-gray-700 mb-2">وصف الهدف (اختياري)</label>
                                <textarea
                                    name="goals[0][description]"
                                    rows="2"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
                                    placeholder="وصف إضافي للهدف"
                                ></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Employees Section -->
                <div class="form-section space-y-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800">الموظفين المشاركين <span class="text-red-500">*</span></h3>
                            <p class="text-sm text-gray-600">اختر الموظفين الذين سيعملون على هذه الخطة</p>
                        </div>
                        @if($employees->count() > 0)
                            <button type="button" id="select-all-employees" class="text-sm text-primary hover:text-primary-700 font-medium">
                                تحديد الكل
                            </button>
                        @endif
                    </div>
                    
                    @if($employees->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4" id="employees-grid">
                            @foreach($employees as $employee)
                                <label class="flex items-center justify-between p-4 border-2 border-gray-200 rounded-xl hover:border-primary hover:bg-primary-50 transition-all cursor-pointer employee-checkbox-label group" style="position: relative;">
                                    <input
                                        type="checkbox"
                                        name="employee_ids[]"
                                        value="{{ $employee->id }}"
                                        class="employee-checkbox w-6 h-6 rounded border-2 border-gray-300 text-primary focus:ring-2 focus:ring-primary cursor-pointer"
                                        style="cursor: pointer; z-index: 10; position: relative;"
                                        {{ in_array($employee->id, old('employee_ids', [])) ? 'checked' : '' }}
                                    />
                                    <div class="mr-3 flex-1" style="cursor: pointer;">
                                        <span class="block text-sm font-medium text-gray-800">{{ $employee->name }}</span>
                                        <span class="text-xs text-gray-500">{{ $employee->role_badge }}</span>
                                    </div>
                                    <span class="material-icons text-primary employee-check-icon opacity-0 group-hover:opacity-30 transition-opacity" style="{{ in_array($employee->id, old('employee_ids', [])) ? 'opacity: 1 !important; color: #6366f1;' : '' }}">check_circle</span>
                                </label>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8 bg-gray-50 rounded-xl border-2 border-dashed border-gray-300">
                            <span class="material-icons text-gray-400 text-5xl mb-3">people_outline</span>
                            <p class="text-gray-600 mb-4">لا يوجد موظفين متاحين</p>
                            <a href="{{ route('employees.create') }}" class="text-primary hover:text-primary-700 font-medium">
                                إضافة موظف جديد
                            </a>
                        </div>
                    @endif
                    @error('employee_ids')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Actions -->
                <div class="flex items-center justify-center rtl-spacing pt-8 border-t border-gray-200">
                    <button type="submit" class="action-button btn-primary text-white px-8 py-4 rounded-2xl flex items-center font-medium text-lg min-w-[160px] justify-center">
                        <span class="material-icons text-lg ml-3">save</span>
                        حفظ الخطة
                    </button>   
                    
                    <a href="{{ route('monthly-plans.index') }}" class="action-button cancel-button flex items-center px-8 py-4 rounded-2xl font-medium text-lg min-w-[140px] justify-center">
                        إلغاء
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

@section('scripts')
<style>
.employee-checkbox-label {
    user-select: none;
    -webkit-user-select: none;
}
.employee-checkbox-label:hover {
    border-color: #6366f1 !important;
    background-color: #eef2ff !important;
}
.employee-checkbox {
    accent-color: #6366f1;
    cursor: pointer !important;
}
.employee-checkbox:checked {
    background-color: #6366f1;
    border-color: #6366f1;
}
</style>
<script>
$(document).ready(function() {
    // تحديث month_number عند اختيار الشهر
    $('#month').on('change', function() {
        const selectedOption = $(this).find('option:selected');
        const monthNumber = selectedOption.data('month');
        $('#month_number').val(monthNumber);
    });

    // إضافة هدف جديد
    let goalIndex = 1;
    $('#add-goal').on('click', function(e) {
        e.preventDefault();
        const newGoalHtml = `
            <div class="goal-item p-4 border border-gray-200 rounded-xl">
                <div class="flex items-center justify-between mb-3">
                    <h4 class="text-sm font-medium text-gray-700">هدف جديد</h4>
                    <button type="button" class="remove-goal text-red-500 hover:text-red-700">
                        <span class="material-icons text-sm">delete</span>
                    </button>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">نوع الهدف</label>
                        <select
                            name="goals[${goalIndex}][goal_type]"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
                        >
                            <option value="posts">نشر بوستات</option>
                            <option value="designs">عمل تصميمات</option>
                            <option value="ads_budget">ميزانية إعلانات</option>
                            <option value="custom">هدف مخصص</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">اسم الهدف</label>
                        <input
                            type="text"
                            name="goals[${goalIndex}][goal_name]"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
                            placeholder="مثال: نشر 12 بوست"
                            required
                        />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">القيمة المستهدفة</label>
                        <input
                            type="number"
                            name="goals[${goalIndex}][target_value]"
                            min="0"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
                            placeholder="12"
                            required
                        />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">الوحدة</label>
                        <input
                            type="text"
                            name="goals[${goalIndex}][unit]"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
                            placeholder="بوست، تصميم، جنيه"
                        />
                    </div>
                </div>
                <div class="mt-3">
                    <label class="block text-sm font-medium text-gray-700 mb-2">وصف الهدف (اختياري)</label>
                    <textarea
                        name="goals[${goalIndex}][description]"
                        rows="2"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
                        placeholder="وصف إضافي للهدف"
                    ></textarea>
                </div>
            </div>
        `;
        $('#goals-container').append(newGoalHtml);
        goalIndex++;
    });

    // حذف هدف
    $(document).on('click', '.remove-goal', function(e) {
        e.preventDefault();
        if ($('#goals-container .goal-item').length > 1) {
            $(this).closest('.goal-item').remove();
        } else {
            alert('يجب وجود هدف واحد على الأقل');
        }
    });

    // تحديث مظهر checkbox عند التغيير
    $(document).on('change', '.employee-checkbox', function() {
        const label = $(this).closest('.employee-checkbox-label');
        const icon = label.find('.employee-check-icon');
        if ($(this).is(':checked')) {
            label.addClass('border-primary bg-primary-50');
            label.removeClass('border-gray-200');
            icon.css({
                'opacity': '1',
                'color': '#6366f1'
            });
        } else {
            label.removeClass('border-primary bg-primary-50');
            label.addClass('border-gray-200');
            icon.css({
                'opacity': '0',
                'color': ''
            });
        }
    });

    // تحديث الحالة الأولية للـ checkboxes
    $('.employee-checkbox').each(function() {
        if ($(this).is(':checked')) {
            const label = $(this).closest('.employee-checkbox-label');
            label.addClass('border-primary bg-primary-50');
            label.removeClass('border-gray-200');
            const icon = label.find('.employee-check-icon');
            icon.css({
                'opacity': '1',
                'color': '#6366f1'
            });
        }
    });

    // تحديد/إلغاء تحديد الكل
    let allSelected = false;
    $('#select-all-employees').on('click', function(e) {
        e.preventDefault();
        allSelected = !allSelected;
        $('.employee-checkbox').prop('checked', allSelected).trigger('change');
        $(this).text(allSelected ? 'إلغاء تحديد الكل' : 'تحديد الكل');
    });

    // التحقق من اختيار موظف واحد على الأقل قبل الإرسال
    $('#monthly-plan-form').on('submit', function(e) {
        const checkedEmployees = $('.employee-checkbox:checked').length;
        if (checkedEmployees === 0) {
            e.preventDefault();
            alert('يرجى اختيار موظف واحد على الأقل');
            // إظهار رسالة الخطأ
            if (!$('#employee-error-message').length) {
                $('#employee_ids-error').after('<p id="employee-error-message" class="mt-1 text-sm text-red-600">يرجى اختيار موظف واحد على الأقل</p>');
            }
            // التمرير إلى قسم الموظفين
            $('html, body').animate({
                scrollTop: $('.form-section').last().offset().top - 100
            }, 500);
            return false;
        }
    });
});
</script>
@endsection
@endsection

