<!DOCTYPE html>
<html lang="ar" dir="rtl" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>MarketLink - المنصة الأولى لإدارة شركات ووكالات التسويق الرقمي</title>
    <meta name="description" content="نظام متكامل لإدارة شركات التسويق الرقمي: العملاء، المشاريع، الموظفين، التقارير المالية، وإنشاء المحتوى بالذكاء الاصطناعي.">

    <!-- Google Fonts: Cairo & Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        cairo: ['Cairo', 'sans-serif'],
                        sans: ['Plus Jakarta Sans', 'Cairo', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50: '#eef2ff',
                            100: '#e0e7ff',
                            400: '#818cf8',
                            500: '#6366f1',
                            600: '#4f46e5',
                            700: '#4338ca',
                            900: '#312e81',
                        },
                        violetAccent: {
                            500: '#8b5cf6',
                            600: '#7c3aed',
                        },
                        cyanAccent: {
                            400: '#22d3ee',
                            500: '#06b6d4',
                        }
                    },
                    animation: {
                        'float': 'float 6s ease-in-out infinite',
                        'pulse-slow': 'pulse 4s cubic-bezier(0.4, 0, 0.6, 1) infinite',
                        'glow': 'glow 3s ease-in-out infinite alternate',
                    },
                    keyframes: {
                        float: {
                            '0%, 100%': { transform: 'translateY(0px)' },
                            '50%': { transform: 'translateY(-15px)' },
                        },
                        glow: {
                            '0%': { opacity: '0.4', filter: 'blur(20px)' },
                            '100%': { opacity: '0.8', filter: 'blur(35px)' },
                        }
                    }
                }
            }
        }
    </script>

    <style>
        * {
            font-family: 'Cairo', 'Plus Jakarta Sans', sans-serif;
        }

        body {
            background-color: #0b0f17;
            color: #f1f5f9;
            overflow-x: hidden;
        }

        /* Glassmorphism utility classes */
        .glass-card {
            background: rgba(15, 23, 42, 0.65);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }

        .glass-card-hover {
            transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .glass-card-hover:hover {
            transform: translateY(-6px);
            background: rgba(30, 41, 59, 0.75);
            border-color: rgba(99, 102, 241, 0.4);
            box-shadow: 0 20px 40px -15px rgba(99, 102, 241, 0.25);
        }

        .glass-nav {
            background: rgba(11, 15, 23, 0.8);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.07);
        }

        /* Gradient text */
        .text-gradient {
            background: linear-gradient(135deg, #ffffff 0%, #cbd5e1 50%, #818cf8 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .text-gradient-primary {
            background: linear-gradient(135deg, #a855f7 0%, #6366f1 50%, #38bdf8 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .text-gradient-cyan {
            background: linear-gradient(135deg, #38bdf8 0%, #22d3ee 50%, #818cf8 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }
        ::-webkit-scrollbar-track {
            background: #0b0f17;
        }
        ::-webkit-scrollbar-thumb {
            background: #1e293b;
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #4f46e5;
        }
    </style>
</head>
<body class="antialiased selection:bg-brand-500 selection:text-white">

    <!-- Ambient Glowing Background Orbs -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none z-0">
        <div class="absolute -top-40 -right-40 w-[600px] h-[600px] bg-indigo-600/20 rounded-full blur-[140px] animate-pulse-slow"></div>
        <div class="absolute top-1/3 -left-40 w-[500px] h-[500px] bg-purple-600/15 rounded-full blur-[140px] animate-pulse-slow" style="animation-delay: 2s;"></div>
        <div class="absolute -bottom-40 right-1/4 w-[600px] h-[600px] bg-cyan-600/15 rounded-full blur-[160px] animate-pulse-slow" style="animation-delay: 4s;"></div>
    </div>

    <!-- Navigation Header -->
    <nav class="fixed top-0 left-0 right-0 z-50 glass-nav transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                
                <!-- Logo -->
                <a href="{{ route('welcome') }}" class="flex items-center gap-3 group">
                    <div class="w-11 h-11 rounded-xl bg-gradient-to-tr from-brand-600 via-violetAccent-600 to-cyanAccent-500 p-[1px] shadow-lg shadow-brand-500/20 group-hover:scale-105 transition-transform">
                        <div class="w-full h-full bg-slate-950 rounded-[11px] flex items-center justify-center">
                            <i class="fa-solid fa-chart-line text-transparent bg-clip-text bg-gradient-to-r from-brand-400 to-cyanAccent-400 text-xl"></i>
                        </div>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-2xl font-extrabold tracking-tight text-white flex items-center gap-1.5">
                            Market<span class="text-brand-400">Link</span>
                        </span>
                        <span class="text-[10px] text-slate-400 font-medium tracking-wide">إدارة وكالات التسويق</span>
                    </div>
                </a>

                <!-- Desktop Nav Links -->
                <div class="hidden md:flex items-center gap-1 bg-slate-900/60 p-1.5 rounded-full border border-slate-800/80 backdrop-blur-md">
                    <a href="#features" class="text-slate-300 hover:text-white px-4 py-2 rounded-full text-sm font-medium transition-colors hover:bg-slate-800/60">المميزات</a>
                    <a href="#ai-tools" class="text-slate-300 hover:text-white px-4 py-2 rounded-full text-sm font-medium transition-colors hover:bg-slate-800/60 flex items-center gap-1.5">
                        <i class="fa-solid fa-wand-magic-sparkles text-violet-400 text-xs"></i>
                        الذكاء الاصطناعي
                    </a>
                    <a href="#pricing" class="text-slate-300 hover:text-white px-4 py-2 rounded-full text-sm font-medium transition-colors hover:bg-slate-800/60">الأسعار</a>
                    <a href="#faq" class="text-slate-300 hover:text-white px-4 py-2 rounded-full text-sm font-medium transition-colors hover:bg-slate-800/60">الأسئلة الشائعة</a>
                    <a href="{{ route('content-creation.index') }}" class="text-slate-300 hover:text-white px-4 py-2 rounded-full text-sm font-medium transition-colors hover:bg-slate-800/60">صناعة المحتوى</a>
                </div>

                <!-- Auth Buttons -->
                <div class="hidden md:flex items-center gap-3">
                    <a href="{{ route('login') }}" class="text-slate-300 hover:text-white px-4 py-2.5 rounded-xl text-sm font-semibold transition-colors hover:bg-slate-800/50 flex items-center gap-2">
                        <i class="fa-solid fa-right-to-bracket text-xs text-slate-400"></i>
                        تسجيل الدخول
                    </a>
                    <a href="{{ route('register') }}" class="relative group overflow-hidden rounded-xl p-[1px]">
                        <span class="absolute inset-0 bg-gradient-to-r from-brand-500 via-violetAccent-600 to-cyanAccent-500 group-hover:opacity-90 transition-opacity"></span>
                        <span class="relative block px-5 py-2.5 bg-slate-950 rounded-[11px] text-sm font-bold text-white group-hover:bg-opacity-0 transition-all">
                            ابدأ تجربتك المجانية
                        </span>
                    </a>
                </div>

                <!-- Mobile Menu Button -->
                <div class="md:hidden">
                    <button id="mobile-menu-btn" class="p-2.5 rounded-xl bg-slate-900 border border-slate-800 text-slate-300 hover:text-white">
                        <i class="fa-solid fa-bars text-lg"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Menu Modal -->
        <div id="mobile-menu" class="hidden md:hidden bg-slate-950/95 backdrop-blur-2xl border-b border-slate-800/80 px-4 pt-3 pb-6 space-y-3">
            <a href="#features" class="block text-slate-200 hover:text-brand-400 px-4 py-2.5 rounded-xl font-medium transition-colors hover:bg-slate-900">المميزات</a>
            <a href="#ai-tools" class="block text-slate-200 hover:text-violet-400 px-4 py-2.5 rounded-xl font-medium transition-colors hover:bg-slate-900">الذكاء الاصطناعي ✨</a>
            <a href="#pricing" class="block text-slate-200 hover:text-brand-400 px-4 py-2.5 rounded-xl font-medium transition-colors hover:bg-slate-900">الأسعار</a>
            <a href="#faq" class="block text-slate-200 hover:text-brand-400 px-4 py-2.5 rounded-xl font-medium transition-colors hover:bg-slate-900">الأسئلة الشائعة</a>
            <a href="{{ route('content-creation.index') }}" class="block text-slate-200 hover:text-cyanAccent-400 px-4 py-2.5 rounded-xl font-medium transition-colors hover:bg-slate-900">صناعة المحتوى</a>
            <div class="pt-3 border-t border-slate-800/80 flex flex-col gap-2">
                <a href="{{ route('login') }}" class="w-full text-center py-3 rounded-xl bg-slate-900 text-white font-semibold border border-slate-800">تسجيل الدخول</a>
                <a href="{{ route('register') }}" class="w-full text-center py-3 rounded-xl bg-gradient-to-r from-brand-600 to-violetAccent-600 text-white font-bold shadow-lg shadow-brand-600/30">ابدأ الآن مجاناً</a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="relative pt-36 pb-20 md:pt-48 md:pb-32 overflow-hidden z-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Top Announcement Pill -->
            <div class="flex justify-center mb-8">
                <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full glass-card border border-indigo-500/30 text-xs font-semibold text-indigo-300 hover:border-indigo-500/60 transition-all cursor-pointer shadow-lg shadow-indigo-500/10">
                    <span class="flex h-2 w-2 relative">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-cyan-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-cyan-500"></span>
                    </span>
                    <span>الجيل الجديد من إدارة وكالات التسويق الرقمي</span>
                    <span class="bg-indigo-500/20 text-indigo-200 px-2 py-0.5 rounded-full text-[10px] font-bold">V2.0 AI</span>
                    <i class="fa-solid fa-arrow-left text-[10px]"></i>
                </div>
            </div>

            <!-- Main Heading -->
            <div class="text-center max-w-4xl mx-auto space-y-6">
                <h1 class="text-4xl sm:text-6xl lg:text-7xl font-extrabold leading-[1.15] tracking-tight">
                    أدِر وكالتك التسويقية بذكاء... 
                    <span class="block mt-2 text-gradient-primary">من العملاء وحتى صناعة المحتوى</span>
                </h1>
                
                <p class="text-lg sm:text-xl text-slate-400 max-w-2xl mx-auto leading-relaxed font-normal">
                    منظومة ERP سحابية متكاملة تمنح شركات التسويق التحكم الكامل في العملاء، والمشاريع، ومتابعة المهام اليومية، والتقارير المالية، مع أدوات الذكاء الاصطناعي الإبداعية.
                </p>

                <!-- Action CTAs -->
                <div class="flex flex-col sm:flex-row items-center justify-center gap-4 pt-4">
                    <a href="{{ route('register') }}" class="w-full sm:w-auto px-8 py-4 rounded-2xl bg-gradient-to-r from-brand-600 via-violetAccent-600 to-indigo-600 hover:from-brand-500 hover:to-indigo-500 text-white font-bold text-lg shadow-xl shadow-brand-600/30 hover:shadow-brand-600/50 transition-all transform hover:-translate-y-0.5 flex items-center justify-center gap-3 group">
                        <span>تجربة مجانية لمدة 14 يوماً</span>
                        <i class="fa-solid fa-rocket text-sm group-hover:translate-x-1 transition-transform"></i>
                    </a>
                    
                    <a href="{{ route('login') }}" class="w-full sm:w-auto px-8 py-4 rounded-2xl glass-card hover:bg-slate-800/80 text-slate-200 hover:text-white font-bold text-lg border border-slate-700/80 transition-all flex items-center justify-center gap-3">
                        <i class="fa-solid fa-laptop-code text-brand-400"></i>
                        <span>دخول النظام</span>
                    </a>
                </div>

                <!-- Trust Indicators -->
                <div class="pt-6 flex flex-wrap items-center justify-center gap-6 text-xs font-medium text-slate-400">
                    <span class="flex items-center gap-1.5"><i class="fa-solid fa-circle-check text-emerald-400"></i> بدون بطاقة ائتمان</span>
                    <span class="flex items-center gap-1.5"><i class="fa-solid fa-circle-check text-emerald-400"></i> إعداد فوري خلال دقيقتين</span>
                    <span class="flex items-center gap-1.5"><i class="fa-solid fa-circle-check text-emerald-400"></i> دعم فني متاح 24/7</span>
                </div>
            </div>

            <!-- Hero SaaS Interactive Mockup Card -->
            <div class="mt-16 max-w-6xl mx-auto relative group">
                <!-- Glowing halo behind mockup -->
                <div class="absolute -inset-1 bg-gradient-to-r from-brand-500 via-violet-600 to-cyan-500 rounded-3xl blur-2xl opacity-30 group-hover:opacity-50 transition duration-1000"></div>

                <div class="relative rounded-2xl glass-card border border-slate-700/80 overflow-hidden shadow-2xl">
                    <!-- Browser Window Header -->
                    <div class="px-5 py-3.5 bg-slate-950/80 border-b border-slate-800/80 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full bg-rose-500/80 inline-block"></span>
                            <span class="w-3 h-3 rounded-full bg-amber-500/80 inline-block"></span>
                            <span class="w-3 h-3 rounded-full bg-emerald-500/80 inline-block"></span>
                            <span class="mr-4 text-xs font-mono text-slate-400 flex items-center gap-2">
                                <i class="fa-solid fa-lock text-[10px] text-emerald-400"></i>
                                https://app.marketlink.app/dashboard
                            </span>
                        </div>
                        <div class="flex items-center gap-3 text-xs text-slate-400">
                            <span class="bg-indigo-500/20 text-indigo-300 px-2 py-0.5 rounded text-[11px] font-semibold">مباشر • لوحة الإدارة</span>
                        </div>
                    </div>

                    <!-- Mockup Dashboard Layout -->
                    <div class="p-6 md:p-8 bg-slate-950/60 grid grid-cols-1 md:grid-cols-4 gap-6">
                        
                        <!-- Metric Card 1 -->
                        <div class="glass-card p-5 rounded-xl border border-slate-800 flex items-center gap-4">
                            <div class="w-12 h-12 rounded-xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-400 text-xl">
                                <i class="fa-solid fa-sack-dollar"></i>
                            </div>
                            <div>
                                <p class="text-xs text-slate-400 font-medium">إجمالي الأرباح</p>
                                <h3 class="text-xl font-bold text-white mt-0.5">148,500 <span class="text-xs text-emerald-400">ج.م</span></h3>
                                <span class="text-[10px] text-emerald-400 flex items-center gap-1 font-semibold mt-1">
                                    <i class="fa-solid fa-arrow-trend-up"></i> +24% هذا الشهر
                                </span>
                            </div>
                        </div>

                        <!-- Metric Card 2 -->
                        <div class="glass-card p-5 rounded-xl border border-slate-800 flex items-center gap-4">
                            <div class="w-12 h-12 rounded-xl bg-indigo-500/10 border border-indigo-500/20 flex items-center justify-center text-indigo-400 text-xl">
                                <i class="fa-solid fa-diagram-project"></i>
                            </div>
                            <div>
                                <p class="text-xs text-slate-400 font-medium">المشاريع النشطة</p>
                                <h3 class="text-xl font-bold text-white mt-0.5">34 <span class="text-xs text-slate-400">حملة</span></h3>
                                <span class="text-[10px] text-indigo-300 flex items-center gap-1 font-semibold mt-1">
                                    <i class="fa-solid fa-spinner animate-spin text-[9px]"></i> 12 قيد المراجعة
                                </span>
                            </div>
                        </div>

                        <!-- Metric Card 3 -->
                        <div class="glass-card p-5 rounded-xl border border-slate-800 flex items-center gap-4">
                            <div class="w-12 h-12 rounded-xl bg-violet-500/10 border border-violet-500/20 flex items-center justify-center text-violet-400 text-xl">
                                <i class="fa-solid fa-wand-magic-sparkles"></i>
                            </div>
                            <div>
                                <p class="text-xs text-slate-400 font-medium">صناعة المحتوى بالذكاء</p>
                                <h3 class="text-xl font-bold text-white mt-0.5">1,420 <span class="text-xs text-slate-400">بوست</span></h3>
                                <span class="text-[10px] text-violet-400 flex items-center gap-1 font-semibold mt-1">
                                    <i class="fa-solid fa-bolt"></i> توفير 80% من الوقت
                                </span>
                            </div>
                        </div>

                        <!-- Metric Card 4 -->
                        <div class="glass-card p-5 rounded-xl border border-slate-800 flex items-center gap-4">
                            <div class="w-12 h-12 rounded-xl bg-cyan-500/10 border border-cyan-500/20 flex items-center justify-center text-cyan-400 text-xl">
                                <i class="fa-solid fa-user-group"></i>
                            </div>
                            <div>
                                <p class="text-xs text-slate-400 font-medium">إنتاجية الفريق</p>
                                <h3 class="text-xl font-bold text-white mt-0.5">98.4%</h3>
                                <span class="text-[10px] text-cyan-400 flex items-center gap-1 font-semibold mt-1">
                                    <i class="fa-solid fa-circle-check"></i> 18 موظف نشط
                                </span>
                            </div>
                        </div>

                        <!-- Inner Workspace Preview -->
                        <div class="md:col-span-3 glass-card p-5 rounded-xl border border-slate-800">
                            <div class="flex items-center justify-between mb-4 border-b border-slate-800/80 pb-3">
                                <h4 class="text-sm font-bold text-white flex items-center gap-2">
                                    <i class="fa-solid fa-list-check text-brand-400"></i>
                                    متابعة مهام مشاريع التسويق (Kanban Board)
                                </h4>
                                <span class="text-xs text-slate-400">مشروع: شركة الأمل للتطوير العقاري</span>
                            </div>
                            <div class="grid grid-cols-3 gap-3">
                                <div class="bg-slate-900/80 p-3 rounded-lg border border-slate-800 space-y-2">
                                    <div class="flex justify-between items-center text-xs font-semibold text-amber-400">
                                        <span>قيد التنفيذ (4)</span>
                                        <span class="w-2 h-2 rounded-full bg-amber-400"></span>
                                    </div>
                                    <div class="bg-slate-950 p-2.5 rounded border border-slate-800 text-xs space-y-1">
                                        <p class="font-medium text-slate-200">كتابة محتوى 10 منشورات فيسبوك</p>
                                        <div class="flex justify-between text-[10px] text-slate-400 pt-1">
                                            <span>أحمد - كاتب محتوى</span>
                                            <span class="text-amber-400 font-bold">اليوم</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="bg-slate-900/80 p-3 rounded-lg border border-slate-800 space-y-2">
                                    <div class="flex justify-between items-center text-xs font-semibold text-cyan-400">
                                        <span>مراجعة العميل (2)</span>
                                        <span class="w-2 h-2 rounded-full bg-cyan-400"></span>
                                    </div>
                                    <div class="bg-slate-950 p-2.5 rounded border border-slate-800 text-xs space-y-1">
                                        <p class="font-medium text-slate-200">تصاميم موشن جرافيك لإعلان انستجرام</p>
                                        <div class="flex justify-between text-[10px] text-slate-400 pt-1">
                                            <span>سارة - مصممة</span>
                                            <span class="text-cyan-400 font-bold">رابط مشاركة</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="bg-slate-900/80 p-3 rounded-lg border border-slate-800 space-y-2">
                                    <div class="flex justify-between items-center text-xs font-semibold text-emerald-400">
                                        <span>تم الاعتماد (12)</span>
                                        <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                                    </div>
                                    <div class="bg-slate-950 p-2.5 rounded border border-slate-800 text-xs space-y-1">
                                        <p class="font-medium text-slate-200">خطة محتوى شهر أغسطس الكاملة</p>
                                        <div class="flex justify-between text-[10px] text-slate-400 pt-1">
                                            <span>مـحـمـد - مـديـر الحساب</span>
                                            <span class="text-emerald-400 font-bold">جاهز للنشر</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Side Widget Preview -->
                        <div class="glass-card p-5 rounded-xl border border-slate-800 flex flex-col justify-between">
                            <div class="space-y-3">
                                <div class="flex items-center justify-between text-xs text-slate-300 font-bold">
                                    <span>مساعد الذكاء الاصطناعي</span>
                                    <i class="fa-solid fa-sparkles text-violet-400"></i>
                                </div>
                                <div class="bg-slate-900 p-3 rounded-lg border border-violet-500/20 text-xs text-slate-300 space-y-2">
                                    <p class="text-[11px] text-slate-400">الاقتراح:</p>
                                    <p class="font-medium text-violet-200">"أفكار منشورات تفاعلية لزيادة التفاعل على انستجرام 🚀"</p>
                                </div>
                            </div>
                            <a href="{{ route('content-creation.index') }}" class="w-full py-2 bg-violet-600/20 hover:bg-violet-600/30 text-violet-300 text-xs font-bold rounded-lg text-center border border-violet-500/30 transition-colors">
                                جرب توليد المحتوى
                            </a>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </section>

    <!-- Stats & Social Proof Section -->
    <section class="py-12 border-y border-slate-800/80 bg-slate-950/60 relative z-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
                <div class="space-y-1">
                    <h3 class="text-3xl sm:text-4xl font-extrabold text-white tracking-tight">+500</h3>
                    <p class="text-sm font-medium text-slate-400">شركة ووكالة تسويق رقمي</p>
                </div>
                <div class="space-y-1">
                    <h3 class="text-3xl sm:text-4xl font-extrabold text-gradient-primary tracking-tight">+150,000</h3>
                    <p class="text-sm font-medium text-slate-400">مهمة وحملة منجزة</p>
                </div>
                <div class="space-y-1">
                    <h3 class="text-3xl sm:text-4xl font-extrabold text-white tracking-tight">99.9%</h3>
                    <p class="text-sm font-medium text-slate-400">جاهزية واستقرار الخدمة</p>
                </div>
                <div class="space-y-1">
                    <h3 class="text-3xl sm:text-4xl font-extrabold text-cyanAccent-400 tracking-tight">4.9 / 5</h3>
                    <p class="text-sm font-medium text-slate-400">تقييم مـديـري الوكالات</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Core Features Section -->
    <section id="features" class="py-24 relative z-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="text-center max-w-3xl mx-auto space-y-4 mb-16">
                <span class="px-3.5 py-1.5 rounded-full bg-brand-500/10 border border-brand-500/20 text-brand-400 text-xs font-bold uppercase tracking-wider">
                    مميزات استثنائية
                </span>
                <h2 class="text-3xl sm:text-5xl font-extrabold text-white">
                    كل ما تحتاجه لإدارة وكالة تسويق ناجحة
                </h2>
                <p class="text-slate-400 text-base sm:text-lg">
                    صممت منصة MarketLink خصيصاً لتلبية التحديات اليومية لشركات التسويق الرقمي وإدارة الفريلانسينج.
                </p>
            </div>

            <!-- Features Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">

                <!-- Feature Card 1 -->
                <div class="glass-card glass-card-hover p-8 rounded-2xl relative overflow-hidden group">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-brand-600 to-indigo-600 flex items-center justify-center text-white text-2xl mb-6 shadow-lg shadow-brand-500/20">
                        <i class="fa-solid fa-users-gear"></i>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-3 group-hover:text-brand-400 transition-colors">إدارة العملاء والـ CRM</h3>
                    <p class="text-slate-400 text-sm leading-relaxed mb-4">
                        سجل كامل لكل عميل يشمل العقود الرسمية، بيانات الاشتراكات، تاريخ الاجتماعات، والملاحظات الهامة في مكان موحد.
                    </p>
                    <span class="text-xs text-brand-400 font-semibold flex items-center gap-1">
                        تتبع العقود والاجتماعات <i class="fa-solid fa-chevron-left text-[10px]"></i>
                    </span>
                </div>

                <!-- Feature Card 2 -->
                <div class="glass-card glass-card-hover p-8 rounded-2xl relative overflow-hidden group">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-violet-600 to-purple-600 flex items-center justify-center text-white text-2xl mb-6 shadow-lg shadow-violet-500/20">
                        <i class="fa-solid fa-diagram-next"></i>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-3 group-hover:text-violet-400 transition-colors">إدارة المشاريع والخطط الشهرية</h3>
                    <p class="text-slate-400 text-sm leading-relaxed mb-4">
                        أنشئ الخطط الشهرية، ووزّع المهام على المصممين، كتاب المحتوى، والمموليين مع تحديد المواعيد النهائية بدقة.
                    </p>
                    <span class="text-xs text-violet-400 font-semibold flex items-center gap-1">
                        جدولة المنشورات والمهام <i class="fa-solid fa-chevron-left text-[10px]"></i>
                    </span>
                </div>

                <!-- Feature Card 3 -->
                <div class="glass-card glass-card-hover p-8 rounded-2xl relative overflow-hidden group">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-cyan-500 to-blue-600 flex items-center justify-center text-white text-2xl mb-6 shadow-lg shadow-cyan-500/20">
                        <i class="fa-solid fa-wand-magic-sparkles"></i>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-3 group-hover:text-cyanAccent-400 transition-colors">صناعة المحتوى بالذكاء الاصطناعي</h3>
                    <p class="text-slate-400 text-sm leading-relaxed mb-4">
                        أدوات متطورة لكتابة المحتوى الإبداعي، توليد الصور، اقتراح أفكار الحملات واستخراج هوية البراند أوتوماتيكياً.
                    </p>
                    <span class="text-xs text-cyanAccent-400 font-semibold flex items-center gap-1">
                        توليد إعلانات وبوستات <i class="fa-solid fa-chevron-left text-[10px]"></i>
                    </span>
                </div>

                <!-- Feature Card 4 -->
                <div class="glass-card glass-card-hover p-8 rounded-2xl relative overflow-hidden group">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-emerald-500 to-teal-600 flex items-center justify-center text-white text-2xl mb-6 shadow-lg shadow-emerald-500/20">
                        <i class="fa-solid fa-file-invoice-dollar"></i>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-3 group-hover:text-emerald-400 transition-colors">التقارير والإدارة المالية</h3>
                    <p class="text-slate-400 text-sm leading-relaxed mb-4">
                        تتبع دقيق للإيرادات، المصروفات، صافي أرباح كل مشروع، ومسحوبات وسلف الموظفين مع إمكانية التصدير.
                    </p>
                    <span class="text-xs text-emerald-400 font-semibold flex items-center gap-1">
                        تقارير الأرباح والمستحقات <i class="fa-solid fa-chevron-left text-[10px]"></i>
                    </span>
                </div>

                <!-- Feature Card 5 -->
                <div class="glass-card glass-card-hover p-8 rounded-2xl relative overflow-hidden group">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-amber-500 to-orange-600 flex items-center justify-center text-white text-2xl mb-6 shadow-lg shadow-amber-500/20">
                        <i class="fa-solid fa-laptop-house"></i>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-3 group-hover:text-amber-400 transition-colors">مساحة عمل فريق العمل (Work Hub)</h3>
                    <p class="text-slate-400 text-sm leading-relaxed mb-4">
                        واجهة مخصصة لكل موظف لاستعراض مهامه الخاصة، رفع الملفات والتصاميم، وتحديث حالة التنفيذ بسهولة.
                    </p>
                    <span class="text-xs text-amber-400 font-semibold flex items-center gap-1">
                        لوحة تحكم الموظفين <i class="fa-solid fa-chevron-left text-[10px]"></i>
                    </span>
                </div>

                <!-- Feature Card 6 -->
                <div class="glass-card glass-card-hover p-8 rounded-2xl relative overflow-hidden group">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-rose-500 to-pink-600 flex items-center justify-center text-white text-2xl mb-6 shadow-lg shadow-rose-500/20">
                        <i class="fa-solid fa-share-nodes"></i>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-3 group-hover:text-rose-400 transition-colors">روابط المشاركة المباشرة مع العملاء</h3>
                    <p class="text-slate-400 text-sm leading-relaxed mb-4">
                        شارك المعاينات والمهام المكتملة مع عملائك عبر روابط عامة آمنة دون الحاجة لإنشاء حسابات لهم.
                    </p>
                    <span class="text-xs text-rose-400 font-semibold flex items-center gap-1">
                        معاينة العميل بدون دخول <i class="fa-solid fa-chevron-left text-[10px]"></i>
                    </span>
                </div>

            </div>

        </div>
    </section>

    <!-- Dedicated AI Content Spotlight Section -->
    <section id="ai-tools" class="py-20 relative z-10 overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="rounded-3xl glass-card border border-violet-500/30 p-8 md:p-14 relative overflow-hidden bg-gradient-to-br from-slate-950 via-slate-900 to-purple-950/40">
                
                <!-- Background Glowing Light -->
                <div class="absolute -top-20 -left-20 w-80 h-80 bg-violet-600/30 rounded-full blur-[100px] pointer-events-none"></div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center relative z-10">
                    
                    <div class="space-y-6">
                        <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-violet-500/20 border border-violet-500/40 text-violet-300 text-xs font-bold">
                            <i class="fa-solid fa-bolt text-yellow-400"></i> الذكاء الاصطناعي المدمج
                        </div>

                        <h2 class="text-3xl sm:text-4xl font-extrabold text-white leading-tight">
                            صناعة المحتوى وكتابة الإعلانات... <br>
                            <span class="text-gradient-primary">أسرع بـ 10 أضعاف</span>
                        </h2>

                        <p class="text-slate-300 text-sm sm:text-base leading-relaxed">
                            استفد من محرك الذكاء الاصطناعي الخاص بـ MarketLink لتوليد أفكار البوستات، كتابة الكابشن الإعلاني، استخراج ألوان وهوية البراند، وإنشاء الصور الاحترافية مباشرة داخل مساحة العمل.
                        </p>

                        <ul class="space-y-3 text-sm text-slate-300">
                            <li class="flex items-center gap-3">
                                <span class="w-6 h-6 rounded-full bg-violet-500/20 text-violet-400 flex items-center justify-center text-xs"><i class="fa-solid fa-check"></i></span>
                                توليد بوستات وحملات إعلانية متوافقة مع الهوية
                            </li>
                            <li class="flex items-center gap-3">
                                <span class="w-6 h-6 rounded-full bg-violet-500/20 text-violet-400 flex items-center justify-center text-xs"><i class="fa-solid fa-check"></i></span>
                                استخراج Brand Style Extractor تلقائياً من روابط المواقع
                            </li>
                            <li class="flex items-center gap-3">
                                <span class="w-6 h-6 rounded-full bg-violet-500/20 text-violet-400 flex items-center justify-center text-xs"><i class="fa-solid fa-check"></i></span>
                                إنشاء صور ومؤثرات بصرية ملائمة لمنصات التواصل
                            </li>
                        </ul>

                        <div class="pt-4">
                            <a href="{{ route('content-creation.index') }}" class="inline-flex items-center gap-3 px-6 py-3.5 rounded-xl bg-violet-600 hover:bg-violet-500 text-white font-bold text-sm shadow-lg shadow-violet-600/30 transition-all">
                                <span>تجربة أدوات المحتوى الآن</span>
                                <i class="fa-solid fa-arrow-left"></i>
                            </a>
                        </div>
                    </div>

                    <!-- AI Card Demo Widget -->
                    <div class="glass-card p-6 rounded-2xl border border-violet-500/30 space-y-4 bg-slate-950/80">
                        <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                            <div class="flex items-center gap-2 text-xs text-violet-300 font-bold">
                                <i class="fa-solid fa-robot text-violet-400"></i>
                                مُولد المحتوى الذكي
                            </div>
                            <span class="text-[11px] text-emerald-400 bg-emerald-500/10 px-2 py-0.5 rounded font-mono">Status: Ready</span>
                        </div>

                        <div class="space-y-2">
                            <label class="text-xs text-slate-400 font-medium">وصف الفكرة المطلوبة:</label>
                            <div class="p-3 bg-slate-900 rounded-xl border border-slate-800 text-xs text-slate-200">
                                "إعلان عن خصم 30% على خدمات التسويق لشركات العقارات بمناسبة الصيف"
                            </div>
                        </div>

                        <div class="p-4 bg-violet-950/40 rounded-xl border border-violet-500/30 space-y-2">
                            <div class="flex justify-between items-center text-xs font-bold text-violet-300">
                                <span>النتيجة المقترحة:</span>
                                <span class="text-[10px] text-slate-400">مكتوب بواسطة AI</span>
                            </div>
                            <p class="text-xs text-slate-200 leading-relaxed">
                                🚀 "صيفك معنا أرباح أكتر! احصل على خصم حصري 30% على باقات التسويق العقاري المتكاملة وادبل مبيعات مشاريعك الآن. 🏢✨ #تسويق_عقاري #MarketLink"
                            </p>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </section>

    <!-- How It Works Section -->
    <section class="py-20 relative z-10 bg-slate-950/40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="text-center max-w-2xl mx-auto space-y-3 mb-16">
                <h2 class="text-3xl sm:text-4xl font-extrabold text-white">كيف تبدأ مع MarketLink؟</h2>
                <p class="text-slate-400 text-sm sm:text-base">ثلاث خطوات بسيطة لنقل إدارة وكالتك إلى المستوى التالي</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 relative">
                
                <!-- Step 1 -->
                <div class="glass-card p-8 rounded-2xl text-center space-y-4 border border-slate-800 relative">
                    <div class="w-16 h-16 rounded-2xl bg-brand-600/20 text-brand-400 border border-brand-500/30 flex items-center justify-center mx-auto text-2xl font-black">
                        1
                    </div>
                    <h3 class="text-lg font-bold text-white">أنشئ حسابك المجاني</h3>
                    <p class="text-slate-400 text-sm leading-relaxed">
                        سجل بيانات وكالتك في أقل من دقيقتين واستمتع بتجربة مجانية كاملة لمدة 14 يوماً بدون أي رسوم.
                    </p>
                </div>

                <!-- Step 2 -->
                <div class="glass-card p-8 rounded-2xl text-center space-y-4 border border-slate-800 relative">
                    <div class="w-16 h-16 rounded-2xl bg-violet-600/20 text-violet-400 border border-violet-500/30 flex items-center justify-center mx-auto text-2xl font-black">
                        2
                    </div>
                    <h3 class="text-lg font-bold text-white">أضف عملائك وفريقك</h3>
                    <p class="text-slate-400 text-sm leading-relaxed">
                        ادعُ الموظفين والفرينلاسرز للأنضمام لمساحة العمل، وأنشئ مشاريع العملاء والخطط الشهرية بسهولة.
                    </p>
                </div>

                <!-- Step 3 -->
                <div class="glass-card p-8 rounded-2xl text-center space-y-4 border border-slate-800 relative">
                    <div class="w-16 h-16 rounded-2xl bg-cyan-600/20 text-cyan-400 border border-cyan-500/30 flex items-center justify-center mx-auto text-2xl font-black">
                        3
                    </div>
                    <h3 class="text-lg font-bold text-white">انطلق وتابع الأرباح</h3>
                    <p class="text-slate-400 text-sm leading-relaxed">
                        تابع تنفيذ المهام أوتوماتيكياً، واستخرج التقارير المالية لزيادة ربحية وكالتك وتنظيم العمل.
                    </p>
                </div>

            </div>

        </div>
    </section>

    <!-- Pricing Section -->
    <section id="pricing" class="py-24 relative z-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="text-center max-w-3xl mx-auto space-y-4 mb-16">
                <span class="px-3.5 py-1.5 rounded-full bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 text-xs font-bold uppercase tracking-wider">
                    خطط مرنة تناسب الجميع
                </span>
                <h2 class="text-3xl sm:text-5xl font-extrabold text-white">خطط الاشتراك والأسعار</h2>
                <p class="text-slate-400 text-base sm:text-lg">اختر الخطة المناسبة لحجم وكالتك. جميع الخطط تشمل 14 يوماً تجربة مجانية.</p>
            </div>

            <!-- Dynamic Pricing Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-6xl mx-auto items-stretch">
                @forelse($plans as $planIndex => $plan)
                    @php
                        $isPopular = $planIndex === 1 || ($plans->count() == 1);
                    @endphp
                    <div class="rounded-3xl glass-card p-8 flex flex-col justify-between transition-all duration-300 relative {{ $isPopular ? 'border-2 border-brand-500/80 shadow-2xl shadow-brand-500/20 bg-gradient-to-b from-slate-900/90 via-indigo-950/30 to-slate-950 transform md:-translate-y-3' : 'border-slate-800 hover:border-slate-700' }}">
                        
                        @if($isPopular)
                            <div class="absolute -top-4 left-1/2 transform -translate-x-1/2 bg-gradient-to-r from-brand-500 to-violet-600 text-white px-4 py-1 rounded-full text-xs font-extrabold shadow-lg tracking-wide">
                                الأكثر اختياراً 🌟
                            </div>
                        @endif

                        <div>
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-2xl font-bold text-white">{{ $plan->name }}</h3>
                                @if($plan->duration_days)
                                    <span class="text-xs bg-slate-800 text-slate-300 px-2.5 py-1 rounded-full font-medium">
                                        {{ $plan->duration_days }} يوم
                                    </span>
                                @endif
                            </div>

                            @if($plan->description)
                                <p class="text-xs text-slate-400 mb-6 leading-relaxed">{{ $plan->description }}</p>
                            @endif

                            <div class="mb-8 pb-6 border-b border-slate-800">
                                <div class="flex items-baseline gap-1">
                                    <span class="text-4xl sm:text-5xl font-black text-white tracking-tight">{{ number_format($plan->price_egp, 0) }}</span>
                                    <span class="text-slate-400 font-bold text-sm">جنيه مصري</span>
                                </div>
                                <p class="text-slate-400 text-xs mt-1">تجدد حسب المدة المحددة</p>
                            </div>

                            <!-- Features List -->
                            <div class="space-y-3 mb-8">
                                <p class="text-xs font-bold text-slate-300 uppercase tracking-wider">المميزات المضمنة:</p>
                                @if($plan->features && $plan->features->count() > 0)
                                    @foreach($plan->features as $feature)
                                        <div class="flex items-start gap-3 text-xs text-slate-300">
                                            <i class="fa-solid fa-circle-check text-brand-400 text-sm mt-0.5 shrink-0"></i>
                                            <span>{{ $feature->feature_name }}</span>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="flex items-start gap-3 text-xs text-slate-300">
                                        <i class="fa-solid fa-circle-check text-brand-400 text-sm mt-0.5 shrink-0"></i>
                                        <span>جميع مميزات النظام الأساسية</span>
                                    </div>
                                    <div class="flex items-start gap-3 text-xs text-slate-300">
                                        <i class="fa-solid fa-circle-check text-brand-400 text-sm mt-0.5 shrink-0"></i>
                                        <span>دعم فني وتحديثات مستمرة</span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div>
                            <a href="{{ route('register') }}" class="w-full py-3.5 rounded-xl font-bold text-sm text-center block transition-all shadow-lg {{ $isPopular ? 'bg-gradient-to-r from-brand-600 to-violet-600 hover:from-brand-500 hover:to-violet-500 text-white shadow-brand-500/30' : 'bg-slate-800 hover:bg-slate-700 text-white border border-slate-700' }}">
                                ابدأ التجربة المجانية الان
                            </a>
                        </div>

                    </div>
                @empty
                    <!-- Fallback default plans if db empty -->
                    <div class="col-span-3 text-center py-12 glass-card rounded-2xl border border-slate-800">
                        <p class="text-slate-400 text-base mb-4">يتم تحديث قائمة الخطط والأسعار حالياً...</p>
                        <a href="{{ route('register') }}" class="inline-block px-6 py-3 rounded-xl bg-brand-600 text-white font-bold text-sm">سجل للحصول على العرض المباشر</a>
                    </div>
                @endforelse
            </div>

        </div>
    </section>

    <!-- FAQ Section Accordion -->
    <section id="faq" class="py-20 relative z-10 bg-slate-950/60 border-t border-slate-800/80">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="text-center space-y-3 mb-14">
                <h2 class="text-3xl sm:text-4xl font-extrabold text-white">الأسئلة الشائعة</h2>
                <p class="text-slate-400 text-sm sm:text-base">إجابات لأهم الاستفسارات حول منصة MarketLink</p>
            </div>

            <div class="space-y-4">

                <div class="glass-card rounded-2xl border border-slate-800 overflow-hidden">
                    <button class="w-full p-5 text-right font-bold text-white text-base flex justify-between items-center gap-4 faq-toggle hover:bg-slate-800/40 transition-colors">
                        <span>ما هي فترة التجربة المجانية؟</span>
                        <i class="fa-solid fa-chevron-down text-slate-400 text-xs transition-transform duration-300"></i>
                    </button>
                    <div class="px-5 pb-5 text-sm text-slate-300 hidden leading-relaxed border-t border-slate-800/60 pt-3">
                        تمنحك منصة MarketLink فترة تجريبية مجانية بالكامل لمدة 14 يوماً للوصول لكافة خصائص النظام ومميزاته دون الحاجة لإدخال بيانات بطاقة الائتمان.
                    </div>
                </div>

                <div class="glass-card rounded-2xl border border-slate-800 overflow-hidden">
                    <button class="w-full p-5 text-right font-bold text-white text-base flex justify-between items-center gap-4 faq-toggle hover:bg-slate-800/40 transition-colors">
                        <span>هل يمكنني إضافة أعضاء فريقي وموظفي الشركة؟</span>
                        <i class="fa-solid fa-chevron-down text-slate-400 text-xs transition-transform duration-300"></i>
                    </button>
                    <div class="px-5 pb-5 text-sm text-slate-300 hidden leading-relaxed border-t border-slate-800/60 pt-3">
                        نعم بالتأكيد! يمكنك إضافة موظفيك (مصممين، كتاب محتوى، مديري حسابات)، وتحديد الأدوار والصلاحيات الخاصة بكل موظف ليدخل على لوحة Work Hub المخصصة له.
                    </div>
                </div>

                <div class="glass-card rounded-2xl border border-slate-800 overflow-hidden">
                    <button class="w-full p-5 text-right font-bold text-white text-base flex justify-between items-center gap-4 faq-toggle hover:bg-slate-800/40 transition-colors">
                        <span>هل يحتاج النظام إلى تثبيت برامج أو سيرفرات؟</span>
                        <i class="fa-solid fa-chevron-down text-slate-400 text-xs transition-transform duration-300"></i>
                    </button>
                    <div class="px-5 pb-5 text-sm text-slate-300 hidden leading-relaxed border-t border-slate-800/60 pt-3">
                        لا، MarketLink هي منصة سحابية (SaaS) تعمل مباشرة عبر متصفح الإنترنت من أي جهاز (كمبيوتر، تابلت، أو هاتف محمول) دون الحاجة لتثبيت أي شيء.
                    </div>
                </div>

                <div class="glass-card rounded-2xl border border-slate-800 overflow-hidden">
                    <button class="w-full p-5 text-right font-bold text-white text-base flex justify-between items-center gap-4 faq-toggle hover:bg-slate-800/40 transition-colors">
                        <span>كيف يمكنني مشاركة المخرجات والتصاميم مع عملائي؟</span>
                        <i class="fa-solid fa-chevron-down text-slate-400 text-xs transition-transform duration-300"></i>
                    </button>
                    <div class="px-5 pb-5 text-sm text-slate-300 hidden leading-relaxed border-t border-slate-800/60 pt-3">
                        يوفر النظام رابط مشاركة عام مخصص وآمن لكل مشروع/مهمة، يمكنك إرساله للعميل لمراجعة المنشورات والملفات وإبداء ملاحظاته دون الحاجة لتسجيل دخول.
                    </div>
                </div>

            </div>

        </div>
    </section>

    <!-- Final Call to Action Section -->
    <section class="py-20 relative z-10 overflow-hidden">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="rounded-3xl glass-card border border-brand-500/40 p-10 sm:p-16 text-center space-y-8 relative overflow-hidden bg-gradient-to-r from-brand-900/40 via-purple-950/50 to-slate-950 shadow-2xl">
                
                <div class="absolute inset-0 bg-gradient-to-r from-brand-600/10 to-cyan-600/10 pointer-events-none"></div>

                <h2 class="text-3xl sm:text-5xl font-extrabold text-white tracking-tight relative z-10 leading-tight">
                    جاهز لنقل عملك إلى عصر السرعة والذكاء؟
                </h2>
                
                <p class="text-slate-300 text-base sm:text-lg max-w-2xl mx-auto relative z-10">
                    انضم الآن إلى مئات وكالات التسويق التي تثق بـ MarketLink لتنظيم مشاريعها وتوفير وقت فريقها.
                </p>

                <div class="flex flex-col sm:flex-row items-center justify-center gap-4 relative z-10 pt-2">
                    <a href="{{ route('register') }}" class="w-full sm:w-auto px-9 py-4 rounded-2xl bg-gradient-to-r from-brand-600 to-violet-600 hover:from-brand-500 hover:to-violet-500 text-white font-extrabold text-lg shadow-xl shadow-brand-600/30 transition-all flex items-center justify-center gap-3">
                        <span>ابدأ التجربة المجانية الآن</span>
                        <i class="fa-solid fa-arrow-left"></i>
                    </a>
                </div>

            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-slate-950 border-t border-slate-900 text-slate-400 py-14 relative z-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-10 mb-12">
                
                <!-- Col 1: Brand Info -->
                <div class="space-y-4">
                    <div class="flex items-center gap-2.5">
                        <div class="w-9 h-9 rounded-xl bg-brand-600 flex items-center justify-center text-white text-lg font-bold">
                            <i class="fa-solid fa-chart-line"></i>
                        </div>
                        <span class="text-xl font-extrabold text-white">Market<span class="text-brand-400">Link</span></span>
                    </div>
                    <p class="text-xs text-slate-400 leading-relaxed">
                        المنصة المتكاملة لإدارة شركات التسويق الرقمي، المشاريع، التقارير المالية، وإنشاء المحتوى بالذكاء الاصطناعي.
                    </p>
                </div>

                <!-- Col 2: Navigation Links -->
                <div class="space-y-3">
                    <h4 class="text-sm font-bold text-white">روابط سريعة</h4>
                    <ul class="space-y-2 text-xs">
                        <li><a href="#features" class="hover:text-white transition-colors">المميزات والخصائص</a></li>
                        <li><a href="#ai-tools" class="hover:text-white transition-colors">أدوات الذكاء الاصطناعي</a></li>
                        <li><a href="#pricing" class="hover:text-white transition-colors">الأسعار والخطط</a></li>
                        <li><a href="#faq" class="hover:text-white transition-colors">الأسئلة الشائعة</a></li>
                    </ul>
                </div>

                <!-- Col 3: Portal Links -->
                <div class="space-y-3">
                    <h4 class="text-sm font-bold text-white">بوابات الدخول</h4>
                    <ul class="space-y-2 text-xs">
                        <li><a href="{{ route('login') }}" class="hover:text-white transition-colors">تسجيل دخول المديرين</a></li>
                        <li><a href="{{ route('employee.login') }}" class="hover:text-white transition-colors">بوابة دخول الموظفين</a></li>
                        <li><a href="{{ route('register') }}" class="hover:text-white transition-colors">إنشاء حساب جديد</a></li>
                        <li><a href="{{ route('content-creation.index') }}" class="hover:text-white transition-colors">مُولد المحتوى العام</a></li>
                    </ul>
                </div>

                <!-- Col 4: Contact -->
                <div class="space-y-3">
                    <h4 class="text-sm font-bold text-white">التواصل والدعم</h4>
                    <ul class="space-y-2 text-xs">
                        <li class="flex items-center gap-2">
                            <i class="fa-solid fa-envelope text-brand-400"></i>
                            <span>atpfreelancer@gmail.com</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <i class="fa-solid fa-phone text-brand-400"></i>
                            <span dir="ltr">+20 100 208 9079</span>
                        </li>
                    </ul>
                </div>

            </div>

            <!-- Bottom Copyright Bar -->
            <div class="pt-8 border-t border-slate-900 flex flex-col sm:flex-row items-center justify-between text-xs text-slate-400 gap-4">
                <p>© 2026 MarketLink. جميع الحقوق محفوظة.</p>
                <div class="flex items-center gap-4">
                    <span class="text-slate-400">صنع بشغف لخدمة شركات التسويق العربي 🚀</span>
                </div>
            </div>

        </div>
    </footer>

    <!-- JavaScript for Mobile Menu & FAQ Accordion -->
    <script>
        // Mobile Menu Toggle
        const menuBtn = document.getElementById('mobile-menu-btn');
        const mobileMenu = document.getElementById('mobile-menu');

        menuBtn.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
        });

        // FAQ Accordion Toggle
        const faqToggles = document.querySelectorAll('.faq-toggle');
        faqToggles.forEach(toggle => {
            toggle.addEventListener('click', () => {
                const content = toggle.nextElementSibling;
                const icon = toggle.querySelector('i');
                
                content.classList.toggle('hidden');
                icon.classList.toggle('rotate-180');
            });
        });
    </script>
</body>
</html>
