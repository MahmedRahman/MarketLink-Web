@extends('layouts.dashboard')

@section('title', 'تفاصيل المصروفات المدفوعة')
@section('page-title', 'تفاصيل المصروفات المدفوعة')
@section('page-description', 'عرض تفاصيل المصروفات المدفوعة للموظف: ' . $employee->name)

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="card page-header rounded-2xl p-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <div class="w-12 h-12 logo-gradient rounded-2xl flex items-center justify-center shadow-lg icon-spacing ml-3">
                    <i class="fas fa-user-check text-white text-xl"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">تفاصيل المصروفات المدفوعة</h2>
                    <p class="text-gray-600">الموظف: {{ $employee->name }}</p>
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
                <a href="{{ route('reports.total-employees-financial', ['date_from' => $dateFrom, 'date_to' => $dateTo]) }}" class="flex items-center px-4 py-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-xl transition-colors">
                    <i class="fas fa-arrow-right text-sm ml-2"></i>
                    العودة للتقرير
                </a>
            </div>
        </div>
    </div>

    <!-- Summary Card -->
    <div class="card rounded-2xl p-6">
        <div class="flex items-center">
            <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center ml-4">
                <i class="fas fa-check-circle text-green-600 text-xl"></i>
            </div>
            <div>
                <p class="text-sm text-gray-600">إجمالي المبلغ المدفوع</p>
                <p class="text-2xl font-bold text-gray-900">
                    {{ number_format($totalPaid, 2) }} جنيه
                </p>
            </div>
        </div>
    </div>

    <!-- Expenses Table -->
    <div class="card rounded-2xl p-6">
        @if($expenses->count() > 0)
            <div class="overflow-x-auto">
                <table id="paidExpensesTable" class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider" style="text-align: justify; padding-right: 33px;">
                                التاريخ
                            </th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider" style="text-align: justify; padding-right: 33px;">
                                المشروع
                            </th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider" style="text-align: justify; padding-right: 33px;">
                                العنوان
                            </th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider" style="text-align: justify; padding-right: 33px;">
                                المبلغ
                            </th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider" style="text-align: justify; padding-right: 33px;">
                                طريقة الدفع
                            </th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider" style="text-align: justify; padding-right: 33px;">
                                الإجراءات
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($expenses as $expense)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-500">{{ $expense->expense_date->format('Y-m-d') }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $expense->project->business_name }}</div>
                                    <div class="text-xs text-gray-500">{{ $expense->project->client->name }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $expense->title }}</div>
                                    @if($expense->description)
                                        <div class="text-sm text-gray-500">{{ Str::limit($expense->description, 50) }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-semibold text-green-600">{{ $expense->formatted_amount }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $expense->payment_method_badge }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                    <a href="{{ route('projects.expenses.show', [$expense->project, $expense]) }}" class="text-blue-600 hover:text-blue-900 p-1" title="عرض التفاصيل">
                                        <i class="fas fa-eye text-sm"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50">
                        <tr>
                            <td colspan="3" class="px-6 py-4 text-right font-bold text-gray-900">
                                الإجمالي
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-bold text-green-600">
                                    {{ number_format($totalPaid, 2) }} جنيه
                                </div>
                            </td>
                            <td colspan="2"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @else
            <div class="text-center py-12">
                <i class="fas fa-receipt text-6xl text-gray-300 mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">لا يوجد مصروفات مدفوعة</h3>
                <p class="text-gray-500">لا توجد مصروفات مدفوعة للموظف في الفترة المحددة</p>
            </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script>
var table;

$(document).ready(function() {
    table = $('#paidExpensesTable').DataTable({
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
                targets: [5], // Actions column
                orderable: false,
                searchable: false
            }
        ],
        order: [[0, 'desc']], // Sort by date descending
        buttons: [
            {
                extend: 'excel',
                text: 'Excel',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4]
                }
            },
            {
                extend: 'pdf',
                text: 'PDF',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4]
                }
            },
            {
                extend: 'print',
                text: 'Print',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4]
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

