@extends('layouts.admin')

@section('title', 'تفاصيل المستخدم')
@section('page-title', 'تفاصيل المستخدم: ' . $user->name)

@section('content')
<div class="space-y-6">
    <div class="card">
        <h3 class="text-lg font-semibold mb-4">المعلومات الأساسية</h3>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <p class="text-sm text-gray-600">الاسم</p>
                <p class="font-medium">{{ $user->name }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">البريد الإلكتروني</p>
                <p class="font-medium">{{ $user->email }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">تاريخ التسجيل</p>
                <p class="font-medium">{{ $user->created_at->format('Y-m-d H:i') }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">الحالة</p>
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
                <span class="inline-block px-3 py-1 text-sm rounded-full {{ $statusColors[$currentStatus] ?? 'bg-gray-100 text-gray-800' }}">
                    {{ $statusText[$currentStatus] ?? $currentStatus }}
                </span>
            </div>
            @if($user->organization)
                <div>
                    <p class="text-sm text-gray-600">المنظمة</p>
                    <a href="{{ route('admin.organizations.show', $user->organization) }}" class="font-medium text-blue-600 hover:text-blue-500">{{ $user->organization->name }}</a>
                </div>
            @endif
        </div>
    </div>
    
    <!-- Change Status Card -->
    <div class="card">
        <h3 class="text-lg font-semibold mb-4">تغيير الحالة</h3>
        <form method="POST" action="{{ route('admin.users.updateStatus', $user) }}">
            @csrf
            @method('PATCH')
            <div class="flex items-center gap-4">
                <select name="status" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    <option value="active" {{ $currentStatus == 'active' ? 'selected' : '' }}>نشط</option>
                    <option value="inactive" {{ $currentStatus == 'inactive' ? 'selected' : '' }}>غير نشط</option>
                    <option value="suspended" {{ $currentStatus == 'suspended' ? 'selected' : '' }}>موقوف</option>
                </select>
                <button type="submit" class="btn-primary">تحديث الحالة</button>
            </div>
        </form>
    </div>
    
    @if($user->organization && $user->organization->activeSubscription())
        <div class="card">
            <h3 class="text-lg font-semibold mb-4">الاشتراك</h3>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-600">الحالة</p>
                    <p class="font-medium">{{ $user->organization->activeSubscription()->status }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">الخطة</p>
                    <p class="font-medium">{{ $user->organization->activeSubscription()->plan }}</p>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

