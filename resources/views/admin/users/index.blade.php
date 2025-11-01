@extends('layouts.admin')

@section('title', 'المستخدمين')
@section('page-title', 'إدارة المستخدمين')

@section('content')
<div class="space-y-6">
    <!-- Search -->
    <div class="card">
        <form method="GET" action="{{ route('admin.users.index') }}" class="flex gap-4">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="ابحث عن مستخدم..." class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
            <button type="submit" class="btn-primary">بحث</button>
        </form>
    </div>
    
    <!-- Users Table -->
    <div class="card">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الاسم</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">البريد</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">المنظمة</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الحالة</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">تاريخ التسجيل</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">إجراءات</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($users as $user)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-500">{{ $user->email }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-500">{{ $user->organization->name ?? 'لا يوجد' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $statusColors = [
                                        'active' => 'bg-green-100 text-green-800',
                                        'inactive' => 'bg-gray-100 text-gray-800',
                                        'suspended' => 'bg-red-100 text-red-800',
                                    ];
                                    $statusText = [
                                        'active' => 'نشط',
                                        'inactive' => 'غير نشط',
                                        'suspended' => 'موقوف',
                                    ];
                                    $currentStatus = $user->status ?? 'active';
                                @endphp
                                <span class="px-2 py-1 text-xs rounded-full {{ $statusColors[$currentStatus] ?? 'bg-gray-100 text-gray-800' }}">
                                    {{ $statusText[$currentStatus] ?? $currentStatus }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-500">{{ $user->created_at->format('Y-m-d') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center gap-2">
                                    <!-- Change Status Form -->
                                    <form method="POST" action="{{ route('admin.users.updateStatus', $user) }}" class="inline" onchange="this.submit()">
                                        @csrf
                                        @method('PATCH')
                                        <select name="status" class="text-xs px-2 py-1 border border-gray-300 rounded focus:ring-2 focus:ring-purple-500 focus:border-transparent {{ $statusColors[$currentStatus] ?? '' }}">
                                            <option value="active" {{ $currentStatus == 'active' ? 'selected' : '' }}>نشط</option>
                                            <option value="inactive" {{ $currentStatus == 'inactive' ? 'selected' : '' }}>غير نشط</option>
                                            <option value="suspended" {{ $currentStatus == 'suspended' ? 'selected' : '' }}>موقوف</option>
                                        </select>
                                    </form>
                                    <a href="{{ route('admin.users.show', $user) }}" class="text-blue-600 hover:text-blue-500">عرض</a>
                                    <form method="POST" action="{{ route('admin.users.delete', $user) }}" class="inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا المستخدم؟')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-500">حذف</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">لا يوجد مستخدمين</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="mt-4">
            {{ $users->links() }}
        </div>
    </div>
</div>
@endsection

