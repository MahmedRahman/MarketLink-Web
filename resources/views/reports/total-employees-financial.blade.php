@extends('layouts.dashboard')

@section('title', 'إجمالي التقرير المالي للموظفين')
@section('page-title', 'إجمالي التقرير المالي للموظفين')
@section('page-description', 'عرض إجمالي المبالغ المدفوعة والمستحقة لكل موظف')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="card page-header rounded-2xl p-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <div class="w-12 h-12 logo-gradient rounded-2xl flex items-center justify-center shadow-lg icon-spacing ml-3">
                    <i class="fas fa-users-cog text-white text-xl"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">إجمالي التقرير المالي للموظفين</h2>
                    <p class="text-gray-600">عرض إجمالي المبالغ المدفوعة والمستحقة لكل موظف</p>
                </div>
            </div>
            <div class="flex items-center space-x-3 rtl:space-x-reverse">
                <button onclick="exportToExcel()" class="w-10 h-10 bg-green-500 hover:bg-green-600 text-white rounded-xl flex items-center justify-center transition-colors" title="تصدير Excel">
                    <i class="fas fa-file-excel text-lg"></i>
                </button>
                <button onclick="exportToPDF()" class="w-10 h-10 bg-red-500 hover:bg-red-600 text-white rounded-xl flex items-center justify-center transition-colors" title="تصدير PDF">
                    <i class="fas fa-file-pdf text-lg"></i>
                </button>
                <button onclick="printTable()" class="w-10 h-10 bg-blue-500 hover:bg-blue-600 text-white rounded-xl flex items-center justify-center transition-colors" title="طباعة">
                    <i class="fas fa-print text-lg"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Date Filters -->
    <div class="card rounded-2xl p-6">
        <form method="GET" action="{{ route('reports.total-employees-financial') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label for="date_from" class="block text-sm font-medium text-gray-700 mb-2">
                    من تاريخ
                </label>
                <input
                    type="date"
                    id="date_from"
                    name="date_from"
                    value="{{ $dateFrom ?? '' }}"
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-colors"
                />
            </div>
            <div>
                <label for="date_to" class="block text-sm font-medium text-gray-700 mb-2">
                    إلى تاريخ
                </label>
                <input
                    type="date"
                    id="date_to"
                    name="date_to"
                    value="{{ $dateTo ?? '' }}"
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-colors"
                />
            </div>
            <div class="flex items-end gap-3">
                <button type="submit" class="btn-primary text-white px-6 py-3 rounded-xl hover:no-underline flex items-center">
                    <i class="fas fa-search text-sm ml-2"></i>
                    بحث
                </button>
                @if($dateFrom || $dateTo)
                <a href="{{ route('reports.total-employees-financial') }}" class="btn-secondary text-white px-6 py-3 rounded-xl hover:no-underline flex items-center">
                    <i class="fas fa-times text-sm ml-2"></i>
                    إلغاء
                </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="card rounded-2xl p-6">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center ml-4">
                    <i class="fas fa-check-circle text-green-600 text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">إجمالي المبالغ المدفوعة</p>
                    <p class="text-2xl font-bold text-gray-900">
                        {{ number_format($grandTotalPaid, 2) }} جنيه
                    </p>
                </div>
            </div>
        </div>

        <div class="card rounded-2xl p-6">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center ml-4">
                    <i class="fas fa-clock text-yellow-600 text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">إجمالي المبالغ المستحقة</p>
                    <p class="text-2xl font-bold text-gray-900">
                        {{ number_format($grandTotalPending, 2) }} جنيه
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Employees Table -->
    <div class="card rounded-2xl p-6">
        @if($employeesData->count() > 0)
            <div class="overflow-x-auto">
                <table id="employeesFinancialTable" class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider" style="text-align: justify; padding-right: 33px;">
                                اسم الموظف
                            </th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider" style="text-align: justify; padding-right: 33px;">
                                عدد المشاريع
                            </th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider" style="text-align: justify; padding-right: 33px;">
                                إجمالي المبلغ المدفوع
                            </th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider" style="text-align: justify; padding-right: 33px;">
                                المبلغ المستحق
                            </th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider" style="text-align: justify; padding-right: 33px;">
                                الإجراءات
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($employeesData as $data)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-gradient-to-r from-purple-500 to-blue-500 rounded-xl flex items-center justify-center ml-3">
                                            <i class="fas fa-user text-white text-sm"></i>
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">{{ $data['employee']->name }}</div>
                                            <div class="text-xs text-gray-500">{{ $data['employee']->role_badge }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-semibold text-blue-600">
                                        {{ $data['projects_count'] }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($data['total_paid'] > 0)
                                    <a href="{{ route('reports.employee-paid-expenses', ['employee' => $data['employee']->id, 'date_from' => $dateFrom, 'date_to' => $dateTo]) }}" class="text-sm font-semibold text-green-600 hover:text-green-800 hover:underline cursor-pointer">
                                        {{ number_format($data['total_paid'], 2) }} جنيه
                                    </a>
                                    @else
                                    <div class="text-sm font-semibold text-gray-400">
                                        {{ number_format($data['total_paid'], 2) }} جنيه
                                    </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-semibold text-orange-600">
                                        {{ number_format($data['total_pending'], 2) }} جنيه
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                    <a href="{{ route('reports.employee-financial', ['employee_id' => $data['employee']->id, 'date_from' => $dateFrom, 'date_to' => $dateTo]) }}" class="text-blue-600 hover:text-blue-900 p-1" title="عرض التفاصيل">
                                        <i class="fas fa-eye text-sm"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50">
                        <tr>
                            <td colspan="2" class="px-6 py-4 text-right font-bold text-gray-900">
                                الإجمالي
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-bold text-green-600">
                                    {{ number_format($grandTotalPaid, 2) }} جنيه
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-bold text-orange-600">
                                    {{ number_format($grandTotalPending, 2) }} جنيه
                                </div>
                            </td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @else
            <div class="text-center py-12">
                <i class="fas fa-users-cog text-6xl text-gray-300 mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">لا يوجد بيانات</h3>
                <p class="text-gray-500">لا توجد مصروفات للموظفين في الفترة المحددة</p>
            </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script>
var table;

$(document).ready(function() {
    table = $('#employeesFinancialTable').DataTable({
        responsive: true,
        paging: false,
        searching: false,
        language: {
            "sProcessing": "جاري المعالجة...",
            "sZeroRecords": "لم يتم العثور على سجلات",
            "sInfo": "عرض _START_ إلى _END_ من _TOTAL_ سجل",
            "sInfoEmpty": "عرض 0 إلى 0 من 0 سجل",
            "sInfoPostFix": ""
        },
        dom: 'rti',
        columnDefs: [
            {
                targets: [4], // Actions column
                orderable: false,
                searchable: false
            }
        ],
        order: [[2, 'desc']], // Sort by paid amount descending
        buttons: [
            {
                extend: 'excel',
                text: 'Excel',
                exportOptions: {
                    columns: [0, 1, 2, 3]
                }
            },
            {
                extend: 'pdf',
                text: 'PDF',
                exportOptions: {
                    columns: [0, 1, 2, 3]
                }
            },
            {
                extend: 'print',
                text: 'Print',
                exportOptions: {
                    columns: [0, 1, 2, 3]
                }
            }
        ]
    });
});

function exportToExcel() {
    table.button(0).trigger();
}

function exportToPDF() {
    table.button(1).trigger();
}

function printTable() {
    table.button(2).trigger();
}
</script>
@endsection

