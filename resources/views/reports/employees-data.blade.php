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
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                الاسم
                            </th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                الهاتف
                            </th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                البريد الإلكتروني
                            </th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                الدور الوظيفي
                            </th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                الحالة
                            </th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                عدد المشاريع
                            </th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                معلومات الدفع
                            </th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                الروابط الاجتماعية
                            </th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                الملاحظات
                            </th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                تاريخ الإنشاء
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
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full status-badge status-{{ $employee->role_color }}">
                                        {{ $employee->role_badge }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full status-badge status-{{ $employee->status }}">
                                        {{ $employee->status_badge }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-semibold text-blue-600">
                                        {{ $employee->projects_count }}
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
                                    <div class="flex items-center gap-2 flex-wrap">
                                        @if($employee->facebook_url)
                                            <a href="{{ $employee->facebook_url }}" target="_blank" class="text-blue-600 hover:text-blue-800" title="فيسبوك">
                                                <i class="fab fa-facebook text-lg"></i>
                                            </a>
                                        @endif
                                        @if($employee->linkedin_url)
                                            <a href="{{ $employee->linkedin_url }}" target="_blank" class="text-blue-700 hover:text-blue-900" title="لينكد إن">
                                                <i class="fab fa-linkedin text-lg"></i>
                                            </a>
                                        @endif
                                        @if($employee->portfolio_url)
                                            <a href="{{ $employee->portfolio_url }}" target="_blank" class="text-purple-600 hover:text-purple-800" title="البورتفوليو">
                                                <i class="fas fa-briefcase text-lg"></i>
                                            </a>
                                        @endif
                                        @if(!$employee->facebook_url && !$employee->linkedin_url && !$employee->portfolio_url)
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900 max-w-xs truncate" title="{{ $employee->notes }}">
                                        {{ $employee->notes ?? '-' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-500">
                                        {{ $employee->created_at->format('Y-m-d') }}
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
$(document).ready(function() {
    $('#employeesDataTable').DataTable({
        responsive: true,
        paging: false,
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
                    columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9]
                }
            },
            {
                extend: 'pdf',
                text: 'تصدير PDF',
                className: 'btn btn-danger',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9]
                }
            },
            {
                extend: 'print',
                text: 'طباعة',
                className: 'btn btn-info',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9]
                }
            }
        ],
        order: [[0, 'asc']], // Sort by name ascending
        columnDefs: [
            {
                targets: [6, 7, 8], // Payment info, Social links, Notes columns
                orderable: false
            }
        ]
    });
});
</script>
@endsection

