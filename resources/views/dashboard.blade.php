@extends('layouts.dashboard')

@section('title', 'لوحة التحكم')
@section('page-title', 'لوحة التحكم')
@section('page-description', 'مرحباً بك في نظام إدارة شركات التسويق الإلكتروني')

@section('content')
<div class="space-y-6">

    @if($isOnTrial && $subscription)
    <!-- Trial Alert -->
    <div class="bg-gradient-to-r from-blue-50 to-purple-50 border border-blue-200 rounded-2xl p-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-blue-500 rounded-xl flex items-center justify-center ml-4">
                    <span class="material-icons text-white">timer</span>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-800">فترة التجربة المجانية</h3>
                    <p class="text-sm text-gray-600">
                        باقي {{ $subscription->remaining_trial_days }} يوم على انتهاء فترة التجربة
                    </p>
                </div>
            </div>
            <a href="{{ route('subscription.plans') }}" class="btn-primary text-white px-6 py-2 rounded-xl hover:no-underline">
                اشترك الآن
            </a>
        </div>
    </div>
    @endif

    @if($hasActiveSubscription && $subscription)
    <!-- Active Subscription Alert -->
    <div class="bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 rounded-2xl p-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-green-500 rounded-xl flex items-center justify-center ml-4">
                    <span class="material-icons text-white">check_circle</span>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-800">اشتراك نشط</h3>
                    <p class="text-sm text-gray-600">
                        خطتك: {{ $subscription->plan === 'basic' ? 'بيسك' : ($subscription->plan === 'professional' ? 'بروفيشنال' : 'إنتربرايز') }}
                    </p>
                </div>
            </div>
            <a href="{{ route('subscription.index') }}" class="bg-white text-gray-700 px-6 py-2 rounded-xl hover:bg-gray-50">
                إدارة الاشتراك
            </a>
        </div>
    </div>
    @endif

    <!-- Welcome Card -->
    <div class="card rounded-2xl p-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-800 mb-2">مرحباً بك، {{ Auth::user()->name }}!</h2>
                <p class="text-gray-600">نظام إدارة شركات التسويق الإلكتروني جاهز للاستخدام</p>
                @if($organization)
                    <p class="text-sm text-gray-500 mt-1">المنظمة: {{ $organization->name }}</p>
                @endif
            </div>
            <div class="w-16 h-16 bg-gradient-to-r from-purple-500 to-blue-500 rounded-2xl flex items-center justify-center">
                <span class="material-icons text-white text-2xl">dashboard</span>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Clients -->
        <div class="card rounded-2xl p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">إجمالي العملاء</p>
                    <p class="text-3xl font-bold text-gray-800">{{ number_format($stats['total_clients']) }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                    <span class="material-icons text-blue-600">people</span>
                </div>
            </div>
        </div>

        <!-- Total Projects -->
        <div class="card rounded-2xl p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">إجمالي المشاريع</p>
                    <p class="text-3xl font-bold text-gray-800">{{ number_format($stats['total_projects']) }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                    <span class="material-icons text-green-600">business</span>
                </div>
            </div>
        </div>

        <!-- Active Projects -->
        <div class="card rounded-2xl p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">المشاريع النشطة</p>
                    <p class="text-3xl font-bold text-gray-800">{{ number_format($stats['active_projects']) }}</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                    <span class="material-icons text-purple-600">work</span>
                </div>
            </div>
        </div>

        <!-- Total Employees -->
        <div class="card rounded-2xl p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">إجمالي الموظفين</p>
                    <p class="text-3xl font-bold text-gray-800">{{ number_format($stats['total_employees']) }}</p>
                </div>
                <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center">
                    <span class="material-icons text-orange-600">people_outline</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Financial Stats -->
    @if($stats['total_revenues'] > 0 || $stats['total_expenses'] > 0)
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Total Revenues -->
        <div class="card rounded-2xl p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">إجمالي الإيرادات</p>
                    <p class="text-2xl font-bold text-green-600">{{ number_format($stats['total_revenues'], 2) }} جنيه</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                    <span class="material-icons text-green-600">trending_up</span>
                </div>
            </div>
        </div>

        <!-- Total Expenses -->
        <div class="card rounded-2xl p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">إجمالي المصروفات</p>
                    <p class="text-2xl font-bold text-red-600">{{ number_format($stats['total_expenses'], 2) }} جنيه</p>
                </div>
                <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center">
                    <span class="material-icons text-red-600">trending_down</span>
                </div>
            </div>
        </div>

        <!-- Net Profit -->
        <div class="card rounded-2xl p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">صافي الربح</p>
                    <p class="text-2xl font-bold {{ $stats['net_profit'] >= 0 ? 'text-blue-600' : 'text-red-600' }}">
                        {{ number_format($stats['net_profit'], 2) }} جنيه
                    </p>
                </div>
                <div class="w-12 h-12 {{ $stats['net_profit'] >= 0 ? 'bg-blue-100' : 'bg-red-100' }} rounded-xl flex items-center justify-center">
                    <span class="material-icons {{ $stats['net_profit'] >= 0 ? 'text-blue-600' : 'text-red-600' }}">account_balance</span>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Quick Actions -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Clients -->
        <div class="card rounded-2xl p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-800">العملاء الأخيرين</h3>
                <a href="{{ route('clients.index') }}" class="text-sm text-blue-600 hover:text-blue-500">عرض الكل</a>
            </div>
            <div class="space-y-3">
                @if($recent_clients->count() > 0)
                    @foreach($recent_clients as $client)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl hover:bg-gray-100 transition-colors">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-gradient-to-r from-purple-500 to-blue-500 rounded-full flex items-center justify-center">
                                <i class="fas fa-user text-white text-sm"></i>
                            </div>
                            <div class="mr-3">
                                <div class="text-sm font-medium text-gray-900">{{ $client->name }}</div>
                                <div class="text-xs text-gray-500">{{ $client->email }}</div>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2 rtl:space-x-reverse">
                            <span class="status-badge status-{{ $client->status_color }} text-xs">
                                {{ $client->status_badge }}
                            </span>
                            <a href="{{ route('clients.show', $client) }}" class="text-blue-600 hover:text-blue-800">
                                <i class="fas fa-eye text-sm"></i>
                            </a>
                        </div>
                    </div>
                    @endforeach
                @else
                    <div class="text-center py-8 text-gray-500">
                        <span class="material-icons text-4xl mb-2">people_outline</span>
                        <p>لا يوجد عملاء بعد</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Recent Projects -->
        <div class="card rounded-2xl p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-800">المشاريع الأخيرة</h3>
                <a href="{{ route('projects.index') }}" class="text-sm text-blue-600 hover:text-blue-500">عرض الكل</a>
            </div>
            <div class="space-y-3">
                @if($recent_projects->count() > 0)
                    @foreach($recent_projects as $project)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl hover:bg-gray-100 transition-colors">
                        <div class="flex items-center flex-1">
                            <div class="w-10 h-10 bg-gradient-to-r from-green-500 to-emerald-500 rounded-full flex items-center justify-center">
                                <i class="fas fa-project-diagram text-white text-sm"></i>
                            </div>
                            <div class="mr-3 flex-1">
                                <div class="text-sm font-medium text-gray-900">{{ $project->business_name }}</div>
                                <div class="text-xs text-gray-500">
                                    {{ $project->client->name ?? 'بدون عميل' }} • {{ $project->created_at->diffForHumans() }}
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2 rtl:space-x-reverse">
                            <span class="status-badge status-{{ $project->status_color }} text-xs">
                                {{ $project->status_badge }}
                            </span>
                            <a href="{{ route('projects.show', $project) }}" class="text-blue-600 hover:text-blue-800">
                                <i class="fas fa-eye text-sm"></i>
                            </a>
                        </div>
                    </div>
                    @endforeach
                @else
                    <div class="text-center py-8 text-gray-500">
                        <span class="material-icons text-4xl mb-2">business_center</span>
                        <p>لا يوجد مشاريع بعد</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="card rounded-2xl p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">الإجراءات السريعة</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <a href="{{ route('clients.index') }}" class="btn-primary text-white px-6 py-3 rounded-xl flex items-center justify-center hover:no-underline">
                <span class="material-icons text-sm ml-2">person_add</span>
                إدارة العملاء
            </a>
            <a href="{{ route('projects.index') }}" class="bg-gray-100 text-gray-700 px-6 py-3 rounded-xl flex items-center justify-center hover:bg-gray-200 transition-colors">
                <span class="material-icons text-sm ml-2">business</span>
                إدارة المشاريع
            </a>
            <a href="{{ route('employees.index') }}" class="bg-gray-100 text-gray-700 px-6 py-3 rounded-xl flex items-center justify-center hover:bg-gray-200 transition-colors">
                <span class="material-icons text-sm ml-2">people</span>
                إدارة الموظفين
            </a>
        </div>
    </div>
</div>
@endsection
