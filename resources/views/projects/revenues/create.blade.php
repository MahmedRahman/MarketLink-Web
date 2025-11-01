@extends('layouts.dashboard')

@section('title', 'إضافة إيراد جديد')
@section('page-title', 'إضافة إيراد جديد')
@section('page-description', 'إضافة إيراد جديد للمشروع: ' . $project->business_name)

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
                        <h2 class="text-2xl font-bold text-gray-800">إضافة إيراد جديد</h2>
                        <p class="text-gray-600">إضافة إيراد جديد للمشروع: {{ $project->business_name }}</p>
                    </div>
                </div>
                <a href="{{ route('projects.revenues.index', $project) }}" class="flex items-center px-4 py-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-xl transition-colors icon-spacing">
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

            <form method="POST" action="{{ route('projects.revenues.store', $project) }}" class="space-y-8" enctype="multipart/form-data">
                @csrf

                <!-- Basic Information Section -->
                <div class="form-section space-y-6">
                    <h3 class="text-lg font-semibold text-gray-800">المعلومات الأساسية</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Title -->
                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                                عنوان الإيراد <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="text"
                                id="title"
                                name="title"
                                value="{{ old('title') }}"
                                required
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-colors"
                                placeholder="أدخل عنوان الإيراد"
                            />
                            @error('title')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Amount -->
                        <div>
                            <label for="amount" class="block text-sm font-medium text-gray-700 mb-2">
                                المبلغ <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="number"
                                id="amount"
                                name="amount"
                                value="{{ old('amount') }}"
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

                        <!-- Remaining Amount -->
                        <div>
                            <label for="remaining_amount" class="block text-sm font-medium text-gray-700 mb-2">
                                المبلغ المتبقي
                            </label>
                            <input
                                type="number"
                                id="remaining_amount"
                                name="remaining_amount"
                                value="{{ old('remaining_amount') }}"
                                step="0.01"
                                min="0"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-colors"
                                placeholder="0.00"
                            />
                            <p class="mt-1 text-xs text-gray-500">المبلغ المتبقي الذي لم يتم استلامه بعد</p>
                            @error('remaining_amount')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
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
                                <option value="EGP" {{ old('currency', 'EGP') === 'EGP' ? 'selected' : '' }}>جنيه مصري (EGP)</option>
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
                                value="{{ old('revenue_date', date('Y-m-d')) }}"
                                required
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-colors"
                            />
                            @error('revenue_date')
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
                        >{{ old('description') }}</textarea>
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
                                <option value="cash" {{ old('payment_method') === 'cash' ? 'selected' : '' }}>نقدي</option>
                                <option value="bank_transfer" {{ old('payment_method', 'bank_transfer') === 'bank_transfer' ? 'selected' : '' }}>تحويل بنكي</option>
                                <option value="vodafone_cash" {{ old('payment_method') === 'vodafone_cash' ? 'selected' : '' }}>فودافون كاش</option>
                                <option value="instapay" {{ old('payment_method') === 'instapay' ? 'selected' : '' }}>انستاباي</option>
                                <option value="paypal" {{ old('payment_method') === 'paypal' ? 'selected' : '' }}>باي بال</option>
                                <option value="western_union" {{ old('payment_method') === 'western_union' ? 'selected' : '' }}>ويسترن يونيون</option>
                                <option value="credit_card" {{ old('payment_method') === 'credit_card' ? 'selected' : '' }}>بطاقة ائتمان</option>
                                <option value="check" {{ old('payment_method') === 'check' ? 'selected' : '' }}>شيك</option>
                                <option value="other" {{ old('payment_method') === 'other' ? 'selected' : '' }}>أخرى</option>
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

                    <!-- Image Preview -->
                    <div id="image-preview-container" class="hidden">
                        <label class="block text-sm font-medium text-gray-700 mb-2">معاينة الصورة</label>
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
                                <option value="pending" {{ old('status', 'pending') === 'pending' ? 'selected' : '' }}>في الانتظار</option>
                                <option value="received" {{ old('status') === 'received' ? 'selected' : '' }}>تم الاستلام</option>
                                <option value="cancelled" {{ old('status') === 'cancelled' ? 'selected' : '' }}>ملغي</option>
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
                        >{{ old('notes') }}</textarea>
                        @error('notes')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex items-center justify-center rtl-spacing pt-8 border-t border-gray-200">
                    <button type="submit" class="action-button btn-primary text-white px-8 py-4 rounded-2xl flex items-center font-medium text-lg min-w-[160px] justify-center">
                        <i class="fas fa-save text-lg ml-3"></i>
                        حفظ الإيراد
                    </button>
                    <a href="{{ route('projects.revenues.index', $project) }}" class="action-button cancel-button flex items-center px-8 py-4 rounded-2xl font-medium text-lg min-w-[140px] justify-center">
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
});
</script>
@endsection
@endsection
