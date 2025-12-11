@extends('layouts.dashboard')

@section('title', 'تفاصيل العقد')
@section('page-title', 'تفاصيل العقد')
@section('page-description', 'عرض تفاصيل العقد')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="card page-header rounded-2xl p-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <div class="w-12 h-12 logo-gradient rounded-2xl flex items-center justify-center shadow-lg icon-spacing ml-3">
                    <i class="fas fa-file-contract text-white text-xl"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">تفاصيل العقد</h2>
                    <p class="text-gray-600">عرض تفاصيل العقد الخاص بـ {{ $contract->employee->name ?? 'الموظف' }}</p>
                </div>
            </div>
            <div class="flex items-center space-x-3 rtl:space-x-reverse">
                <a href="{{ route('contracts.edit', $contract) }}" class="btn-primary text-white px-6 py-3 rounded-xl flex items-center hover:no-underline">
                    <i class="fas fa-edit text-sm ml-2"></i>
                    تعديل
                </a>
                <a href="{{ route('contracts.index') }}" class="flex items-center px-4 py-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-xl transition-colors icon-spacing">
                    العودة للقائمة
                </a>
            </div>
        </div>
    </div>

    <!-- Contract Details -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Information -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Basic Information -->
            <div class="card rounded-2xl p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">المعلومات الأساسية</h3>
                <div class="space-y-4">
                    <div>
                        <label class="text-sm font-medium text-gray-600">الموظف</label>
                        <p class="text-lg text-gray-900 mt-1">
                            {{ $contract->employee->name ?? 'غير محدد' }}
                            @if($contract->employee && $contract->employee->email)
                                <span class="text-gray-500 text-sm">({{ $contract->employee->email }})</span>
                            @endif
                        </p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-600">نظام التعامل</label>
                        <p class="text-lg text-gray-900 mt-1">
                            <span class="px-3 py-1 text-sm font-medium rounded-full bg-blue-100 text-blue-800">
                                {{ $contract->payment_type_label }}
                            </span>
                        </p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-600">المبلغ المتفق عليه</label>
                        <p class="text-2xl font-bold text-gray-900 mt-1">
                            {{ number_format($contract->agreed_amount, 2) }} ج.م
                        </p>
                    </div>
                </div>
            </div>

            <!-- Dates Information -->
            <div class="card rounded-2xl p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">مدة العقد</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm font-medium text-gray-600">تاريخ البداية</label>
                        <p class="text-lg text-gray-900 mt-1">{{ $contract->start_date->format('Y-m-d') }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-600">تاريخ النهاية</label>
                        <p class="text-lg text-gray-900 mt-1">{{ $contract->end_date->format('Y-m-d') }}</p>
                    </div>
                    <div class="md:col-span-2">
                        <label class="text-sm font-medium text-gray-600">مدة العقد</label>
                        <p class="text-lg text-gray-900 mt-1">
                            {{ $contract->start_date->diffInDays($contract->end_date) }} يوم
                            ({{ $contract->start_date->diffInMonths($contract->end_date) }} شهر تقريباً)
                        </p>
                    </div>
                </div>
            </div>

            <!-- Notes -->
            @if($contract->notes)
            <div class="card rounded-2xl p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">ملاحظات</h3>
                <p class="text-gray-900">{{ $contract->notes }}</p>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Status -->
            <div class="card rounded-2xl p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">الحالة</h3>
                <div>
                    <span class="status-badge
                        @if($contract->status === 'active') status-active
                        @elseif($contract->status === 'completed') status-completed
                        @else status-cancelled @endif">
                        {{ $contract->status_badge }}
                    </span>
                </div>
            </div>

            <!-- Actions -->
            <div class="card rounded-2xl p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">الإجراءات</h3>
                <div class="space-y-2">
                    <a href="{{ route('contracts.edit', $contract) }}" class="w-full btn-primary text-white px-4 py-2 rounded-xl flex items-center justify-center hover:no-underline">
                        <i class="fas fa-edit text-sm ml-2"></i>
                        تعديل
                    </a>
                    <button onclick="confirmDelete('{{ route('contracts.destroy', $contract) }}', 'تأكيد حذف العقد', 'هل أنت متأكد من حذف العقد الخاص بـ {{ $contract->employee->name ?? 'الموظف' }}؟')" class="w-full bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-xl flex items-center justify-center transition-colors">
                        <i class="fas fa-trash text-sm ml-2"></i>
                        حذف
                    </button>
                </div>
            </div>

            <!-- Contract Info -->
            <div class="card rounded-2xl p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">معلومات العقد</h3>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">تاريخ الإنشاء:</span>
                        <span class="text-gray-900">{{ $contract->created_at->format('Y-m-d') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">آخر تحديث:</span>
                        <span class="text-gray-900">{{ $contract->updated_at->format('Y-m-d') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection





