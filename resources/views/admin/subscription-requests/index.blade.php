@extends('layouts.admin')

@section('title', 'طلبات الاشتراك')
@section('page-title', 'طلبات الاشتراك')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="card page-header rounded-2xl p-6 bg-gradient-to-r from-orange-600 to-red-600 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl md:text-3xl font-bold mb-2">طلبات الاشتراك</h2>
                <p class="text-orange-100">عرض وإدارة طلبات الاشتراك من المنظمات</p>
            </div>
            <div class="hidden md:block">
                <div class="w-20 h-20 bg-white bg-opacity-20 rounded-2xl flex items-center justify-center">
                    <i class="fas fa-file-invoice text-4xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6">
        <div class="card rounded-2xl p-6 hover:shadow-xl transition-all duration-300 border-l-4 border-yellow-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 mb-1">قيد المراجعة</p>
                    <p class="text-3xl font-bold text-gray-800">{{ number_format($stats['pending']) }}</p>
                </div>
                <div class="w-14 h-14 bg-yellow-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-clock text-yellow-600 text-xl"></i>
                </div>
            </div>
        </div>
        <div class="card rounded-2xl p-6 hover:shadow-xl transition-all duration-300 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 mb-1">موافق عليها</p>
                    <p class="text-3xl font-bold text-gray-800">{{ number_format($stats['approved']) }}</p>
                </div>
                <div class="w-14 h-14 bg-green-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-check-circle text-green-600 text-xl"></i>
                </div>
            </div>
        </div>
        <div class="card rounded-2xl p-6 hover:shadow-xl transition-all duration-300 border-l-4 border-red-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 mb-1">مرفوضة</p>
                    <p class="text-3xl font-bold text-gray-800">{{ number_format($stats['rejected']) }}</p>
                </div>
                <div class="w-14 h-14 bg-red-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-times-circle text-red-600 text-xl"></i>
                </div>
            </div>
        </div>
        <div class="card rounded-2xl p-6 hover:shadow-xl transition-all duration-300 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 mb-1">الإجمالي</p>
                    <p class="text-3xl font-bold text-gray-800">{{ number_format($stats['total']) }}</p>
                </div>
                <div class="w-14 h-14 bg-blue-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-list text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-500 text-green-800 px-4 py-3 rounded-lg flex items-center">
            <i class="fas fa-check-circle ml-2"></i>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 border-l-4 border-red-500 text-red-800 px-4 py-3 rounded-lg flex items-center">
            <i class="fas fa-exclamation-circle ml-2"></i>
            {{ session('error') }}
        </div>
    @endif

    <!-- Filters -->
    <div class="card rounded-2xl p-6">
        <form method="GET" action="{{ route('admin.subscription-requests.index') }}" class="flex flex-wrap gap-4 items-end">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium text-gray-700 mb-2">فلترة بالحالة</label>
                <select name="status" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    <option value="all" {{ request('status') === 'all' || !request('status') ? 'selected' : '' }}>جميع الطلبات</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>قيد المراجعة</option>
                    <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>موافق عليها</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>مرفوضة</option>
                </select>
            </div>
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium text-gray-700 mb-2">بحث عن منظمة</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="ابحث عن منظمة..." class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent">
            </div>
            <div class="flex gap-2">
                <button type="submit" class="btn-primary px-6 py-3 rounded-xl flex items-center">
                    <i class="fas fa-filter ml-2"></i>
                    تصفية
                </button>
                @if(request('status') || request('search'))
                    <a href="{{ route('admin.subscription-requests.index') }}" class="px-6 py-3 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 flex items-center">
                        <i class="fas fa-times ml-2"></i>
                        إلغاء
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Requests Table -->
    <div class="card rounded-2xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                    <tr>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-gray-700 uppercase tracking-wider">تاريخ الطلب</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-gray-700 uppercase tracking-wider">المنظمة</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-gray-700 uppercase tracking-wider">الخطة</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-gray-700 uppercase tracking-wider">السعر</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-gray-700 uppercase tracking-wider">الحالة</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">إجراءات</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($requests as $request)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-500">{{ $request->created_at->format('Y-m-d') }}</div>
                                <div class="text-xs text-gray-400">{{ $request->created_at->format('H:i') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-gradient-to-br from-blue-400 to-blue-600 rounded-xl flex items-center justify-center text-white font-bold ml-3">
                                        {{ strtoupper(substr($request->organization->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="text-sm font-semibold text-gray-900">{{ $request->organization->name }}</div>
                                        @if($request->organization->email)
                                            <div class="text-xs text-gray-500">{{ $request->organization->email }}</div>
                                        @else
                                            <div class="text-xs text-gray-400 italic">لا يوجد بريد</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center ml-2">
                                        <i class="fas fa-crown text-purple-600 text-sm"></i>
                                    </div>
                                    <div>
                                        <div class="text-sm font-semibold text-gray-900">{{ $request->plan->name }}</div>
                                        <div class="text-xs text-gray-500">{{ $request->plan->duration_days }} يوم</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center ml-2">
                                        <i class="fas fa-money-bill-wave text-green-600 text-sm"></i>
                                    </div>
                                    <div>
                                        <div class="text-lg font-bold text-purple-600">{{ number_format($request->plan->price_egp, 2) }}</div>
                                        <div class="text-xs text-gray-500">جنيه</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $statusColors = [
                                        'pending' => 'bg-yellow-100 text-yellow-800',
                                        'approved' => 'bg-green-100 text-green-800',
                                        'rejected' => 'bg-red-100 text-red-800',
                                    ];
                                    $statusText = [
                                        'pending' => 'قيد المراجعة',
                                        'approved' => 'موافق عليها',
                                        'rejected' => 'مرفوضة',
                                    ];
                                    $statusIcons = [
                                        'pending' => 'fa-clock',
                                        'approved' => 'fa-check-circle',
                                        'rejected' => 'fa-times-circle',
                                    ];
                                @endphp
                                <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-medium {{ $statusColors[$request->status] }}">
                                    <i class="fas {{ $statusIcons[$request->status] }} ml-1"></i>
                                    {{ $statusText[$request->status] }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <a href="{{ route('admin.subscription-requests.show', $request) }}" class="inline-flex items-center px-4 py-2 bg-blue-50 text-blue-600 rounded-xl hover:bg-blue-100 transition-colors text-sm font-medium">
                                    <i class="fas fa-eye ml-2"></i>
                                    عرض التفاصيل
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                        <i class="fas fa-file-invoice text-gray-400 text-2xl"></i>
                                    </div>
                                    <p class="text-gray-500 font-medium text-lg">لا توجد طلبات اشتراك</p>
                                    <p class="text-sm text-gray-400 mt-1">لم يتم العثور على أي طلبات</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($requests->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $requests->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

