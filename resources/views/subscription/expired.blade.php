@extends('layouts.dashboard')

@section('title', 'انتهاء فترة التجربة')
@section('page-title', 'انتهاء فترة التجربة')
@section('page-description', 'فترة التجربة المجانية قد انتهت')

@section('content')
<div class="space-y-6">
    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg flex items-center">
            <span class="material-icons ml-2">error</span>
            {{ session('error') }}
        </div>
    @endif

    <div class="max-w-4xl mx-auto">
        <!-- Expired Trial Card -->
        <div class="card rounded-2xl p-8 md:p-12 bg-gradient-to-br from-red-50 via-orange-50 to-yellow-50 border-2 border-red-200 shadow-xl">
            <div class="text-center">
                <!-- Icon with Animation -->
                <div class="relative inline-block mb-6">
                    <div class="w-24 h-24 md:w-28 md:h-28 bg-gradient-to-br from-red-500 to-orange-500 rounded-full flex items-center justify-center mx-auto shadow-lg transform hover:scale-110 transition-transform duration-300">
                        <span class="material-icons text-white text-5xl md:text-6xl">error_outline</span>
                    </div>
                    <div class="absolute -top-2 -right-2 w-6 h-6 bg-yellow-400 rounded-full animate-pulse"></div>
                </div>
                
                <!-- Title -->
                <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-4">انتهت فترة التجربة المجانية</h2>
                
                <!-- Description -->
                <div class="space-y-3 mb-8">
                    <p class="text-xl md:text-2xl text-gray-700 font-medium">
                        شكراً لتجربة MarketLink! 🎉
                    </p>
                    <p class="text-lg text-gray-600 max-w-2xl mx-auto leading-relaxed">
                        للاستمرار في استخدام النظام والاستفادة من جميع الميزات المتقدمة، يرجى الاشتراك في إحدى خططنا المناسبة
                    </p>
                </div>
                
                <!-- Trial End Date -->
                @if($subscription && $subscription->trial_ends_at)
                    <div class="bg-white/80 backdrop-blur-sm rounded-xl p-6 mb-8 max-w-md mx-auto border border-gray-200 shadow-md">
                        <div class="flex items-center justify-center mb-3">
                            <span class="material-icons text-gray-500 ml-2">calendar_today</span>
                            <p class="text-sm text-gray-600 font-medium">تاريخ انتهاء التجربة</p>
                        </div>
                        <p class="text-2xl font-bold text-gray-800">
                            {{ $subscription->trial_ends_at->format('Y-m-d') }}
                        </p>
                        <p class="text-sm text-gray-500 mt-2">
                            {{ $subscription->trial_ends_at->diffForHumans() }}
                        </p>
                    </div>
                @endif

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row gap-4 justify-center items-center mt-8">
                    <a href="{{ route('subscription.plans') }}" class="btn-primary text-white px-8 py-4 rounded-xl text-lg font-semibold inline-flex items-center justify-center shadow-lg hover:shadow-xl transition-all duration-300 min-w-[200px]">
                        <span class="material-icons ml-2">payment</span>
                        عرض الخطط والاشتراك
                    </a>
                    <a href="{{ route('dashboard') }}" class="bg-white text-gray-700 border-2 border-gray-300 px-8 py-4 rounded-xl text-lg font-semibold inline-flex items-center justify-center hover:bg-gray-50 hover:border-gray-400 transition-all duration-300 min-w-[200px] shadow-md">
                        <span class="material-icons ml-2">arrow_back</span>
                        العودة للوحة التحكم
                    </a>
                </div>
            </div>
        </div>

        <!-- Why Subscribe Section -->
        <div class="card rounded-2xl p-8 md:p-10 mt-8">
            <div class="text-center mb-8">
                <h3 class="text-2xl md:text-3xl font-bold text-gray-800 mb-2">لماذا تشترك في MarketLink؟</h3>
                <p class="text-gray-600">اكتشف الميزات الرائعة التي تنتظرك</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="flex items-start p-6 rounded-xl bg-gradient-to-br from-green-50 to-emerald-50 border border-green-200 hover:shadow-lg transition-all duration-300">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-green-500 rounded-full flex items-center justify-center shadow-md">
                            <span class="material-icons text-white">check_circle</span>
                        </div>
                    </div>
                    <div class="mr-4">
                        <h4 class="font-bold text-gray-800 mb-2 text-lg">إدارة شاملة</h4>
                        <p class="text-gray-600 leading-relaxed">أدِر عملاءك ومشاريعك وموظفيك من مكان واحد بسهولة تامة</p>
                    </div>
                </div>
                
                <div class="flex items-start p-6 rounded-xl bg-gradient-to-br from-blue-50 to-cyan-50 border border-blue-200 hover:shadow-lg transition-all duration-300">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center shadow-md">
                            <span class="material-icons text-white">assessment</span>
                        </div>
                    </div>
                    <div class="mr-4">
                        <h4 class="font-bold text-gray-800 mb-2 text-lg">تقارير مفصلة</h4>
                        <p class="text-gray-600 leading-relaxed">احصل على تقارير شاملة ومفصلة عن أداء عملك ومشاريعك</p>
                    </div>
                </div>
                
                <div class="flex items-start p-6 rounded-xl bg-gradient-to-br from-purple-50 to-pink-50 border border-purple-200 hover:shadow-lg transition-all duration-300">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-purple-500 rounded-full flex items-center justify-center shadow-md">
                            <span class="material-icons text-white">support_agent</span>
                        </div>
                    </div>
                    <div class="mr-4">
                        <h4 class="font-bold text-gray-800 mb-2 text-lg">دعم فني متواصل</h4>
                        <p class="text-gray-600 leading-relaxed">فريقنا المحترف جاهز لمساعدتك في أي وقت على مدار الساعة</p>
                    </div>
                </div>
                
                <div class="flex items-start p-6 rounded-xl bg-gradient-to-br from-indigo-50 to-violet-50 border border-indigo-200 hover:shadow-lg transition-all duration-300">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-indigo-500 rounded-full flex items-center justify-center shadow-md">
                            <span class="material-icons text-white">security</span>
                        </div>
                    </div>
                    <div class="mr-4">
                        <h4 class="font-bold text-gray-800 mb-2 text-lg">أمان عالي</h4>
                        <p class="text-gray-600 leading-relaxed">بياناتك محمية بأحدث تقنيات الأمان والتشفير المتقدم</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contact Support -->
        <div class="card rounded-2xl p-8 md:p-10 mt-8 bg-gradient-to-br from-blue-50 to-cyan-50 border-2 border-blue-200">
            <div class="text-center">
                <div class="w-16 h-16 bg-blue-500 rounded-full flex items-center justify-center mx-auto mb-4 shadow-lg">
                    <span class="material-icons text-white text-3xl">support_agent</span>
                </div>
                <h4 class="text-xl font-bold text-gray-800 mb-2">تحتاج مساعدة؟</h4>
                <p class="text-gray-600 mb-6 max-w-md mx-auto">
                    فريقنا جاهز للإجابة على جميع أسئلتك ومساعدتك في اختيار الخطة المناسبة لك
                </p>
                <a href="mailto:support@marketlink.com" class="inline-flex items-center text-blue-600 hover:text-blue-700 font-semibold text-lg transition-colors duration-300">
                    <span class="material-icons ml-2">email</span>
                    تواصل معنا
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

