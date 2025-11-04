@extends('layouts.employee')

@section('title', 'الخطط الشهرية')
@section('page-title', 'الخطط الشهرية')
@section('page-description', 'الخطط الشهرية للمشاريع التي تديرها')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="card p-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <div class="w-12 h-12 logo-gradient rounded-xl flex items-center justify-center shadow-lg ml-3">
                    <span class="material-icons text-white text-xl">calendar_month</span>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">الخطط الشهرية</h2>
                    <p class="text-gray-600">الخطط الشهرية للمشاريع التي تديرها</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="card p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">إجمالي الخطط</p>
                    <p class="text-2xl font-bold text-gray-800 mt-1">{{ $stats['total'] }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <span class="material-icons text-blue-600">calendar_month</span>
                </div>
            </div>
        </div>

        <div class="card p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">نشطة</p>
                    <p class="text-2xl font-bold text-green-600 mt-1">{{ $stats['active'] }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <span class="material-icons text-green-600">check_circle</span>
                </div>
            </div>
        </div>

        <div class="card p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">مكتملة</p>
                    <p class="text-2xl font-bold text-blue-600 mt-1">{{ $stats['completed'] }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <span class="material-icons text-blue-600">done_all</span>
                </div>
            </div>
        </div>

        <div class="card p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">مسودات</p>
                    <p class="text-2xl font-bold text-gray-600 mt-1">{{ $stats['draft'] }}</p>
                </div>
                <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center">
                    <span class="material-icons text-gray-600">drafts</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Monthly Plans List -->
    @if($monthlyPlans->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($monthlyPlans as $plan)
                <div class="card p-6 hover:shadow-lg transition-shadow">
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex-1">
                            <h3 class="text-lg font-bold text-gray-800 mb-1">{{ $plan->project->business_name }}</h3>
                            <p class="text-sm text-gray-600">{{ $plan->month }} {{ $plan->year }}</p>
                        </div>
                        <span class="px-3 py-1 text-xs rounded-full bg-{{ $plan->status_color }}-100 text-{{ $plan->status_color }}-800">
                            {{ $plan->status_badge }}
                        </span>
                    </div>

                    @if($plan->description)
                        <p class="text-sm text-gray-600 mb-4 line-clamp-2">{{ Str::limit($plan->description, 100) }}</p>
                    @endif

                        <div class="flex items-center justify-between mb-4 pt-4 border-t border-gray-100">
                            <div class="flex items-center text-xs text-gray-500">
                                <span class="material-icons text-xs ml-1">flag</span>
                                {{ $plan->goals->count() }} أهداف
                            </div>
                            <div class="flex items-center text-xs text-gray-500">
                                <span class="material-icons text-xs ml-1">task</span>
                                {{ $plan->tasks()->count() }} مهام
                            </div>
                        </div>

                    <a href="{{ route('employee.monthly-plans.show', $plan->id) }}" 
                       class="w-full btn-primary text-white px-4 py-3 rounded-xl flex items-center justify-center">
                        <span class="material-icons text-sm ml-2">visibility</span>
                        عرض الخطة
                    </a>
                </div>
            @endforeach
        </div>
    @else
        <div class="card p-12 text-center">
            <span class="material-icons text-gray-400 text-6xl mb-4">calendar_month</span>
            <p class="text-gray-600 text-lg">لا توجد خطط شهرية للمشاريع التي تديرها</p>
        </div>
    @endif
</div>
@endsection

