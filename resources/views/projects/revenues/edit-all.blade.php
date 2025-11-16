@extends('layouts.dashboard')

@section('title', 'تعديل الإيراد')
@section('page-title', 'تعديل الإيراد')
@section('page-description', 'تعديل الإيراد: ' . $revenue->title)

@section('content')
<div class="container mx-auto px-4">
    <div class="max-w-4xl mx-auto space-y-6">
        <!-- Header -->
        <div class="card page-header rounded-2xl p-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="w-12 h-12 logo-gradient rounded-2xl flex items-center justify-center shadow-lg icon-spacing ml-3">
                        <i class="fas fa-edit text-white text-xl"></i>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800">تعديل الإيراد</h2>
                        <p class="text-gray-600">تعديل الإيراد: {{ $revenue->title }}</p>
                    </div>
                </div>
                <a href="{{ route('revenues.all') }}" class="flex items-center px-4 py-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-xl transition-colors icon-spacing">
                    العودة للقائمة
                </a>
            </div>
        </div>

        <!-- Form Card -->
        <div class="card rounded-2xl p-8">
            @if ($errors->any())
                <div class="mb-6 bg-red-50 border border-red-200 rounded-xl p-4">
                    <div class="flex items-center mb-2">
                        <i class="fas fa-exclamation-triangle text-red-500 ml-2"></i>
                        <h3 class="text-red-800 font-semibold">يرجى تصحيح الأخطاء التالية:</h3>
                    </div>
                    <ul class="text-red-700 text-sm space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>• {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (session('error'))
                <div class="mb-6 bg-red-50 border border-red-200 rounded-xl p-4">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-triangle text-red-500 ml-2"></i>
                        <span class="text-red-800">{{ session('error') }}</span>
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('revenues.update', $revenue) }}" class="space-y-8" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <!-- Basic Information Section -->
                <div class="form-section space-y-6">
                    <h3 class="text-lg font-semibold text-gray-800">المعلومات الأساسية</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Project -->
                        <div>
                            <label for="project_id" class="block text-sm font-medium text-gray-700 mb-2">
                                المشروع <span class="text-red-500">*</span>
                            </label>
                            <select
                                id="project_id"
                                name="project_id"
                                required
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-colors select2"
                            >
                                <option value="">اختر المشروع</option>
                                @foreach($projects as $project)
                                    <option value="{{ $project->id }}" {{ old('project_id', $revenue->project_id) == $project->id ? 'selected' : '' }}>
                                        {{ $project->business_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('project_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Title -->
                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                                عنوان الإيراد <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="text"
                                id="title"
                                name="title"
                                value="{{ old('title', $revenue->title) }}"
                                required
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-colors"
                                placeholder="أدخل عنوان الإيراد"
                            />
                            @error('title')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Total Amount -->
                        <div>
                            <label for="amount" class="block text-sm font-medium text-gray-700 mb-2">
                                المبلغ الإجمالي <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="number"
                                id="amount"
                                name="amount"
                                value="{{ old('amount', $revenue->amount) }}"
                                required
                                step="0.01"
                                min="0"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-colors"
                                placeholder="0.00"
                            />
                            @error('amount')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Paid Amount -->
                        <div>
                            <label for="paid_amount" class="block text-sm font-medium text-gray-700 mb-2">
                                المبلغ المدفوع
                            </label>
                            <input
                                type="number"
                                id="paid_amount"
                                name="paid_amount"
                                value="{{ old('paid_amount', $revenue->paid_amount) }}"
                                step="0.01"
                                min="0"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-colors"
                                placeholder="0.00"
                            />
                            <p class="mt-1 text-xs text-gray-500">المبلغ الذي تم دفعه بالفعل</p>
                            @error('paid_amount')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Calculated Remaining Amount (Read-only) -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                المبلغ المتبقي (محسوب تلقائياً)
                            </label>
                            <div class="w-full px-4 py-3 border border-gray-300 rounded-xl bg-gray-50 text-gray-700 font-semibold" id="calculated_remaining_display">
                                {{ $revenue->formatted_calculated_remaining_amount }}
                            </div>
                            <p class="mt-1 text-xs text-gray-500">المبلغ المتبقي = المبلغ الإجمالي - المبلغ المدفوع</p>
                        </div>

                        <!-- Currency -->
                        <div>
                            <label for="currency" class="block text-sm font-medium text-gray-700 mb-2">
                                العملة <span class="text-red-500">*</span>
                            </label>
                            <select
                                id="currency"
                                name="currency"
                                required
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-colors select2"
                            >
                                <option value="EGP" {{ old('currency', $revenue->currency) === 'EGP' ? 'selected' : '' }}>جنيه مصري (EGP)</option>
                            </select>
                            @error('currency')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Revenue Date -->
                        <div>
                            <label for="revenue_date" class="block text-sm font-medium text-gray-700 mb-2">
                                تاريخ الإيراد <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="date"
                                id="revenue_date"
                                name="revenue_date"
                                value="{{ old('revenue_date', $revenue->revenue_date->format('Y-m-d')) }}"
                                required
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-colors"
                            />
                            @error('revenue_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Record Month Year -->
                        <div>
                            <label for="record_month_year" class="block text-sm font-medium text-gray-700 mb-2">
                                السجلات الشهرية (الشهر والسنة)
                            </label>
                            <input
                                type="month"
                                id="record_month_year"
                                name="record_month_year"
                                value="{{ old('record_month_year', $revenue->record_month_year ?? $revenue->revenue_date->format('Y-m')) }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-colors"
                            />
                            <p class="mt-1 text-xs text-gray-500">الشهر والسنة للسجل الشهري</p>
                            @error('record_month_year')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                            وصف الإيراد
                        </label>
                        <textarea
                            id="description"
                            name="description"
                            rows="4"
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-colors"
                            placeholder="أدخل وصفاً مفصلاً للإيراد"
                        >{{ old('description', $revenue->description) }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Payment Information Section -->
                <div class="form-section space-y-6">
                    <h3 class="text-lg font-semibold text-gray-800">معلومات الدفع</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Payment Method -->
                        <div>
                            <label for="payment_method" class="block text-sm font-medium text-gray-700 mb-2">
                                طريقة الدفع <span class="text-red-500">*</span>
                            </label>
                            <select
                                id="payment_method"
                                name="payment_method"
                                required
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-colors select2"
                            >
                                <option value="">اختر طريقة الدفع</option>
                                <option value="cash" {{ old('payment_method', $revenue->payment_method) === 'cash' ? 'selected' : '' }}>نقدي</option>
                                <option value="bank_transfer" {{ old('payment_method', $revenue->payment_method) === 'bank_transfer' ? 'selected' : '' }}>تحويل بنكي</option>
                                <option value="vodafone_cash" {{ old('payment_method', $revenue->payment_method) === 'vodafone_cash' ? 'selected' : '' }}>فودافون كاش</option>
                                <option value="instapay" {{ old('payment_method', $revenue->payment_method) === 'instapay' ? 'selected' : '' }}>انستاباي</option>
                                <option value="paypal" {{ old('payment_method', $revenue->payment_method) === 'paypal' ? 'selected' : '' }}>باي بال</option>
                                <option value="western_union" {{ old('payment_method', $revenue->payment_method) === 'western_union' ? 'selected' : '' }}>ويسترن يونيون</option>
                                <option value="credit_card" {{ old('payment_method', $revenue->payment_method) === 'credit_card' ? 'selected' : '' }}>بطاقة ائتمان</option>
                                <option value="check" {{ old('payment_method', $revenue->payment_method) === 'check' ? 'selected' : '' }}>شيك</option>
                                <option value="other" {{ old('payment_method', $revenue->payment_method) === 'other' ? 'selected' : '' }}>أخرى</option>
                            </select>
                            @error('payment_method')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Transfer Image -->
                        <div>
                            <label for="transfer_image" class="block text-sm font-medium text-gray-700 mb-2">
                                صورة التحويل
                            </label>
                            <input
                                type="file"
                                id="transfer_image"
                                name="transfer_image"
                                accept="image/jpeg,image/jpg,image/png,image/gif"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-colors"
                            />
                            <p class="mt-1 text-xs text-gray-500">الحجم الأقصى: 5MB (JPG, PNG, GIF)</p>
                            @error('transfer_image')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Current Image -->
                    @if($revenue->transfer_image)
                    <div class="mt-4 p-4 bg-gray-50 rounded-xl border border-gray-200">
                        <label class="block text-sm font-medium text-gray-700 mb-3">صورة التحويل الحالية</label>
                        <div class="relative inline-block">
                            @if($revenue->transfer_image_url)
                                <img src="{{ $revenue->transfer_image_url }}" alt="صورة التحويل" class="max-w-xs rounded-lg border border-gray-300 shadow-sm cursor-pointer hover:shadow-md transition-shadow" onclick="window.open('{{ $revenue->transfer_image_url }}', '_blank')" title="اضغط لفتح الصورة بحجم كامل" onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                                <div style="display: none;" class="text-red-500 text-sm p-2 bg-red-50 rounded">تعذر تحميل الصورة</div>
                            @else
                                <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                                    <p class="text-sm text-yellow-800">الصورة موجودة لكن الرابط غير صحيح</p>
                                    <p class="text-xs text-yellow-600 mt-1">المسار: {{ $revenue->transfer_image }}</p>
                                </div>
                            @endif
                            <p class="text-xs text-gray-500 mt-2 mb-3">اضغط على الصورة لفتحها بحجم كامل</p>
                            <label class="flex items-center cursor-pointer">
                                <input type="checkbox" id="delete_transfer_image" name="delete_transfer_image" value="1" class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                                <span class="mr-2 text-sm text-red-600 font-medium">حذف الصورة الحالية</span>
                            </label>
                        </div>
                    </div>
                    @endif

                    <!-- Image Preview for New Upload -->
                    <div id="image-preview-container" class="hidden mt-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">معاينة الصورة الجديدة</label>
                        <div class="relative inline-block">
                            <img id="image-preview" src="" alt="Preview" class="max-w-xs rounded-lg border border-gray-300 shadow-sm">
                        </div>
                    </div>
                </div>

                <!-- Status and Notes Section -->
                <div class="form-section space-y-6">
                    <h3 class="text-lg font-semibold text-gray-800">الحالة والملاحظات</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Status -->
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                                الحالة <span class="text-red-500">*</span>
                            </label>
                            <select
                                id="status"
                                name="status"
                                required
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-colors select2"
                            >
                                <option value="">اختر الحالة</option>
                                <option value="pending" {{ old('status', $revenue->status) === 'pending' ? 'selected' : '' }}>في الانتظار</option>
                                <option value="received" {{ old('status', $revenue->status) === 'received' ? 'selected' : '' }}>تم الاستلام</option>
                                <option value="cancelled" {{ old('status', $revenue->status) === 'cancelled' ? 'selected' : '' }}>ملغي</option>
                            </select>
                            @error('status')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Notes -->
                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                            ملاحظات إضافية
                        </label>
                        <textarea
                            id="notes"
                            name="notes"
                            rows="4"
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-colors"
                            placeholder="أدخل أي ملاحظات إضافية"
                        >{{ old('notes', $revenue->notes) }}</textarea>
                        @error('notes')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex items-center justify-center rtl-spacing pt-8 border-t border-gray-200">
                    <button type="submit" class="action-button btn-primary text-white px-8 py-4 rounded-2xl flex items-center font-medium text-lg min-w-[160px] justify-center">
                        <i class="fas fa-save text-lg ml-3"></i>
                        حفظ التعديلات
                    </button>
                    <a href="{{ route('revenues.all') }}" class="action-button cancel-button flex items-center px-8 py-4 rounded-2xl font-medium text-lg min-w-[140px] justify-center">
                        إلغاء
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

@section('scripts')
<script>
$(document).ready(function() {
    // Initialize Select2
    $('.select2').select2({
        placeholder: 'اختر من القائمة',
        allowClear: true,
        dir: 'rtl',
        width: '100%',
        language: {
            noResults: function() {
                return 'لا توجد نتائج';
            },
            searching: function() {
                return 'جاري البحث...';
            }
        }
    });

    // معاينة صورة التحويل
    $('#transfer_image').on('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            // التحقق من نوع الملف
            const validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
            if (!validTypes.includes(file.type)) {
                alert('نوع الملف غير مدعوم. يرجى اختيار صورة (JPG, PNG, GIF)');
                $(this).val('');
                return;
            }
            
            // التحقق من حجم الملف (5MB)
            if (file.size > 5 * 1024 * 1024) {
                alert('حجم الملف كبير جداً. الحد الأقصى 5MB');
                $(this).val('');
                return;
            }
            
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#image-preview').attr('src', e.target.result);
                $('#image-preview-container').removeClass('hidden');
            };
            reader.readAsDataURL(file);
        } else {
            $('#image-preview-container').addClass('hidden');
        }
    });

    // إخفاء معاينة الصورة عند تحديد حذف الصورة الحالية
    $('#delete_transfer_image').on('change', function() {
        if ($(this).is(':checked')) {
            // يمكن إضافة تنبيه هنا إذا لزم الأمر
        }
    });

    // حساب المبلغ المتبقي تلقائياً
    function calculateRemaining() {
        const totalAmount = parseFloat($('#amount').val()) || 0;
        const paidAmount = parseFloat($('#paid_amount').val()) || 0;
        const remaining = Math.max(0, totalAmount - paidAmount);
        $('#calculated_remaining_display').text(remaining.toFixed(2) + ' جنيه');
    }

    $('#amount, #paid_amount').on('input change', function() {
        calculateRemaining();
    });

    // حساب عند تحميل الصفحة
    calculateRemaining();

    // تحديث السجلات الشهرية تلقائياً عند تغيير تاريخ الإيراد
    $('#revenue_date').on('change', function() {
        const dateValue = $(this).val();
        if (dateValue) {
            const date = new Date(dateValue);
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            $('#record_month_year').val(year + '-' + month);
        }
    });
});
</script>
@endsection
@endsection









