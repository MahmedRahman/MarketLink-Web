@extends('layouts.dashboard')

@section('title', 'الخطط الشهرية')
@section('page-title', 'الخطط الشهرية')
@section('page-description', 'إدارة الخطط الشهرية للمشاريع')

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
                        <h2 class="text-xl md:text-2xl font-bold text-gray-800">الخطط الشهرية</h2>
                        <p class="text-sm md:text-base text-gray-600 hidden md:block">إدارة وتتبع الخطط الشهرية للمشاريع</p>
                    </div>
                </div>
                <a href="{{ route('monthly-plans.create') }}" class="btn-primary text-white px-4 md:px-6 py-2 md:py-3 rounded-xl flex items-center justify-center text-sm md:text-base w-full md:w-auto">
                    <span class="material-icons text-xs md:text-sm ml-2">add</span>
                    <span class="hidden sm:inline">إضافة خطة شهرية جديدة</span>
                    <span class="sm:hidden">إضافة خطة</span>
                </a>
            </div>
        </div>

        <!-- Plans List -->
        <div class="card rounded-xl md:rounded-2xl p-3 md:p-6">
            @if($plans->count() > 0)
                <!-- Desktop Table View -->
                <div class="hidden md:block overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 md:px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">المشروع</th>
                                <th class="px-4 md:px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الشهر</th>
                                <th class="px-4 md:px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الأهداف</th>
                                <th class="px-4 md:px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الموظفين</th>
                                <th class="px-4 md:px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الحالة</th>
                                <th class="px-4 md:px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($plans as $plan)
                                <tr>
                                    <td class="px-4 md:px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $plan->project->business_name }}</div>
                                    </td>
                                    <td class="px-4 md:px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $plan->month }} {{ $plan->year }}</div>
                                    </td>
                                    <td class="px-4 md:px-6 py-4">
                                        <div class="text-sm text-gray-500">{{ $plan->goals->count() }} هدف</div>
                                    </td>
                                    <td class="px-4 md:px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-500">{{ $plan->employees->count() }} موظف</div>
                                    </td>
                                    <td class="px-4 md:px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 py-1 text-xs rounded-full bg-{{ $plan->status_color }}-100 text-{{ $plan->status_color }}-800">
                                            {{ $plan->status_badge }}
                                        </span>
                                    </td>
                                    <td class="px-4 md:px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex items-center space-x-3 rtl:space-x-reverse">
                                            <a href="{{ route('monthly-plans.show', $plan) }}" class="text-blue-600 hover:text-blue-900">عرض</a>
                                            <a href="{{ route('monthly-plans.edit', $plan) }}" class="text-green-600 hover:text-green-900">تعديل</a>
                                            <button onclick="confirmDelete('{{ route('monthly-plans.destroy', $plan) }}', 'تأكيد حذف الخطة', 'هل أنت متأكد من حذف الخطة الشهرية {{ $plan->month }} {{ $plan->year }}؟')" class="text-red-600 hover:text-red-900" title="حذف">
                                                <span class="material-icons text-sm">delete</span>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Mobile Card View -->
                <div class="md:hidden space-y-4">
                    @foreach($plans as $plan)
                        <div class="bg-white border border-gray-200 rounded-xl p-4">
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex-1">
                                    <h3 class="text-base font-semibold text-gray-900 mb-1">{{ $plan->project->business_name }}</h3>
                                    <p class="text-sm text-gray-600">{{ $plan->month }} {{ $plan->year }}</p>
                                </div>
                                <span class="px-2 py-1 text-xs rounded-full bg-{{ $plan->status_color }}-100 text-{{ $plan->status_color }}-800 flex-shrink-0">
                                    {{ $plan->status_badge }}
                                </span>
                            </div>
                            <div class="flex items-center gap-4 mb-3 text-sm text-gray-600">
                                <div class="flex items-center gap-1">
                                    <span class="material-icons text-xs">flag</span>
                                    <span>{{ $plan->goals->count() }} هدف</span>
                                </div>
                                <div class="flex items-center gap-1">
                                    <span class="material-icons text-xs">people</span>
                                    <span>{{ $plan->employees->count() }} موظف</span>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 pt-3 border-t border-gray-200">
                                <a href="{{ route('monthly-plans.show', $plan) }}" class="flex-1 text-center px-3 py-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition-colors text-sm font-medium">
                                    عرض
                                </a>
                                <a href="{{ route('monthly-plans.edit', $plan) }}" class="flex-1 text-center px-3 py-2 bg-green-50 text-green-600 rounded-lg hover:bg-green-100 transition-colors text-sm font-medium">
                                    تعديل
                                </a>
                                <button onclick="confirmDelete('{{ route('monthly-plans.destroy', $plan) }}', 'تأكيد حذف الخطة', 'هل أنت متأكد من حذف الخطة الشهرية {{ $plan->month }} {{ $plan->year }}؟')" class="px-3 py-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition-colors" title="حذف">
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
                    <h3 class="text-base md:text-lg font-medium text-gray-900 mb-2">لا توجد خطط شهرية</h3>
                    <p class="text-sm md:text-base text-gray-500 mb-4 md:mb-6">ابدأ بإنشاء خطة شهرية جديدة للمشاريع</p>
                    <a href="{{ route('monthly-plans.create') }}" class="btn-primary text-white px-4 md:px-6 py-2.5 md:py-3 rounded-xl inline-flex items-center hover:no-underline text-sm md:text-base">
                        <span class="material-icons text-xs md:text-sm ml-2">add</span>
                        إضافة خطة شهرية جديدة
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

