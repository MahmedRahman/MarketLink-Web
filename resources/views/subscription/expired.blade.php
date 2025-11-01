@extends('layouts.dashboard')

@section('title', 'انتهاء فترة التجربة')
@section('page-title', 'انتهاء فترة التجربة')
@section('page-description', 'فترة التجربة المجانية قد انتهت')

@section('content')
<div class="space-y-6">
    <div class="max-w-3xl mx-auto">
        <!-- Expired Trial Card -->
        <div class="card bg-gradient-to-br from-red-50 to-orange-50 border-2 border-red-200">
            <div class="text-center py-8">
                <div class="w-20 h-20 bg-red-500 rounded-full flex items-center justify-center mx-auto mb-6">
                    <span class="material-icons text-white text-4xl">error_outline</span>
                </div>
                <h2 class="text-3xl font-bold text-gray-800 mb-4">انتهت فترة التجربة المجانية</h2>
                <p class="text-lg text-gray-600 mb-2">
                    شكراً لتجربة MarketLink!
                </p>
                <p class="text-gray-600 mb-8">
                    للاستمرار في استخدام النظام، يرجى الاشتراك في إحدى خططنا المناسبة
                </p>
                
                @if($subscription)
                    <div class="bg-white rounded-lg p-4 mb-6 text-right">
                        <p class="text-sm text-gray-600 mb-2">تاريخ انتهاء التجربة:</p>
                        <p class="font-semibold text-gray-800">{{ $subscription->trial_ends_at ? $subscription->trial_ends_at->format('Y-m-d') : '-' }}</p>
                    </div>
                @endif

                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('subscription.plans') }}" class="btn-primary text-white px-8 py-4 rounded-xl text-lg font-semibold inline-flex items-center justify-center">
                        <span class="material-icons ml-2">payment</span>
                        عرض الخطط والاشتراك
                    </a>
                    <a href="{{ route('dashboard') }}" class="bg-white text-gray-700 border-2 border-gray-300 px-8 py-4 rounded-xl text-lg font-semibold inline-flex items-center justify-center hover:bg-gray-50">
                        <span class="material-icons ml-2">arrow_back</span>
                        العودة للوحة التحكم
                    </a>
                </div>
            </div>
        </div>

        <!-- Why Subscribe Section -->
        <div class="card mt-6">
            <h3 class="text-xl font-bold text-gray-800 mb-4 text-center">لماذا تشترك في MarketLink؟</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="flex items-start">
                    <span class="material-icons text-green-500 ml-3 flex-shrink-0">check_circle</span>
                    <div>
                        <h4 class="font-semibold text-gray-800 mb-1">إدارة شاملة</h4>
                        <p class="text-sm text-gray-600">أدِر عملاءك ومشاريعك وموظفيك من مكان واحد</p>
                    </div>
                </div>
                <div class="flex items-start">
                    <span class="material-icons text-green-500 ml-3 flex-shrink-0">check_circle</span>
                    <div>
                        <h4 class="font-semibold text-gray-800 mb-1">تقارير مفصلة</h4>
                        <p class="text-sm text-gray-600">احصل على تقارير شاملة عن أداء عملك</p>
                    </div>
                </div>
                <div class="flex items-start">
                    <span class="material-icons text-green-500 ml-3 flex-shrink-0">check_circle</span>
                    <div>
                        <h4 class="font-semibold text-gray-800 mb-1">دعم فني متواصل</h4>
                        <p class="text-sm text-gray-600">فريقنا جاهز لمساعدتك في أي وقت</p>
                    </div>
                </div>
                <div class="flex items-start">
                    <span class="material-icons text-green-500 ml-3 flex-shrink-0">check_circle</span>
                    <div>
                        <h4 class="font-semibold text-gray-800 mb-1">أمان عالي</h4>
                        <p class="text-sm text-gray-600">بياناتك محمية بأحدث تقنيات الأمان</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contact Support -->
        <div class="card mt-6 bg-blue-50 border border-blue-200">
            <div class="text-center">
                <span class="material-icons text-blue-600 text-3xl mb-3">support_agent</span>
                <h4 class="font-semibold text-gray-800 mb-2">تحتاج مساعدة؟</h4>
                <p class="text-sm text-gray-600 mb-4">فريقنا جاهز للإجابة على أسئلتك</p>
                <a href="mailto:support@marketlink.com" class="text-blue-600 hover:text-blue-700 font-semibold">
                    تواصل معنا
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

