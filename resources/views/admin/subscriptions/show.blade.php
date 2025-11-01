@extends('layouts.admin')

@section('title', 'تفاصيل الاشتراك')
@section('page-title', 'تفاصيل الاشتراك')

@section('content')
<div class="space-y-6">
    <div class="card">
        <h3 class="text-lg font-semibold mb-4">معلومات الاشتراك</h3>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <p class="text-sm text-gray-600">المنظمة</p>
                <p class="font-medium">{{ $subscription->organization->name }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">الحالة</p>
                <p class="font-medium">{{ $subscription->status }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">الخطة</p>
                <p class="font-medium">{{ $subscription->plan }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">تاريخ البدء</p>
                <p class="font-medium">{{ $subscription->starts_at ? $subscription->starts_at->format('Y-m-d H:i') : '-' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">تاريخ الانتهاء</p>
                <p class="font-medium">{{ $subscription->ends_at ? $subscription->ends_at->format('Y-m-d H:i') : '-' }}</p>
            </div>
            @if($subscription->trial_ends_at)
                <div>
                    <p class="text-sm text-gray-600">نهاية فترة Trial</p>
                    <p class="font-medium">{{ $subscription->trial_ends_at->format('Y-m-d H:i') }}</p>
                </div>
            @endif
        </div>
    </div>
    
    <!-- Update Form -->
    <div class="card">
        <h3 class="text-lg font-semibold mb-4">تحديث الاشتراك</h3>
        <form method="POST" action="{{ route('admin.subscriptions.update', $subscription) }}">
            @csrf
            @method('PATCH')
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">الحالة</label>
                    <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                        <option value="trial" {{ $subscription->status == 'trial' ? 'selected' : '' }}>Trial</option>
                        <option value="active" {{ $subscription->status == 'active' ? 'selected' : '' }}>نشط</option>
                        <option value="expired" {{ $subscription->status == 'expired' ? 'selected' : '' }}>منتهي</option>
                        <option value="cancelled" {{ $subscription->status == 'cancelled' ? 'selected' : '' }}>ملغي</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">الخطة</label>
                    <select name="plan" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                        <option value="trial" {{ $subscription->plan == 'trial' ? 'selected' : '' }}>Trial</option>
                        <option value="basic" {{ $subscription->plan == 'basic' ? 'selected' : '' }}>بيسك</option>
                        <option value="professional" {{ $subscription->plan == 'professional' ? 'selected' : '' }}>بروفيشنال</option>
                        <option value="enterprise" {{ $subscription->plan == 'enterprise' ? 'selected' : '' }}>إنتربرايز</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">تاريخ الانتهاء</label>
                    <input type="date" name="ends_at" value="{{ $subscription->ends_at ? $subscription->ends_at->format('Y-m-d') : '' }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                </div>
            </div>
            <button type="submit" class="mt-4 btn-primary">تحديث</button>
        </form>
    </div>
</div>
@endsection

