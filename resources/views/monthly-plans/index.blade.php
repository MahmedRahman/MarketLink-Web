@extends('layouts.dashboard')

@section('title', 'الخطط الشهرية')
@section('page-title', 'الخطط الشهرية')
@section('page-description', 'إدارة الخطط الشهرية للمشاريع')

@section('content')
<div class="container mx-auto px-4">
    <div class="max-w-7xl mx-auto space-y-6">
        <!-- Header -->
        <div class="card page-header rounded-2xl p-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="w-12 h-12 logo-gradient rounded-2xl flex items-center justify-center shadow-lg icon-spacing ml-3">
                        <span class="material-icons text-white text-xl">calendar_month</span>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800">الخطط الشهرية</h2>
                        <p class="text-gray-600">إدارة وتتبع الخطط الشهرية للمشاريع</p>
                    </div>
                </div>
                <a href="{{ route('monthly-plans.create') }}" class="btn-primary text-white px-6 py-3 rounded-xl flex items-center">
                    <span class="material-icons text-sm ml-2">add</span>
                    إضافة خطة شهرية جديدة
                </a>
            </div>
        </div>

        <!-- Plans List -->
        <div class="card rounded-2xl p-6">
            @if($plans->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">المشروع</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الشهر</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الأهداف</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الموظفين</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الحالة</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($plans as $plan)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $plan->project->business_name }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $plan->month }} {{ $plan->year }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-500">{{ $plan->goals->count() }} هدف</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-500">{{ $plan->employees->count() }} موظف</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 py-1 text-xs rounded-full bg-{{ $plan->status_color }}-100 text-{{ $plan->status_color }}-800">
                                            {{ $plan->status_badge }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="{{ route('monthly-plans.show', $plan) }}" class="text-blue-600 hover:text-blue-900 ml-3">عرض</a>
                                        <a href="{{ route('monthly-plans.edit', $plan) }}" class="text-green-600 hover:text-green-900 ml-3">تعديل</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">
                    {{ $plans->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <span class="material-icons text-gray-400 text-6xl mb-4">calendar_month</span>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">لا توجد خطط شهرية</h3>
                    <p class="text-gray-500 mb-6">ابدأ بإنشاء خطة شهرية جديدة للمشاريع</p>
                    <a href="{{ route('monthly-plans.create') }}" class="btn-primary text-white px-6 py-3 rounded-xl inline-flex items-center">
                        <span class="material-icons text-sm ml-2">add</span>
                        إضافة خطة شهرية جديدة
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

