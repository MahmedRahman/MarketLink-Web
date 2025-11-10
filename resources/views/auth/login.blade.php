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
                    <span class="material-icons text-white text-2xl">business</span>
                </div>
                <h1 class="text-2xl font-bold text-gray-800 mb-2">MarketLink</h1>
                <p class="text-gray-600 text-sm">نظام إدارة شركات التسويق الإلكتروني</p>
            </div>

            <!-- Login Form -->
            <form method="POST" action="{{ route('login') }}" class="space-y-6">
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
                            class="material-3-input w-full pr-10 pl-10 py-3 rounded-xl text-sm"
                            placeholder="أدخل كلمة المرور"
                        />
                        <button 
                            type="button" 
                            id="togglePassword" 
                            class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600 focus:outline-none transition-colors"
                            onclick="togglePasswordVisibility()"
                        >
                            <span class="material-icons text-sm" id="passwordIcon">visibility</span>
                        </button>
                    </div>
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
                        <span class="mr-2 text-sm text-gray-600">تذكرني</span>
                    </label>
                </div>

                <!-- Login Button -->
                <button type="submit" class="material-3-button w-full py-3 px-4 rounded-xl text-white font-medium text-sm flex items-center justify-center">
                    <span class="material-icons text-sm ml-2">login</span>
                    تسجيل الدخول
                </button>
            </form>

            <!-- Employee Login Link -->
            <div class="mt-6 text-center pt-6 border-t border-gray-200">
                <a href="{{ route('employee.login') }}" class="inline-flex items-center justify-center w-full py-3 px-4 rounded-xl text-sm font-medium text-blue-600 bg-blue-50 hover:bg-blue-100 transition-colors mb-3">
                    <span class="material-icons text-sm ml-2">person</span>
                    تسجيل دخول الموظف
                </a>
            </div>

            <!-- Register Link -->
            <div class="mt-4 text-center pt-4 border-t border-gray-200">
                <p class="text-sm text-gray-600 mb-3">
                    ليس لديك حساب؟
                </p>
                <a href="{{ route('register') }}" class="inline-flex items-center justify-center w-full py-3 px-4 rounded-xl text-sm font-medium text-purple-600 bg-purple-50 hover:bg-purple-100 transition-colors">
                    <span class="material-icons text-sm ml-2">person_add</span>
                    إنشاء حساب جديد
                </a>
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

    <!-- Password Toggle Script -->
    <script>
        function togglePasswordVisibility() {
            const passwordInput = document.getElementById('password');
            const passwordIcon = document.getElementById('passwordIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                passwordIcon.textContent = 'visibility_off';
            } else {
                passwordInput.type = 'password';
                passwordIcon.textContent = 'visibility';
            }
        }
    </script>

    <!-- SweetAlert Script -->
    <script>
        @if(session('status'))
            Swal.fire({
                icon: 'success',
                title: 'نجح',
                text: {!! json_encode(session('status'), JSON_UNESCAPED_UNICODE) !!},
                confirmButtonText: 'حسناً',
                confirmButtonColor: '#667eea',
            });
        @endif

        @if($errors->has('email'))
            @php
                $errorMessage = $errors->first('email');
                $isSuspended = str_contains($errorMessage, 'إيقاف');
                $isInactive = str_contains($errorMessage, 'غير نشط');
                $isThrottle = str_contains(strtolower($errorMessage), 'throttle') || str_contains($errorMessage, 'كثير');
            @endphp
            
            @if($isSuspended)
                Swal.fire({
                    icon: 'error',
                    title: 'حسابك موقوف',
                    html: '<div class="text-right"><p class="mb-3 font-semibold">{!! e($errorMessage) !!}</p><p class="text-sm text-gray-600 mt-2">يرجى التواصل مع الدعم الفني لتفعيل حسابك</p></div>',
                    confirmButtonText: 'حسناً',
                    confirmButtonColor: '#ef4444',
                    showDenyButton: true,
                    denyButtonText: 'العودة للصفحة الرئيسية',
                    denyButtonColor: '#6b7280',
                    footer: '<a href="mailto:support@marketlink.com" style="color: #3b82f6; text-decoration: underline;">تواصل مع الدعم الفني</a>',
                }).then((result) => {
                    if (result.isDenied) {
                        window.location.href = '{{ route("welcome") }}';
                    }
                });
            @elseif($isInactive)
                Swal.fire({
                    icon: 'warning',
                    title: 'حسابك غير نشط',
                    html: '<div class="text-right"><p class="mb-3 font-semibold">{!! e($errorMessage) !!}</p><p class="text-sm text-gray-600 mt-2">يرجى التواصل مع الدعم الفني</p></div>',
                    confirmButtonText: 'حسناً',
                    confirmButtonColor: '#f59e0b',
                    showDenyButton: true,
                    denyButtonText: 'العودة للصفحة الرئيسية',
                    denyButtonColor: '#6b7280',
                    footer: '<a href="mailto:support@marketlink.com" style="color: #3b82f6; text-decoration: underline;">تواصل مع الدعم الفني</a>',
                }).then((result) => {
                    if (result.isDenied) {
                        window.location.href = '{{ route("welcome") }}';
                    }
                });
            @elseif($isThrottle)
                Swal.fire({
                    icon: 'warning',
                    title: 'محاولات كثيرة',
                    text: {!! json_encode($errorMessage, JSON_UNESCAPED_UNICODE) !!},
                    confirmButtonText: 'حسناً',
                    confirmButtonColor: '#f59e0b',
                    showDenyButton: true,
                    denyButtonText: 'العودة للصفحة الرئيسية',
                    denyButtonColor: '#6b7280',
                }).then((result) => {
                    if (result.isDenied) {
                        window.location.href = '{{ route("welcome") }}';
                    }
                });
            @else
                Swal.fire({
                    icon: 'error',
                    title: 'خطأ في تسجيل الدخول',
                    text: {!! json_encode($errorMessage, JSON_UNESCAPED_UNICODE) !!},
                    confirmButtonText: 'حسناً',
                    confirmButtonColor: '#ef4444',
                    showDenyButton: true,
                    denyButtonText: 'العودة للصفحة الرئيسية',
                    denyButtonColor: '#6b7280',
                }).then((result) => {
                    if (result.isDenied) {
                        window.location.href = '{{ route("welcome") }}';
                    }
                });
            @endif
        @endif

        @if($errors->has('password'))
            Swal.fire({
                icon: 'error',
                title: 'خطأ',
                text: {!! json_encode($errors->first('password'), JSON_UNESCAPED_UNICODE) !!},
                confirmButtonText: 'حسناً',
                confirmButtonColor: '#ef4444',
                showDenyButton: true,
                denyButtonText: 'العودة للصفحة الرئيسية',
                denyButtonColor: '#6b7280',
            }).then((result) => {
                if (result.isDenied) {
                    window.location.href = '{{ route("welcome") }}';
                }
            });
        @endif
    </script>
</body>
</html>
