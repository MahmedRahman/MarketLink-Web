@extends('layouts.dashboard')

@section('title', 'تحليل المحتوى - ' . $project->business_name)
@section('page-title', 'تحليل المحتوى')
@section('page-description', 'تحليل جميع المحتويات النصية للمشروع: ' . $project->business_name)

@section('content')
<div class="space-y-6">
        <!-- Header -->
        <div class="card page-header rounded-2xl p-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="w-12 h-12 logo-gradient rounded-2xl flex items-center justify-center shadow-lg icon-spacing ml-3">
                        <i class="fas fa-magic text-white text-xl"></i>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800">تحليل المحتوى</h2>
                        <p class="text-gray-600">المشروع: {{ $project->business_name }}</p>
                    </div>
                </div>
                <div class="flex items-center space-x-3 rtl:space-x-reverse">
                    <a href="{{ route('projects.show', $project) }}" class="btn-secondary text-gray-700 px-6 py-3 rounded-xl flex items-center hover:no-underline">
                        <i class="fas fa-arrow-right text-sm ml-2"></i>
                        العودة للمشروع
                    </a>
                </div>
            </div>
        </div>

        <!-- Content List -->
        @if($posts->count() > 0)
            <div class="card rounded-2xl p-6">
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-purple-100 rounded-xl flex items-center justify-center icon-spacing ml-3">
                            <i class="fas fa-file-alt text-purple-600"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800">المحتوى النصي</h3>
                            <p class="text-sm text-gray-600">سيتم تحليل {{ $posts->count() }} محتوى نصي</p>
                        </div>
                    </div>
                    <button 
                        type="button" 
                        id="analyzeBtn"
                        class="btn-primary text-white px-6 py-3 rounded-xl flex items-center hover:no-underline shadow-lg"
                    >
                        <i class="fas fa-magic text-sm ml-2"></i>
                        <span id="analyzeBtnText">بدء التحليل</span>
                        <span id="analyzeBtnLoading" class="hidden">
                            <i class="fas fa-spinner fa-spin text-sm ml-2"></i>
                            جاري التحليل...
                        </span>
                    </button>
                </div>

                <div class="space-y-4">
                    @foreach($posts as $post)
                        <div class="bg-gradient-to-br from-purple-50 to-indigo-50 rounded-xl p-4 border border-purple-100">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center flex-1">
                                    <div class="w-10 h-10 bg-purple-500 rounded-lg flex items-center justify-center ml-3">
                                        @if($post->content_type == 'post')
                                            <i class="fas fa-file-alt text-white text-sm"></i>
                                        @elseif($post->content_type == 'reels')
                                            <i class="fas fa-video text-white text-sm"></i>
                                        @else
                                            <i class="fas fa-align-left text-white text-sm"></i>
                                        @endif
                                    </div>
                                    <div class="flex-1">
                                        <h4 class="font-semibold text-gray-800">{{ $post->content_type_label }}</h4>
                                        <p class="text-sm text-gray-600 mt-1 line-clamp-2">{{ Str::limit($post->content, 100) }}</p>
                                        <p class="text-xs text-gray-500 mt-1">{{ $post->created_at->format('Y-m-d') }}</p>
                                    </div>
                                </div>
                                @if($post->revenue && $post->revenue > 0)
                                    <div class="bg-green-100 text-green-700 px-3 py-1 rounded-lg text-sm font-semibold ml-3">
                                        {{ number_format($post->revenue, 2) }} {{ $post->currency ?? 'EGP' }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            <div class="card rounded-2xl p-6">
                <div class="text-center py-12">
                    <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-file-alt text-purple-400 text-2xl"></i>
                    </div>
                    <h4 class="text-lg font-medium text-gray-900 mb-2">لا يوجد محتوى نصي</h4>
                    <p class="text-gray-500 mb-6">لم يتم إضافة أي محتوى نصي لهذا المشروع بعد</p>
                    <a href="{{ route('projects.content.create', $project) }}" class="btn-primary text-white px-6 py-3 rounded-xl inline-flex items-center hover:no-underline">
                        <i class="fas fa-plus text-sm ml-2"></i>
                        إضافة محتوى جديد
                    </a>
                </div>
            </div>
        @endif

        <!-- Brand Profile Section -->
        @if($project->brand_profile && is_array($project->brand_profile) && count($project->brand_profile) > 0)
            <div class="card rounded-2xl p-6">
                <div class="flex items-center mb-6">
                    <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center icon-spacing ml-3">
                        <i class="fas fa-magic text-white"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-800">Brand Profile</h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @if(isset($project->brand_profile['voice']) && !empty($project->brand_profile['voice']))
                        <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl p-5 border border-blue-100">
                            <div class="flex items-center mb-3">
                                <div class="w-8 h-8 bg-blue-500 rounded-lg flex items-center justify-center ml-3">
                                    <i class="fas fa-microphone text-white text-xs"></i>
                                </div>
                                <h4 class="font-semibold text-gray-800">Voice</h4>
                            </div>
                            <p class="text-sm text-gray-700 leading-relaxed">{{ $project->brand_profile['voice'] }}</p>
                        </div>
                    @endif

                    @if(isset($project->brand_profile['tone']) && !empty($project->brand_profile['tone']))
                        <div class="bg-gradient-to-br from-purple-50 to-pink-50 rounded-xl p-5 border border-purple-100">
                            <div class="flex items-center mb-3">
                                <div class="w-8 h-8 bg-purple-500 rounded-lg flex items-center justify-center ml-3">
                                    <i class="fas fa-volume-up text-white text-xs"></i>
                                </div>
                                <h4 class="font-semibold text-gray-800">Tone</h4>
                            </div>
                            <p class="text-sm text-gray-700 leading-relaxed">{{ $project->brand_profile['tone'] }}</p>
                        </div>
                    @endif

                    @if(isset($project->brand_profile['structure']) && !empty($project->brand_profile['structure']))
                        <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-xl p-5 border border-green-100">
                            <div class="flex items-center mb-3">
                                <div class="w-8 h-8 bg-green-500 rounded-lg flex items-center justify-center ml-3">
                                    <i class="fas fa-sitemap text-white text-xs"></i>
                                </div>
                                <h4 class="font-semibold text-gray-800">Structure</h4>
                            </div>
                            <p class="text-sm text-gray-700 leading-relaxed">{{ $project->brand_profile['structure'] }}</p>
                        </div>
                    @endif

                    @if(isset($project->brand_profile['language_style']) && !empty($project->brand_profile['language_style']))
                        <div class="bg-gradient-to-br from-yellow-50 to-orange-50 rounded-xl p-5 border border-yellow-100">
                            <div class="flex items-center mb-3">
                                <div class="w-8 h-8 bg-yellow-500 rounded-lg flex items-center justify-center ml-3">
                                    <i class="fas fa-language text-white text-xs"></i>
                                </div>
                                <h4 class="font-semibold text-gray-800">Language Style</h4>
                            </div>
                            <p class="text-sm text-gray-700 leading-relaxed">{{ $project->brand_profile['language_style'] }}</p>
                        </div>
                    @endif

                    @if(isset($project->brand_profile['cta_style']) && !empty($project->brand_profile['cta_style']))
                        <div class="bg-gradient-to-br from-red-50 to-pink-50 rounded-xl p-5 border border-red-100">
                            <div class="flex items-center mb-3">
                                <div class="w-8 h-8 bg-red-500 rounded-lg flex items-center justify-center ml-3">
                                    <i class="fas fa-hand-pointer text-white text-xs"></i>
                                </div>
                                <h4 class="font-semibold text-gray-800">CTA Style</h4>
                            </div>
                            <p class="text-sm text-gray-700 leading-relaxed">{{ $project->brand_profile['cta_style'] }}</p>
                        </div>
                    @endif

                    @if(isset($project->brand_profile['enemy']) && !empty($project->brand_profile['enemy']))
                        <div class="bg-gradient-to-br from-gray-50 to-slate-50 rounded-xl p-5 border border-gray-100">
                            <div class="flex items-center mb-3">
                                <div class="w-8 h-8 bg-gray-500 rounded-lg flex items-center justify-center ml-3">
                                    <i class="fas fa-shield-alt text-white text-xs"></i>
                                </div>
                                <h4 class="font-semibold text-gray-800">Enemy</h4>
                            </div>
                            <p class="text-sm text-gray-700 leading-relaxed">{{ $project->brand_profile['enemy'] }}</p>
                        </div>
                    @endif

                    @if(isset($project->brand_profile['values']) && !empty($project->brand_profile['values']))
                        <div class="bg-gradient-to-br from-indigo-50 to-blue-50 rounded-xl p-5 border border-indigo-100">
                            <div class="flex items-center mb-3">
                                <div class="w-8 h-8 bg-indigo-500 rounded-lg flex items-center justify-center ml-3">
                                    <i class="fas fa-heart text-white text-xs"></i>
                                </div>
                                <h4 class="font-semibold text-gray-800">Values</h4>
                            </div>
                            <p class="text-sm text-gray-700 leading-relaxed">{{ $project->brand_profile['values'] }}</p>
                        </div>
                    @endif

                    @if(isset($project->brand_profile['hook_patterns']) && !empty($project->brand_profile['hook_patterns']))
                        <div class="bg-gradient-to-br from-teal-50 to-cyan-50 rounded-xl p-5 border border-teal-100">
                            <div class="flex items-center mb-3">
                                <div class="w-8 h-8 bg-teal-500 rounded-lg flex items-center justify-center ml-3">
                                    <i class="fas fa-fish text-white text-xs"></i>
                                </div>
                                <h4 class="font-semibold text-gray-800">Hook Patterns</h4>
                            </div>
                            <p class="text-sm text-gray-700 leading-relaxed">{{ $project->brand_profile['hook_patterns'] }}</p>
                        </div>
                    @endif

                    @if(isset($project->brand_profile['phrases']) && !empty($project->brand_profile['phrases']))
                        <div class="bg-gradient-to-br from-amber-50 to-yellow-50 rounded-xl p-5 border border-amber-100 md:col-span-2">
                            <div class="flex items-center mb-3">
                                <div class="w-8 h-8 bg-amber-500 rounded-lg flex items-center justify-center ml-3">
                                    <i class="fas fa-quote-left text-white text-xs"></i>
                                </div>
                                <h4 class="font-semibold text-gray-800">Phrases</h4>
                            </div>
                            <p class="text-sm text-gray-700 leading-relaxed">{{ $project->brand_profile['phrases'] }}</p>
                        </div>
                    @endif
                </div>
            </div>
        @endif
</div>
@endsection
@section('scripts')
<script>
const projectId = {{ $project->id }};

document.addEventListener('DOMContentLoaded', function() {
    const analyzeBtn = document.getElementById('analyzeBtn');
    const analyzeBtnText = document.getElementById('analyzeBtnText');
    const analyzeBtnLoading = document.getElementById('analyzeBtnLoading');
    
    if (analyzeBtn) {
        analyzeBtn.addEventListener('click', function() {
            // تعطيل الزر
            analyzeBtn.disabled = true;
            analyzeBtnText.classList.add('hidden');
            analyzeBtnLoading.classList.remove('hidden');
            
            // إظهار رسالة تحميل
            Swal.fire({
                title: 'جاري التحليل...',
                text: 'يرجى الانتظار بينما نقوم بتحليل جميع المحتويات النصية للمشروع',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // استدعاء API
            console.log('Starting content analysis for project:', projectId);
            
            const csrfTokenElement = document.querySelector('meta[name="csrf-token"]');
            if (!csrfTokenElement) {
                Swal.fire({
                    icon: 'error',
                    title: 'خطأ',
                    text: 'CSRF token غير موجود',
                    confirmButtonText: 'حسناً'
                });
                analyzeBtn.disabled = false;
                analyzeBtnText.classList.remove('hidden');
                analyzeBtnLoading.classList.add('hidden');
                return;
            }
            
            const csrfToken = csrfTokenElement.getAttribute('content');
            console.log('CSRF Token:', csrfToken ? 'Found' : 'Not found');
            
            fetch(`/projects/${projectId}/analyze-content`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                console.log('Response status:', response.status);
                if (!response.ok) {
                    return response.json().then(data => {
                        throw new Error(data.error || `HTTP error! status: ${response.status}`);
                    });
                }
                return response.json();
            })
            .then(data => {
                console.log('Response data:', data);
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'تم التحليل بنجاح!',
                        text: 'تم تحليل جميع المحتويات النصية وحفظ Brand Profile على مستوى المشروع',
                        confirmButtonText: 'حسناً'
                    }).then(() => {
                        // إعادة تحميل الصفحة لعرض البيانات المحدثة
                        location.reload();
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
                    text: error.message || 'حدث خطأ أثناء الاتصال بالخادم',
                    confirmButtonText: 'حسناً'
                });
            })
            .finally(() => {
                // إعادة تفعيل الزر
                analyzeBtn.disabled = false;
                analyzeBtnText.classList.remove('hidden');
                analyzeBtnLoading.classList.add('hidden');
            });
        });
    }
});
</script>
@endsection

