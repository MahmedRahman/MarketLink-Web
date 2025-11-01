@extends('layouts.dashboard')

@section('title', 'حالة الاشتراك')
@section('page-title', 'حالة الاشتراك')
@section('page-description', 'عرض تفاصيل اشتراكك الحالي')

@section('content')
<div class="space-y-6">
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

    @if(!$organization)
        <div class="card">
            <div class="text-center py-12">
                <span class="material-icons text-gray-400 text-6xl mb-4">error_outline</span>
                <h3 class="text-xl font-semibold text-gray-800 mb-2">لا توجد منظمة مرتبطة</h3>
                <p class="text-gray-600 mb-6">يرجى التواصل مع الدعم الفني</p>
            </div>
        </div>
    @elseif($pendingRequest)
        <!-- Pending Request -->
        <div class="card bg-gradient-to-r from-yellow-50 to-orange-50 border-2 border-yellow-200">
            <div class="text-center py-12">
                <span class="material-icons text-yellow-600 text-6xl mb-4">pending_actions</span>
                <h3 class="text-xl font-semibold text-gray-800 mb-2">طلب اشتراك قيد المراجعة</h3>
                <p class="text-gray-600 mb-4">لديك طلب اشتراك في الخطة <strong>{{ $pendingRequest->plan->name }}</strong> قيد المراجعة</p>
                <div class="bg-white rounded-lg p-4 mb-6 inline-block">
                    <p class="text-sm text-gray-600 mb-1">تاريخ الطلب:</p>
                    <p class="font-semibold text-gray-800">{{ $pendingRequest->created_at->format('Y-m-d H:i') }}</p>
                </div>
                <p class="text-gray-600">سيتم إشعارك عند مراجعة الطلب من قبل الإدارة</p>
            </div>
        </div>
    @elseif(!$subscription)
        <!-- No Subscription -->
        <div class="card">
            <div class="text-center py-12">
                <span class="material-icons text-gray-400 text-6xl mb-4">card_membership</span>
                <h3 class="text-xl font-semibold text-gray-800 mb-2">لا يوجد اشتراك نشط</h3>
                <p class="text-gray-600 mb-6">قم بالاشتراك في إحدى خططنا للاستمرار في استخدام النظام</p>
                <a href="{{ route('subscription.plans') }}" class="btn-primary text-white px-6 py-3 rounded-xl inline-flex items-center">
                    <span class="material-icons ml-2 text-sm">payment</span>
                    عرض الخطط والاشتراك
                </a>
            </div>
        </div>
    @else
        <!-- Subscription Details -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Subscription Card -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Subscription Status Card -->
                <div class="card">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-xl font-bold text-gray-800">تفاصيل الاشتراك</h3>
                        @php
                            $statusColors = [
                                'trial' => 'bg-blue-100 text-blue-800',
                                'active' => 'bg-green-100 text-green-800',
                                'expired' => 'bg-red-100 text-red-800',
                                'cancelled' => 'bg-gray-100 text-gray-800',
                            ];
                            $statusText = [
                                'trial' => 'تجربة مجانية',
                                'active' => 'نشط',
                                'expired' => 'منتهي',
                                'cancelled' => 'ملغي',
                            ];
                        @endphp
                        <span class="px-3 py-1 text-sm font-semibold rounded-full {{ $statusColors[$subscription->status] ?? 'bg-gray-100 text-gray-800' }}">
                            {{ $statusText[$subscription->status] ?? $subscription->status }}
                        </span>
                    </div>

                    <div class="space-y-4">
                        @if($subscription->planModel)
                            <div class="flex items-center justify-between py-3 border-b border-gray-200">
                                <span class="text-gray-600">الخطة:</span>
                                <span class="font-semibold text-gray-800">{{ $subscription->planModel->name }}</span>
                            </div>
                            <div class="flex items-center justify-between py-3 border-b border-gray-200">
                                <span class="text-gray-600">السعر:</span>
                                <span class="font-semibold text-gray-800">{{ number_format($subscription->planModel->price_egp, 2) }} جنيه مصري</span>
                            </div>
                        @else
                            <div class="flex items-center justify-between py-3 border-b border-gray-200">
                                <span class="text-gray-600">الخطة:</span>
                                <span class="font-semibold text-gray-800">
                                    {{ $subscription->plan === 'basic' ? 'بيسك' : ($subscription->plan === 'professional' ? 'بروفيشنال' : ($subscription->plan === 'enterprise' ? 'إنتربرايز' : 'تجربة')) }}
                                </span>
                            </div>
                        @endif

                        @if($subscription->status === 'trial' && $subscription->trial_ends_at)
                            <div class="flex items-center justify-between py-3 border-b border-gray-200">
                                <span class="text-gray-600">نهاية فترة التجربة:</span>
                                <span class="font-semibold text-gray-800">{{ $subscription->trial_ends_at->format('Y-m-d') }}</span>
                            </div>
                            <div class="flex items-center justify-between py-3 border-b border-gray-200">
                                <span class="text-gray-600">الأيام المتبقية:</span>
                                <span class="font-semibold text-blue-600">{{ $subscription->remaining_trial_days }} يوم</span>
                            </div>
                        @endif

                        @if($subscription->starts_at)
                            <div class="flex items-center justify-between py-3 border-b border-gray-200">
                                <span class="text-gray-600">تاريخ البدء:</span>
                                <span class="font-semibold text-gray-800">{{ $subscription->starts_at->format('Y-m-d') }}</span>
                            </div>
                        @endif

                        @if($subscription->ends_at)
                            <div class="flex items-center justify-between py-3 border-b border-gray-200">
                                <span class="text-gray-600">تاريخ الانتهاء:</span>
                                <span class="font-semibold text-gray-800">{{ $subscription->ends_at->format('Y-m-d') }}</span>
                            </div>
                        @endif

                        @if($subscription->cancelled_at)
                            <div class="flex items-center justify-between py-3 border-b border-gray-200">
                                <span class="text-gray-600">تاريخ الإلغاء:</span>
                                <span class="font-semibold text-gray-800">{{ $subscription->cancelled_at->format('Y-m-d') }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Organization Info -->
                <div class="card">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">معلومات المنظمة</h3>
                    <div class="space-y-3">
                        <div>
                            <span class="text-sm text-gray-600">اسم المنظمة:</span>
                            <p class="font-semibold text-gray-800">{{ $organization->name }}</p>
                        </div>
                        @if($organization->email)
                            <div>
                                <span class="text-sm text-gray-600">البريد الإلكتروني:</span>
                                <p class="font-semibold text-gray-800">{{ $organization->email }}</p>
                            </div>
                        @endif
                        @if($organization->phone)
                            <div>
                                <span class="text-sm text-gray-600">الهاتف:</span>
                                <p class="font-semibold text-gray-800">{{ $organization->phone }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Actions Sidebar -->
            <div class="space-y-6">
                @if($subscription->status === 'trial')
                    <div class="card bg-gradient-to-br from-blue-50 to-purple-50 border border-blue-200">
                        <div class="text-center">
                            <span class="material-icons text-blue-600 text-4xl mb-4">timer</span>
                            <h4 class="font-semibold text-gray-800 mb-2">فترة التجربة المجانية</h4>
                            <p class="text-sm text-gray-600 mb-4">باقي {{ $subscription->remaining_trial_days }} يوم</p>
                            <a href="{{ route('subscription.plans') }}" class="btn-primary text-white w-full py-2 rounded-lg inline-block text-center">
                                اشترك الآن
                            </a>
                        </div>
                    </div>
                @endif

                @if($subscription->status === 'active')
                    <div class="card bg-gradient-to-br from-green-50 to-emerald-50 border border-green-200">
                        <div class="text-center">
                            <span class="material-icons text-green-600 text-4xl mb-4">check_circle</span>
                            <h4 class="font-semibold text-gray-800 mb-2">اشتراك نشط</h4>
                            <p class="text-sm text-gray-600 mb-4">اشتراكك يعمل بشكل طبيعي</p>
                        </div>
                    </div>
                @endif

                @if($subscription->status === 'expired')
                    <div class="card bg-gradient-to-br from-red-50 to-pink-50 border border-red-200">
                        <div class="text-center">
                            <span class="material-icons text-red-600 text-4xl mb-4">error</span>
                            <h4 class="font-semibold text-gray-800 mb-2">اشتراك منتهي</h4>
                            <p class="text-sm text-gray-600 mb-4">يرجى تجديد اشتراكك للاستمرار</p>
                            <a href="{{ route('subscription.plans') }}" class="bg-red-600 text-white w-full py-2 rounded-lg inline-block text-center hover:bg-red-700">
                                تجديد الاشتراك
                            </a>
                        </div>
                    </div>
                @endif

                <!-- Quick Actions -->
                <div class="card">
                    <h4 class="font-semibold text-gray-800 mb-4">إجراءات سريعة</h4>
                    <div class="space-y-2">
                        <a href="{{ route('subscription.plans') }}" class="block w-full text-center px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                            عرض الخطط
                        </a>
                        <a href="{{ route('dashboard') }}" class="block w-full text-center px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                            العودة للوحة التحكم
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

