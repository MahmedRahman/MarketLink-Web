@extends('layouts.dashboard')

@section('title', 'إدارة الموظفين')
@section('page-title', 'إدارة الموظفين')
@section('page-description', 'عرض وإدارة قائمة الموظفين')

@section('content')
<div class="space-y-6">
    <!-- Header Actions -->
    <div class="card page-header rounded-2xl p-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <div class="w-12 h-12 logo-gradient rounded-2xl flex items-center justify-center shadow-lg icon-spacing ml-3">
                    <i class="fas fa-users-cog text-white text-xl"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">الموظفين</h2>
                    <p class="text-gray-600">إدارة قائمة الموظفين والأدوار الوظيفية</p>
                </div>
            </div>
            <a href="{{ route('employees.create') }}" class="btn-primary text-white px-6 py-3 rounded-xl flex items-center hover:no-underline">
                <i class="fas fa-user-plus text-sm mr-2"></i>
                إضافة موظف جديد
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="card rounded-2xl p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">إجمالي الموظفين</p>
                    <p class="text-3xl font-bold text-gray-800">{{ $employees->total() }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-users text-blue-600"></i>
                </div>
            </div>
        </div>

        <div class="card rounded-2xl p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">الموظفين النشطين</p>
                    <p class="text-3xl font-bold text-green-600">{{ $employees->where('status', 'active')->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-check-circle text-green-600"></i>
                </div>
            </div>
        </div>

        <div class="card rounded-2xl p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">كتاب المحتوى</p>
                    <p class="text-3xl font-bold text-blue-600">{{ $employees->where('role', 'content_writer')->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-pen text-blue-600"></i>
                </div>
            </div>
        </div>

        <div class="card rounded-2xl p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">المصممين</p>
                    <p class="text-3xl font-bold text-purple-600">{{ $employees->where('role', 'designer')->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-palette text-purple-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Employees Table -->
    <div class="card rounded-2xl p-6">
        @if($employees->count() > 0)
            <div class="overflow-x-auto">
                <table id="employeesTable" class="w-full">
                    <thead>
                        <tr class="border-b border-gray-200">
                            <th class="text-right py-3 px-4 font-medium text-gray-700">الاسم</th>
                            <th class="text-right py-3 px-4 font-medium text-gray-700">البريد الإلكتروني</th>
                            <th class="text-right py-3 px-4 font-medium text-gray-700">رقم الهاتف</th>
                            <th class="text-right py-3 px-4 font-medium text-gray-700">الدور الوظيفي</th>
                            <th class="text-right py-3 px-4 font-medium text-gray-700">الحالة</th>
                            <th class="text-right py-3 px-4 font-medium text-gray-700">تاريخ الإنشاء</th>
                            <th class="text-right py-3 px-4 font-medium text-gray-700">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($employees as $employee)
                            <tr class="border-b border-gray-100 hover:bg-gray-50">
                                <td class="py-4 px-4">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-gradient-to-r from-purple-500 to-blue-500 rounded-xl flex items-center justify-center mr-3">
                                            <i class="fas fa-user text-white text-sm"></i>
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">{{ $employee->name }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="text-sm text-gray-900">{{ $employee->email }}</div>
                                </td>
                                <td>
                                    <div class="text-sm text-gray-900">{{ $employee->phone ?? 'غير محدد' }}</div>
                                </td>
                                <td>
                                    <span class="role-badge role-{{ $employee->role_color }}">
                                        {{ $employee->role_badge }}
                                    </span>
                                </td>
                                <td>
                                    <span class="status-badge
                                        @if($employee->status === 'active') status-active
                                        @elseif($employee->status === 'inactive') status-inactive
                                        @else status-pending @endif">
                                        {{ $employee->status_badge }}
                                    </span>
                                </td>
                                <td>
                                    <div class="text-sm text-gray-500">{{ $employee->created_at->format('Y-m-d') }}</div>
                                </td>
                                <td>
                                    <div class="flex items-center space-x-2 rtl:space-x-reverse">
                                        <a href="{{ route('employees.show', $employee) }}" class="text-blue-600 hover:text-blue-900 p-1" title="عرض التفاصيل">
                                            <i class="fas fa-eye text-sm"></i>
                                        </a>
                                        <a href="{{ route('employees.edit', $employee) }}" class="text-yellow-600 hover:text-yellow-900 p-1" title="تعديل">
                                            <i class="fas fa-edit text-sm"></i>
                                        </a>
                                        <button onclick="confirmDelete('{{ route('employees.destroy', $employee) }}', 'تأكيد حذف الموظف', 'هل أنت متأكد من حذف الموظف {{ $employee->name }}؟')" class="text-red-600 hover:text-red-900 p-1" title="حذف">
                                            <i class="fas fa-trash text-sm"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-12">
                <i class="fas fa-users-cog text-6xl text-gray-300 mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">لا يوجد موظفين</h3>
                <p class="text-gray-500 mb-6">ابدأ بإضافة موظف جديد</p>
                <a href="{{ route('employees.create') }}" class="btn-primary text-white px-6 py-3 rounded-xl inline-flex items-center hover:no-underline">
                    <i class="fas fa-user-plus text-sm mr-2"></i>
                    إضافة موظف جديد
                </a>
            </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    $('#employeesTable').DataTable({
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
                    columns: [0, 1, 2, 3, 4, 5]
                }
            },
            {
                extend: 'pdf',
                text: 'تصدير PDF',
                className: 'btn btn-danger',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5]
                }
            },
            {
                extend: 'print',
                text: 'طباعة',
                className: 'btn btn-info',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5]
                }
            }
        ],
        columnDefs: [
            {
                targets: [6], // Actions column
                orderable: false,
                searchable: false
            }
        ],
        order: [[5, 'desc']], // Sort by date descending
        pageLength: 10,
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]]
    });
});
</script>
@endsection
