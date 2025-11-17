@extends('layouts.admin')

@section('title', 'المستخدمين')
@section('page-title', 'إدارة المستخدمين')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="card page-header rounded-2xl p-6 bg-gradient-to-r from-indigo-600 to-purple-600 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl md:text-3xl font-bold mb-2">إدارة المستخدمين</h2>
                <p class="text-indigo-100">عرض وإدارة جميع المستخدمين في النظام</p>
            </div>
            <div class="hidden md:block">
                <div class="w-20 h-20 bg-white bg-opacity-20 rounded-2xl flex items-center justify-center">
                    <i class="fas fa-users text-4xl"></i>
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

    <!-- Search and Filter -->
    <div class="card rounded-2xl p-6">
        <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-col md:flex-row gap-4">
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-2">البحث</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="ابحث بالاسم أو البريد الإلكتروني..." class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent">
            </div>
            <div class="md:w-64">
                <label class="block text-sm font-medium text-gray-700 mb-2">نوع المستخدم</label>
                <select name="user_type" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    <option value="">جميع المستخدمين</option>
                    <option value="admin" {{ request('user_type') == 'admin' ? 'selected' : '' }}>مديرين</option>
                    <option value="organization" {{ request('user_type') == 'organization' ? 'selected' : '' }}>مستخدمي المنظمات</option>
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="btn-primary px-6 py-3 rounded-xl flex items-center">
                    <i class="fas fa-search ml-2"></i>
                    بحث
                </button>
                @if(request('search') || request('user_type'))
                    <a href="{{ route('admin.users.index') }}" class="px-6 py-3 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 transition-colors flex items-center">
                        <i class="fas fa-times ml-2"></i>
                        إلغاء
                    </a>
                @endif
            </div>
        </form>
    </div>
    
    <!-- Users Table -->
    <div class="card rounded-2xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                    <tr>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-gray-700 uppercase tracking-wider">تاريخ التسجيل</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-gray-700 uppercase tracking-wider">الاسم</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-gray-700 uppercase tracking-wider">البريد</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-gray-700 uppercase tracking-wider">نوع المستخدم</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-gray-700 uppercase tracking-wider">عدد العملاء</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-gray-700 uppercase tracking-wider">عدد الموظفين</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-gray-700 uppercase tracking-wider">عدد المشاريع</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-gray-700 uppercase tracking-wider">الحالة</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">إجراءات</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($users as $user)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center ml-3">
                                        <i class="fas fa-calendar-alt text-indigo-600 text-sm"></i>
                                    </div>
                                    <div>
                                        <div class="text-sm font-semibold text-gray-900">{{ $user->created_at->format('Y-m-d') }}</div>
                                        <div class="text-xs text-gray-500">{{ $user->created_at->format('H:i') }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-12 h-12 bg-gradient-to-br from-indigo-400 to-purple-600 rounded-xl flex items-center justify-center text-white font-bold ml-3">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <div class="text-sm font-semibold text-gray-900">{{ $user->name }}</div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center ml-2">
                                        <i class="fas fa-envelope text-blue-600 text-sm"></i>
                                    </div>
                                    <div class="text-sm text-gray-900">{{ $user->email }}</div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($user->is_admin)
                                    <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                        <i class="fas fa-user-shield ml-1"></i>
                                        مدير
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        <i class="fas fa-building ml-1"></i>
                                        مستخدم منظمة
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($user->organization)
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center ml-2">
                                            <i class="fas fa-users text-blue-600 text-sm"></i>
                                        </div>
                                        <span class="text-sm font-semibold text-gray-900">{{ $user->organization->clients->count() ?? 0 }}</span>
                                    </div>
                                @else
                                    <span class="text-sm text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($user->organization)
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center ml-2">
                                            <i class="fas fa-user-tie text-green-600 text-sm"></i>
                                        </div>
                                        <span class="text-sm font-semibold text-gray-900">{{ $user->organization->employees->count() ?? 0 }}</span>
                                    </div>
                                @else
                                    <span class="text-sm text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($user->organization)
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center ml-2">
                                            <i class="fas fa-folder text-purple-600 text-sm"></i>
                                        </div>
                                        <span class="text-sm font-semibold text-gray-900">{{ $user->organization->projects->count() ?? 0 }}</span>
                                    </div>
                                @else
                                    <span class="text-sm text-gray-400">-</span>
                                @endif
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
                                <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-medium {{ $statusColors[$currentStatus] ?? 'bg-gray-100 text-gray-800' }}">
                                    <i class="fas fa-circle text-[8px] ml-1"></i>
                                    {{ $statusText[$currentStatus] ?? $currentStatus }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <div class="flex items-center justify-center gap-2">
                                    @if(!$user->is_admin)
                                        <form method="POST" action="{{ route('admin.users.impersonate', $user) }}" class="inline" onsubmit="return confirm('هل أنت متأكد من الدخول كالمستخدم: {{ $user->name }}؟');">
                                            @csrf
                                            <button type="submit" class="w-9 h-9 bg-green-50 text-green-600 rounded-lg hover:bg-green-100 transition-colors flex items-center justify-center" title="الدخول كالمستخدم">
                                                <i class="fas fa-sign-in-alt text-sm"></i>
                                            </button>
                                        </form>
                                    @endif
                                    <form method="POST" action="{{ route('admin.users.updateStatus', $user) }}" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <select name="status" onchange="this.form.submit()" class="text-xs px-2 py-1.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent {{ $statusColors[$currentStatus] ?? '' }}">
                                            <option value="active" {{ $currentStatus == 'active' ? 'selected' : '' }}>نشط</option>
                                            <option value="inactive" {{ $currentStatus == 'inactive' ? 'selected' : '' }}>غير نشط</option>
                                            <option value="suspended" {{ $currentStatus == 'suspended' ? 'selected' : '' }}>موقوف</option>
                                        </select>
                                    </form>
                                    <a href="{{ route('admin.users.show', $user) }}" class="w-9 h-9 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition-colors flex items-center justify-center" title="عرض">
                                        <i class="fas fa-eye text-sm"></i>
                                    </a>
                                    @if(!$user->is_admin)
                                        <form method="POST" action="{{ route('admin.users.delete', $user) }}" class="inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا المستخدم؟');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="w-9 h-9 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition-colors flex items-center justify-center" title="حذف">
                                                <i class="fas fa-trash text-sm"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                        <i class="fas fa-users text-gray-400 text-2xl"></i>
                                    </div>
                                    <p class="text-gray-500 font-medium text-lg">لا يوجد مستخدمين</p>
                                    <p class="text-sm text-gray-400 mt-1">لم يتم العثور على أي مستخدمين</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($users->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
