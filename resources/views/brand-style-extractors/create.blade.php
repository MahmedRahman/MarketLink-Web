@extends('layouts.dashboard')

@section('title', 'إضافة محتوى جديد - Brand Style Extractor')
@section('page-title', 'إضافة محتوى جديد')
@section('page-description', 'إضافة محتوى جديد للعلامة التجارية')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="card page-header rounded-2xl p-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <div class="w-12 h-12 logo-gradient rounded-2xl flex items-center justify-center shadow-lg icon-spacing ml-3">
                    <i class="fas fa-palette text-white text-xl"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">إضافة محتوى جديد</h2>
                    <p class="text-gray-600">إضافة محتوى للعلامة التجارية</p>
                </div>
            </div>
            <a href="{{ route('brand-style-extractors.index') }}" class="btn-secondary text-gray-700 px-6 py-3 rounded-xl flex items-center hover:no-underline">
                <i class="fas fa-arrow-right text-sm ml-2"></i>
                العودة للقائمة
            </a>
        </div>
    </div>

    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-xl" role="alert">
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle ml-2"></i>
                <div>
                    <strong class="font-bold">يرجى تصحيح الأخطاء التالية:</strong>
                    <ul class="list-disc list-inside mt-2">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <form method="POST" action="{{ route('brand-style-extractors.store') }}" class="space-y-8">
        @csrf
        
        <!-- Basic Information Section -->
        <div class="form-section space-y-6">
            <h3 class="text-lg font-semibold text-gray-800">المعلومات الأساسية</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Project -->
                <div>
                    <label for="project_id" class="block text-sm font-medium text-gray-700 mb-2">
                        المشروع <span class="text-red-500">*</span>
                    </label>
                    <select
                        id="project_id"
                        name="project_id"
                        required
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-colors select2"
                    >
                        <option value="">اختر المشروع</option>
                        @foreach($projects as $proj)
                            <option value="{{ $proj->id }}" {{ (old('project_id') == $proj->id || (isset($project) && $project->id == $proj->id)) ? 'selected' : '' }}>
                                {{ $proj->business_name }}
                            </option>
                        @endforeach
                    </select>
                    @error('project_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Content Type -->
                <div>
                    <label for="content_type" class="block text-sm font-medium text-gray-700 mb-2">
                        نوع المحتوى <span class="text-red-500">*</span>
                    </label>
                    <select
                        id="content_type"
                        name="content_type"
                        required
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-colors"
                    >
                        <option value="">اختر نوع المحتوى</option>
                        <optgroup label="المحتوى النصي">
                            <option value="post" {{ old('content_type') == 'post' ? 'selected' : '' }}>البوست</option>
                            <option value="reels" {{ old('content_type') == 'reels' ? 'selected' : '' }}>الريلز</option>
                            <option value="book" {{ old('content_type') == 'book' ? 'selected' : '' }}>الكتاب</option>
                            <option value="text" {{ old('content_type') == 'text' ? 'selected' : '' }}>النصوص</option>
                        </optgroup>
                        <optgroup label="التصميم">
                            <option value="colors" {{ old('content_type') == 'colors' ? 'selected' : '' }}>الألوان</option>
                            <option value="fonts" {{ old('content_type') == 'fonts' ? 'selected' : '' }}>الخطوط</option>
                            <option value="logos" {{ old('content_type') == 'logos' ? 'selected' : '' }}>الشعارات</option>
                            <option value="images" {{ old('content_type') == 'images' ? 'selected' : '' }}>الصور</option>
                            <option value="icons" {{ old('content_type') == 'icons' ? 'selected' : '' }}>الأيقونات</option>
                            <option value="patterns" {{ old('content_type') == 'patterns' ? 'selected' : '' }}>الأنماط</option>
                            <option value="spacing" {{ old('content_type') == 'spacing' ? 'selected' : '' }}>المسافات</option>
                        </optgroup>
                        <optgroup label="أخرى">
                            <option value="other" {{ old('content_type') == 'other' ? 'selected' : '' }}>أخرى</option>
                        </optgroup>
                    </select>
                    @error('content_type')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Content -->
            <div>
                <label for="content" class="block text-sm font-medium text-gray-700 mb-2">
                    المحتوى <span class="text-red-500">*</span>
                </label>
                <textarea
                    id="content"
                    name="content"
                    rows="8"
                    required
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-colors"
                    placeholder="أدخل المحتوى المنشور هنا..."
                >{{ old('content') }}</textarea>
                @error('content')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Revenue Section -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 border-t border-gray-200 pt-6 mt-6">
                <!-- Revenue -->
                <div>
                    <label for="revenue" class="block text-sm font-medium text-gray-700 mb-2">
                        العائد المحقق (اختياري)
                    </label>
                    <input
                        type="number"
                        id="revenue"
                        name="revenue"
                        step="0.01"
                        min="0"
                        value="{{ old('revenue') }}"
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-colors"
                        placeholder="0.00"
                    />
                    @error('revenue')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Currency -->
                <div>
                    <label for="currency" class="block text-sm font-medium text-gray-700 mb-2">
                        العملة
                    </label>
                    <select
                        id="currency"
                        name="currency"
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-colors"
                    >
                        <option value="EGP" {{ old('currency', 'EGP') == 'EGP' ? 'selected' : '' }}>جنيه مصري (EGP)</option>
                        <option value="USD" {{ old('currency') == 'USD' ? 'selected' : '' }}>دولار أمريكي (USD)</option>
                        <option value="EUR" {{ old('currency') == 'EUR' ? 'selected' : '' }}>يورو (EUR)</option>
                        <option value="SAR" {{ old('currency') == 'SAR' ? 'selected' : '' }}>ريال سعودي (SAR)</option>
                        <option value="AED" {{ old('currency') == 'AED' ? 'selected' : '' }}>درهم إماراتي (AED)</option>
                    </select>
                    @error('currency')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Brand Profile Section -->
        <div class="form-section space-y-6">
            <h3 class="text-lg font-semibold text-gray-800">Brand Profile (بعد تحليل المحتوى)</h3>
            <p class="text-sm text-gray-600 mb-4">يمكن ملء هذه الحقول بعد تحليل المحتوى</p>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Voice -->
                <div>
                    <label for="brand_profile_voice" class="block text-sm font-medium text-gray-700 mb-2">
                        Voice
                    </label>
                    <input
                        type="text"
                        id="brand_profile_voice"
                        name="brand_profile[voice]"
                        value="{{ old('brand_profile.voice') }}"
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-colors"
                        placeholder="Voice"
                    />
                </div>

                <!-- Tone -->
                <div>
                    <label for="brand_profile_tone" class="block text-sm font-medium text-gray-700 mb-2">
                        Tone
                    </label>
                    <input
                        type="text"
                        id="brand_profile_tone"
                        name="brand_profile[tone]"
                        value="{{ old('brand_profile.tone') }}"
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-colors"
                        placeholder="Tone"
                    />
                </div>

                <!-- Structure -->
                <div>
                    <label for="brand_profile_structure" class="block text-sm font-medium text-gray-700 mb-2">
                        Structure
                    </label>
                    <textarea
                        id="brand_profile_structure"
                        name="brand_profile[structure]"
                        rows="3"
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-colors"
                        placeholder="Structure"
                    >{{ old('brand_profile.structure') }}</textarea>
                </div>

                <!-- Language Style -->
                <div>
                    <label for="brand_profile_language_style" class="block text-sm font-medium text-gray-700 mb-2">
                        Language Style
                    </label>
                    <input
                        type="text"
                        id="brand_profile_language_style"
                        name="brand_profile[language_style]"
                        value="{{ old('brand_profile.language_style') }}"
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-colors"
                        placeholder="Language Style"
                    />
                </div>

                <!-- CTA Style -->
                <div>
                    <label for="brand_profile_cta_style" class="block text-sm font-medium text-gray-700 mb-2">
                        CTA Style
                    </label>
                    <input
                        type="text"
                        id="brand_profile_cta_style"
                        name="brand_profile[cta_style]"
                        value="{{ old('brand_profile.cta_style') }}"
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-colors"
                        placeholder="CTA Style"
                    />
                </div>

                <!-- Enemy -->
                <div>
                    <label for="brand_profile_enemy" class="block text-sm font-medium text-gray-700 mb-2">
                        Enemy
                    </label>
                    <input
                        type="text"
                        id="brand_profile_enemy"
                        name="brand_profile[enemy]"
                        value="{{ old('brand_profile.enemy') }}"
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-colors"
                        placeholder="Enemy"
                    />
                </div>

                <!-- Values -->
                <div>
                    <label for="brand_profile_values" class="block text-sm font-medium text-gray-700 mb-2">
                        Values
                    </label>
                    <textarea
                        id="brand_profile_values"
                        name="brand_profile[values]"
                        rows="3"
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-colors"
                        placeholder="Values"
                    >{{ old('brand_profile.values') }}</textarea>
                </div>

                <!-- Hook Patterns -->
                <div>
                    <label for="brand_profile_hook_patterns" class="block text-sm font-medium text-gray-700 mb-2">
                        Hook Patterns
                    </label>
                    <textarea
                        id="brand_profile_hook_patterns"
                        name="brand_profile[hook_patterns]"
                        rows="3"
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-colors"
                        placeholder="Hook Patterns"
                    >{{ old('brand_profile.hook_patterns') }}</textarea>
                </div>
            </div>

            <!-- Phrases -->
            <div>
                <label for="brand_profile_phrases" class="block text-sm font-medium text-gray-700 mb-2">
                    Phrases
                </label>
                <textarea
                    id="brand_profile_phrases"
                    name="brand_profile[phrases]"
                    rows="4"
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-colors"
                    placeholder="Phrases"
                >{{ old('brand_profile.phrases') }}</textarea>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="flex items-center justify-end space-x-4 rtl:space-x-reverse pt-6 border-t border-gray-200">
            <a href="{{ route('brand-style-extractors.index') }}" class="btn-secondary text-gray-700 px-8 py-3 rounded-xl">
                إلغاء
            </a>
            <button type="submit" class="btn-primary text-white px-8 py-3 rounded-xl">
                <i class="fas fa-save ml-2"></i>
                حفظ المحتوى
            </button>
        </div>
    </form>
</div>

<script>
$(document).ready(function() {
    $('.select2').select2({
        dir: 'rtl',
        language: {
            noResults: function() {
                return "لا توجد نتائج";
            }
        }
    });
});
</script>
@endsection

