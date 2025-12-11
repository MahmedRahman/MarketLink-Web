@extends('layouts.dashboard')

@section('title', 'تفاصيل المحتوى - Brand Style Extractor')
@section('page-title', 'تفاصيل المحتوى')
@section('page-description', 'عرض تفاصيل محتوى العلامة التجارية')

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
                    <h2 class="text-2xl font-bold text-gray-800">تفاصيل المحتوى</h2>
                    <p class="text-gray-600">عرض تفاصيل محتوى العلامة التجارية</p>
                </div>
            </div>
            <div class="flex items-center space-x-3 rtl:space-x-reverse">
                <a href="{{ route('brand-style-extractors.edit', $brandStyleExtractor) }}" class="btn-secondary text-gray-700 px-6 py-3 rounded-xl flex items-center hover:no-underline">
                    <i class="fas fa-edit text-sm ml-2"></i>
                    تعديل
                </a>
                <a href="{{ route('brand-style-extractors.index') }}" class="btn-secondary text-gray-700 px-6 py-3 rounded-xl flex items-center hover:no-underline">
                    <i class="fas fa-arrow-right text-sm ml-2"></i>
                    العودة للقائمة
                </a>
            </div>
        </div>
    </div>

    <!-- Content Details -->
    <div class="card rounded-2xl p-6">
        <div class="space-y-6">
            <!-- Project Info -->
            <div class="border-b border-gray-200 pb-4">
                <div class="flex items-center mb-2">
                    <i class="fas fa-project-diagram text-primary ml-2"></i>
                    <span class="text-sm font-medium text-gray-600">المشروع</span>
                </div>
                <p class="text-lg font-semibold text-gray-800">{{ $brandStyleExtractor->project->business_name ?? 'غير محدد' }}</p>
            </div>

            <!-- Content Type -->
            <div class="border-b border-gray-200 pb-4">
                <div class="flex items-center mb-2">
                    <i class="fas fa-tag text-primary ml-2"></i>
                    <span class="text-sm font-medium text-gray-600">نوع المحتوى</span>
                </div>
                <span class="inline-block px-4 py-2 bg-primary/10 text-primary rounded-lg font-semibold">
                    {{ $brandStyleExtractor->content_type_label }}
                </span>
            </div>

            <!-- Content -->
            <div class="border-b border-gray-200 pb-4">
                <div class="flex items-center mb-2">
                    <i class="fas fa-file-alt text-primary ml-2"></i>
                    <span class="text-sm font-medium text-gray-600">المحتوى</span>
                </div>
                <div class="bg-gray-50 rounded-xl p-4 border-r-4 border-primary">
                    <p class="text-gray-700 whitespace-pre-wrap">{{ $brandStyleExtractor->content }}</p>
                </div>
            </div>

            <!-- Brand Profile -->
            @if($brandStyleExtractor->brand_profile && !empty($brandStyleExtractor->brand_profile))
                <div class="border-b border-gray-200 pb-4">
                    <div class="flex items-center mb-4">
                        <i class="fas fa-user-tag text-primary ml-2"></i>
                        <span class="text-lg font-semibold text-gray-800">Brand Profile</span>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @if(isset($brandStyleExtractor->brand_profile['voice']))
                            <div>
                                <span class="text-sm font-medium text-gray-600">Voice:</span>
                                <p class="text-gray-800">{{ $brandStyleExtractor->brand_profile['voice'] }}</p>
                            </div>
                        @endif
                        @if(isset($brandStyleExtractor->brand_profile['tone']))
                            <div>
                                <span class="text-sm font-medium text-gray-600">Tone:</span>
                                <p class="text-gray-800">{{ $brandStyleExtractor->brand_profile['tone'] }}</p>
                            </div>
                        @endif
                        @if(isset($brandStyleExtractor->brand_profile['structure']))
                            <div class="md:col-span-2">
                                <span class="text-sm font-medium text-gray-600">Structure:</span>
                                <p class="text-gray-800 whitespace-pre-wrap">{{ $brandStyleExtractor->brand_profile['structure'] }}</p>
                            </div>
                        @endif
                        @if(isset($brandStyleExtractor->brand_profile['language_style']))
                            <div>
                                <span class="text-sm font-medium text-gray-600">Language Style:</span>
                                <p class="text-gray-800">{{ $brandStyleExtractor->brand_profile['language_style'] }}</p>
                            </div>
                        @endif
                        @if(isset($brandStyleExtractor->brand_profile['cta_style']))
                            <div>
                                <span class="text-sm font-medium text-gray-600">CTA Style:</span>
                                <p class="text-gray-800">{{ $brandStyleExtractor->brand_profile['cta_style'] }}</p>
                            </div>
                        @endif
                        @if(isset($brandStyleExtractor->brand_profile['enemy']))
                            <div>
                                <span class="text-sm font-medium text-gray-600">Enemy:</span>
                                <p class="text-gray-800">{{ $brandStyleExtractor->brand_profile['enemy'] }}</p>
                            </div>
                        @endif
                        @if(isset($brandStyleExtractor->brand_profile['values']))
                            <div class="md:col-span-2">
                                <span class="text-sm font-medium text-gray-600">Values:</span>
                                <p class="text-gray-800 whitespace-pre-wrap">{{ $brandStyleExtractor->brand_profile['values'] }}</p>
                            </div>
                        @endif
                        @if(isset($brandStyleExtractor->brand_profile['hook_patterns']))
                            <div class="md:col-span-2">
                                <span class="text-sm font-medium text-gray-600">Hook Patterns:</span>
                                <p class="text-gray-800 whitespace-pre-wrap">{{ $brandStyleExtractor->brand_profile['hook_patterns'] }}</p>
                            </div>
                        @endif
                        @if(isset($brandStyleExtractor->brand_profile['phrases']))
                            <div class="md:col-span-2">
                                <span class="text-sm font-medium text-gray-600">Phrases:</span>
                                <p class="text-gray-800 whitespace-pre-wrap">{{ $brandStyleExtractor->brand_profile['phrases'] }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Metadata -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <div class="flex items-center mb-2">
                        <i class="fas fa-user text-primary ml-2"></i>
                        <span class="text-sm font-medium text-gray-600">تم الإنشاء بواسطة</span>
                    </div>
                    <p class="text-gray-800">{{ $brandStyleExtractor->creator->name ?? 'غير محدد' }}</p>
                </div>
                <div>
                    <div class="flex items-center mb-2">
                        <i class="fas fa-clock text-primary ml-2"></i>
                        <span class="text-sm font-medium text-gray-600">تاريخ الإنشاء</span>
                    </div>
                    <p class="text-gray-800">{{ $brandStyleExtractor->created_at->format('Y-m-d H:i') }}</p>
                </div>
                <div>
                    <div class="flex items-center mb-2">
                        <i class="fas fa-edit text-primary ml-2"></i>
                        <span class="text-sm font-medium text-gray-600">آخر تحديث</span>
                    </div>
                    <p class="text-gray-800">{{ $brandStyleExtractor->updated_at->format('Y-m-d H:i') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

