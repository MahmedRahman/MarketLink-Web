@extends('layouts.admin')

@section('title', 'تفاصيل المنظمة')
@section('page-title', 'تفاصيل المنظمة: ' . $organization->name)

@section('content')
<div class="space-y-6">
    <div class="card">
        <h3 class="text-lg font-semibold mb-4">معلومات المنظمة</h3>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <p class="text-sm text-gray-600">الاسم</p>
                <p class="font-medium">{{ $organization->name }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">البريد الإلكتروني</p>
                <p class="font-medium">{{ $organization->email ?? '-' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">تاريخ الإنشاء</p>
                <p class="font-medium">{{ $organization->created_at->format('Y-m-d H:i') }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">عدد المستخدمين</p>
                <p class="font-medium">{{ $organization->users->count() }}</p>
            </div>
        </div>
    </div>
    
    <!-- Users -->
    <div class="card">
        <h3 class="text-lg font-semibold mb-4">المستخدمين</h3>
        <div class="space-y-3">
            @forelse($organization->users as $user)
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <div>
                        <p class="font-medium">{{ $user->name }}</p>
                        <p class="text-sm text-gray-600">{{ $user->email }}</p>
                    </div>
                    <a href="{{ route('admin.users.show', $user) }}" class="text-blue-600 hover:text-blue-500">عرض</a>
                </div>
            @empty
                <p class="text-gray-500">لا يوجد مستخدمين</p>
            @endforelse
        </div>
    </div>
    
    <!-- Subscriptions -->
    @if($organization->subscription->count() > 0)
        <div class="card">
            <h3 class="text-lg font-semibold mb-4">الاشتراكات</h3>
            <div class="space-y-3">
                @foreach($organization->subscription as $sub)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div>
                            <p class="font-medium">{{ $sub->plan }} - {{ $sub->status }}</p>
                            <p class="text-sm text-gray-600">{{ $sub->starts_at ? $sub->starts_at->format('Y-m-d') : '' }}</p>
                        </div>
                        <a href="{{ route('admin.subscriptions.show', $sub) }}" class="text-blue-600 hover:text-blue-500">عرض</a>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection

