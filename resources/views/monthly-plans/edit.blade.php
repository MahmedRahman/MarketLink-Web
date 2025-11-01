@extends('layouts.dashboard')

@section('title', 'تعديل الخطة الشهرية')
@section('page-title', 'تعديل الخطة الشهرية')
@section('page-description', 'تعديل بيانات الخطة الشهرية')

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
                        <h2 class="text-2xl font-bold text-gray-800">تعديل الخطة الشهرية</h2>
                        <p class="text-gray-600">تعديل بيانات الخطة: {{ $monthlyPlan->month }} {{ $monthlyPlan->year }}</p>
                    </div>
                </div>
                <a href="{{ route('monthly-plans.show', $monthlyPlan) }}" class="flex items-center px-4 py-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-xl transition-colors icon-spacing">
                    العودة للخطة
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

            <form method="POST" action="{{ route('monthly-plans.update', $monthlyPlan) }}" class="space-y-8">
                @csrf
                @method('PUT')
                
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
                                @foreach($projects as $project)
                                    <option value="{{ $project->id }}" {{ old('project_id', $monthlyPlan->project_id) == $project->id ? 'selected' : '' }}>
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
                                @php
                                    $months = [
                                        1 => 'يناير', 2 => 'فبراير', 3 => 'مارس', 4 => 'أبريل',
                                        5 => 'مايو', 6 => 'يونيو', 7 => 'يوليو', 8 => 'أغسطس',
                                        9 => 'سبتمبر', 10 => 'أكتوبر', 11 => 'نوفمبر', 12 => 'ديسمبر'
                                    ];
                                @endphp
                                @foreach($months as $num => $name)
                                    <option value="{{ $name }}" data-month="{{ $num }}" {{ old('month', $monthlyPlan->month) == $name ? 'selected' : '' }}>
                                        {{ $name }}
                                    </option>
                                @endforeach
                            </select>
                            <input type="hidden" id="month_number" name="month_number" value="{{ old('month_number', $monthlyPlan->month_number) }}">
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
                                value="{{ old('year', $monthlyPlan->year) }}"
                                min="2020"
                                max="2100"
                                required
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-colors"
                            />
                            @error('year')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

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
                            <option value="draft" {{ old('status', $monthlyPlan->status) == 'draft' ? 'selected' : '' }}>مسودة</option>
                            <option value="active" {{ old('status', $monthlyPlan->status) == 'active' ? 'selected' : '' }}>نشطة</option>
                            <option value="completed" {{ old('status', $monthlyPlan->status) == 'completed' ? 'selected' : '' }}>مكتملة</option>
                            <option value="cancelled" {{ old('status', $monthlyPlan->status) == 'cancelled' ? 'selected' : '' }}>ملغاة</option>
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
                            rows="3"
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-colors"
                            placeholder="أدخل وصف للخطة الشهرية (اختياري)"
                        >{{ old('description', $monthlyPlan->description) }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex items-center justify-center rtl-spacing pt-8 border-t border-gray-200">
                    <button type="submit" class="action-button btn-primary text-white px-8 py-4 rounded-2xl flex items-center font-medium text-lg min-w-[160px] justify-center">
                        <span class="material-icons text-lg ml-3">save</span>
                        حفظ التغييرات
                    </button>   
                    
                    <a href="{{ route('monthly-plans.show', $monthlyPlan) }}" class="action-button cancel-button flex items-center px-8 py-4 rounded-2xl font-medium text-lg min-w-[140px] justify-center">
                        إلغاء
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

@section('scripts')
<script>
$(document).ready(function() {
    // تحديث month_number عند اختيار الشهر
    $('#month').on('change', function() {
        const selectedOption = $(this).find('option:selected');
        const monthNumber = selectedOption.data('month');
        $('#month_number').val(monthNumber);
    });
});
</script>
@endsection
@endsection

