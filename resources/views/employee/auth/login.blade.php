<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>تسجيل دخول الموظف - MarketLink</title>
    
    <!-- Cairo Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Material Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
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
                    <span class="material-icons text-white text-2xl">person</span>
                </div>
                <h1 class="text-2xl font-bold text-gray-800 mb-2">تسجيل دخول الموظف</h1>
                <p class="text-gray-600 text-sm">MarketLink</p>
            </div>

            <!-- Login Form -->
            <form method="POST" action="{{ route('employee.login.store') }}" class="space-y-6">
                @csrf

                <!-- Email Field -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                        البريد الإلكتروني
                    </label>
                    <div class="relative">
                        <span class="material-icons absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 text-sm">email</span>
                        <input 
                            id="email" 
                            type="email" 
                            name="email" 
                            value="{{ old('email') }}" 
                            required 
                            autofocus 
                            autocomplete="username"
                            class="material-3-input w-full pr-10 pl-4 py-3 rounded-xl text-sm"
                            placeholder="أدخل بريدك الإلكتروني"
                        />
                    </div>
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password Field -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                        كلمة المرور
                    </label>
                    <div class="relative">
                        <span class="material-icons absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 text-sm">lock</span>
                        <input 
                            id="password" 
                            type="password" 
                            name="password" 
                            required 
                            autocomplete="current-password"
                            class="material-3-input w-full pr-10 pl-4 py-3 rounded-xl text-sm"
                            placeholder="أدخل كلمة المرور"
                        />
                    </div>
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Remember Me -->
                <div class="flex items-center">
                    <label for="remember_me" class="flex items-center">
                        <input 
                            id="remember_me" 
                            type="checkbox" 
                            name="remember"
                            class="rounded border-gray-300 text-purple-600 shadow-sm focus:ring-purple-500"
                        />
                        <span class="ml-2 text-sm text-gray-600">تذكرني</span>
                    </label>
                </div>

                <!-- Login Button -->
                <button type="submit" class="material-3-button w-full py-3 px-4 rounded-xl text-white font-medium text-sm flex items-center justify-center">
                    <span class="material-icons text-sm ml-2">login</span>
                    تسجيل الدخول
                </button>
            </form>

            <!-- Footer -->
            <div class="mt-6 text-center pt-6 border-t border-gray-200">
                <a href="{{ route('login') }}" class="text-sm text-gray-600 hover:text-purple-600 transition-colors">
                    تسجيل دخول المدير
                </a>
            </div>
        </div>

        <!-- Footer -->
        <div class="text-center mt-6 space-y-2">
            <p class="text-xs text-white/80">© 2024 MarketLink. جميع الحقوق محفوظة.</p>
        </div>
    </div>

    <!-- SweetAlert Script -->
    <script>
        @if($errors->has('email'))
            @php
                $errorMessage = $errors->first('email');
                $isInactive = str_contains($errorMessage, 'غير نشط');
                $isPending = str_contains($errorMessage, 'انتظار');
                $isThrottle = str_contains(strtolower($errorMessage), 'throttle') || str_contains($errorMessage, 'كثير');
            @endphp
            
            @if($isInactive)
                Swal.fire({
                    icon: 'warning',
                    title: 'حسابك غير نشط',
                    html: '<div class="text-right"><p class="mb-3 font-semibold">{!! e($errorMessage) !!}</p><p class="text-sm text-gray-600 mt-2">يرجى التواصل مع المدير</p></div>',
                    confirmButtonText: 'حسناً',
                    confirmButtonColor: '#f59e0b',
                });
            @elseif($isPending)
                Swal.fire({
                    icon: 'info',
                    title: 'في انتظار الموافقة',
                    html: '<div class="text-right"><p class="mb-3 font-semibold">{!! e($errorMessage) !!}</p><p class="text-sm text-gray-600 mt-2">يرجى التواصل مع المدير</p></div>',
                    confirmButtonText: 'حسناً',
                    confirmButtonColor: '#3b82f6',
                });
            @elseif($isThrottle)
                Swal.fire({
                    icon: 'warning',
                    title: 'محاولات كثيرة',
                    text: {!! json_encode($errorMessage, JSON_UNESCAPED_UNICODE) !!},
                    confirmButtonText: 'حسناً',
                    confirmButtonColor: '#f59e0b',
                });
            @else
                Swal.fire({
                    icon: 'error',
                    title: 'خطأ في تسجيل الدخول',
                    text: {!! json_encode($errorMessage, JSON_UNESCAPED_UNICODE) !!},
                    confirmButtonText: 'حسناً',
                    confirmButtonColor: '#ef4444',
                });
            @endif
        @endif
    </script>
</body>
</html>

