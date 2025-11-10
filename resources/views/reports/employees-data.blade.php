@extends('layouts.dashboard')

@section('title', 'بيانات الموظفين')
@section('page-title', 'بيانات الموظفين')
@section('page-description', 'عرض بيانات الموظفين الفعلية')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="card page-header rounded-2xl p-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <div class="w-12 h-12 logo-gradient rounded-2xl flex items-center justify-center shadow-lg icon-spacing ml-3">
                    <i class="fas fa-user-circle text-white text-xl"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">بيانات الموظفين</h2>
                    <p class="text-gray-600">عرض بيانات الموظفين الفعلية</p>
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

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="card rounded-2xl p-6">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center ml-4">
                    <i class="fas fa-users text-blue-600 text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">إجمالي الموظفين</p>
                    <p class="text-2xl font-bold text-gray-900">
                        {{ $employees->count() }}
                    </p>
                </div>
            </div>
        </div>

        <div class="card rounded-2xl p-6">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center ml-4">
                    <i class="fas fa-check-circle text-green-600 text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">الموظفين النشطين</p>
                    <p class="text-2xl font-bold text-gray-900">
                        {{ $employees->where('status', 'active')->count() }}
                    </p>
                </div>
            </div>
        </div>

        <div class="card rounded-2xl p-6">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center ml-4">
                    <i class="fas fa-briefcase text-purple-600 text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">إجمالي المشاريع</p>
                    <p class="text-2xl font-bold text-gray-900">
                        {{ $employees->sum('projects_count') }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Employees Table -->
    <div class="card rounded-2xl p-6">
        @if($employees->count() > 0)
            <div class="overflow-x-auto">
                <table id="employeesDataTable" class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider" style="text-align: justify; padding-right: 33px;">
                                الاسم
                            </th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider" style="text-align: justify; padding-right: 33px;">
                                الهاتف
                            </th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider" style="text-align: justify; padding-right: 33px;">
                                البريد الإلكتروني
                            </th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider" style="text-align: justify; padding-right: 33px;">
                                معلومات الدفع
                            </th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider" style="text-align: justify; padding-right: 33px;">
                                المشاريع
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($employees as $employee)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-gradient-to-r from-purple-500 to-blue-500 rounded-xl flex items-center justify-center ml-3">
                                            <i class="fas fa-user text-white text-sm"></i>
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">{{ $employee->name }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        {{ $employee->phone ?? '-' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        {{ $employee->email }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900 space-y-1">
                                        @if($employee->instapay_number)
                                            <div><span class="font-medium">إنستاباي:</span> {{ $employee->instapay_number }}</div>
                                        @endif
                                        @if($employee->vodafone_cash_number)
                                            <div><span class="font-medium">فودافون كاش:</span> {{ $employee->vodafone_cash_number }}</div>
                                        @endif
                                        @if(!$employee->instapay_number && !$employee->vodafone_cash_number)
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900">
                                        @if($employee->projects->count() > 0)
                                            <div class="flex flex-wrap gap-2">
                                                @foreach($employee->projects as $project)
                                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                        {{ $project->business_name }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="text-gray-400">لا يوجد مشاريع</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-12">
                <i class="fas fa-user-circle text-6xl text-gray-300 mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">لا يوجد بيانات</h3>
                <p class="text-gray-500">لا توجد موظفين مسجلين</p>
            </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script>
var table;

$(document).ready(function() {
    table = $('#employeesDataTable').DataTable({
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
        order: [[0, 'asc']], // Sort by name ascending
        columnDefs: [
            {
                targets: [3, 4], // Payment info, Projects columns
                orderable: false
            }
        ],
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

