<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'لوحة تحكم المدير') - MarketLink</title>
    
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
        
        .card {
            background: white;
            border-radius: 1rem;
            padding: 1.5rem;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        }
    </style>
</head>
<body class="bg-gray-50">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <div class="w-64 bg-gray-900 text-white">
            <div class="p-6">
                <div class="flex items-center mb-8">
                    <div class="w-10 h-10 bg-gradient-to-r from-purple-500 to-blue-500 rounded-xl flex items-center justify-center">
                        <span class="material-icons">admin_panel_settings</span>
                    </div>
                    <span class="mr-3 text-xl font-bold">لوحة تحكم المدير</span>
                </div>
                
                <nav class="space-y-2">
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center px-4 py-3 rounded-lg hover:bg-gray-800 transition-colors {{ request()->routeIs('admin.dashboard') ? 'bg-gray-800' : '' }}">
                        <span class="material-icons ml-3">dashboard</span>
                        لوحة التحكم
                    </a>
                    <a href="{{ route('admin.users.index') }}" class="flex items-center px-4 py-3 rounded-lg hover:bg-gray-800 transition-colors {{ request()->routeIs('admin.users.*') ? 'bg-gray-800' : '' }}">
                        <span class="material-icons ml-3">people</span>
                        المستخدمين
                    </a>
                    <a href="{{ route('admin.organizations.index') }}" class="flex items-center px-4 py-3 rounded-lg hover:bg-gray-800 transition-colors {{ request()->routeIs('admin.organizations.*') ? 'bg-gray-800' : '' }}">
                        <span class="material-icons ml-3">business</span>
                        المنظمات
                    </a>
                    <a href="{{ route('admin.subscriptions.index') }}" class="flex items-center px-4 py-3 rounded-lg hover:bg-gray-800 transition-colors {{ request()->routeIs('admin.subscriptions.*') ? 'bg-gray-800' : '' }}">
                        <span class="material-icons ml-3">subscriptions</span>
                        الاشتراكات
                    </a>
                    <a href="{{ route('admin.plans.index') }}" class="flex items-center px-4 py-3 rounded-lg hover:bg-gray-800 transition-colors {{ request()->routeIs('admin.plans.*') ? 'bg-gray-800' : '' }}">
                        <span class="material-icons ml-3">card_membership</span>
                        خطط الاشتراك
                    </a>
                    <a href="{{ route('admin.subscription-requests.index') }}" class="flex items-center px-4 py-3 rounded-lg hover:bg-gray-800 transition-colors {{ request()->routeIs('admin.subscription-requests.*') ? 'bg-gray-800' : '' }}">
                        <span class="material-icons ml-3">pending_actions</span>
                        طلبات الاشتراك
                        @php
                            $pendingCount = \App\Models\SubscriptionRequest::pending()->count();
                        @endphp
                        @if($pendingCount > 0)
                            <span class="bg-red-500 text-white text-xs rounded-full px-2 py-0.5 mr-2">{{ $pendingCount }}</span>
                        @endif
                    </a>
                </nav>
            </div>
            
            <div class="absolute bottom-0 w-64 p-4 border-t border-gray-800">
                <a href="{{ route('dashboard') }}" class="flex items-center px-4 py-2 text-gray-400 hover:text-white transition-colors">
                    <span class="material-icons ml-3">arrow_back</span>
                    العودة للوحة التحكم
                </a>
                <form method="POST" action="{{ route('logout') }}" class="mt-2">
                    @csrf
                    <button type="submit" class="flex items-center w-full px-4 py-2 text-gray-400 hover:text-white transition-colors">
                        <span class="material-icons ml-3">logout</span>
                        تسجيل الخروج
                    </button>
                </form>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="flex-1 overflow-y-auto">
            <!-- Header -->
            <header class="bg-white border-b border-gray-200 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-reverse space-x-4">
                        <h1 class="text-2xl font-bold text-gray-800">@yield('page-title', 'لوحة تحكم المدير')</h1>
                        <a href="{{ route('dashboard') }}" class="flex items-center px-4 py-2 text-sm bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition-colors">
                            <span class="material-icons text-sm ml-2">dashboard</span>
                            الذهاب للنظام
                        </a>
                    </div>
                    <div class="flex items-center space-x-reverse space-x-4">
                        <span class="text-gray-600">{{ Auth::user()->name }}</span>
                        <div class="w-10 h-10 bg-purple-500 rounded-full flex items-center justify-center text-white font-bold">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>
                    </div>
                </div>
            </header>
            
            <!-- Content -->
            <main class="p-6">
                @if(session('success'))
                    <div class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
                        {{ session('success') }}
                    </div>
                @endif
                
                @if(session('error'))
                    <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                        {{ session('error') }}
                    </div>
                @endif
                
                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>

