@extends('layouts.dashboard')

@section('title', 'تعديل المحتوى - Brand Style Extractor')
@section('page-title', 'تعديل المحتوى')
@section('page-description', 'تعديل محتوى العلامة التجارية')

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
                    <h2 class="text-2xl font-bold text-gray-800">تعديل المحتوى</h2>
                    <p class="text-gray-600">تعديل محتوى العلامة التجارية</p>
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

    <form method="POST" action="{{ route('brand-style-extractors.update', $brandStyleExtractor) }}" class="space-y-8">
        @csrf
        @method('PUT')
        
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
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}" {{ old('project_id', $brandStyleExtractor->project_id) == $project->id ? 'selected' : '' }}>
                                {{ $project->business_name }}
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
                            <option value="post" {{ old('content_type', $brandStyleExtractor->content_type) == 'post' ? 'selected' : '' }}>البوست</option>
                            <option value="reels" {{ old('content_type', $brandStyleExtractor->content_type) == 'reels' ? 'selected' : '' }}>الريلز</option>
                            <option value="book" {{ old('content_type', $brandStyleExtractor->content_type) == 'book' ? 'selected' : '' }}>الكتاب</option>
                            <option value="text" {{ old('content_type', $brandStyleExtractor->content_type) == 'text' ? 'selected' : '' }}>النصوص</option>
                        </optgroup>
                        <optgroup label="التصميم">
                            <option value="colors" {{ old('content_type', $brandStyleExtractor->content_type) == 'colors' ? 'selected' : '' }}>الألوان</option>
                            <option value="fonts" {{ old('content_type', $brandStyleExtractor->content_type) == 'fonts' ? 'selected' : '' }}>الخطوط</option>
                            <option value="logos" {{ old('content_type', $brandStyleExtractor->content_type) == 'logos' ? 'selected' : '' }}>الشعارات</option>
                            <option value="images" {{ old('content_type', $brandStyleExtractor->content_type) == 'images' ? 'selected' : '' }}>الصور</option>
                            <option value="icons" {{ old('content_type', $brandStyleExtractor->content_type) == 'icons' ? 'selected' : '' }}>الأيقونات</option>
                            <option value="patterns" {{ old('content_type', $brandStyleExtractor->content_type) == 'patterns' ? 'selected' : '' }}>الأنماط</option>
                            <option value="spacing" {{ old('content_type', $brandStyleExtractor->content_type) == 'spacing' ? 'selected' : '' }}>المسافات</option>
                        </optgroup>
                        <optgroup label="أخرى">
                            <option value="other" {{ old('content_type', $brandStyleExtractor->content_type) == 'other' ? 'selected' : '' }}>أخرى</option>
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
                    placeholder="أدخل المحتوى هنا..."
                >{{ old('content', $brandStyleExtractor->content) }}</textarea>
                @error('content')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Brand Profile Section -->
        <div class="form-section space-y-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800">Brand Profile (بعد تحليل المحتوى)</h3>
                    <p class="text-sm text-gray-600 mt-1">يمكن ملء هذه الحقول بعد تحليل المحتوى</p>
                </div>
                <button 
                    type="button" 
                    id="analyzeContentBtn"
                    class="btn-primary text-white px-6 py-3 rounded-xl flex items-center hover:no-underline"
                    onclick="analyzeContent()"
                >
                    <i class="fas fa-magic text-sm ml-2"></i>
                    <span id="analyzeBtnText">تحليل المحتوى تلقائياً</span>
                    <span id="analyzeBtnLoading" class="hidden">
                        <i class="fas fa-spinner fa-spin text-sm ml-2"></i>
                        جاري التحليل...
                    </span>
                </button>
            </div>
            
            @php
                $brandProfile = old('brand_profile', $brandStyleExtractor->brand_profile ?? []);
            @endphp
            
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
                        value="{{ $brandProfile['voice'] ?? '' }}"
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
                        value="{{ $brandProfile['tone'] ?? '' }}"
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
                    >{{ $brandProfile['structure'] ?? '' }}</textarea>
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
                        value="{{ $brandProfile['language_style'] ?? '' }}"
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
                        value="{{ $brandProfile['cta_style'] ?? '' }}"
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
                        value="{{ $brandProfile['enemy'] ?? '' }}"
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
                    >{{ $brandProfile['values'] ?? '' }}</textarea>
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
                    >{{ $brandProfile['hook_patterns'] ?? '' }}</textarea>
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
                >{{ $brandProfile['phrases'] ?? '' }}</textarea>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="flex items-center justify-end space-x-4 rtl:space-x-reverse pt-6 border-t border-gray-200">
            <a href="{{ route('brand-style-extractors.index') }}" class="btn-secondary text-gray-700 px-8 py-3 rounded-xl">
                إلغاء
            </a>
            <button type="submit" class="btn-primary text-white px-8 py-3 rounded-xl">
                <i class="fas fa-save ml-2"></i>
                حفظ التغييرات
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

function analyzeContent() {
    const btn = document.getElementById('analyzeContentBtn');
    const btnText = document.getElementById('analyzeBtnText');
    const btnLoading = document.getElementById('analyzeBtnLoading');
    const extractorId = {{ $brandStyleExtractor->id }};
    
    // تعطيل الزر وإظهار التحميل
    btn.disabled = true;
    btnText.classList.add('hidden');
    btnLoading.classList.remove('hidden');
    
    // إظهار رسالة تحميل
    Swal.fire({
        title: 'جاري التحليل...',
        text: 'يرجى الانتظار بينما نقوم بتحليل المحتوى',
        allowOutsideClick: false,
        allowEscapeKey: false,
        showConfirmButton: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    // استدعاء API
    fetch(`/brand-style-extractors/${extractorId}/analyze`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // ملء الحقول بالبيانات المستخرجة
            if (data.brand_profile.voice) {
                document.getElementById('brand_profile_voice').value = data.brand_profile.voice;
            }
            if (data.brand_profile.tone) {
                document.getElementById('brand_profile_tone').value = data.brand_profile.tone;
            }
            if (data.brand_profile.structure) {
                document.getElementById('brand_profile_structure').value = data.brand_profile.structure;
            }
            if (data.brand_profile.language_style) {
                document.getElementById('brand_profile_language_style').value = data.brand_profile.language_style;
            }
            if (data.brand_profile.cta_style) {
                document.getElementById('brand_profile_cta_style').value = data.brand_profile.cta_style;
            }
            if (data.brand_profile.enemy) {
                document.getElementById('brand_profile_enemy').value = data.brand_profile.enemy;
            }
            if (data.brand_profile.values) {
                document.getElementById('brand_profile_values').value = data.brand_profile.values;
            }
            if (data.brand_profile.hook_patterns) {
                document.getElementById('brand_profile_hook_patterns').value = data.brand_profile.hook_patterns;
            }
            if (data.brand_profile.phrases) {
                document.getElementById('brand_profile_phrases').value = data.brand_profile.phrases;
            }
            
            Swal.fire({
                icon: 'success',
                title: 'تم التحليل بنجاح!',
                text: 'تم ملء حقول Brand Profile تلقائياً',
                confirmButtonText: 'حسناً'
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'فشل التحليل',
                text: data.error || 'حدث خطأ أثناء تحليل المحتوى',
                confirmButtonText: 'حسناً'
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'حدث خطأ',
            text: 'حدث خطأ أثناء الاتصال بالخادم',
            confirmButtonText: 'حسناً'
        });
    })
    .finally(() => {
        // إعادة تفعيل الزر
        btn.disabled = false;
        btnText.classList.remove('hidden');
        btnLoading.classList.add('hidden');
    });
}
</script>
@endsection

