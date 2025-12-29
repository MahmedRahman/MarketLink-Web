@extends('layouts.dashboard')

@section('title', 'الحملات الإعلانية')
@section('page-title', 'الحملات الإعلانية')
@section('page-description', 'إدارة الحملات الإعلانية للمشاريع')

@section('content')
<div class="container mx-auto px-3 md:px-4">
    <div class="max-w-7xl mx-auto space-y-4 md:space-y-6">
        <!-- Success/Error Messages -->
        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-800 px-3 md:px-4 py-2 md:py-3 rounded-lg flex items-center text-sm md:text-base">
                <span class="material-icons text-base md:text-lg ml-2">check_circle</span>
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-50 border border-red-200 text-red-800 px-3 md:px-4 py-2 md:py-3 rounded-lg flex items-center text-sm md:text-base">
                <span class="material-icons text-base md:text-lg ml-2">error</span>
                {{ session('error') }}
            </div>
        @endif

        <!-- Header -->
        <div class="card page-header rounded-xl md:rounded-2xl p-4 md:p-6">
            <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                <div class="flex items-center flex-1">
                    <div class="w-10 h-10 md:w-12 md:h-12 logo-gradient rounded-xl md:rounded-2xl flex items-center justify-center shadow-lg icon-spacing ml-2 md:ml-3 flex-shrink-0">
                        <span class="material-icons text-white text-lg md:text-xl">calendar_month</span>
                    </div>
                    <div>
                        <h2 class="text-xl md:text-2xl font-bold text-gray-800">الحملات الإعلانية</h2>
                        <p class="text-sm md:text-base text-gray-600 hidden md:block">إدارة وتتبع الحملات الإعلانية للمشاريع</p>
                    </div>
                </div>
                    <a href="{{ route('monthly-plans.create') }}" class="btn-primary text-white px-4 md:px-6 py-2 md:py-3 rounded-xl flex items-center justify-center text-sm md:text-base w-full md:w-auto">
                    <span class="material-icons text-xs md:text-sm ml-2">add</span>
                    <span class="hidden sm:inline">إضافة حملة إعلانية جديدة</span>
                    <span class="sm:hidden">إضافة حملة</span>
                </a>
            </div>
        </div>

        <!-- Plans List -->
        <div>
            @if($plans->count() > 0)
                <!-- Cards Grid View -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6">
                    @foreach($plans as $plan)
                        <div class="card rounded-xl md:rounded-2xl p-4 md:p-6 hover:shadow-lg transition-shadow">
                            <div class="flex items-start justify-between mb-4">
                                <div class="flex-1">
                                    <h3 class="text-lg md:text-xl font-bold text-gray-800 mb-1">{{ $plan->project->business_name }}</h3>
                                    <p class="text-sm md:text-base text-gray-600 flex items-center gap-2">
                                        <span class="material-icons text-sm">calendar_month</span>
                                        {{ $plan->month }} {{ $plan->year }}
                                    </p>
                                </div>
                                <span class="px-3 py-1 text-xs rounded-full bg-{{ $plan->status_color }}-100 text-{{ $plan->status_color }}-800 flex-shrink-0">
                                    {{ $plan->status_badge }}
                                </span>
                            </div>

                            @if($plan->description)
                                <p class="text-sm text-gray-600 mb-4 line-clamp-2">{{ Str::limit($plan->description, 100) }}</p>
                            @endif

                            <div class="flex items-center gap-4 mb-4 pt-4 border-t border-gray-100">
                                <div class="flex items-center gap-2 text-sm text-gray-600">
                                    <div class="w-8 h-8 bg-purple-50 rounded-lg flex items-center justify-center">
                                        <span class="material-icons text-purple-600 text-sm">flag</span>
                                    </div>
                                    <span class="font-medium">{{ $plan->goals->count() }}</span>
                                    <span class="text-gray-500">هدف</span>
                                </div>
                                <div class="flex items-center gap-2 text-sm text-gray-600">
                                    <div class="w-8 h-8 bg-blue-50 rounded-lg flex items-center justify-center">
                                        <span class="material-icons text-blue-600 text-sm">people</span>
                                    </div>
                                    <span class="font-medium">{{ $plan->employees->count() }}</span>
                                    <span class="text-gray-500">موظف</span>
                                </div>
                            </div>

                            <div class="flex items-center gap-2 pt-4 border-t border-gray-100">
                                <a href="{{ route('monthly-plans.show', $plan) }}" class="flex-1 text-center px-3 py-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition-colors text-sm font-medium flex items-center justify-center gap-1">
                                    <span class="material-icons text-sm">visibility</span>
                                    عرض
                                </a>
                                <a href="{{ route('monthly-plans.edit', $plan) }}" class="flex-1 text-center px-3 py-2 bg-green-50 text-green-600 rounded-lg hover:bg-green-100 transition-colors text-sm font-medium flex items-center justify-center gap-1">
                                    <span class="material-icons text-sm">edit</span>
                                    تعديل
                                </a>
                                <button onclick="confirmDelete('{{ route('monthly-plans.destroy', $plan) }}', 'تأكيد حذف الحملة', 'هل أنت متأكد من حذف الحملة الإعلانية {{ $plan->month }} {{ $plan->year }}؟')" class="px-3 py-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition-colors flex items-center justify-center" title="حذف">
                                    <span class="material-icons text-sm">delete</span>
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <div class="mt-4">
                    {{ $plans->links() }}
                </div>
            @else
                <div class="text-center py-8 md:py-12 px-4">
                    <span class="material-icons text-gray-400 text-4xl md:text-6xl mb-3 md:mb-4">calendar_month</span>
                    <h3 class="text-base md:text-lg font-medium text-gray-900 mb-2">لا توجد حملات إعلانية</h3>
                    <p class="text-sm md:text-base text-gray-500 mb-4 md:mb-6">ابدأ بإنشاء حملة إعلانية جديدة للمشاريع</p>
                    <a href="{{ route('monthly-plans.create') }}" class="btn-primary text-white px-4 md:px-6 py-2.5 md:py-3 rounded-xl inline-flex items-center hover:no-underline text-sm md:text-base">
                        <span class="material-icons text-xs md:text-sm ml-2">add</span>
                        إضافة حملة إعلانية جديدة
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

