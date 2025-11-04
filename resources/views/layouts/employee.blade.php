<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'لوحة التحكم') - MarketLink</title>
    
    <!-- Cairo Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Material Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    @yield('styles')
    
    <style>
        * {
            font-family: 'Cairo', sans-serif;
        }
        
        .material-icons {
            font-family: 'Material Icons';
            font-weight: normal;
            font-style: normal;
            font-size: 24px;
            line-height: 1;
            display: inline-block;
        }
        
        .logo-gradient {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .card {
            background: white;
            border-radius: 1rem;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            transition: all 0.3s;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Sidebar -->
    <div class="fixed right-0 top-0 h-full w-64 bg-white shadow-lg z-50">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center">
                <div class="w-10 h-10 logo-gradient rounded-xl flex items-center justify-center ml-3">
                    <span class="material-icons text-white text-sm">person</span>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-gray-800">MarketLink</h2>
                    <p class="text-xs text-gray-500">الموظف</p>
                </div>
            </div>
        </div>
        
        <nav class="p-4 space-y-2 overflow-y-auto" style="max-height: calc(100vh - 200px);">
            <a href="{{ route('employee.dashboard') }}" class="flex items-center px-4 py-3 text-gray-700 rounded-xl hover:bg-gray-100 {{ request()->routeIs('employee.dashboard') ? 'bg-purple-50 text-purple-700' : '' }}">
                <span class="material-icons text-lg ml-3">dashboard</span>
                <span class="font-medium">لوحة التحكم</span>
            </a>
            
            <a href="{{ route('employee.projects.index') }}" class="flex items-center px-4 py-3 text-gray-700 rounded-xl hover:bg-gray-100 {{ request()->routeIs('employee.projects.*') ? 'bg-purple-50 text-purple-700' : '' }}">
                <span class="material-icons text-lg ml-3">folder</span>
                <span class="font-medium">المشاريع</span>
            </a>
            
            <a href="{{ route('employee.expenses.index') }}" class="flex items-center px-4 py-3 text-gray-700 rounded-xl hover:bg-gray-100 {{ request()->routeIs('employee.expenses.*') ? 'bg-purple-50 text-purple-700' : '' }}">
                <span class="material-icons text-lg ml-3">receipt</span>
                <span class="font-medium">الإيصالات</span>
            </a>
            
            @php
                $employee = Auth::guard('employee')->user();
                $hasManagerRole = $employee->managedProjects()->count() > 0;
            @endphp
            @if($hasManagerRole)
                <a href="{{ route('employee.monthly-plans.index') }}" class="flex items-center px-4 py-3 text-gray-700 rounded-xl hover:bg-gray-100 {{ request()->routeIs('employee.monthly-plans.*') ? 'bg-purple-50 text-purple-700' : '' }}">
                    <span class="material-icons text-lg ml-3">calendar_month</span>
                    <span class="font-medium">الخطط الشهرية</span>
                </a>
            @endif
        </nav>
        
        <div class="p-4 border-t border-gray-200">
            <form method="POST" action="{{ route('employee.logout') }}">
                @csrf
                <button type="submit" class="w-full flex items-center px-4 py-3 text-red-600 rounded-xl hover:bg-red-50">
                    <span class="material-icons text-lg ml-3">logout</span>
                    <span class="font-medium">تسجيل الخروج</span>
                </button>
            </form>
        </div>
        
        <!-- Employee Info -->
        <div class="absolute bottom-0 w-full p-4 border-t border-gray-200">
            <div class="flex items-center">
                <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center ml-3">
                    <span class="material-icons text-purple-600 text-sm">person</span>
                </div>
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-800">{{ Auth::guard('employee')->user()->name }}</p>
                    <p class="text-xs text-gray-500">{{ Auth::guard('employee')->user()->role_badge }}</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Main Content -->
    <div class="mr-64 min-h-screen">
        <!-- Header -->
        <header class="bg-white shadow-sm border-b border-gray-200 sticky top-0 z-40">
            <div class="px-6 py-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">@yield('page-title', 'لوحة التحكم')</h1>
                        <p class="text-sm text-gray-600">@yield('page-description', '')</p>
                    </div>
                </div>
            </div>
        </header>
        
        <!-- Content -->
        <main class="p-6">
            @if(session('success'))
                <div class="mb-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg flex items-center">
                    <span class="material-icons ml-2">check_circle</span>
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg flex items-center">
                    <span class="material-icons ml-2">error</span>
                    {{ session('error') }}
                </div>
            @endif

            @yield('content')
        </main>
    </div>
    
    @yield('scripts')
</body>
</html>

