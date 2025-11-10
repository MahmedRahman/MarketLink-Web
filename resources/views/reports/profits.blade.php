@extends('layouts.dashboard')

@section('title', 'تقرير الأرباح')
@section('page-title', 'تقرير الأرباح')
@section('page-description', 'عرض الأرباح لكل مشروع')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="card page-header rounded-2xl p-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <div class="w-12 h-12 logo-gradient rounded-2xl flex items-center justify-center shadow-lg icon-spacing ml-3">
                    <i class="fas fa-chart-pie text-white text-xl"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">تقرير الأرباح</h2>
                    <p class="text-gray-600">عرض الإيرادات المحصلة والمصروفات المدفوعة والربح لكل مشروع</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Month Filter -->
    <div class="card rounded-2xl p-6">
        <form method="GET" action="{{ route('reports.profits') }}" class="flex items-center gap-4">
            <div class="flex-1">
                <label for="month" class="block text-sm font-medium text-gray-700 mb-2">
                    فلترة بالشهر
                </label>
                <input
                    type="month"
                    id="month"
                    name="month"
                    value="{{ $selectedMonth ?? '' }}"
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-colors"
                    onchange="this.form.submit()"
                />
            </div>
            @if($selectedMonth)
            <div class="flex items-end">
                <a href="{{ route('reports.profits') }}" class="btn-secondary text-white px-6 py-3 rounded-xl hover:no-underline">
                    <i class="fas fa-times text-sm ml-2"></i>
                    إلغاء الفلتر
                </a>
            </div>
            @endif
        </form>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="card rounded-2xl p-6">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center ml-4">
                    <i class="fas fa-arrow-up text-green-600 text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">إجمالي الإيرادات المحصلة</p>
                    <p class="text-2xl font-bold text-gray-900">
                        {{ number_format($grandTotalPaidRevenues, 2) }} جنيه
                    </p>
                </div>
            </div>
        </div>

        <div class="card rounded-2xl p-6">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center ml-4">
                    <i class="fas fa-arrow-down text-red-600 text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">إجمالي المصروفات المدفوعة</p>
                    <p class="text-2xl font-bold text-gray-900">
                        {{ number_format($grandTotalPaidExpenses, 2) }} جنيه
                    </p>
                </div>
            </div>
        </div>

        <div class="card rounded-2xl p-6">
            <div class="flex items-center">
                <div class="w-12 h-12 {{ $grandTotalProfit >= 0 ? 'bg-blue-100' : 'bg-red-100' }} rounded-xl flex items-center justify-center ml-4">
                    <i class="fas fa-chart-line {{ $grandTotalProfit >= 0 ? 'text-blue-600' : 'text-red-600' }} text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">إجمالي الأرباح</p>
                    <p class="text-2xl font-bold {{ $grandTotalProfit >= 0 ? 'text-blue-600' : 'text-red-600' }}">
                        {{ number_format($grandTotalProfit, 2) }} جنيه
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Projects Table -->
    <div class="card rounded-2xl p-6">
        @if($projectsData->count() > 0)
            <div class="overflow-x-auto">
                <table id="profitsTable" class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                اسم المشروع
                            </th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                الإيرادات المحصلة
                            </th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                المصروفات المدفوعة
                            </th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                الربح
                            </th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                الإجراءات
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($projectsData as $data)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-gradient-to-r from-purple-500 to-blue-500 rounded-xl flex items-center justify-center ml-3">
                                            <i class="fas fa-building text-white text-sm"></i>
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">{{ $data['project']->business_name }}</div>
                                            <div class="text-xs text-gray-500">{{ $data['project']->client->name }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-semibold text-green-600">
                                        {{ number_format($data['total_paid_revenues'], 2) }} جنيه
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-semibold text-red-600">
                                        {{ number_format($data['total_paid_expenses'], 2) }} جنيه
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-semibold {{ $data['profit'] >= 0 ? 'text-blue-600' : 'text-red-600' }}">
                                        {{ number_format($data['profit'], 2) }} جنيه
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                    <a href="{{ route('projects.show', $data['project']) }}" class="text-blue-600 hover:text-blue-900 p-1" title="عرض المشروع">
                                        <i class="fas fa-eye text-sm"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50">
                        <tr>
                            <td class="px-6 py-4 text-right font-bold text-gray-900">
                                الإجمالي
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-bold text-green-600">
                                    {{ number_format($grandTotalPaidRevenues, 2) }} جنيه
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-bold text-red-600">
                                    {{ number_format($grandTotalPaidExpenses, 2) }} جنيه
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-bold {{ $grandTotalProfit >= 0 ? 'text-blue-600' : 'text-red-600' }}">
                                    {{ number_format($grandTotalProfit, 2) }} جنيه
                                </div>
                            </td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @else
            <div class="text-center py-12">
                <i class="fas fa-chart-pie text-6xl text-gray-300 mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">لا يوجد بيانات</h3>
                <p class="text-gray-500">لا توجد مشاريع لعرض الأرباح</p>
            </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    $('#profitsTable').DataTable({
        responsive: true,
        language: {
            "sProcessing": "جاري المعالجة...",
            "sLengthMenu": "عرض _MENU_ سجل",
            "sZeroRecords": "لم يتم العثور على سجلات",
            "sInfo": "عرض _START_ إلى _END_ من _TOTAL_ سجل",
            "sInfoEmpty": "عرض 0 إلى 0 من 0 سجل",
            "sInfoFiltered": "(تصفية من _MAX_ سجل)",
            "sInfoPostFix": "",
            "sSearch": "البحث:",
            "sUrl": "",
            "oPaginate": {
                "sFirst": "الأول",
                "sPrevious": "السابق",
                "sNext": "التالي",
                "sLast": "الأخير"
            }
        },
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'excel',
                text: 'تصدير Excel',
                className: 'btn btn-success',
                exportOptions: {
                    columns: [0, 1, 2, 3]
                }
            },
            {
                extend: 'pdf',
                text: 'تصدير PDF',
                className: 'btn btn-danger',
                exportOptions: {
                    columns: [0, 1, 2, 3]
                }
            },
            {
                extend: 'print',
                text: 'طباعة',
                className: 'btn btn-info',
                exportOptions: {
                    columns: [0, 1, 2, 3]
                }
            }
        ],
        columnDefs: [
            {
                targets: [4], // Actions column
                orderable: false,
                searchable: false
            }
        ],
        order: [[3, 'desc']], // Sort by profit descending
        pageLength: 10,
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]]
    });
});
</script>
@endsection

