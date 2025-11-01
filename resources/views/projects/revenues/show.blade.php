@extends('layouts.dashboard')

@section('title', 'تفاصيل الإيراد')
@section('page-title', 'تفاصيل الإيراد')
@section('page-description', 'عرض تفاصيل الإيراد: ' . $revenue->title)

@section('content')
<div class="container mx-auto px-4">
    <div class="max-w-4xl mx-auto space-y-6">
        <!-- Header -->
        <div class="card page-header rounded-2xl p-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="w-12 h-12 logo-gradient rounded-2xl flex items-center justify-center shadow-lg icon-spacing ml-3">
                        <i class="fas fa-money-bill-wave text-white text-xl"></i>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800">تفاصيل الإيراد</h2>
                        <p class="text-gray-600">عرض تفاصيل الإيراد: {{ $revenue->title }}</p>
                    </div>
                </div>
                <div class="flex items-center space-x-3 rtl:space-x-reverse">
                    <a href="{{ route('projects.revenues.edit', [$project, $revenue]) }}" class="flex items-center px-4 py-2 text-blue-600 hover:text-blue-700 hover:bg-blue-50 rounded-xl transition-colors">
                        <i class="fas fa-edit text-sm ml-2"></i>
                        تعديل
                    </a>
                    <a href="{{ route('projects.revenues.index', $project) }}" class="flex items-center px-4 py-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-xl transition-colors">
                        العودة للقائمة
                    </a>
                </div>
            </div>
        </div>

        <!-- Revenue Details -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Information -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Basic Information -->
                <div class="card rounded-2xl p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">المعلومات الأساسية</h3>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between py-3 border-b border-gray-100">
                            <span class="text-sm font-medium text-gray-600">العنوان:</span>
                            <span class="text-sm text-gray-900">{{ $revenue->title }}</span>
                        </div>
                        
                        @if($revenue->description)
                        <div class="flex items-start justify-between py-3 border-b border-gray-100">
                            <span class="text-sm font-medium text-gray-600">الوصف:</span>
                            <span class="text-sm text-gray-900 text-right max-w-xs">{{ $revenue->description }}</span>
                        </div>
                        @endif
                        
                        <div class="flex items-center justify-between py-3 border-b border-gray-100">
                            <span class="text-sm font-medium text-gray-600">المبلغ الإجمالي:</span>
                            <span class="text-lg font-bold text-green-600">{{ $revenue->formatted_amount }}</span>
                        </div>
                        
                        @if($revenue->paid_amount !== null && $revenue->paid_amount > 0)
                        <div class="flex items-center justify-between py-3 border-b border-gray-100">
                            <span class="text-sm font-medium text-gray-600">المبلغ المدفوع:</span>
                            <span class="text-lg font-bold text-blue-600">{{ $revenue->formatted_paid_amount }}</span>
                        </div>
                        @endif

                        <div class="flex items-center justify-between py-3 border-b border-gray-100">
                            <span class="text-sm font-medium text-gray-600">المبلغ المتبقي:</span>
                            <span class="text-lg font-bold text-orange-600">{{ $revenue->formatted_calculated_remaining_amount }}</span>
                        </div>
                        
                        <div class="flex items-center justify-between py-3 border-b border-gray-100">
                            <span class="text-sm font-medium text-gray-600">تاريخ الإيراد:</span>
                            <span class="text-sm text-gray-900">{{ $revenue->revenue_date->format('Y-m-d') }}</span>
                        </div>
                        
                        <div class="flex items-center justify-between py-3 border-b border-gray-100">
                            <span class="text-sm font-medium text-gray-600">الحالة:</span>
                            <span class="status-badge status-{{ $revenue->status_color }}">
                                {{ $revenue->status_badge }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Payment Information -->
                @if($revenue->payment_method || $revenue->transfer_image)
                <div class="card rounded-2xl p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">معلومات الدفع</h3>
                    <div class="space-y-4">
                        @if($revenue->payment_method)
                        <div class="flex items-center justify-between py-3 border-b border-gray-100">
                            <span class="text-sm font-medium text-gray-600">طريقة الدفع:</span>
                            <span class="text-sm text-gray-900">{{ $revenue->payment_method_badge }}</span>
                        </div>
                        @endif
                        
                        @if($revenue->transfer_image && $revenue->transfer_image_url)
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-2">صورة التحويل:</label>
                            <div class="mt-2">
                                <img src="{{ $revenue->transfer_image_url }}" alt="صورة التحويل" class="max-w-full md:max-w-md rounded-lg border border-gray-300 shadow-sm cursor-pointer hover:shadow-md transition-shadow" onclick="window.open('{{ $revenue->transfer_image_url }}', '_blank')" onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                                <div style="display: none;" class="text-red-500 text-sm">تعذر تحميل الصورة</div>
                            </div>
                        </div>
                        @elseif($revenue->transfer_image)
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-2">صورة التحويل:</label>
                            <div class="mt-2 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                                <p class="text-sm text-yellow-800">الصورة موجودة لكن الرابط غير صحيح</p>
                                <p class="text-xs text-yellow-600 mt-1">المسار: {{ $revenue->transfer_image }}</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                @endif

                <!-- Notes -->
                @if($revenue->notes)
                <div class="card rounded-2xl p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">ملاحظات</h3>
                    <div class="bg-gray-50 rounded-xl p-4">
                        <p class="text-sm text-gray-700">{{ $revenue->notes }}</p>
                    </div>
                </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Project Information -->
                <div class="card rounded-2xl p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">معلومات المشروع</h3>
                    <div class="space-y-3">
                        <div>
                            <span class="text-sm font-medium text-gray-600">اسم المشروع:</span>
                            <p class="text-sm text-gray-900 mt-1">{{ $project->business_name }}</p>
                        </div>
                        <div>
                            <span class="text-sm font-medium text-gray-600">العميل:</span>
                            <p class="text-sm text-gray-900 mt-1">{{ $project->client->name ?? 'غير محدد' }}</p>
                        </div>
                        <div>
                            <span class="text-sm font-medium text-gray-600">حالة المشروع:</span>
                            <span class="status-badge status-{{ $project->status_color }} mt-1">
                                {{ $project->status_badge }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="card rounded-2xl p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">إجراءات سريعة</h3>
                    <div class="space-y-3">
                        <a href="{{ route('projects.revenues.edit', [$project, $revenue]) }}" class="w-full flex items-center justify-center px-4 py-3 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition-colors">
                            <i class="fas fa-edit text-sm ml-2"></i>
                            تعديل الإيراد
                        </a>
                        
                        <button onclick="confirmDelete('{{ route('projects.revenues.destroy', [$project, $revenue]) }}', 'تأكيد حذف الإيراد', 'هل أنت متأكد من حذف الإيراد {{ $revenue->title }}؟')" class="w-full flex items-center justify-center px-4 py-3 bg-red-600 text-white rounded-xl hover:bg-red-700 transition-colors">
                            <i class="fas fa-trash text-sm ml-2"></i>
                            حذف الإيراد
                        </button>
                        
                        <a href="{{ route('projects.revenues.index', $project) }}" class="w-full flex items-center justify-center px-4 py-3 bg-gray-600 text-white rounded-xl hover:bg-gray-700 transition-colors">
                            <i class="fas fa-arrow-right text-sm ml-2"></i>
                            العودة للقائمة
                        </a>
                    </div>
                </div>

                <!-- Revenue Statistics -->
                <div class="card rounded-2xl p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">إحصائيات الإيراد</h3>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">تاريخ الإنشاء:</span>
                            <span class="text-sm text-gray-900">{{ $revenue->created_at->format('Y-m-d') }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">آخر تحديث:</span>
                            <span class="text-sm text-gray-900">{{ $revenue->updated_at->format('Y-m-d') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script>
function confirmDelete(url, title, message) {
    Swal.fire({
        title: title,
        text: message,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'نعم، احذف',
        cancelButtonText: 'إلغاء'
    }).then((result) => {
        if (result.isConfirmed) {
            // Create a form to submit the delete request
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = url;
            
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = csrfToken;
            
            const methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            methodInput.value = 'DELETE';
            
            form.appendChild(csrfInput);
            form.appendChild(methodInput);
            document.body.appendChild(form);
            form.submit();
        }
    });
}
</script>
@endsection
@endsection









