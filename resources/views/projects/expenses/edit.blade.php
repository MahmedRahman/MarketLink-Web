@extends('layouts.dashboard')

@section('title', 'تعديل المصروف')
@section('page-title', 'تعديل المصروف')
@section('page-description', 'تعديل المصروف: ' . $expense->title)

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
                        <h2 class="text-2xl font-bold text-gray-800">تعديل المصروف</h2>
                        <p class="text-gray-600">تعديل المصروف: {{ $expense->title }}</p>
                    </div>
                </div>
                <a href="{{ route('projects.expenses.show', [$project, $expense]) }}" class="flex items-center px-4 py-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-xl transition-colors icon-spacing">
                    العودة للتفاصيل
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

            <form method="POST" action="{{ route('projects.expenses.update', [$project, $expense]) }}" class="space-y-8">
                @csrf
                @method('PUT')

                <!-- Basic Information Section -->
                <div class="form-section space-y-6">
                    <h3 class="text-lg font-semibold text-gray-800">المعلومات الأساسية</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Title -->
                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                                عنوان المصروف <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="text"
                                id="title"
                                name="title"
                                value="{{ old('title', $expense->title) }}"
                                required
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-colors"
                                placeholder="أدخل عنوان المصروف"
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
                                value="{{ old('amount', $expense->amount) }}"
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
                                <option value="EGP" {{ old('currency', $expense->currency) === 'EGP' ? 'selected' : '' }}>جنيه مصري (EGP)</option>
                            </select>
                            @error('currency')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Expense Date -->
                        <div>
                            <label for="expense_date" class="block text-sm font-medium text-gray-700 mb-2">
                                تاريخ المصروف <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="date"
                                id="expense_date"
                                name="expense_date"
                                value="{{ old('expense_date', $expense->expense_date->format('Y-m-d')) }}"
                                required
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-colors"
                            />
                            @error('expense_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Category -->
                        <div>
                            <label for="category" class="block text-sm font-medium text-gray-700 mb-2">
                                فئة المصروف <span class="text-red-500">*</span>
                            </label>
                            <select
                                id="category"
                                name="category"
                                required
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-colors select2"
                            >
                                <option value="">اختر فئة المصروف</option>
                                <option value="marketing" {{ old('category', $expense->category') === 'marketing' ? 'selected' : '' }}>تسويق</option>
                                <option value="advertising" {{ old('category', $expense->category') === 'advertising' ? 'selected' : '' }}>إعلانات</option>
                                <option value="design" {{ old('category', $expense->category') === 'design' ? 'selected' : '' }}>تصميم</option>
                                <option value="development" {{ old('category', $expense->category') === 'development' ? 'selected' : '' }}>تطوير</option>
                                <option value="content" {{ old('category', $expense->category') === 'content' ? 'selected' : '' }}>محتوى</option>
                                <option value="tools" {{ old('category', $expense->category') === 'tools' ? 'selected' : '' }}>أدوات</option>
                                <option value="subscriptions" {{ old('category', $expense->category') === 'subscriptions' ? 'selected' : '' }}>اشتراكات</option>
                                <option value="other" {{ old('category', $expense->category') === 'other' ? 'selected' : '' }}>أخرى</option>
                            </select>
                            @error('category')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

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
                                <option value="pending" {{ old('status', $expense->status) === 'pending' ? 'selected' : '' }}>في الانتظار</option>
                                <option value="paid" {{ old('status', $expense->status) === 'paid' ? 'selected' : '' }}>تم الدفع</option>
                                <option value="cancelled" {{ old('status', $expense->status) === 'cancelled' ? 'selected' : '' }}>ملغي</option>
                            </select>
                            @error('status')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                            وصف المصروف
                        </label>
                        <textarea
                            id="description"
                            name="description"
                            rows="4"
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-colors"
                            placeholder="أدخل وصفاً مفصلاً للمصروف"
                        >{{ old('description', $expense->description) }}</textarea>
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
                                <option value="cash" {{ old('payment_method', $expense->payment_method) === 'cash' ? 'selected' : '' }}>نقدي</option>
                                <option value="bank_transfer" {{ old('payment_method', $expense->payment_method) === 'bank_transfer' ? 'selected' : '' }}>تحويل بنكي</option>
                                <option value="credit_card" {{ old('payment_method', $expense->payment_method) === 'credit_card' ? 'selected' : '' }}>بطاقة ائتمان</option>
                                <option value="check" {{ old('payment_method', $expense->payment_method) === 'check' ? 'selected' : '' }}>شيك</option>
                                <option value="other" {{ old('payment_method', $expense->payment_method) === 'other' ? 'selected' : '' }}>أخرى</option>
                            </select>
                            @error('payment_method')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Payment Reference -->
                        <div>
                            <label for="payment_reference" class="block text-sm font-medium text-gray-700 mb-2">
                                مرجع الدفع
                            </label>
                            <input
                                type="text"
                                id="payment_reference"
                                name="payment_reference"
                                value="{{ old('payment_reference', $expense->payment_reference) }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-colors"
                                placeholder="مثال: 01234567890 أو رقم التحويل"
                            />
                            @error('payment_reference')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Notes Section -->
                <div class="form-section space-y-6">
                    <h3 class="text-lg font-semibold text-gray-800">ملاحظات إضافية</h3>

                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                            ملاحظات
                        </label>
                        <textarea
                            id="notes"
                            name="notes"
                            rows="4"
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-colors"
                            placeholder="أدخل أي ملاحظات إضافية"
                        >{{ old('notes', $expense->notes) }}</textarea>
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
                    <a href="{{ route('projects.expenses.show', [$project, $expense]) }}" class="action-button cancel-button flex items-center px-8 py-4 rounded-2xl font-medium text-lg min-w-[140px] justify-center">
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
});
</script>
@endsection
@endsection
