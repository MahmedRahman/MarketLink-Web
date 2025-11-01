@extends('layouts.admin')

@section('title', 'الاشتراكات')
@section('page-title', 'إدارة الاشتراكات')

@section('content')
<div class="space-y-6">
    <!-- Filters -->
    <div class="card">
        <form method="GET" action="{{ route('admin.subscriptions.index') }}" class="flex gap-4">
            <select name="status" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                <option value="">جميع الحالات</option>
                <option value="trial" {{ request('status') == 'trial' ? 'selected' : '' }}>Trial</option>
                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>نشط</option>
                <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>منتهي</option>
            </select>
            <select name="plan" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                <option value="">جميع الخطط</option>
                <option value="trial" {{ request('plan') == 'trial' ? 'selected' : '' }}>Trial</option>
                <option value="basic" {{ request('plan') == 'basic' ? 'selected' : '' }}>بيسك</option>
                <option value="professional" {{ request('plan') == 'professional' ? 'selected' : '' }}>بروفيشنال</option>
                <option value="enterprise" {{ request('plan') == 'enterprise' ? 'selected' : '' }}>إنتربرايز</option>
            </select>
            <button type="submit" class="btn-primary">تصفية</button>
        </form>
    </div>
    
    <!-- Subscriptions Table -->
    <div class="card">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">المنظمة</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الحالة</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الخطة</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">تاريخ البدء</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">تاريخ الانتهاء</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">إجراءات</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($subscriptions as $subscription)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $subscription->organization->name }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs rounded-full {{ $subscription->status == 'active' ? 'bg-green-100 text-green-800' : ($subscription->status == 'trial' ? 'bg-blue-100 text-blue-800' : 'bg-red-100 text-red-800') }}">
                                    {{ $subscription->status == 'active' ? 'نشط' : ($subscription->status == 'trial' ? 'Trial' : 'منتهي') }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-500">{{ $subscription->plan }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-500">{{ $subscription->starts_at ? $subscription->starts_at->format('Y-m-d') : '-' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-500">{{ $subscription->ends_at ? $subscription->ends_at->format('Y-m-d') : '-' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('admin.subscriptions.show', $subscription) }}" class="text-blue-600 hover:text-blue-500">عرض</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">لا توجد اشتراكات</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="mt-4">
            {{ $subscriptions->links() }}
        </div>
    </div>
</div>
@endsection

