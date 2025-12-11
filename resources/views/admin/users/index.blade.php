@extends('layouts.admin')

@section('title', 'المستخدمين')
@section('page-title', 'إدارة المستخدمين')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="card page-header rounded-2xl p-6 bg-gradient-to-r from-indigo-600 to-purple-600 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl md:text-3xl font-bold mb-2">إدارة المستخدمين</h2>
                <p class="text-indigo-100">عرض وإدارة جميع المستخدمين في النظام</p>
            </div>
            <div class="hidden md:block">
                <div class="w-20 h-20 bg-white bg-opacity-20 rounded-2xl flex items-center justify-center">
                    <i class="fas fa-users text-4xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Message -->
    @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-500 text-green-800 px-4 py-3 rounded-lg flex items-center">
            <i class="fas fa-check-circle ml-2"></i>
            {{ session('success') }}
        </div>
    @endif

    <!-- Search and Filter -->
    <div class="card rounded-2xl p-6">
        <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-col md:flex-row gap-4">
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-2">البحث</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="ابحث بالاسم أو البريد الإلكتروني..." class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent">
            </div>
            <div class="md:w-64">
                <label class="block text-sm font-medium text-gray-700 mb-2">نوع المستخدم</label>
                <select name="user_type" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    <option value="">جميع المستخدمين</option>
                    <option value="admin" {{ request('user_type') == 'admin' ? 'selected' : '' }}>مديرين</option>
                    <option value="organization" {{ request('user_type') == 'organization' ? 'selected' : '' }}>مستخدمي المنظمات</option>
                </select>
            </div>
            <div class="md:w-64">
                <label class="block text-sm font-medium text-gray-700 mb-2">الترتيب حسب</label>
                <select name="sort_by" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>تاريخ التسجيل</option>
                    <option value="clients_count" {{ request('sort_by') == 'clients_count' ? 'selected' : '' }}>عدد العملاء</option>
                    <option value="employees_count" {{ request('sort_by') == 'employees_count' ? 'selected' : '' }}>عدد الموظفين</option>
                    <option value="projects_count" {{ request('sort_by') == 'projects_count' ? 'selected' : '' }}>عدد المشاريع</option>
                </select>
            </div>
            <div class="md:w-48">
                <label class="block text-sm font-medium text-gray-700 mb-2">الاتجاه</label>
                <select name="sort_order" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    <option value="desc" {{ request('sort_order', 'desc') == 'desc' ? 'selected' : '' }}>تنازلي</option>
                    <option value="asc" {{ request('sort_order') == 'asc' ? 'selected' : '' }}>تصاعدي</option>
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="btn-primary px-6 py-3 rounded-xl flex items-center">
                    <i class="fas fa-search ml-2"></i>
                    بحث
                </button>
                @if(request('search') || request('user_type') || request('sort_by'))
                    <a href="{{ route('admin.users.index') }}" class="px-6 py-3 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 transition-colors flex items-center">
                        <i class="fas fa-times ml-2"></i>
                        إلغاء
                    </a>
                @endif
            </div>
        </form>
    </div>
    
    <!-- Users Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($users as $user)
            @php
                $statusColors = [
                    'active' => 'bg-green-100 text-green-800 border-green-200',
                    'inactive' => 'bg-gray-100 text-gray-800 border-gray-200',
                    'suspended' => 'bg-red-100 text-red-800 border-red-200',
                ];
                $statusText = [
                    'active' => 'نشط',
                    'inactive' => 'غير نشط',
                    'suspended' => 'موقوف',
                ];
                $currentStatus = $user->status ?? 'active';
                
                // حساب الأرقام
                $clientsCount = 0;
                $employeesCount = 0;
                $projectsCount = 0;
                
                if ($user->organization) {
                    $clientsCount = $user->organization->clients_count ?? ($user->organization->clients ? $user->organization->clients->count() : 0);
                    $employeesCount = $user->organization->employees_count ?? ($user->organization->employees ? $user->organization->employees->count() : 0);
                    $projectsCount = $user->organization->projects_count ?? ($user->organization->projects ? $user->organization->projects->count() : 0);
                }
            @endphp
            
            <div class="card rounded-2xl p-6 hover:shadow-xl transition-all duration-300 border border-gray-200">
                <!-- User Header -->
                <div class="flex items-start justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-16 h-16 bg-gradient-to-br from-indigo-400 to-purple-600 rounded-xl flex items-center justify-center text-white font-bold text-xl shadow-lg">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">{{ $user->name }}</h3>
                            <p class="text-sm text-gray-500 mt-1">{{ $user->email }}</p>
                        </div>
                    </div>
                    @if($user->is_admin)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                            <i class="fas fa-user-shield ml-1"></i>
                            مدير
                        </span>
                    @else
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            <i class="fas fa-building ml-1"></i>
                            منظمة
                        </span>
                    @endif
                </div>

                <!-- Stats -->
                <div class="grid grid-cols-3 gap-3 mb-4">
                    <div class="bg-blue-50 rounded-xl p-3 text-center border border-blue-100">
                        <div class="flex items-center justify-center mb-1">
                            <i class="fas fa-users text-blue-600 text-lg"></i>
                        </div>
                        <div class="text-2xl font-bold text-blue-900">{{ $clientsCount }}</div>
                        <div class="text-xs text-blue-600 mt-1">عملاء</div>
                    </div>
                    <div class="bg-green-50 rounded-xl p-3 text-center border border-green-100">
                        <div class="flex items-center justify-center mb-1">
                            <i class="fas fa-user-tie text-green-600 text-lg"></i>
                        </div>
                        <div class="text-2xl font-bold text-green-900">{{ $employeesCount }}</div>
                        <div class="text-xs text-green-600 mt-1">موظفين</div>
                    </div>
                    <div class="bg-purple-50 rounded-xl p-3 text-center border border-purple-100">
                        <div class="flex items-center justify-center mb-1">
                            <i class="fas fa-folder text-purple-600 text-lg"></i>
                        </div>
                        <div class="text-2xl font-bold text-purple-900">{{ $projectsCount }}</div>
                        <div class="text-xs text-purple-600 mt-1">مشاريع</div>
                    </div>
                </div>

                <!-- Status and Date -->
                <div class="flex items-center justify-between mb-4 pb-4 border-b border-gray-200">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $statusColors[$currentStatus] ?? 'bg-gray-100 text-gray-800' }} border">
                        <i class="fas fa-circle text-[8px] ml-1"></i>
                        {{ $statusText[$currentStatus] ?? $currentStatus }}
                    </span>
                    <div class="text-xs text-gray-500">
                        <i class="fas fa-calendar-alt ml-1"></i>
                        {{ $user->created_at->format('Y-m-d') }}
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex items-center gap-2">
                    @if(!$user->is_admin)
                        <form method="POST" action="{{ route('admin.users.impersonate', $user) }}" class="flex-1" onsubmit="return confirm('هل أنت متأكد من الدخول كالمستخدم: {{ $user->name }}؟');">
                            @csrf
                            <button type="submit" class="w-full px-4 py-2 bg-green-50 text-green-600 rounded-lg hover:bg-green-100 transition-colors flex items-center justify-center text-sm font-medium" title="الدخول كالمستخدم">
                                <i class="fas fa-sign-in-alt ml-2"></i>
                                دخول
                            </button>
                        </form>
                    @endif
                    <form method="POST" action="{{ route('admin.users.updateStatus', $user) }}" class="flex-1">
                        @csrf
                        @method('PATCH')
                        <select name="status" onchange="this.form.submit()" class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent {{ $statusColors[$currentStatus] ?? '' }}">
                            <option value="active" {{ $currentStatus == 'active' ? 'selected' : '' }}>نشط</option>
                            <option value="inactive" {{ $currentStatus == 'inactive' ? 'selected' : '' }}>غير نشط</option>
                            <option value="suspended" {{ $currentStatus == 'suspended' ? 'selected' : '' }}>موقوف</option>
                        </select>
                    </form>
                    <a href="{{ route('admin.users.show', $user) }}" class="px-4 py-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition-colors flex items-center justify-center" title="عرض">
                        <i class="fas fa-eye"></i>
                    </a>
                    @if(!$user->is_admin)
                        <form method="POST" action="{{ route('admin.users.delete', $user) }}" class="inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا المستخدم؟');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-4 py-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition-colors flex items-center justify-center" title="حذف">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        @empty
            <div class="col-span-full">
                <div class="card rounded-2xl p-12 text-center">
                    <div class="flex flex-col items-center">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                            <i class="fas fa-users text-gray-400 text-2xl"></i>
                        </div>
                        <p class="text-gray-500 font-medium text-lg">لا يوجد مستخدمين</p>
                        <p class="text-sm text-gray-400 mt-1">لم يتم العثور على أي مستخدمين</p>
                    </div>
                </div>
            </div>
        @endforelse
    </div>
    
    <!-- Pagination -->
    @if($users->hasPages())
        <div class="card rounded-2xl p-4">
            {{ $users->links() }}
        </div>
    @endif
</div>
@endsection
