@extends('layouts.dashboard')

@section('title', 'تعديل الخطة الشهرية')
@section('page-title', 'تعديل الخطة الشهرية')
@section('page-description', 'تعديل بيانات الخطة الشهرية')

@section('content')
<div class="container mx-auto px-3 md:px-4">
    <div class="max-w-5xl mx-auto space-y-4 md:space-y-6">
        <!-- Header -->
        <div class="card page-header rounded-xl md:rounded-2xl p-4 md:p-6">
            <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                <div class="flex items-center flex-1">
                    <div class="w-10 h-10 md:w-12 md:h-12 logo-gradient rounded-xl md:rounded-2xl flex items-center justify-center shadow-lg icon-spacing ml-2 md:ml-3 flex-shrink-0">
                        <span class="material-icons text-white text-lg md:text-xl">calendar_month</span>
                    </div>
                    <div>
                        <h2 class="text-xl md:text-2xl font-bold text-gray-800">تعديل الخطة الشهرية</h2>
                        <p class="text-sm md:text-base text-gray-600 hidden md:block">تعديل بيانات الخطة: {{ $monthlyPlan->month }} {{ $monthlyPlan->year }}</p>
                    </div>
                </div>
                <a href="{{ route('monthly-plans.show', $monthlyPlan) }}" class="flex items-center px-3 md:px-4 py-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-xl transition-colors text-sm md:text-base w-full md:w-auto justify-center">
                    <i class="fas fa-arrow-right text-xs md:text-sm ml-2"></i>
                    العودة للخطة
                </a>
            </div>
        </div>

        <!-- Form Card -->
        <div class="card rounded-xl md:rounded-2xl p-4 md:p-8">
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

            <form method="POST" action="{{ route('monthly-plans.update', $monthlyPlan) }}" class="space-y-8">
                @csrf
                @method('PUT')
                
                <!-- Basic Information Section -->
                <div class="form-section space-y-4 md:space-y-6">
                    <h3 class="text-base md:text-lg font-semibold text-gray-800">المعلومات الأساسية</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 md:gap-6">
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
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-colors text-lg leading-relaxed"
                            style="font-size: 1.125rem; line-height: 1.75; font-family: 'Cairo', 'Segoe UI', Tahoma, sans-serif;"
                            placeholder="أدخل وصف للخطة الشهرية (اختياري)"
                        >{{ old('description', $monthlyPlan->description) }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Goals Section -->
                <div class="form-section space-y-4 md:space-y-6">
                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                        <h3 class="text-base md:text-lg font-semibold text-gray-800">أهداف الخطة</h3>
                        <button type="button" id="add-goal" class="btn-primary text-white px-3 md:px-4 py-2 rounded-lg flex items-center justify-center text-sm md:text-base w-full sm:w-auto">
                            <span class="material-icons text-xs md:text-sm ml-2">add</span>
                            إضافة هدف
                        </button>
                    </div>
                    
                    <div id="goals-container" class="space-y-3 md:space-y-4">
                        @if(old('goals') || $monthlyPlan->goals->count() > 0)
                            @php
                                $goalsToShow = old('goals') ?: $monthlyPlan->goals->map(function($goal, $index) {
                                    return [
                                        'goal_type' => $goal->goal_type,
                                        'goal_name' => $goal->goal_name,
                                        'target_value' => $goal->target_value,
                                        'posts' => $goal->posts ?? 0,
                                        'carousel' => $goal->carousel ?? 0,
                                        'reels' => $goal->reels ?? 0,
                                        'ads_campaigns' => $goal->ads_campaigns ?? 0,
                                        'other_goals' => $goal->other_goals ?? 0,
                                        'description' => $goal->description,
                                        'id' => $goal->id,
                                    ];
                                })->toArray();
                            @endphp
                            @foreach($goalsToShow as $index => $goal)
                                <div class="goal-item p-3 md:p-4 border border-gray-200 rounded-xl">
                                    <div class="flex items-center justify-between mb-3">
                                        <h4 class="text-xs md:text-sm font-medium text-gray-700">هدف {{ $index + 1 }}</h4>
                                        @if(count($goalsToShow) > 1)
                                            <button type="button" class="remove-goal text-red-500 hover:text-red-700">
                                                <span class="material-icons text-xs md:text-sm">delete</span>
                                            </button>
                                        @endif
                                    </div>
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3 md:gap-4 mb-3 md:mb-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">نوع الهدف</label>
                                            <select
                                                name="goals[{{ $index }}][goal_type]"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
                                            >
                                                <option value="posts" {{ ($goal['goal_type'] ?? '') == 'posts' ? 'selected' : '' }}>نشر بوستات</option>
                                                <option value="designs" {{ ($goal['goal_type'] ?? '') == 'designs' ? 'selected' : '' }}>عمل تصميمات</option>
                                                <option value="ads_budget" {{ ($goal['goal_type'] ?? '') == 'ads_budget' ? 'selected' : '' }}>ميزانية إعلانات</option>
                                                <option value="custom" {{ ($goal['goal_type'] ?? '') == 'custom' ? 'selected' : '' }}>هدف مخصص</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">اسم الهدف</label>
                                            <input
                                                type="text"
                                                name="goals[{{ $index }}][goal_name]"
                                                value="{{ $goal['goal_name'] ?? '' }}"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
                                                placeholder="مثال: نشر 12 بوست"
                                                required
                                            />
                                            @if(isset($goal['id']))
                                                <input type="hidden" name="goals[{{ $index }}][id]" value="{{ $goal['id'] }}">
                                            @endif
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">القيمة المستهدفة</label>
                                            <input
                                                type="number"
                                                name="goals[{{ $index }}][target_value]"
                                                value="{{ $goal['target_value'] ?? '' }}"
                                                min="0"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
                                                placeholder="12"
                                                required
                                            />
                                        </div>
                                    </div>
                                    
                                    <!-- Content Sections -->
                                    <div class="bg-gray-50 rounded-lg p-3 md:p-4 mb-3 md:mb-4">
                                        <h4 class="text-xs md:text-sm font-semibold text-gray-700 mb-2 md:mb-3">المحتوى</h4>
                                        <div class="grid grid-cols-1 gap-3 md:gap-4">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">البوستات</label>
                                                <input
                                                    type="number"
                                                    name="goals[{{ $index }}][posts]"
                                                    value="{{ $goal['posts'] ?? 0 }}"
                                                    min="0"
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
                                                    placeholder="0"
                                                />
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">الكروسول</label>
                                                <input
                                                    type="number"
                                                    name="goals[{{ $index }}][carousel]"
                                                    value="{{ $goal['carousel'] ?? 0 }}"
                                                    min="0"
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
                                                    placeholder="0"
                                                />
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">الريلز</label>
                                                <input
                                                    type="number"
                                                    name="goals[{{ $index }}][reels]"
                                                    value="{{ $goal['reels'] ?? 0 }}"
                                                    min="0"
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
                                                    placeholder="0"
                                                />
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">حملات الإعلانية</label>
                                                <input
                                                    type="number"
                                                    name="goals[{{ $index }}][ads_campaigns]"
                                                    value="{{ $goal['ads_campaigns'] ?? 0 }}"
                                                    min="0"
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
                                                    placeholder="0"
                                                />
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">أهداف أخرى</label>
                                                <input
                                                    type="number"
                                                    name="goals[{{ $index }}][other_goals]"
                                                    value="{{ $goal['other_goals'] ?? 0 }}"
                                                    min="0"
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
                                                    placeholder="0"
                                                />
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="mt-3">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">وصف الهدف (اختياري)</label>
                                        <textarea
                                            name="goals[{{ $index }}][description]"
                                            rows="2"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
                                            placeholder="وصف إضافي للهدف"
                                        >{{ $goal['description'] ?? '' }}</textarea>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="goal-item p-4 border border-gray-200 rounded-xl">
                                <div class="flex items-center justify-between mb-3">
                                    <h4 class="text-sm font-medium text-gray-700">هدف 1</h4>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
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
                                </div>
                                
                                <!-- Content Sections -->
                                <div class="bg-gray-50 rounded-lg p-4 mb-4">
                                    <h4 class="text-sm font-semibold text-gray-700 mb-3">المحتوى</h4>
                                    <div class="grid grid-cols-1 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">البوستات</label>
                                            <input
                                                type="number"
                                                name="goals[0][posts]"
                                                min="0"
                                                value="0"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
                                                placeholder="0"
                                            />
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">الكروسول</label>
                                            <input
                                                type="number"
                                                name="goals[0][carousel]"
                                                min="0"
                                                value="0"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
                                                placeholder="0"
                                            />
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">الريلز</label>
                                            <input
                                                type="number"
                                                name="goals[0][reels]"
                                                min="0"
                                                value="0"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
                                                placeholder="0"
                                            />
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">حملات الإعلانية</label>
                                            <input
                                                type="number"
                                                name="goals[0][ads_campaigns]"
                                                min="0"
                                                value="0"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
                                                placeholder="0"
                                            />
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">أهداف أخرى</label>
                                            <input
                                                type="number"
                                                name="goals[0][other_goals]"
                                                min="0"
                                                value="0"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
                                                placeholder="0"
                                            />
                                        </div>
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
                        @endif
                    </div>
                    @error('goals')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    @error('goals.*')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Generate Tasks Section -->
                <div class="form-section space-y-4 md:space-y-6">
                    <div class="bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 rounded-xl md:rounded-2xl p-4 md:p-6">
                        <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-2">
                                    <div class="w-10 h-10 md:w-12 md:h-12 bg-green-100 rounded-xl flex items-center justify-center">
                                        <span class="material-icons text-green-600 text-lg md:text-xl">auto_awesome</span>
                                    </div>
                                    <div>
                                        <h3 class="text-base md:text-lg font-semibold text-gray-800">إنشاء الخطة تلقائياً</h3>
                                        <p class="text-xs md:text-sm text-gray-600 mt-1">سيتم إنشاء المهام تلقائياً بناءً على الأهداف والمحتوى المحدد</p>
                                    </div>
                                </div>
                            </div>
                            <button type="button" id="generate-tasks" class="bg-green-600 hover:bg-green-700 text-white px-6 md:px-8 py-3 md:py-4 rounded-xl md:rounded-2xl flex items-center justify-center text-sm md:text-base font-semibold shadow-lg hover:shadow-xl transition-all w-full md:w-auto">
                                <span class="material-icons text-base md:text-lg ml-2">auto_awesome</span>
                                إنشاء الخطة
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Employees Section -->
                <div class="form-section space-y-4 md:space-y-6">
                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                        <div>
                            <h3 class="text-base md:text-lg font-semibold text-gray-800">الموظفين المشاركين <span class="text-red-500">*</span></h3>
                            <p class="text-xs md:text-sm text-gray-600 hidden md:block">اختر الموظفين الذين سيعملون على هذه الخطة</p>
                        </div>
                        @if($employees->count() > 0)
                            <button type="button" id="select-all-employees" class="text-xs md:text-sm text-primary hover:text-primary-700 font-medium w-full sm:w-auto text-center sm:text-right">
                                تحديد الكل
                            </button>
                        @endif
                    </div>
                    
                    @if($employees->count() > 0)
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 md:gap-4" id="employees-grid">
                            @php
                                $selectedEmployeeIds = old('employee_ids', $monthlyPlan->employees->pluck('id')->toArray());
                            @endphp
                            @foreach($employees as $employee)
                                <label class="flex items-center justify-between p-4 border-2 {{ in_array($employee->id, $selectedEmployeeIds) ? 'border-primary bg-primary-50' : 'border-gray-200' }} rounded-xl hover:border-primary hover:bg-primary-50 transition-all cursor-pointer employee-checkbox-label group" style="position: relative;">
                                    <input
                                        type="checkbox"
                                        name="employee_ids[]"
                                        value="{{ $employee->id }}"
                                        class="employee-checkbox w-6 h-6 rounded border-2 border-gray-300 text-primary focus:ring-2 focus:ring-primary cursor-pointer"
                                        style="cursor: pointer; z-index: 10; position: relative;"
                                        {{ in_array($employee->id, $selectedEmployeeIds) ? 'checked' : '' }}
                                    />
                                    <div class="mr-3 flex-1" style="cursor: pointer;">
                                        <span class="block text-sm font-medium text-gray-800">{{ $employee->name }}</span>
                                        <span class="text-xs text-gray-500">{{ $employee->role_badge }}</span>
                                    </div>
                                    <span class="material-icons text-primary employee-check-icon {{ in_array($employee->id, $selectedEmployeeIds) ? 'opacity-100' : 'opacity-0 group-hover:opacity-30' }} transition-opacity" style="{{ in_array($employee->id, $selectedEmployeeIds) ? 'opacity: 1 !important; color: #6366f1;' : '' }}">check_circle</span>
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
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-center gap-3 md:gap-4 pt-6 md:pt-8 border-t border-gray-200">
                    <button type="submit" class="action-button btn-primary text-white px-6 md:px-8 py-3 md:py-4 rounded-xl md:rounded-2xl flex items-center justify-center font-medium text-base md:text-lg w-full sm:w-auto sm:min-w-[160px]">
                        <span class="material-icons text-base md:text-lg ml-2 md:ml-3">save</span>
                        حفظ التغييرات
                    </button>   
                    
                    <a href="{{ route('monthly-plans.show', $monthlyPlan) }}" class="action-button cancel-button flex items-center justify-center px-6 md:px-8 py-3 md:py-4 rounded-xl md:rounded-2xl font-medium text-base md:text-lg w-full sm:w-auto sm:min-w-[140px]">
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
    let goalIndex = $('#goals-container .goal-item').length;
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
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
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
                </div>
                
                <!-- Content Sections -->
                <div class="bg-gray-50 rounded-lg p-4 mb-4">
                    <h4 class="text-sm font-semibold text-gray-700 mb-3">المحتوى</h4>
                    <div class="grid grid-cols-1 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">البوستات</label>
                            <input
                                type="number"
                                name="goals[${goalIndex}][posts]"
                                min="0"
                                value="0"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
                                placeholder="0"
                            />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">الكروسول</label>
                            <input
                                type="number"
                                name="goals[${goalIndex}][carousel]"
                                min="0"
                                value="0"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
                                placeholder="0"
                            />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">الريلز</label>
                            <input
                                type="number"
                                name="goals[${goalIndex}][reels]"
                                min="0"
                                value="0"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
                                placeholder="0"
                            />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">حملات الإعلانية</label>
                            <input
                                type="number"
                                name="goals[${goalIndex}][ads_campaigns]"
                                min="0"
                                value="0"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
                                placeholder="0"
                            />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">أهداف أخرى</label>
                            <input
                                type="number"
                                name="goals[${goalIndex}][other_goals]"
                                min="0"
                                value="0"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
                                placeholder="0"
                            />
                        </div>
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

    // إنشاء المهام تلقائياً
    $('#generate-tasks').on('click', function(e) {
        e.preventDefault();
        
        if (!confirm('هل أنت متأكد من إنشاء المهام بناءً على الأهداف المحددة؟ سيتم إنشاء مهام جديدة لكل هدف.')) {
            return;
        }

        const button = $(this);
        const originalText = button.html();
        button.prop('disabled', true);
        button.html('<span class="material-icons text-xs md:text-sm ml-2 animate-spin">sync</span> جاري الإنشاء...');

        $.ajax({
            url: '{{ route("monthly-plans.generate-tasks", $monthlyPlan) }}',
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Accept': 'application/json'
            },
            success: function(response) {
                if (response.success) {
                    alert(response.message || 'تم إنشاء المهام بنجاح');
                    window.location.href = '{{ route("monthly-plans.show", $monthlyPlan) }}';
                } else {
                    alert(response.error || 'حدث خطأ أثناء إنشاء المهام');
                    button.prop('disabled', false);
                    button.html(originalText);
                }
            },
            error: function(xhr) {
                const errorMessage = xhr.responseJSON?.error || xhr.responseJSON?.message || 'حدث خطأ أثناء إنشاء المهام';
                alert(errorMessage);
                button.prop('disabled', false);
                button.html(originalText);
            }
        });
    });

    // التحقق من اختيار موظف واحد على الأقل قبل الإرسال
    $('form').on('submit', function(e) {
        const checkedEmployees = $('.employee-checkbox:checked').length;
        if (checkedEmployees === 0) {
            e.preventDefault();
            alert('يرجى اختيار موظف واحد على الأقل');
            // إظهار رسالة الخطأ
            if (!$('#employee-error-message').length) {
                $('#employees-grid').after('<p id="employee-error-message" class="mt-1 text-sm text-red-600">يرجى اختيار موظف واحد على الأقل</p>');
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

