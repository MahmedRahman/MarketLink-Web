@extends('layouts.dashboard')

@section('title', 'التقرير المالي الشهري')
@section('page-title', 'التقرير المالي الشهري')
@section('page-description', 'تقرير مالي شهري للمشروع: ' . $project->business_name)

@section('content')
<div class="container mx-auto px-4">
    <div class="max-w-7xl mx-auto space-y-6">
        <!-- Header -->
        <div class="card page-header rounded-2xl p-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="w-12 h-12 logo-gradient rounded-2xl flex items-center justify-center shadow-lg icon-spacing ml-3">
                        <i class="fas fa-chart-line text-white text-xl"></i>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800">التقرير المالي الشهري</h2>
                        <p class="text-gray-600">المشروع: {{ $project->business_name }}</p>
                    </div>
                </div>
                <div class="flex items-center space-x-3 rtl:space-x-reverse">
                    <a href="{{ route('projects.show', $project) }}" class="flex items-center px-4 py-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-xl transition-colors">
                        العودة للمشروع
                    </a>
                </div>
            </div>
        </div>

        <!-- Month Selection -->
        <div class="card rounded-2xl p-6">
            <form method="GET" action="{{ route('projects.financial-report', $project) }}" class="flex items-center gap-4">
                <div class="flex-1">
                    <label for="month" class="block text-sm font-medium text-gray-700 mb-2">
                        اختر الشهر
                    </label>
                    <input
                        type="month"
                        id="month"
                        name="month"
                        value="{{ $selectedMonth }}"
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-colors"
                        onchange="this.form.submit()"
                    />
                </div>
                <div class="flex items-end">
                    <button type="submit" class="btn-primary text-white px-6 py-3 rounded-xl hover:no-underline">
                        <i class="fas fa-search text-sm ml-2"></i>
                        عرض التقرير
                    </button>
                </div>
            </form>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Total Revenues -->
            <div class="card rounded-2xl p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">إجمالي الإيرادات</p>
                        <p class="text-2xl font-bold text-green-600">{{ number_format($total_revenues, 2) }} جنيه</p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-arrow-up text-green-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Paid Revenues -->
            <div class="card rounded-2xl p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">المبلغ المدفوع (إيرادات)</p>
                        <p class="text-2xl font-bold text-blue-600">{{ number_format($total_paid_revenues, 2) }} جنيه</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-check-circle text-blue-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Remaining Revenues -->
            <div class="card rounded-2xl p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">المبلغ المتبقي (إيرادات)</p>
                        <p class="text-2xl font-bold text-orange-600">{{ number_format($total_remaining_revenues, 2) }} جنيه</p>
                    </div>
                    <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-clock text-orange-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Total Expenses -->
            <div class="card rounded-2xl p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">إجمالي المصروفات</p>
                        <p class="text-2xl font-bold text-red-600">{{ number_format($total_expenses, 2) }} جنيه</p>
                    </div>
                    <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-arrow-down text-red-600 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Expense Details -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Paid Expenses -->
            <div class="card rounded-2xl p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">المبلغ المدفوع (مصروفات)</p>
                        <p class="text-2xl font-bold text-purple-600">{{ number_format($total_paid_expenses, 2) }} جنيه</p>
                    </div>
                    <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-money-bill-wave text-purple-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Net Profit -->
            <div class="card rounded-2xl p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">صافي الربح</p>
                        <p class="text-2xl font-bold {{ $net_profit >= 0 ? 'text-green-600' : 'text-red-600' }}">
                            {{ number_format($net_profit, 2) }} جنيه
                        </p>
                    </div>
                    <div class="w-12 h-12 {{ $net_profit >= 0 ? 'bg-green-100' : 'bg-red-100' }} rounded-xl flex items-center justify-center">
                        <i class="fas fa-chart-pie {{ $net_profit >= 0 ? 'text-green-600' : 'text-red-600' }} text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Revenues Section -->
        <div class="card rounded-2xl p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-semibold text-gray-800">الإيرادات - {{ $monthDate->format('F Y') }}</h3>
                <div class="flex items-center space-x-4 rtl:space-x-reverse">
                    <div class="text-sm text-gray-600">
                        <span class="font-medium">المستلمة:</span>
                        <span class="text-green-600 font-bold">{{ number_format($revenues_received, 2) }} جنيه</span>
                    </div>
                    <div class="text-sm text-gray-600">
                        <span class="font-medium">في الانتظار:</span>
                        <span class="text-yellow-600 font-bold">{{ number_format($revenues_pending, 2) }} جنيه</span>
                    </div>
                    @if($revenues_cancelled > 0)
                    <div class="text-sm text-gray-600">
                        <span class="font-medium">ملغية:</span>
                        <span class="text-red-600 font-bold">{{ number_format($revenues_cancelled, 2) }} جنيه</span>
                    </div>
                    @endif
                </div>
            </div>

            @if($revenues->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">التاريخ</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">العنوان</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">المبلغ الإجمالي</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">المبلغ المدفوع</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">المبلغ المتبقي</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الحالة</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($revenues as $revenue)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $revenue->revenue_date->format('Y-m-d') }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                <div class="font-medium">{{ $revenue->title }}</div>
                                @if($revenue->description)
                                    <div class="text-xs text-gray-500 mt-1">{{ Str::limit($revenue->description, 50) }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                                {{ number_format($revenue->amount, 2) }} جنيه
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-blue-600">
                                {{ number_format($revenue->paid_amount ?? 0, 2) }} جنيه
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-orange-600">
                                {{ number_format($revenue->calculated_remaining_amount, 2) }} جنيه
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="status-badge status-{{ $revenue->status_color }}">
                                    {{ $revenue->status_badge }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50">
                        <tr>
                            <td colspan="2" class="px-6 py-4 text-sm font-semibold text-gray-900 text-right">الإجمالي:</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">
                                {{ number_format($total_revenues, 2) }} جنيه
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-blue-600">
                                {{ number_format($total_paid_revenues, 2) }} جنيه
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-orange-600">
                                {{ number_format($total_remaining_revenues, 2) }} جنيه
                            </td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            @else
            <div class="text-center py-12 text-gray-500">
                <i class="fas fa-money-bill-wave text-6xl mb-4"></i>
                <p>لا توجد إيرادات في هذا الشهر</p>
            </div>
            @endif
        </div>

        <!-- Expenses Section -->
        <div class="card rounded-2xl p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-semibold text-gray-800">المصروفات - {{ $monthDate->format('F Y') }}</h3>
                <div class="flex items-center space-x-4 rtl:space-x-reverse">
                    <div class="text-sm text-gray-600">
                        <span class="font-medium">المدفوعة:</span>
                        <span class="text-green-600 font-bold">{{ number_format($expenses_paid, 2) }} جنيه</span>
                    </div>
                    <div class="text-sm text-gray-600">
                        <span class="font-medium">في الانتظار:</span>
                        <span class="text-yellow-600 font-bold">{{ number_format($expenses_pending, 2) }} جنيه</span>
                    </div>
                    @if($expenses_cancelled > 0)
                    <div class="text-sm text-gray-600">
                        <span class="font-medium">ملغية:</span>
                        <span class="text-red-600 font-bold">{{ number_format($expenses_cancelled, 2) }} جنيه</span>
                    </div>
                    @endif
                </div>
            </div>

            @if($expenses->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">التاريخ</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">العنوان</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">المبلغ</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الموظف</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الفئة</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الحالة</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($expenses as $expense)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $expense->expense_date->format('Y-m-d') }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                <div class="font-medium">{{ $expense->title }}</div>
                                @if($expense->description)
                                    <div class="text-xs text-gray-500 mt-1">{{ Str::limit($expense->description, 50) }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-red-600">
                                {{ number_format($expense->amount, 2) }} جنيه
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                {{ $expense->employee->name ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                {{ $expense->category_badge }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="status-badge status-{{ $expense->status_color }}">
                                    {{ $expense->status_badge }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50">
                        <tr>
                            <td colspan="2" class="px-6 py-4 text-sm font-semibold text-gray-900 text-right">الإجمالي:</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-red-600">
                                {{ number_format($total_expenses, 2) }} جنيه
                            </td>
                            <td colspan="3"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            @else
            <div class="text-center py-12 text-gray-500">
                <i class="fas fa-receipt text-6xl mb-4"></i>
                <p>لا توجد مصروفات في هذا الشهر</p>
            </div>
            @endif
        </div>

        <!-- Summary Card -->
        <div class="card rounded-2xl p-6 bg-gradient-to-r from-blue-50 to-purple-50 border border-blue-200">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">ملخص التقرير المالي</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <p class="text-sm text-gray-600 mb-2">إجمالي الإيرادات</p>
                    <p class="text-2xl font-bold text-green-600">{{ number_format($total_revenues, 2) }} جنيه</p>
                    <p class="text-xs text-gray-500 mt-1">
                        مدفوع: {{ number_format($total_paid_revenues, 2) }} | متبقي: {{ number_format($total_remaining_revenues, 2) }}
                    </p>
                </div>
                <div>
                    <p class="text-sm text-gray-600 mb-2">إجمالي المصروفات</p>
                    <p class="text-2xl font-bold text-red-600">{{ number_format($total_expenses, 2) }} جنيه</p>
                    <p class="text-xs text-gray-500 mt-1">
                        مدفوع: {{ number_format($total_paid_expenses, 2) }} | معلق: {{ number_format($total_pending_expenses, 2) }}
                    </p>
                </div>
                <div>
                    <p class="text-sm text-gray-600 mb-2">صافي الربح</p>
                    <p class="text-2xl font-bold {{ $net_profit >= 0 ? 'text-green-600' : 'text-red-600' }}">
                        {{ number_format($net_profit, 2) }} جنيه
                    </p>
                    <p class="text-xs text-gray-500 mt-1">الإيرادات المدفوعة - المصروفات المدفوعة</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

