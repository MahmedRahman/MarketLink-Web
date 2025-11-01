@extends('layouts.admin')

@section('title', 'تفاصيل طلب الاشتراك')
@section('page-title', 'تفاصيل طلب الاشتراك')

@section('content')
<div class="space-y-6">
    <div class="max-w-4xl mx-auto">
        <!-- Request Details Card -->
        <div class="card">
            <div class="flex justify-between items-start mb-6">
                <h2 class="text-2xl font-bold text-gray-800">تفاصيل طلب الاشتراك</h2>
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
                <span class="px-4 py-2 text-sm font-semibold rounded-full {{ $statusColors[$subscriptionRequest->status] }}">
                    {{ $statusText[$subscriptionRequest->status] }}
                </span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Organization Info -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">معلومات المنظمة</h3>
                    <div class="space-y-3">
                        <div>
                            <span class="text-sm text-gray-600">اسم المنظمة:</span>
                            <p class="font-semibold text-gray-800">{{ $subscriptionRequest->organization->name }}</p>
                        </div>
                        @if($subscriptionRequest->organization->email)
                            <div>
                                <span class="text-sm text-gray-600">البريد الإلكتروني:</span>
                                <p class="font-semibold text-gray-800">{{ $subscriptionRequest->organization->email }}</p>
                            </div>
                        @endif
                        @if($subscriptionRequest->organization->phone)
                            <div>
                                <span class="text-sm text-gray-600">الهاتف:</span>
                                <p class="font-semibold text-gray-800">{{ $subscriptionRequest->organization->phone }}</p>
                            </div>
                        @endif
                        <div>
                            <span class="text-sm text-gray-600">عدد المستخدمين:</span>
                            <p class="font-semibold text-gray-800">{{ $subscriptionRequest->organization->users->count() }} مستخدم</p>
                        </div>
                    </div>
                </div>

                <!-- Plan Info -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">معلومات الخطة</h3>
                    <div class="space-y-3">
                        <div>
                            <span class="text-sm text-gray-600">اسم الخطة:</span>
                            <p class="font-semibold text-gray-800">{{ $subscriptionRequest->plan->name }}</p>
                        </div>
                        <div>
                            <span class="text-sm text-gray-600">السعر:</span>
                            <p class="font-semibold text-purple-600 text-xl">{{ number_format($subscriptionRequest->plan->price_egp, 2) }} جنيه مصري</p>
                        </div>
                        <div>
                            <span class="text-sm text-gray-600">مدة الاشتراك:</span>
                            <p class="font-semibold text-gray-800">{{ $subscriptionRequest->plan->duration_days }} يوم</p>
                        </div>
                        @if($subscriptionRequest->plan->description)
                            <div>
                                <span class="text-sm text-gray-600">الوصف:</span>
                                <p class="font-semibold text-gray-800">{{ $subscriptionRequest->plan->description }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Plan Features -->
            @if($subscriptionRequest->plan->features->count() > 0)
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">ميزات الخطة</h3>
                    <ul class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        @foreach($subscriptionRequest->plan->features as $feature)
                            <li class="flex items-start">
                                <span class="material-icons text-green-500 ml-2 text-sm flex-shrink-0">check_circle</span>
                                <span class="text-gray-700">{{ $feature->feature_name }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Request Dates -->
            <div class="mt-6 pt-6 border-t border-gray-200">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <span class="text-sm text-gray-600">تاريخ الطلب:</span>
                        <p class="font-semibold text-gray-800">{{ $subscriptionRequest->created_at->format('Y-m-d H:i') }}</p>
                    </div>
                    @if($subscriptionRequest->approved_at)
                        <div>
                            <span class="text-sm text-gray-600">تاريخ الموافقة:</span>
                            <p class="font-semibold text-gray-800">{{ $subscriptionRequest->approved_at->format('Y-m-d H:i') }}</p>
                        </div>
                    @endif
                    @if($subscriptionRequest->rejected_at)
                        <div>
                            <span class="text-sm text-gray-600">تاريخ الرفض:</span>
                            <p class="font-semibold text-gray-800">{{ $subscriptionRequest->rejected_at->format('Y-m-d H:i') }}</p>
                        </div>
                    @endif
                    @if($subscriptionRequest->approver)
                        <div>
                            <span class="text-sm text-gray-600">تمت المعالجة بواسطة:</span>
                            <p class="font-semibold text-gray-800">{{ $subscriptionRequest->approver->name }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Notes -->
            @if($subscriptionRequest->notes)
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800 mb-2">ملاحظات</h3>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-gray-700">{{ $subscriptionRequest->notes }}</p>
                    </div>
                </div>
            @endif
        </div>

        <!-- Actions -->
        @if($subscriptionRequest->status === 'pending')
            <div class="card bg-gradient-to-r from-blue-50 to-purple-50 border border-blue-200">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">إجراءات</h3>
                
                <!-- Approve Form -->
                <form action="{{ route('admin.subscription-requests.approve', $subscriptionRequest) }}" method="POST" class="mb-4">
                    @csrf
                    <div class="mb-4">
                        <label for="approve_notes" class="block text-sm font-medium text-gray-700 mb-2">ملاحظات (اختياري)</label>
                        <textarea id="approve_notes" name="notes" rows="3" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                            placeholder="أضف ملاحظات عن الموافقة..."></textarea>
                    </div>
                    <button type="submit" class="bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 transition-colors flex items-center">
                        <span class="material-icons ml-2 text-sm">check_circle</span>
                        الموافقة على الطلب وإنشاء الاشتراك
                    </button>
                </form>

                <!-- Reject Form -->
                <form action="{{ route('admin.subscription-requests.reject', $subscriptionRequest) }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label for="reject_notes" class="block text-sm font-medium text-gray-700 mb-2">سبب الرفض *</label>
                        <textarea id="reject_notes" name="notes" rows="3" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                            placeholder="اذكر سبب رفض الطلب..."></textarea>
                    </div>
                    <button type="submit" class="bg-red-600 text-white px-6 py-3 rounded-lg hover:bg-red-700 transition-colors flex items-center">
                        <span class="material-icons ml-2 text-sm">cancel</span>
                        رفض الطلب
                    </button>
                </form>
            </div>
        @endif

        <!-- Back Button -->
        <div class="mt-6">
            <a href="{{ route('admin.subscription-requests.index') }}" class="text-purple-600 hover:underline flex items-center">
                <span class="material-icons ml-2">arrow_back</span>
                العودة لقائمة الطلبات
            </a>
        </div>
    </div>
</div>
@endsection

