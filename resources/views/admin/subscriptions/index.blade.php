@extends('layouts.admin')

@section('title', 'الاشتراكات')
@section('page-title', 'إدارة الاشتراكات')

@section('content')
<div class="space-y-6">
    <!-- Header with Stats -->
    <div class="card page-header rounded-2xl p-6 bg-gradient-to-r from-purple-600 to-blue-600 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl md:text-3xl font-bold mb-2">إدارة الاشتراكات</h2>
                <p class="text-purple-100">عرض وإدارة جميع اشتراكات المنظمات</p>
            </div>
            <div class="hidden md:block">
                <div class="w-20 h-20 bg-white bg-opacity-20 rounded-2xl flex items-center justify-center">
                    <i class="fas fa-credit-card text-4xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6">
        <div class="card rounded-2xl p-6 hover:shadow-xl transition-all duration-300 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 mb-1">إجمالي الاشتراكات</p>
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
                    <p class="text-sm font-medium text-gray-600 mb-1">اشتراكات نشطة</p>
                    <p class="text-3xl font-bold text-gray-800">{{ number_format($stats['active']) }}</p>
                </div>
                <div class="w-14 h-14 bg-green-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-check-circle text-green-600 text-xl"></i>
                </div>
            </div>
        </div>
        <div class="card rounded-2xl p-6 hover:shadow-xl transition-all duration-300 border-l-4 border-orange-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 mb-1">فترة Trial</p>
                    <p class="text-3xl font-bold text-gray-800">{{ number_format($stats['trial']) }}</p>
                </div>
                <div class="w-14 h-14 bg-orange-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-clock text-orange-600 text-xl"></i>
                </div>
            </div>
        </div>
        <div class="card rounded-2xl p-6 hover:shadow-xl transition-all duration-300 border-l-4 border-red-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 mb-1">اشتراكات منتهية</p>
                    <p class="text-3xl font-bold text-gray-800">{{ number_format($stats['expired']) }}</p>
                </div>
                <div class="w-14 h-14 bg-red-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-times-circle text-red-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card rounded-2xl p-6">
        <form method="GET" action="{{ route('admin.subscriptions.index') }}" class="flex flex-wrap gap-4 items-end">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium text-gray-700 mb-2">فلترة بالحالة</label>
                <select name="status" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    <option value="">جميع الحالات</option>
                    <option value="trial" {{ request('status') == 'trial' ? 'selected' : '' }}>Trial</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>نشط</option>
                    <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>منتهي</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>ملغي</option>
                </select>
            </div>
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium text-gray-700 mb-2">فلترة بالخطة</label>
                <select name="plan" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    <option value="">جميع الخطط</option>
                    <option value="trial" {{ request('plan') == 'trial' ? 'selected' : '' }}>Trial</option>
                    <option value="basic" {{ request('plan') == 'basic' ? 'selected' : '' }}>بيسك</option>
                    <option value="professional" {{ request('plan') == 'professional' ? 'selected' : '' }}>بروفيشنال</option>
                    <option value="enterprise" {{ request('plan') == 'enterprise' ? 'selected' : '' }}>إنتربرايز</option>
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="btn-primary px-6 py-3 rounded-xl flex items-center">
                    <i class="fas fa-filter ml-2"></i>
                    تصفية
                </button>
                @if(request('status') || request('plan'))
                    <a href="{{ route('admin.subscriptions.index') }}" class="px-6 py-3 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 flex items-center">
                        <i class="fas fa-times ml-2"></i>
                        إلغاء
                    </a>
                @endif
            </div>
        </form>
    </div>
    
    <!-- Subscriptions Table -->
    <div class="card rounded-2xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                    <tr>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-gray-700 uppercase tracking-wider">تاريخ الإنشاء</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-gray-700 uppercase tracking-wider">المنظمة</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-gray-700 uppercase tracking-wider">الخطة</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-gray-700 uppercase tracking-wider">الحالة</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-gray-700 uppercase tracking-wider">تاريخ البدء</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-gray-700 uppercase tracking-wider">تاريخ الانتهاء</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">إجراءات</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($subscriptions as $subscription)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-500">{{ $subscription->created_at->format('Y-m-d') }}</div>
                                <div class="text-xs text-gray-400">{{ $subscription->created_at->format('H:i') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-gradient-to-br from-blue-400 to-blue-600 rounded-xl flex items-center justify-center text-white font-bold ml-3">
                                        {{ strtoupper(substr($subscription->organization->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="text-sm font-semibold text-gray-900">{{ $subscription->organization->name }}</div>
                                        @if($subscription->organization->email)
                                            <div class="text-xs text-gray-500">{{ $subscription->organization->email }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $planColors = [
                                        'trial' => 'bg-orange-100 text-orange-800',
                                        'basic' => 'bg-blue-100 text-blue-800',
                                        'professional' => 'bg-purple-100 text-purple-800',
                                        'enterprise' => 'bg-indigo-100 text-indigo-800',
                                    ];
                                    $planNames = [
                                        'trial' => 'Trial',
                                        'basic' => 'بيسك',
                                        'professional' => 'بروفيشنال',
                                        'enterprise' => 'إنتربرايز',
                                    ];
                                    $planIcons = [
                                        'trial' => 'fa-clock',
                                        'basic' => 'fa-star',
                                        'professional' => 'fa-crown',
                                        'enterprise' => 'fa-gem',
                                    ];
                                @endphp
                                <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-medium {{ $planColors[$subscription->plan] ?? 'bg-gray-100 text-gray-800' }}">
                                    <i class="fas {{ $planIcons[$subscription->plan] ?? 'fa-tag' }} ml-1"></i>
                                    {{ $planNames[$subscription->plan] ?? $subscription->plan }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $statusColors = [
                                        'active' => 'bg-green-100 text-green-800',
                                        'trial' => 'bg-blue-100 text-blue-800',
                                        'expired' => 'bg-red-100 text-red-800',
                                        'cancelled' => 'bg-gray-100 text-gray-800',
                                    ];
                                    $statusNames = [
                                        'active' => 'نشط',
                                        'trial' => 'Trial',
                                        'expired' => 'منتهي',
                                        'cancelled' => 'ملغي',
                                    ];
                                    $statusIcons = [
                                        'active' => 'fa-check-circle',
                                        'trial' => 'fa-clock',
                                        'expired' => 'fa-times-circle',
                                        'cancelled' => 'fa-ban',
                                    ];
                                @endphp
                                <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-medium {{ $statusColors[$subscription->status] ?? 'bg-gray-100 text-gray-800' }}">
                                    <i class="fas {{ $statusIcons[$subscription->status] ?? 'fa-circle' }} ml-1"></i>
                                    {{ $statusNames[$subscription->status] ?? $subscription->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($subscription->starts_at)
                                    <div class="text-sm font-medium text-gray-900">{{ $subscription->starts_at->format('Y-m-d') }}</div>
                                    <div class="text-xs text-gray-500">{{ $subscription->starts_at->format('H:i') }}</div>
                                @else
                                    <span class="text-sm text-gray-400 italic">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($subscription->ends_at)
                                    <div class="text-sm font-medium text-gray-900">{{ $subscription->ends_at->format('Y-m-d') }}</div>
                                    <div class="text-xs text-gray-500">{{ $subscription->ends_at->format('H:i') }}</div>
                                    @if($subscription->ends_at->isPast() && $subscription->status == 'active')
                                        <div class="text-xs text-red-600 mt-1">منتهي</div>
                                    @elseif($subscription->ends_at->diffInDays(now()) <= 7 && $subscription->ends_at->isFuture())
                                        <div class="text-xs text-orange-600 mt-1">ينتهي قريباً</div>
                                    @endif
                                @else
                                    <span class="text-sm text-gray-400 italic">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <a href="{{ route('admin.subscriptions.show', $subscription) }}" class="inline-flex items-center px-4 py-2 bg-blue-50 text-blue-600 rounded-xl hover:bg-blue-100 transition-colors text-sm font-medium">
                                    <i class="fas fa-eye ml-2"></i>
                                    عرض التفاصيل
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                        <i class="fas fa-credit-card text-gray-400 text-2xl"></i>
                                    </div>
                                    <p class="text-gray-500 font-medium text-lg">لا توجد اشتراكات</p>
                                    <p class="text-sm text-gray-400 mt-1">لم يتم العثور على أي اشتراكات</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($subscriptions->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $subscriptions->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

