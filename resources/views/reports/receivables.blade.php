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
            <div class="flex items-center space-x-3 rtl:space-x-reverse">
            </div>
        </div>
    </div>

    <!-- Monthly Records Cards -->
    <div class="card rounded-2xl p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4">السجلات الشهرية</h3>
        <div class="flex flex-wrap gap-4">
            <!-- All Card -->
            <a href="{{ route('reports.receivables') }}" class="monthly-record-card flex-1 min-w-[150px] max-w-[200px] p-4 rounded-xl border-2 transition-all cursor-pointer {{ !request('month') ? 'border-primary bg-primary-50' : 'border-gray-200 hover:border-primary hover:bg-gray-50' }}">
                <div class="text-center">
                    <div class="w-12 h-12 bg-primary rounded-xl flex items-center justify-center mx-auto mb-2">
                        <i class="fas fa-calendar-alt text-white text-xl"></i>
                    </div>
                    <h4 class="font-bold text-gray-800">الكل</h4>
                    <p class="text-sm text-gray-600 mt-1">{{ $totalCount }} سجل</p>
                </div>
            </a>

            <!-- Monthly Records Cards -->
            @foreach($monthlyRecords as $record)
                @php
                    $recordMonthValue = $record['record_month_year'];
                    $months = [
                        1 => 'يناير', 2 => 'فبراير', 3 => 'مارس', 4 => 'أبريل',
                        5 => 'مايو', 6 => 'يونيو', 7 => 'يوليو', 8 => 'أغسطس',
                        9 => 'سبتمبر', 10 => 'أكتوبر', 11 => 'نوفمبر', 12 => 'ديسمبر'
                    ];
                    $parts = explode('-', $recordMonthValue);
                    $year = $parts[0] ?? '';
                    $monthNum = isset($parts[1]) ? (int)$parts[1] : 0;
                    $monthName = $months[$monthNum] ?? '';
                    $isActive = request('month') == $recordMonthValue;
                    $count = $record['count'];
                @endphp
                <a href="{{ route('reports.receivables', ['month' => $recordMonthValue]) }}" class="monthly-record-card flex-1 min-w-[150px] max-w-[200px] p-4 rounded-xl border-2 transition-all cursor-pointer {{ $isActive ? 'border-primary bg-primary-50' : 'border-gray-200 hover:border-primary hover:bg-gray-50' }}">
                    <div class="text-center">
                        <div class="w-12 h-12 {{ $isActive ? 'bg-primary' : 'bg-gray-200' }} rounded-xl flex items-center justify-center mx-auto mb-2">
                            <i class="fas fa-calendar text-white text-xl"></i>
                        </div>
                        <h4 class="font-bold text-gray-800">{{ $monthName }}</h4>
                        <p class="text-xs text-gray-500">{{ $year }}</p>
                        <p class="text-sm text-gray-600 mt-1">{{ $count }} سجل</p>
                    </div>
                </a>
            @endforeach
        </div>
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
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider" style="text-align: justify; padding-right: 33px;">
                                اسم المشروع
                            </th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider" style="text-align: justify; padding-right: 33px;">
                                العميل
                            </th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider" style="text-align: justify; padding-right: 33px;">
                                المبلغ المحصل
                            </th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider" style="text-align: justify; padding-right: 33px;">
                                المبلغ المتبقي
                            </th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider" style="text-align: justify; padding-right: 33px;">
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

@section('styles')
<style>
.monthly-record-card {
    text-decoration: none;
    transition: all 0.3s ease;
}

.monthly-record-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.monthly-record-card h4 {
    transition: color 0.3s ease;
}

.monthly-record-card:hover h4 {
    color: #6366f1;
}
</style>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    $('#receivablesTable').DataTable({
        responsive: true,
        paging: true,
        searching: false,
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
        dom: 'rtip',
        columnDefs: [
            {
                targets: [4], // Actions column
                orderable: false,
                searchable: false
            }
        ],
        order: [[2, 'desc']], // Sort by paid amount descending
        pageLength: 100,
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]]
    });
});
</script>
@endsection

