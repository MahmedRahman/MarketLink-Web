@extends('layouts.admin')

@section('title', 'لوحة تحكم المدير')
@section('page-title', 'لوحة تحكم المدير')

@section('content')
<div class="space-y-6">
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
        <div class="card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">إجمالي المستخدمين</p>
                    <p class="text-3xl font-bold text-gray-800">{{ $stats['total_users'] }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                    <span class="material-icons text-blue-600">people</span>
                </div>
            </div>
        </div>
        
        <div class="card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">إجمالي المنظمات</p>
                    <p class="text-3xl font-bold text-gray-800">{{ $stats['total_organizations'] }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                    <span class="material-icons text-green-600">business</span>
                </div>
            </div>
        </div>
        
        <div class="card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">اشتراكات نشطة</p>
                    <p class="text-3xl font-bold text-gray-800">{{ $stats['active_subscriptions'] }}</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                    <span class="material-icons text-purple-600">check_circle</span>
                </div>
            </div>
        </div>
        
        <div class="card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">في فترة Trial</p>
                    <p class="text-3xl font-bold text-gray-800">{{ $stats['trial_subscriptions'] }}</p>
                </div>
                <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center">
                    <span class="material-icons text-orange-600">timer</span>
                </div>
            </div>
        </div>
        
        <div class="card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">اشتراكات منتهية</p>
                    <p class="text-3xl font-bold text-gray-800">{{ $stats['expired_subscriptions'] }}</p>
                </div>
                <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center">
                    <span class="material-icons text-red-600">cancel</span>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Recent Users and Organizations -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Users -->
        <div class="card">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-800">آخر المستخدمين</h3>
                <a href="{{ route('admin.users.index') }}" class="text-sm text-blue-600 hover:text-blue-500">عرض الكل</a>
            </div>
            <div class="space-y-3">
                @forelse($recent_users as $user)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div>
                            <p class="font-medium text-gray-800">{{ $user->name }}</p>
                            <p class="text-sm text-gray-600">{{ $user->email }}</p>
                            @if($user->organization)
                                <p class="text-xs text-gray-500">{{ $user->organization->name }}</p>
                            @endif
                        </div>
                        <a href="{{ route('admin.users.show', $user) }}" class="text-blue-600 hover:text-blue-500">
                            <span class="material-icons text-sm">arrow_back</span>
                        </a>
                    </div>
                @empty
                    <p class="text-center text-gray-500 py-4">لا يوجد مستخدمين</p>
                @endforelse
            </div>
        </div>
        
        <!-- Recent Organizations -->
        <div class="card">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-800">آخر المنظمات</h3>
                <a href="{{ route('admin.organizations.index') }}" class="text-sm text-blue-600 hover:text-blue-500">عرض الكل</a>
            </div>
            <div class="space-y-3">
                @forelse($recent_organizations as $org)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div>
                            <p class="font-medium text-gray-800">{{ $org->name }}</p>
                            @php
                                $activeSub = $org->subscription->first();
                            @endphp
                            @if($activeSub)
                                <p class="text-sm text-gray-600">
                                    {{ $activeSub->plan === 'trial' ? 'Trial' : ($activeSub->plan === 'basic' ? 'بيسك' : ($activeSub->plan === 'professional' ? 'بروفيشنال' : 'إنتربرايز')) }}
                                </p>
                            @endif
                        </div>
                        <a href="{{ route('admin.organizations.show', $org) }}" class="text-blue-600 hover:text-blue-500">
                            <span class="material-icons text-sm">arrow_back</span>
                        </a>
                    </div>
                @empty
                    <p class="text-center text-gray-500 py-4">لا توجد منظمات</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

