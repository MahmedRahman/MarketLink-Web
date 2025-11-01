@extends('layouts.dashboard')

@section('title', 'التقرير المالي للموظف')
@section('page-title', 'التقرير المالي للموظف')
@section('page-description', 'عرض الوضع المالي للموظفين')

@section('content')
<div class="container mx-auto px-4">
    <div class="max-w-7xl mx-auto space-y-6">
        <!-- Header -->
        <div class="card page-header rounded-2xl p-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="w-12 h-12 logo-gradient rounded-2xl flex items-center justify-center shadow-lg icon-spacing ml-3">
                        <i class="fas fa-user-circle text-white text-xl"></i>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800">التقرير المالي للموظف</h2>
                        <p class="text-gray-600">عرض الوضع المالي للموظف المحدد</p>
                    </div>
                </div>
                <div class="flex items-center space-x-3 rtl:space-x-reverse">
                    <a href="{{ route('reports.index') }}" class="flex items-center px-4 py-2 text-gray-600 hover:text-gray-800 hover:bg-gray-100 rounded-xl transition-colors">
                        <i class="fas fa-arrow-right text-sm ml-2"></i>
                        العودة للتقارير
                    </a>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="card rounded-2xl p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">فلاتر البحث</h3>
            <form method="GET" action="{{ route('reports.employee-financial') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Employee Filter -->
                <div>
                    <label for="employee_id" class="block text-sm font-medium text-gray-700 mb-2">
                        الموظف <span class="text-red-500">*</span>
                    </label>
                    <select name="employee_id" id="employee_id" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-colors select2">
                        <option value="">اختر الموظف</option>
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}" {{ $employeeId == $emp->id ? 'selected' : '' }}>
                                {{ $emp->name }} - {{ $emp->role_badge }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Date From -->
                <div>
                    <label for="date_from" class="block text-sm font-medium text-gray-700 mb-2">من تاريخ</label>
                    <input type="date" name="date_from" id="date_from" value="{{ $dateFrom }}"
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-colors">
                </div>

                <!-- Date To -->
                <div>
                    <label for="date_to" class="block text-sm font-medium text-gray-700 mb-2">إلى تاريخ</label>
                    <input type="date" name="date_to" id="date_to" value="{{ $dateTo }}"
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-colors">
                </div>

                <!-- Filter Buttons -->
                <div class="md:col-span-3 flex items-center justify-center space-x-3 rtl:space-x-reverse pt-4">
                    <button type="submit" class="btn-primary text-white px-6 py-3 rounded-xl flex items-center">
                        <i class="fas fa-search text-sm ml-2"></i>
                        عرض التقرير
                    </button>
                    <a href="{{ route('reports.employee-financial') }}" class="cancel-button px-6 py-3 rounded-xl flex items-center">
                        <i class="fas fa-times text-sm ml-2"></i>
                        إعادة تعيين
                    </a>
                </div>
            </form>
        </div>

        @if($employee)
        <!-- Employee Info Card -->
        <div class="card rounded-2xl p-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="w-16 h-16 bg-indigo-100 rounded-full flex items-center justify-center ml-4">
                        <i class="fas fa-user text-indigo-600 text-2xl"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-800">{{ $employee->name }}</h3>
                        <p class="text-gray-600">{{ $employee->role_badge }}</p>
                        @if($employee->email)
                            <p class="text-sm text-gray-500">{{ $employee->email }}</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <!-- Total Amount -->
            <div class="card rounded-2xl p-6">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center ml-4">
                        <i class="fas fa-money-bill-wave text-blue-600 text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">إجمالي المبلغ</p>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($totalAmount, 2) }} جنيه</p>
                    </div>
                </div>
            </div>

            <!-- Paid Amount -->
            <div class="card rounded-2xl p-6">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center ml-4">
                        <i class="fas fa-check-circle text-green-600 text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">تم الدفع</p>
                        <p class="text-2xl font-bold text-green-600">{{ number_format($totalPaid, 2) }} جنيه</p>
                    </div>
                </div>
            </div>

            <!-- Pending Amount -->
            <div class="card rounded-2xl p-6">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center ml-4">
                        <i class="fas fa-clock text-yellow-600 text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">في الانتظار</p>
                        <p class="text-2xl font-bold text-yellow-600">{{ number_format($totalPending, 2) }} جنيه</p>
                    </div>
                </div>
            </div>

            <!-- Cancelled Amount -->
            <div class="card rounded-2xl p-6">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center ml-4">
                        <i class="fas fa-times-circle text-red-600 text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">ملغي</p>
                        <p class="text-2xl font-bold text-red-600">{{ number_format($totalCancelled, 2) }} جنيه</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Expenses Table -->
        <div class="card rounded-2xl p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">تفاصيل المدفوعات</h3>
            @if($expenses->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead>
                            <tr class="border-b border-gray-200">
                                <th class="text-right py-3 px-4 font-medium text-gray-600">المشروع</th>
                                <th class="text-right py-3 px-4 font-medium text-gray-600">العنوان</th>
                                <th class="text-right py-3 px-4 font-medium text-gray-600">المبلغ</th>
                                <th class="text-right py-3 px-4 font-medium text-gray-600">التاريخ</th>
                                <th class="text-right py-3 px-4 font-medium text-gray-600">الحالة</th>
                                <th class="text-right py-3 px-4 font-medium text-gray-600">طريقة الدفع</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($expenses as $expense)
                            <tr class="border-b border-gray-100 hover:bg-gray-50">
                                <td class="py-3 px-4 text-sm text-gray-900">
                                    {{ $expense->project->business_name ?? 'غير محدد' }}
                                </td>
                                <td class="py-3 px-4 text-sm text-gray-900">{{ $expense->title }}</td>
                                <td class="py-3 px-4 text-sm font-medium text-gray-900">
                                    {{ number_format($expense->amount, 2) }} جنيه
                                </td>
                                <td class="py-3 px-4 text-sm text-gray-500">
                                    {{ $expense->expense_date->format('Y-m-d') }}
                                </td>
                                <td class="py-3 px-4">
                                    <span class="status-badge status-{{ $expense->status_color }}">
                                        {{ $expense->status_badge }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-sm text-gray-600">
                                    {{ $expense->payment_method_badge }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="bg-gray-50 font-semibold">
                                <td colspan="2" class="py-3 px-4 text-right">الإجمالي:</td>
                                <td class="py-3 px-4 text-lg text-gray-900">
                                    {{ number_format($totalAmount, 2) }} جنيه
                                </td>
                                <td colspan="3"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @else
                <div class="text-center py-12">
                    <i class="fas fa-receipt text-5xl text-gray-300 mb-4"></i>
                    <p class="text-gray-500 text-lg">لا توجد مدفوعات لهذا الموظف في الفترة المحددة</p>
                </div>
            @endif
        </div>
        @else
        <!-- Empty State -->
        <div class="card rounded-2xl p-12">
            <div class="text-center">
                <i class="fas fa-user-check text-5xl text-gray-300 mb-4"></i>
                <p class="text-gray-500 text-lg">يرجى اختيار موظف لعرض تقريره المالي</p>
            </div>
        </div>
        @endif
    </div>
</div>

@section('scripts')
<script>
$(document).ready(function() {
    // Initialize Select2
    $('.select2').select2({
        placeholder: 'اختر الموظف',
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

