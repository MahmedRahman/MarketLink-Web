@extends('layouts.admin')

@section('title', 'خطط الاشتراك')
@section('page-title', 'إدارة خطط الاشتراك')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="card page-header rounded-2xl p-6 bg-gradient-to-r from-purple-600 to-blue-600 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl md:text-3xl font-bold mb-2">إدارة خطط الاشتراك</h2>
                <p class="text-purple-100">عرض وإدارة جميع خطط الاشتراك المتاحة</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.plans.create') }}" class="bg-white bg-opacity-20 hover:bg-opacity-30 px-6 py-3 rounded-xl transition-all duration-300 flex items-center">
                    <i class="fas fa-plus ml-2"></i>
                    إضافة خطة جديدة
                </a>
                <div class="hidden md:block">
                    <div class="w-20 h-20 bg-white bg-opacity-20 rounded-2xl flex items-center justify-center">
                        <i class="fas fa-crown text-4xl"></i>
                    </div>
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

    <!-- Error Message -->
    @if(session('error'))
        <div class="bg-red-50 border-l-4 border-red-500 text-red-800 px-4 py-3 rounded-lg flex items-center">
            <i class="fas fa-exclamation-circle ml-2"></i>
            {{ session('error') }}
        </div>
    @endif

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 md:gap-6">
        <div class="card rounded-2xl p-6 hover:shadow-xl transition-all duration-300 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 mb-1">إجمالي الخطط</p>
                    <p class="text-3xl font-bold text-gray-800">{{ number_format($stats['total']) }}</p>
                </div>
                <div class="w-14 h-14 bg-blue-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-list text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>
        <div class="card rounded-2xl p-6 hover:shadow-xl transition-all duration-300 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 mb-1">خطط نشطة</p>
                    <p class="text-3xl font-bold text-gray-800">{{ number_format($stats['active']) }}</p>
                </div>
                <div class="w-14 h-14 bg-green-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-check-circle text-green-600 text-xl"></i>
                </div>
            </div>
        </div>
        <div class="card rounded-2xl p-6 hover:shadow-xl transition-all duration-300 border-l-4 border-gray-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 mb-1">خطط غير نشطة</p>
                    <p class="text-3xl font-bold text-gray-800">{{ number_format($stats['inactive']) }}</p>
                </div>
                <div class="w-14 h-14 bg-gray-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-ban text-gray-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Plans Table -->
    <div class="card rounded-2xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                    <tr>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-gray-700 uppercase tracking-wider">الاسم</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-gray-700 uppercase tracking-wider">السعر</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-gray-700 uppercase tracking-wider">المدة</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-gray-700 uppercase tracking-wider">الميزات</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-gray-700 uppercase tracking-wider">الحالة</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-gray-700 uppercase tracking-wider">ترتيب العرض</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">إجراءات</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($plans as $plan)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-12 h-12 bg-gradient-to-br from-purple-400 to-purple-600 rounded-xl flex items-center justify-center text-white font-bold ml-3">
                                        {{ strtoupper(substr($plan->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="text-sm font-semibold text-gray-900">{{ $plan->name }}</div>
                                        @if($plan->description)
                                            <div class="text-xs text-gray-500 mt-1">{{ Str::limit($plan->description, 40) }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center ml-2">
                                        <i class="fas fa-money-bill-wave text-purple-600 text-sm"></i>
                                    </div>
                                    <div>
                                        <div class="text-lg font-bold text-purple-600">{{ number_format($plan->price_egp, 2) }}</div>
                                        <div class="text-xs text-gray-500">جنيه</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center ml-2">
                                        <i class="fas fa-calendar-alt text-blue-600 text-sm"></i>
                                    </div>
                                    <div>
                                        <div class="text-sm font-semibold text-gray-900">{{ $plan->duration_days }}</div>
                                        <div class="text-xs text-gray-500">يوم</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center ml-2">
                                        <i class="fas fa-star text-green-600 text-sm"></i>
                                    </div>
                                    <div>
                                        <div class="text-sm font-semibold text-gray-900">{{ $plan->features->count() }} ميزة</div>
                                        @if($plan->features->count() > 0)
                                            <div class="text-xs text-gray-500 mt-1">
                                                {{ Str::limit($plan->features->first()->feature_name, 25) }}
                                                @if($plan->features->count() > 1)
                                                    <span class="text-purple-600 font-medium">+{{ $plan->features->count() - 1 }}</span>
                                                @endif
                                            </div>
                                        @else
                                            <div class="text-xs text-gray-400">لا توجد ميزات</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($plan->is_active)
                                    <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <i class="fas fa-check-circle ml-1"></i>
                                        نشط
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        <i class="fas fa-ban ml-1"></i>
                                        غير نشط
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center justify-center">
                                    <div class="w-8 h-8 bg-indigo-100 rounded-lg flex items-center justify-center">
                                        <span class="text-sm font-bold text-indigo-600">{{ $plan->sort_order }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('admin.plans.show', $plan) }}" class="w-9 h-9 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition-colors flex items-center justify-center" title="عرض">
                                        <i class="fas fa-eye text-sm"></i>
                                    </a>
                                    <a href="{{ route('admin.plans.edit', $plan) }}" class="w-9 h-9 bg-purple-50 text-purple-600 rounded-lg hover:bg-purple-100 transition-colors flex items-center justify-center" title="تعديل">
                                        <i class="fas fa-edit text-sm"></i>
                                    </a>
                                    <form action="{{ route('admin.plans.destroy', $plan) }}" method="POST" class="inline" onsubmit="return confirm('هل أنت متأكد من حذف هذه الخطة؟');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="w-9 h-9 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition-colors flex items-center justify-center" title="حذف">
                                            <i class="fas fa-trash text-sm"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                        <i class="fas fa-crown text-gray-400 text-2xl"></i>
                                    </div>
                                    <p class="text-gray-500 font-medium text-lg">لا توجد خطط متاحة</p>
                                    <p class="text-sm text-gray-400 mt-1 mb-4">ابدأ بإضافة خطة جديدة</p>
                                    <a href="{{ route('admin.plans.create') }}" class="btn-primary px-6 py-3 rounded-xl flex items-center">
                                        <i class="fas fa-plus ml-2"></i>
                                        إضافة خطة جديدة
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($plans->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $plans->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

