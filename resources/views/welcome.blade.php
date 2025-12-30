<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>MarketLink - نظام إدارة شركات التسويق الإلكتروني</title>
    
    <!-- Cairo Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Material Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <style>
        * {
            font-family: 'Cairo', sans-serif;
        }
        
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .card-shadow {
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }
        
        .glass-effect {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
        
        .float-animation {
            animation: float 6s ease-in-out infinite;
        }
        
        .smooth-scroll {
            scroll-behavior: smooth;
        }
        
        .feature-card {
            transition: all 0.3s ease;
        }
        
        .feature-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
        }
        
        .pricing-card {
            transition: all 0.3s ease;
        }
        
        .pricing-card:hover {
            transform: scale(1.05);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            transition: all 0.3s ease;
            box-shadow: 0 4px 14px 0 rgba(102, 126, 234, 0.4);
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px 0 rgba(102, 126, 234, 0.6);
        }
        
        .btn-outline {
            background: transparent;
            border: 2px solid white;
            transition: all 0.3s ease;
        }
        
        .btn-outline:hover {
            background: white;
            color: #667eea;
        }
    </style>
</head>
<body class="smooth-scroll bg-gray-50">
    <!-- Navigation -->
    <nav class="fixed top-0 left-0 right-0 z-50 glass-effect">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-gradient-to-r from-purple-500 to-blue-500 rounded-xl flex items-center justify-center">
                        <span class="material-icons text-white">business</span>
                    </div>
                    <span class="mr-3 text-xl font-bold text-white">MarketLink</span>
                </div>
                <div class="hidden md:flex items-center space-x-reverse space-x-4">
                    <a href="{{ route('content-creation.index') }}" class="text-white hover:text-gray-200 px-4 py-2 transition-colors">إنشاء محتوى</a>
                    <a href="#features" class="text-white hover:text-gray-200 px-4 py-2 transition-colors">المميزات</a>
                    <a href="#pricing" class="text-white hover:text-gray-200 px-4 py-2 transition-colors">الأسعار</a>
                    <a href="#how-it-works" class="text-white hover:text-gray-200 px-4 py-2 transition-colors">كيف يعمل</a>
                    <a href="{{ route('login') }}" class="text-white hover:text-gray-200 px-4 py-2 transition-colors">تسجيل الدخول</a>
                    <a href="{{ route('register') }}" class="btn-primary text-white px-6 py-2 rounded-xl">ابدأ مجاناً</a>
                </div>
                <div class="md:hidden">
                    <button id="mobile-menu-btn" class="text-white">
                        <span class="material-icons">menu</span>
                    </button>
                </div>
            </div>
        </div>
        <!-- Mobile Menu -->
        <div id="mobile-menu" class="hidden md:hidden bg-white/95 backdrop-blur-lg border-t border-white/20">
            <div class="px-4 py-4 space-y-2">
                <a href="{{ route('content-creation.index') }}" class="block text-gray-800 hover:text-purple-600 px-4 py-2 rounded-lg transition-colors">إنشاء محتوى</a>
                <a href="#features" class="block text-gray-800 hover:text-purple-600 px-4 py-2 rounded-lg transition-colors">المميزات</a>
                <a href="#pricing" class="block text-gray-800 hover:text-purple-600 px-4 py-2 rounded-lg transition-colors">الأسعار</a>
                <a href="#how-it-works" class="block text-gray-800 hover:text-purple-600 px-4 py-2 rounded-lg transition-colors">كيف يعمل</a>
                <a href="{{ route('login') }}" class="block text-gray-800 hover:text-purple-600 px-4 py-2 rounded-lg transition-colors">تسجيل الدخول</a>
                <a href="{{ route('register') }}" class="block btn-primary text-white px-4 py-2 rounded-lg text-center">ابدأ مجاناً</a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="gradient-bg min-h-screen flex items-center justify-center pt-16 relative overflow-hidden">
        <!-- Background Shapes -->
        <div class="absolute inset-0 overflow-hidden">
            <div class="absolute w-96 h-96 bg-white/10 rounded-full -top-48 -right-48 float-animation"></div>
            <div class="absolute w-72 h-72 bg-white/10 rounded-full -bottom-36 -left-36 float-animation" style="animation-delay: 2s;"></div>
        </div>
        
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 relative z-10">
            <div class="text-center">
                <h1 class="text-5xl md:text-6xl font-bold text-white mb-6 leading-tight">
                    نظام إدارة شركات التسويق الإلكتروني
                </h1>
                <p class="text-xl md:text-2xl text-white/90 mb-8 max-w-3xl mx-auto">
                    أدِر عملاءك ومشاريعك وموظفيك من مكان واحد. نظام متكامل لشركات التسويق الرقمي
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                    <a href="{{ route('register') }}" class="btn-primary text-white px-8 py-4 rounded-xl text-lg font-semibold inline-flex items-center">
                        <span class="material-icons ml-2">rocket_launch</span>
                        ابدأ تجربتك المجانية
                    </a>
                    <a href="{{ route('login') }}" class="btn-outline text-white px-8 py-4 rounded-xl text-lg font-semibold inline-flex items-center">
                        <span class="material-icons ml-2">login</span>
                        تسجيل الدخول
                    </a>
                </div>
                <p class="text-white/80 mt-6 text-sm">
                    ✨ تجربة مجانية 14 يوم - بدون بطاقة ائتمان
                </p>
            </div>
        </div>
        
        <!-- Scroll Indicator -->
        <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 text-white animate-bounce">
            <span class="material-icons text-4xl">keyboard_arrow_down</span>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-gray-800 mb-4">مميزات النظام</h2>
                <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                    كل ما تحتاجه لإدارة شركة تسويق إلكتروني ناجحة في مكان واحد
                </p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="feature-card bg-white rounded-2xl p-8 card-shadow border border-gray-100">
                    <div class="w-16 h-16 bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl flex items-center justify-center mb-6">
                        <span class="material-icons text-white text-3xl">people</span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">إدارة العملاء</h3>
                    <p class="text-gray-600">
                        أدِر جميع عملائك من مكان واحد. احفظ معلوماتهم، حالة المشاريع، والتواصل بسهولة
                    </p>
                </div>
                
                <!-- Feature 2 -->
                <div class="feature-card bg-white rounded-2xl p-8 card-shadow border border-gray-100">
                    <div class="w-16 h-16 bg-gradient-to-r from-green-500 to-green-600 rounded-xl flex items-center justify-center mb-6">
                        <span class="material-icons text-white text-3xl">work</span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">إدارة المشاريع</h3>
                    <p class="text-gray-600">
                        تتبع جميع مشاريعك مع تفاصيل كاملة عن الحسابات والمعلومات المطلوبة
                    </p>
                </div>
                
                <!-- Feature 3 -->
                <div class="feature-card bg-white rounded-2xl p-8 card-shadow border border-gray-100">
                    <div class="w-16 h-16 bg-gradient-to-r from-purple-500 to-purple-600 rounded-xl flex items-center justify-center mb-6">
                        <span class="material-icons text-white text-3xl">group</span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">إدارة الموظفين</h3>
                    <p class="text-gray-600">
                        نظم فريقك بكفاءة. أدِر أدوار الموظفين وحالاتهم بسهولة
                    </p>
                </div>
                
                <!-- Feature 4 -->
                <div class="feature-card bg-white rounded-2xl p-8 card-shadow border border-gray-100">
                    <div class="w-16 h-16 bg-gradient-to-r from-orange-500 to-orange-600 rounded-xl flex items-center justify-center mb-6">
                        <span class="material-icons text-white text-3xl">analytics</span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">التقارير والإحصائيات</h3>
                    <p class="text-gray-600">
                        احصل على تقارير مفصلة عن الإيرادات والمصروفات والأداء العام
                    </p>
                </div>
                
                <!-- Feature 5 -->
                <div class="feature-card bg-white rounded-2xl p-8 card-shadow border border-gray-100">
                    <div class="w-16 h-16 bg-gradient-to-r from-red-500 to-red-600 rounded-xl flex items-center justify-center mb-6">
                        <span class="material-icons text-white text-3xl">security</span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">آمن ومحمي</h3>
                    <p class="text-gray-600">
                        بياناتك محمية بأحدث تقنيات الأمان. كل منظمة لها بياناتها الخاصة
                    </p>
                </div>
                
                <!-- Feature 6 -->
                <div class="feature-card bg-white rounded-2xl p-8 card-shadow border border-gray-100">
                    <div class="w-16 h-16 bg-gradient-to-r from-teal-500 to-teal-600 rounded-xl flex items-center justify-center mb-6">
                        <span class="material-icons text-white text-3xl">cloud</span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">في السحابة</h3>
                    <p class="text-gray-600">
                        وصول من أي مكان وفي أي وقت. لا حاجة لتثبيت أو صيانة
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works Section -->
    <section id="how-it-works" class="py-20 bg-gradient-to-br from-gray-50 to-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-gray-800 mb-4">كيف يعمل النظام</h2>
                <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                    ابدأ في دقائق. سهولة في الاستخدام، قوة في الأداء
                </p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="text-center">
                    <div class="w-20 h-20 bg-gradient-to-r from-blue-500 to-purple-500 rounded-full flex items-center justify-center mx-auto mb-6 text-white text-3xl font-bold">
                        1
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">سجل مجاناً</h3>
                    <p class="text-gray-600">
                        أنشئ حسابك في دقائق. لا حاجة لبطاقة ائتمان. ابدأ تجربتك المجانية 14 يوم
                    </p>
                </div>
                
                <div class="text-center">
                    <div class="w-20 h-20 bg-gradient-to-r from-purple-500 to-pink-500 rounded-full flex items-center justify-center mx-auto mb-6 text-white text-3xl font-bold">
                        2
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">أضف بياناتك</h3>
                    <p class="text-gray-600">
                        ابدأ بإضافة عملائك ومشاريعك وموظفيك. واجهة سهلة وبسيطة
                    </p>
                </div>
                
                <div class="text-center">
                    <div class="w-20 h-20 bg-gradient-to-r from-pink-500 to-red-500 rounded-full flex items-center justify-center mx-auto mb-6 text-white text-3xl font-bold">
                        3
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">استمتع بالنتائج</h3>
                    <p class="text-gray-600">
                        أدِر عملك بكفاءة. تابع التقارير واتخذ قرارات مدروسة
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Pricing Section -->
    <section id="pricing" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-gray-800 mb-4">خطط الاشتراك</h2>
                <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                    اختر الخطة المناسبة لك. جميع الخطط تشمل تجربة مجانية 14 يوم
                </p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 max-w-6xl mx-auto">
                @forelse($plans as $planIndex => $plan)
                    @php
                        $isPopular = $planIndex === 1 && $plans->count() >= 3;
                    @endphp
                    <div class="pricing-card bg-white rounded-2xl p-8 card-shadow border-2 {{ $isPopular ? 'border-purple-400 transform scale-105 bg-gradient-to-br from-purple-500 to-blue-500 relative' : 'border-gray-200' }}">
                        @if($isPopular)
                            <div class="absolute top-0 left-1/2 transform -translate-x-1/2 -translate-y-1/2 bg-yellow-400 text-gray-800 px-4 py-1 rounded-full text-sm font-bold z-10">
                                الأكثر شعبية
                            </div>
                        @endif
                        
                        <h3 class="text-2xl font-bold {{ $isPopular ? 'text-white' : 'text-gray-800' }} mb-2">{{ $plan->name }}</h3>
                        
                        @if($plan->description)
                            <p class="text-sm {{ $isPopular ? 'text-white/80' : 'text-gray-600' }} mb-4">{{ $plan->description }}</p>
                        @endif
                        
                        <div class="mb-6">
                            <div class="flex items-baseline">
                                <span class="text-4xl font-bold {{ $isPopular ? 'text-white' : 'text-gray-800' }}">{{ number_format($plan->price_egp, 0) }}</span>
                                <span class="{{ $isPopular ? 'text-white/80' : 'text-gray-600' }} mr-2"> جنيه مصري</span>
                            </div>
                            <div class="text-sm {{ $isPopular ? 'text-white/70' : 'text-gray-500' }} mt-1">لـ {{ $plan->duration_days }} يوم</div>
                        </div>
                        
                        @if($plan->features->count() > 0)
                            <ul class="space-y-4 mb-8">
                                @foreach($plan->features as $feature)
                                    <li class="flex items-start">
                                        <span class="material-icons {{ $isPopular ? 'text-white' : 'text-green-500' }} ml-2 flex-shrink-0">check_circle</span>
                                        <span class="{{ $isPopular ? 'text-white' : 'text-gray-600' }}">{{ $feature->feature_name }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <div class="mb-8">
                                <p class="{{ $isPopular ? 'text-white/70' : 'text-gray-500' }} text-sm">لا توجد ميزات لهذه الخطة</p>
                            </div>
                        @endif
                        
                        <a href="{{ route('register') }}" class="{{ $isPopular ? 'bg-white text-purple-600 hover:bg-gray-100' : 'btn-primary text-white' }} w-full py-3 rounded-xl text-center block font-semibold transition-colors">
                            ابدأ الآن
                        </a>
                    </div>
                @empty
                    <div class="col-span-3 text-center py-12">
                        <p class="text-gray-500 text-lg">لا توجد خطط متاحة حالياً</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 gradient-bg">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-4xl font-bold text-white mb-6">جاهز للبدء؟</h2>
            <p class="text-xl text-white/90 mb-8">
                انضم إلى مئات الشركات التي تستخدم MarketLink لإدارة أعمالها
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('register') }}" class="bg-white text-purple-600 px-8 py-4 rounded-xl text-lg font-semibold inline-flex items-center justify-center hover:bg-gray-100 transition-colors">
                    <span class="material-icons ml-2">rocket_launch</span>
                    ابدأ تجربتك المجانية
                </a>
                <a href="{{ route('login') }}" class="btn-outline text-white px-8 py-4 rounded-xl text-lg font-semibold inline-flex items-center justify-center">
                    <span class="material-icons ml-2">login</span>
                    تسجيل الدخول
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <div class="flex items-center mb-4">
                        <div class="w-10 h-10 bg-gradient-to-r from-purple-500 to-blue-500 rounded-xl flex items-center justify-center">
                            <span class="material-icons">business</span>
                        </div>
                        <span class="mr-3 text-xl font-bold">MarketLink</span>
                    </div>
                    <p class="text-gray-400 text-sm">
                        نظام إدارة شركات التسويق الإلكتروني المتكامل
                    </p>
                </div>
                
                <div>
                    <h4 class="font-bold mb-4">روابط سريعة</h4>
                    <ul class="space-y-2 text-sm text-gray-400">
                        <li><a href="#features" class="hover:text-white transition-colors">المميزات</a></li>
                        <li><a href="#pricing" class="hover:text-white transition-colors">الأسعار</a></li>
                        <li><a href="#how-it-works" class="hover:text-white transition-colors">كيف يعمل</a></li>
                    </ul>
                </div>
                
                <div>
                    <h4 class="font-bold mb-4">الحساب</h4>
                    <ul class="space-y-2 text-sm text-gray-400">
                        <li><a href="{{ route('login') }}" class="hover:text-white transition-colors">تسجيل الدخول</a></li>
                        <li><a href="{{ route('register') }}" class="hover:text-white transition-colors">إنشاء حساب</a></li>
                    </ul>
                </div>
                
                <div>
                    <h4 class="font-bold mb-4">تواصل معنا</h4>
                    <p class="text-sm text-gray-400 mb-2">atpfreelancer@gmail.com</p>
                    <p class="text-sm text-gray-400">+201002089079</p>
                </div>
            </div>
            
            <div class="border-t border-gray-800 mt-8 pt-8 text-center text-sm text-gray-400">
                <p>© 2024 MarketLink. جميع الحقوق محفوظة.</p>
            </div>
        </div>
    </footer>

    <!-- JavaScript for Mobile Menu -->
    <script>
        document.getElementById('mobile-menu-btn').addEventListener('click', function() {
            const menu = document.getElementById('mobile-menu');
            menu.classList.toggle('hidden');
        });

        // Close mobile menu when clicking on a link
        document.querySelectorAll('#mobile-menu a').forEach(link => {
            link.addEventListener('click', function() {
                document.getElementById('mobile-menu').classList.add('hidden');
            });
        });
    </script>
</body>
</html>

