@extends('layouts.dashboard')

@section('title', 'تقرير المديونية')
@section('page-title', 'تقرير المديونية')
@section('page-description', 'عرض المديونية لكل مشروع')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="card page-header rounded-2xl p-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <div class="w-12 h-12 logo-gradient rounded-2xl flex items-center justify-center shadow-lg icon-spacing ml-3">
                    <i class="fas fa-file-invoice-dollar text-white text-xl"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">تقرير المديونية</h2>
                    <p class="text-gray-600">عرض المبلغ المتبقي والمحصل لكل مشروع</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Month Filter -->
    <div class="card rounded-2xl p-6">
        <form method="GET" action="{{ route('reports.receivables') }}" class="flex items-center gap-4">
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
                <a href="{{ route('reports.receivables') }}" class="btn-secondary text-white px-6 py-3 rounded-xl hover:no-underline">
                    <i class="fas fa-times text-sm ml-2"></i>
                    إلغاء الفلتر
                </a>
            </div>
            @endif
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
                    <p class="text-sm text-gray-600">إجمالي المبلغ المحصل</p>
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
                    <p class="text-sm text-gray-600">إجمالي المبلغ المتبقي</p>
                    <p class="text-2xl font-bold text-gray-900">
                        {{ number_format($grandTotalRemaining, 2) }} جنيه
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Projects Table -->
    <div class="card rounded-2xl p-6">
        @if($projectsData->count() > 0)
            <div class="overflow-x-auto">
                <table id="receivablesTable" class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                اسم المشروع
                            </th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                العميل
                            </th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                المبلغ المحصل
                            </th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                المبلغ المتبقي
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
                                            <div class="text-xs text-gray-500">{{ Str::limit($data['project']->business_description, 50) }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $data['project']->client->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $data['project']->client->email }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-semibold text-green-600">
                                        {{ number_format($data['total_paid'], 2) }} جنيه
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-semibold text-orange-600">
                                        {{ number_format($data['total_remaining'], 2) }} جنيه
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                    <a href="{{ route('projects.revenues.index', $data['project']) }}" class="text-blue-600 hover:text-blue-900 p-1" title="عرض الإيرادات">
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
                                    {{ number_format($grandTotalRemaining, 2) }} جنيه
                                </div>
                            </td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @else
            <div class="text-center py-12">
                <i class="fas fa-file-invoice-dollar text-6xl text-gray-300 mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">لا يوجد مشاريع</h3>
                <p class="text-gray-500">لا توجد مشاريع لعرض المديونية</p>
            </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    $('#receivablesTable').DataTable({
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
        order: [[2, 'desc']], // Sort by paid amount descending
        pageLength: 10,
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]]
    });
});
</script>
@endsection

