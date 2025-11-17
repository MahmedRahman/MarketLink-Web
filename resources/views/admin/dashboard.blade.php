@extends('layouts.admin')

@section('title', 'لوحة تحكم المدير')
@section('page-title', 'لوحة تحكم المدير')

@section('content')
<div class="space-y-6">
    <!-- Welcome Header -->
    <div class="card page-header rounded-2xl p-6 bg-gradient-to-r from-purple-600 to-blue-600 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl md:text-3xl font-bold mb-2">مرحباً بك، {{ Auth::user()->name }}</h2>
                <p class="text-purple-100">نظرة عامة على النظام والإحصائيات</p>
            </div>
            <div class="hidden md:block">
                <div class="w-20 h-20 bg-white bg-opacity-20 rounded-2xl flex items-center justify-center">
                    <i class="fas fa-chart-line text-4xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 md:gap-6">
        <!-- Total Users -->
        <div class="card rounded-2xl p-6 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600 mb-1">إجمالي المستخدمين</p>
                    <p class="text-3xl font-bold text-gray-800">{{ number_format($stats['total_users']) }}</p>
                    <p class="text-xs text-gray-500 mt-1">مستخدم نشط</p>
                </div>
                <div class="w-14 h-14 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg">
                    <i class="fas fa-users text-white text-xl"></i>
                </div>
            </div>
        </div>
        
        <!-- Total Organizations -->
        <div class="card rounded-2xl p-6 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600 mb-1">إجمالي المنظمات</p>
                    <p class="text-3xl font-bold text-gray-800">{{ number_format($stats['total_organizations']) }}</p>
                    <p class="text-xs text-gray-500 mt-1">منظمة مسجلة</p>
                </div>
                <div class="w-14 h-14 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center shadow-lg">
                    <i class="fas fa-building text-white text-xl"></i>
                </div>
            </div>
        </div>
        
        <!-- Active Subscriptions -->
        <div class="card rounded-2xl p-6 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 border-l-4 border-purple-500">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600 mb-1">اشتراكات نشطة</p>
                    <p class="text-3xl font-bold text-gray-800">{{ number_format($stats['active_subscriptions']) }}</p>
                    <p class="text-xs text-gray-500 mt-1">اشتراك نشط</p>
                </div>
                <div class="w-14 h-14 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg">
                    <i class="fas fa-check-circle text-white text-xl"></i>
                </div>
            </div>
        </div>
        
        <!-- Trial Subscriptions -->
        <div class="card rounded-2xl p-6 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 border-l-4 border-orange-500">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600 mb-1">في فترة Trial</p>
                    <p class="text-3xl font-bold text-gray-800">{{ number_format($stats['trial_subscriptions']) }}</p>
                    <p class="text-xs text-gray-500 mt-1">فترة تجريبية</p>
                </div>
                <div class="w-14 h-14 bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl flex items-center justify-center shadow-lg">
                    <i class="fas fa-clock text-white text-xl"></i>
                </div>
            </div>
        </div>
        
        <!-- Expired Subscriptions -->
        <div class="card rounded-2xl p-6 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 border-l-4 border-red-500">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600 mb-1">اشتراكات منتهية</p>
                    <p class="text-3xl font-bold text-gray-800">{{ number_format($stats['expired_subscriptions']) }}</p>
                    <p class="text-xs text-gray-500 mt-1">انتهت صلاحيتها</p>
                </div>
                <div class="w-14 h-14 bg-gradient-to-br from-red-500 to-red-600 rounded-xl flex items-center justify-center shadow-lg">
                    <i class="fas fa-times-circle text-white text-xl"></i>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Recent Users and Organizations -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Users -->
        <div class="card rounded-2xl overflow-hidden">
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 p-6 text-white">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-white bg-opacity-20 rounded-xl flex items-center justify-center ml-3">
                            <i class="fas fa-users text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold">آخر المستخدمين</h3>
                            <p class="text-blue-100 text-sm">أحدث المستخدمين المسجلين</p>
                        </div>
                    </div>
                    <a href="{{ route('admin.users.index') }}" class="bg-white bg-opacity-20 hover:bg-opacity-30 px-4 py-2 rounded-xl transition-all duration-300 flex items-center">
                        <span class="text-sm ml-2">عرض الكل</span>
                        <i class="fas fa-arrow-left text-sm"></i>
                    </a>
                </div>
            </div>
            <div class="p-6">
                <div class="space-y-3">
                    @forelse($recent_users as $user)
                        <a href="{{ route('admin.users.show', $user) }}" class="flex items-center justify-between p-4 bg-gray-50 hover:bg-blue-50 rounded-xl transition-all duration-300 group border border-transparent hover:border-blue-200">
                            <div class="flex items-center flex-1">
                                <div class="w-12 h-12 bg-gradient-to-br from-blue-400 to-blue-600 rounded-xl flex items-center justify-center text-white font-bold ml-3 shadow-md">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <div class="flex-1">
                                    <p class="font-semibold text-gray-800 group-hover:text-blue-600 transition-colors">{{ $user->name }}</p>
                                    <p class="text-sm text-gray-600">{{ $user->email }}</p>
                                    @if($user->organization)
                                        <p class="text-xs text-gray-500 mt-1">
                                            <i class="fas fa-building text-xs ml-1"></i>
                                            {{ $user->organization->name }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                            <div class="text-blue-600 opacity-0 group-hover:opacity-100 transition-opacity">
                                <i class="fas fa-chevron-left"></i>
                            </div>
                        </a>
                    @empty
                        <div class="text-center py-12">
                            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-users text-gray-400 text-2xl"></i>
                            </div>
                            <p class="text-gray-500 font-medium">لا يوجد مستخدمين</p>
                            <p class="text-sm text-gray-400 mt-1">لم يتم تسجيل أي مستخدمين بعد</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
        
        <!-- Recent Organizations -->
        <div class="card rounded-2xl overflow-hidden">
            <div class="bg-gradient-to-r from-green-500 to-green-600 p-6 text-white">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-white bg-opacity-20 rounded-xl flex items-center justify-center ml-3">
                            <i class="fas fa-building text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold">آخر المنظمات</h3>
                            <p class="text-green-100 text-sm">أحدث المنظمات المسجلة</p>
                        </div>
                    </div>
                    <a href="{{ route('admin.organizations.index') }}" class="bg-white bg-opacity-20 hover:bg-opacity-30 px-4 py-2 rounded-xl transition-all duration-300 flex items-center">
                        <span class="text-sm ml-2">عرض الكل</span>
                        <i class="fas fa-arrow-left text-sm"></i>
                    </a>
                </div>
            </div>
            <div class="p-6">
                <div class="space-y-3">
                    @forelse($recent_organizations as $org)
                        <a href="{{ route('admin.organizations.show', $org) }}" class="flex items-center justify-between p-4 bg-gray-50 hover:bg-green-50 rounded-xl transition-all duration-300 group border border-transparent hover:border-green-200">
                            <div class="flex items-center flex-1">
                                <div class="w-12 h-12 bg-gradient-to-br from-green-400 to-green-600 rounded-xl flex items-center justify-center text-white ml-3 shadow-md">
                                    <i class="fas fa-building text-lg"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="font-semibold text-gray-800 group-hover:text-green-600 transition-colors">{{ $org->name }}</p>
                                    @php
                                        $activeSub = $org->subscription->first();
                                    @endphp
                                    @if($activeSub)
                                        <div class="flex items-center mt-1">
                                            @if($activeSub->plan === 'trial')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-orange-100 text-orange-800">
                                                    <i class="fas fa-clock text-xs ml-1"></i>
                                                    Trial
                                                </span>
                                            @elseif($activeSub->plan === 'basic')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                                    <i class="fas fa-star text-xs ml-1"></i>
                                                    بيسك
                                                </span>
                                            @elseif($activeSub->plan === 'professional')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800">
                                                    <i class="fas fa-crown text-xs ml-1"></i>
                                                    بروفيشنال
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-indigo-100 text-indigo-800">
                                                    <i class="fas fa-gem text-xs ml-1"></i>
                                                    إنتربرايز
                                                </span>
                                            @endif
                                        </div>
                                    @else
                                        <p class="text-xs text-gray-500 mt-1">لا يوجد اشتراك نشط</p>
                                    @endif
                                </div>
                            </div>
                            <div class="text-green-600 opacity-0 group-hover:opacity-100 transition-opacity">
                                <i class="fas fa-chevron-left"></i>
                            </div>
                        </a>
                    @empty
                        <div class="text-center py-12">
                            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-building text-gray-400 text-2xl"></i>
                            </div>
                            <p class="text-gray-500 font-medium">لا توجد منظمات</p>
                            <p class="text-sm text-gray-400 mt-1">لم يتم تسجيل أي منظمات بعد</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

