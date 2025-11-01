<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>MarketLink - إنشاء حساب جديد</title>
    
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
        
        .material-3-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 
                0 8px 32px rgba(0, 0, 0, 0.1),
                0 1px 2px rgba(0, 0, 0, 0.05);
        }
        
        .material-3-button {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 4px 14px 0 rgba(102, 126, 234, 0.4);
        }
        
        .material-3-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px 0 rgba(102, 126, 234, 0.6);
        }
        
        .material-3-input {
            background: rgba(255, 255, 255, 0.9);
            border: 2px solid rgba(102, 126, 234, 0.1);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .material-3-input:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
            background: rgba(255, 255, 255, 1);
        }
        
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .floating-shapes {
            position: absolute;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: 0;
        }
        
        .shape {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            animation: float 6s ease-in-out infinite;
        }
        
        .shape:nth-child(1) {
            width: 80px;
            height: 80px;
            top: 20%;
            left: 10%;
            animation-delay: 0s;
        }
        
        .shape:nth-child(2) {
            width: 120px;
            height: 120px;
            top: 60%;
            right: 10%;
            animation-delay: 2s;
        }
        
        .shape:nth-child(3) {
            width: 60px;
            height: 60px;
            top: 80%;
            left: 20%;
            animation-delay: 4s;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(180deg); }
        }
        
        .glass-effect {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
    </style>
</head>
<body class="gradient-bg min-h-screen flex items-center justify-center p-4">
    <!-- Floating Shapes -->
    <div class="floating-shapes">
        <div class="shape"></div>
        <div class="shape"></div>
        <div class="shape"></div>
    </div>
    
    <div class="relative z-10 w-full max-w-md">
        <!-- Main Card -->
        <div class="material-3-card rounded-3xl p-8">
            <!-- Header -->
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-r from-purple-500 to-blue-500 rounded-2xl mb-4">
                    <span class="material-icons text-white text-2xl">person_add</span>
                </div>
                <h1 class="text-2xl font-bold text-gray-800 mb-2">إنشاء حساب جديد</h1>
                <p class="text-gray-600 text-sm">ابدأ رحلتك مع MarketLink - تجربة مجانية 14 يوم</p>
            </div>

            <!-- Trial Info Card -->
            <div class="glass-effect rounded-2xl p-4 mb-6">
                <div class="flex items-center mb-2">
                    <span class="material-icons text-blue-600 text-sm ml-2">timer</span>
                    <h3 class="text-sm font-semibold text-blue-800">تجربة مجانية</h3>
                </div>
                <div class="text-xs text-blue-700">
                    <p>احصل على 14 يوم تجربة مجانية كاملة للتعرف على جميع الميزات</p>
                </div>
            </div>

            <!-- Errors -->
            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 rounded-xl p-3 mb-4">
                    <div class="flex items-start">
                        <span class="material-icons text-red-600 text-sm ml-2">error</span>
                        <div class="text-sm text-red-700">
                            <ul class="list-disc list-inside space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Register Form -->
            <form method="POST" action="{{ route('register') }}" class="space-y-5">
                @csrf

                <!-- Name Field -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        الاسم الكامل <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="material-icons absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 text-sm">person</span>
                        <input 
                            id="name" 
                            type="text" 
                            name="name" 
                            value="{{ old('name') }}" 
                            required
                            autofocus
                            class="material-3-input w-full pr-10 pl-4 py-3 rounded-xl text-sm"
                            placeholder="أدخل اسمك الكامل"
                        />
                    </div>
                    @error('name')
                        <p class="mt-1 text-xs text-red-600 flex items-center">
                            <span class="material-icons text-xs ml-1">error</span>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Email Field -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                        البريد الإلكتروني <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="material-icons absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 text-sm">email</span>
                        <input 
                            id="email" 
                            type="email" 
                            name="email" 
                            value="{{ old('email') }}" 
                            required 
                            autocomplete="username"
                            class="material-3-input w-full pr-10 pl-4 py-3 rounded-xl text-sm"
                            placeholder="أدخل بريدك الإلكتروني"
                        />
                    </div>
                    @error('email')
                        <p class="mt-1 text-xs text-red-600 flex items-center">
                            <span class="material-icons text-xs ml-1">error</span>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Password Field -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                        كلمة المرور <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="material-icons absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 text-sm">lock</span>
                        <input 
                            id="password" 
                            type="password" 
                            name="password" 
                            required 
                            autocomplete="new-password"
                            class="material-3-input w-full pr-10 pl-4 py-3 rounded-xl text-sm"
                            placeholder="أدخل كلمة المرور"
                        />
                    </div>
                    @error('password')
                        <p class="mt-1 text-xs text-red-600 flex items-center">
                            <span class="material-icons text-xs ml-1">error</span>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Password Confirmation Field -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                        تأكيد كلمة المرور <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="material-icons absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 text-sm">lock</span>
                        <input 
                            id="password_confirmation" 
                            type="password" 
                            name="password_confirmation" 
                            required 
                            autocomplete="new-password"
                            class="material-3-input w-full pr-10 pl-4 py-3 rounded-xl text-sm"
                            placeholder="أعد إدخال كلمة المرور"
                        />
                    </div>
                </div>

                <!-- Register Button -->
                <button type="submit" class="material-3-button w-full py-3 px-4 rounded-xl text-white font-medium text-sm flex items-center justify-center mt-6">
                    <span class="material-icons text-sm ml-2">person_add</span>
                    إنشاء الحساب
                </button>
            </form>

            <!-- Login Link -->
            <div class="mt-6 text-center">
                <p class="text-sm text-gray-600">
                    لديك حساب بالفعل؟ 
                    <a href="{{ route('login') }}" class="text-purple-600 hover:text-purple-700 font-medium">
                        تسجيل الدخول
                    </a>
                </p>
            </div>
        </div>

        <!-- Footer -->
        <div class="text-center mt-6 space-y-2">
            <a href="{{ route('welcome') }}" class="text-white/80 hover:text-white text-xs block transition-colors">
                ← العودة للصفحة الرئيسية
            </a>
            <p class="text-xs text-white/80">© 2024 MarketLink. جميع الحقوق محفوظة.</p>
        </div>
    </div>
</body>
</html>

