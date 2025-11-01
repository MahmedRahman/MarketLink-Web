@extends('layouts.admin')

@section('title', 'المنظمات')
@section('page-title', 'إدارة المنظمات')

@section('content')
<div class="space-y-6">
    <!-- Search -->
    <div class="card">
        <form method="GET" action="{{ route('admin.organizations.index') }}" class="flex gap-4">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="ابحث عن منظمة..." class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
            <button type="submit" class="btn-primary">بحث</button>
        </form>
    </div>
    
    <!-- Organizations Table -->
    <div class="card">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الاسم</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">البريد</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">عدد المستخدمين</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الاشتراك</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">إجراءات</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($organizations as $org)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $org->name }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-500">{{ $org->email ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-500">{{ $org->users->count() }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $activeSub = $org->subscription->first();
                                @endphp
                                @if($activeSub)
                                    <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">{{ $activeSub->plan }}</span>
                                @else
                                    <span class="text-gray-500">لا يوجد</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('admin.organizations.show', $org) }}" class="text-blue-600 hover:text-blue-500 ml-4">عرض</a>
                                <form method="POST" action="{{ route('admin.organizations.delete', $org) }}" class="inline" onsubmit="return confirm('هل أنت متأكد من حذف هذه المنظمة؟')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-500">حذف</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-gray-500">لا توجد منظمات</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="mt-4">
            {{ $organizations->links() }}
        </div>
    </div>
</div>
@endsection

