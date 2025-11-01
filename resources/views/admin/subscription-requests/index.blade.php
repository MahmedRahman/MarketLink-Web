@extends('layouts.admin')

@section('title', 'طلبات الاشتراك')
@section('page-title', 'طلبات الاشتراك')

@section('content')
<div class="space-y-6">
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="card bg-blue-50 border border-blue-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-blue-600">قيد المراجعة</p>
                    <p class="text-3xl font-bold text-blue-800">{{ $stats['pending'] }}</p>
                </div>
                <span class="material-icons text-blue-600 text-4xl">pending_actions</span>
            </div>
        </div>
        <div class="card bg-green-50 border border-green-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-green-600">موافق عليها</p>
                    <p class="text-3xl font-bold text-green-800">{{ $stats['approved'] }}</p>
                </div>
                <span class="material-icons text-green-600 text-4xl">check_circle</span>
            </div>
        </div>
        <div class="card bg-red-50 border border-red-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-red-600">مرفوضة</p>
                    <p class="text-3xl font-bold text-red-800">{{ $stats['rejected'] }}</p>
                </div>
                <span class="material-icons text-red-600 text-4xl">cancel</span>
            </div>
        </div>
        <div class="card bg-gray-50 border border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">الإجمالي</p>
                    <p class="text-3xl font-bold text-gray-800">{{ $stats['total'] }}</p>
                </div>
                <span class="material-icons text-gray-600 text-4xl">list_alt</span>
            </div>
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg flex items-center">
            <span class="material-icons ml-2">check_circle</span>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg flex items-center">
            <span class="material-icons ml-2">error</span>
            {{ session('error') }}
        </div>
    @endif

    <!-- Filters -->
    <div class="card">
        <form method="GET" action="{{ route('admin.subscription-requests.index') }}" class="flex gap-4">
            <select name="status" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                <option value="all" {{ request('status') === 'all' || !request('status') ? 'selected' : '' }}>جميع الطلبات</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>قيد المراجعة</option>
                <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>موافق عليها</option>
                <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>مرفوضة</option>
            </select>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="ابحث عن منظمة..." class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
            <button type="submit" class="btn-primary">بحث</button>
            <a href="{{ route('admin.subscription-requests.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">إعادة تعيين</a>
        </form>
    </div>

    <!-- Requests Table -->
    <div class="card">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">المنظمة</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الخطة</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">السعر</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الحالة</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">تاريخ الطلب</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">إجراءات</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($requests as $request)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $request->organization->name }}</div>
                                <div class="text-xs text-gray-500">{{ $request->organization->email ?? 'لا يوجد بريد' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $request->plan->name }}</div>
                                <div class="text-xs text-gray-500">{{ $request->plan->duration_days }} يوم</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-bold text-purple-600">{{ number_format($request->plan->price_egp, 2) }} ج.م</div>
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
                                @endphp
                                <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $statusColors[$request->status] }}">
                                    {{ $statusText[$request->status] }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $request->created_at->format('Y-m-d H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('admin.subscription-requests.show', $request) }}" class="text-blue-600 hover:text-blue-900 ml-4">
                                    <span class="material-icons text-sm">visibility</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                لا توجد طلبات اشتراك
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($requests->hasPages())
            <div class="mt-4">
                {{ $requests->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

