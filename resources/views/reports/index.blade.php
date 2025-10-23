@extends('layouts.dashboard')

@section('title', 'التقارير المالية')
@section('page-title', 'التقارير المالية')
@section('page-description', 'عرض التقارير المالية الشاملة للمشاريع')

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
                        <h2 class="text-2xl font-bold text-gray-800">التقارير المالية</h2>
                        <p class="text-gray-600">عرض التقارير المالية الشاملة للمشاريع</p>
                    </div>
                </div>
                <div class="flex items-center space-x-3 rtl:space-x-reverse">
                    <a href="{{ route('reports.export', request()->query()) }}" class="flex items-center px-4 py-2 bg-green-600 text-white rounded-xl hover:bg-green-700 transition-colors">
                        <i class="fas fa-download text-sm mr-2"></i>
                        تصدير Excel
                    </a>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="card rounded-2xl p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">فلاتر البحث</h3>
            <form method="GET" action="{{ route('reports.index') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                <!-- Project Filter -->
                <div>
                    <label for="project_id" class="block text-sm font-medium text-gray-700 mb-2">المشروع</label>
                    <select name="project_id" id="project_id" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-colors select2">
                        <option value="">جميع المشاريع</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}" {{ $projectId == $project->id ? 'selected' : '' }}>
                                {{ $project->business_name }} - {{ $project->client->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Client Filter -->
                <div>
                    <label for="client_id" class="block text-sm font-medium text-gray-700 mb-2">العميل</label>
                    <select name="client_id" id="client_id" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-colors select2">
                        <option value="">جميع العملاء</option>
                        @foreach($clients as $client)
                            <option value="{{ $client->id }}" {{ $clientId == $client->id ? 'selected' : '' }}>
                                {{ $client->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Date From -->
                <div>
                    <label for="date_from" class="block text-sm font-medium text-gray-700 mb-2">من تاريخ</label>
                    <input type="date" name="date_from" id="date_from" value="{{ $dateFrom }}" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-colors">
                </div>

                <!-- Date To -->
                <div>
                    <label for="date_to" class="block text-sm font-medium text-gray-700 mb-2">إلى تاريخ</label>
                    <input type="date" name="date_to" id="date_to" value="{{ $dateTo }}" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-colors">
                </div>

                <!-- Status Filter -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">النوع</label>
                    <select name="status" id="status" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-colors select2">
                        <option value="">الكل</option>
                        <option value="revenue" {{ $status === 'revenue' ? 'selected' : '' }}>الإيرادات فقط</option>
                        <option value="expense" {{ $status === 'expense' ? 'selected' : '' }}>المصروفات فقط</option>
                    </select>
                </div>

                <!-- Filter Buttons -->
                <div class="lg:col-span-5 flex items-center justify-center space-x-3 rtl:space-x-reverse pt-4">
                    <button type="submit" class="btn-primary text-white px-6 py-3 rounded-xl flex items-center">
                        <i class="fas fa-search text-sm mr-2"></i>
                        تطبيق الفلاتر
                    </button>
                    <a href="{{ route('reports.index') }}" class="cancel-button px-6 py-3 rounded-xl flex items-center">
                        <i class="fas fa-times text-sm mr-2"></i>
                        إعادة تعيين
                    </a>
                </div>
            </form>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Total Revenues -->
            <div class="card rounded-2xl p-6">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center mr-4">
                        <i class="fas fa-arrow-up text-green-600 text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">إجمالي الإيرادات</p>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($totalRevenues, 2) }} جنيه</p>
                    </div>
                </div>
            </div>

            <!-- Total Expenses -->
            <div class="card rounded-2xl p-6">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center mr-4">
                        <i class="fas fa-arrow-down text-red-600 text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">إجمالي المصروفات</p>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($totalExpenses, 2) }} جنيه</p>
                    </div>
                </div>
            </div>

            <!-- Net Profit -->
            <div class="card rounded-2xl p-6">
                <div class="flex items-center">
                    <div class="w-12 h-12 {{ $netProfit >= 0 ? 'bg-blue-100' : 'bg-orange-100' }} rounded-xl flex items-center justify-center mr-4">
                        <i class="fas fa-chart-line {{ $netProfit >= 0 ? 'text-blue-600' : 'text-orange-600' }} text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">صافي الربح</p>
                        <p class="text-2xl font-bold {{ $netProfit >= 0 ? 'text-blue-600' : 'text-orange-600' }}">
                            {{ number_format($netProfit, 2) }} جنيه
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Project Statistics (if specific project selected) -->
        @if($projectStats)
        <div class="card rounded-2xl p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">إحصائيات المشروع: {{ $projectStats['project']->business_name }}</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="text-center">
                    <p class="text-sm text-gray-600">إجمالي الإيرادات</p>
                    <p class="text-xl font-bold text-green-600">{{ number_format($projectStats['total_revenues'], 2) }} جنيه</p>
                </div>
                <div class="text-center">
                    <p class="text-sm text-gray-600">إجمالي المصروفات</p>
                    <p class="text-xl font-bold text-red-600">{{ number_format($projectStats['total_expenses'], 2) }} جنيه</p>
                </div>
                <div class="text-center">
                    <p class="text-sm text-gray-600">صافي الربح</p>
                    <p class="text-xl font-bold {{ $projectStats['net_profit'] >= 0 ? 'text-blue-600' : 'text-orange-600' }}">
                        {{ number_format($projectStats['net_profit'], 2) }} جنيه
                    </p>
                </div>
            </div>
        </div>
        @endif

        <!-- Monthly Chart -->
        <div class="card rounded-2xl p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">البيانات الشهرية</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="border-b border-gray-200">
                            <th class="text-right py-3 px-4 font-medium text-gray-600">الشهر</th>
                            <th class="text-right py-3 px-4 font-medium text-gray-600">الإيرادات</th>
                            <th class="text-right py-3 px-4 font-medium text-gray-600">المصروفات</th>
                            <th class="text-right py-3 px-4 font-medium text-gray-600">الربح</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($monthlyData as $month)
                        <tr class="border-b border-gray-100">
                            <td class="py-3 px-4 text-gray-900">{{ $month['month'] }}</td>
                            <td class="py-3 px-4 text-green-600 font-medium">{{ number_format($month['revenues'], 2) }} جنيه</td>
                            <td class="py-3 px-4 text-red-600 font-medium">{{ number_format($month['expenses'], 2) }} جنيه</td>
                            <td class="py-3 px-4 font-medium {{ $month['profit'] >= 0 ? 'text-blue-600' : 'text-orange-600' }}">
                                {{ number_format($month['profit'], 2) }} جنيه
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Detailed Reports -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Revenues Table -->
            <div class="card rounded-2xl p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">الإيرادات</h3>
                @if($revenues->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead>
                                <tr class="border-b border-gray-200">
                                    <th class="text-right py-2 px-3 text-xs font-medium text-gray-500">المشروع</th>
                                    <th class="text-right py-2 px-3 text-xs font-medium text-gray-500">العنوان</th>
                                    <th class="text-right py-2 px-3 text-xs font-medium text-gray-500">المبلغ</th>
                                    <th class="text-right py-2 px-3 text-xs font-medium text-gray-500">التاريخ</th>
                                    <th class="text-right py-2 px-3 text-xs font-medium text-gray-500">الحالة</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($revenues as $revenue)
                                <tr class="border-b border-gray-100">
                                    <td class="py-2 px-3 text-sm text-gray-900">{{ $revenue->project->business_name ?? 'غير محدد' }}</td>
                                    <td class="py-2 px-3 text-sm text-gray-900">{{ Str::limit($revenue->title, 20) }}</td>
                                    <td class="py-2 px-3 text-sm font-medium text-green-600">{{ number_format($revenue->amount, 2) }} جنيه</td>
                                    <td class="py-2 px-3 text-sm text-gray-500">{{ $revenue->revenue_date->format('Y-m-d') }}</td>
                                    <td class="py-2 px-3">
                                        <span class="status-badge status-{{ $revenue->status_color }}">
                                            {{ $revenue->status_badge }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-8">
                        <i class="fas fa-chart-line text-4xl text-gray-300 mb-2"></i>
                        <p class="text-gray-500">لا توجد إيرادات</p>
                    </div>
                @endif
            </div>

            <!-- Expenses Table -->
            <div class="card rounded-2xl p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">المصروفات</h3>
                @if($expenses->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead>
                                <tr class="border-b border-gray-200">
                                    <th class="text-right py-2 px-3 text-xs font-medium text-gray-500">المشروع</th>
                                    <th class="text-right py-2 px-3 text-xs font-medium text-gray-500">العنوان</th>
                                    <th class="text-right py-2 px-3 text-xs font-medium text-gray-500">المبلغ</th>
                                    <th class="text-right py-2 px-3 text-xs font-medium text-gray-500">التاريخ</th>
                                    <th class="text-right py-2 px-3 text-xs font-medium text-gray-500">الحالة</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($expenses as $expense)
                                <tr class="border-b border-gray-100">
                                    <td class="py-2 px-3 text-sm text-gray-900">{{ $expense->project->business_name ?? 'غير محدد' }}</td>
                                    <td class="py-2 px-3 text-sm text-gray-900">{{ Str::limit($expense->title, 20) }}</td>
                                    <td class="py-2 px-3 text-sm font-medium text-red-600">{{ number_format($expense->amount, 2) }} جنيه</td>
                                    <td class="py-2 px-3 text-sm text-gray-500">{{ $expense->expense_date->format('Y-m-d') }}</td>
                                    <td class="py-2 px-3">
                                        <span class="status-badge status-{{ $expense->status_color }}">
                                            {{ $expense->status_badge }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-8">
                        <i class="fas fa-receipt text-4xl text-gray-300 mb-2"></i>
                        <p class="text-gray-500">لا توجد مصروفات</p>
                    </div>
                @endif
            </div>
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

