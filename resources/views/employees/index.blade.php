@extends('layouts.dashboard')

@section('title', 'إدارة الموظفين')
@section('page-title', 'إدارة الموظفين')
@section('page-description', 'عرض وإدارة قائمة الموظفين')

@section('content')
<div class="space-y-6">
    <!-- Header Actions -->
    <div class="card page-header rounded-xl md:rounded-2xl p-4 md:p-6 mb-4 md:mb-6">
        <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
            <div class="flex items-center flex-1">
                <div class="w-10 h-10 md:w-12 md:h-12 logo-gradient rounded-xl md:rounded-2xl flex items-center justify-center shadow-lg icon-spacing ml-2 md:ml-3 flex-shrink-0">
                    <i class="fas fa-users-cog text-white text-lg md:text-xl"></i>
                </div>
                <div>
                    <h2 class="text-xl md:text-2xl font-bold text-gray-800">الموظفين</h2>
                    <p class="text-sm md:text-base text-gray-600 hidden md:block">إدارة قائمة الموظفين والأدوار الوظيفية</p>
                </div>
            </div>
            <div class="flex items-center gap-2 md:gap-3 w-full md:w-auto">
                <button onclick="exportToExcel()" class="w-9 h-9 md:w-10 md:h-10 bg-green-500 hover:bg-green-600 text-white rounded-xl flex items-center justify-center transition-colors flex-shrink-0" title="تصدير Excel">
                    <i class="fas fa-file-excel text-sm md:text-lg"></i>
                </button>
                <button onclick="exportToPDF()" class="w-9 h-9 md:w-10 md:h-10 bg-red-500 hover:bg-red-600 text-white rounded-xl flex items-center justify-center transition-colors flex-shrink-0" title="تصدير PDF">
                    <i class="fas fa-file-pdf text-sm md:text-lg"></i>
                </button>
                <button onclick="printTable()" class="w-9 h-9 md:w-10 md:h-10 bg-blue-500 hover:bg-blue-600 text-white rounded-xl flex items-center justify-center transition-colors flex-shrink-0" title="طباعة">
                    <i class="fas fa-print text-sm md:text-lg"></i>
                </button>
                <a href="{{ route('employees.create') }}" class="btn-primary text-white px-4 md:px-6 py-2 md:py-3 rounded-xl flex items-center justify-center hover:no-underline text-sm md:text-base flex-1 md:flex-initial">
                    <i class="fas fa-user-plus text-xs md:text-sm ml-2"></i>
                    <span class="hidden sm:inline">إضافة موظف جديد</span>
                    <span class="sm:hidden">إضافة</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6 mb-4 md:mb-6">
        <!-- Total Employees -->
        <div class="card rounded-xl md:rounded-2xl p-4 md:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs md:text-sm font-medium text-gray-600">إجمالي الموظفين</p>
                    <p class="text-2xl md:text-3xl font-bold text-gray-800">{{ $employees->count() }}</p>
                </div>
                <div class="w-10 h-10 md:w-12 md:h-12 bg-blue-100 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-users text-blue-600 text-lg md:text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Content Writers -->
        <div class="card rounded-xl md:rounded-2xl p-4 md:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs md:text-sm font-medium text-gray-600">كتاب المحتوى</p>
                    <p class="text-2xl md:text-3xl font-bold text-blue-600">{{ $employees->where('role', 'content_writer')->count() }}</p>
                </div>
                <div class="w-10 h-10 md:w-12 md:h-12 bg-blue-100 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-pen text-blue-600 text-lg md:text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Designers -->
        <div class="card rounded-xl md:rounded-2xl p-4 md:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs md:text-sm font-medium text-gray-600">المصممين</p>
                    <p class="text-2xl md:text-3xl font-bold text-purple-600">{{ $employees->where('role', 'designer')->count() }}</p>
                </div>
                <div class="w-10 h-10 md:w-12 md:h-12 bg-purple-100 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-palette text-purple-600 text-lg md:text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Ad Managers -->
        <div class="card rounded-xl md:rounded-2xl p-4 md:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs md:text-sm font-medium text-gray-600">إدارة إعلانات</p>
                    <p class="text-2xl md:text-3xl font-bold text-green-600">{{ $employees->where('role', 'ad_manager')->count() }}</p>
                </div>
                <div class="w-10 h-10 md:w-12 md:h-12 bg-green-100 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-bullhorn text-green-600 text-lg md:text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Video Editors -->
        <div class="card rounded-xl md:rounded-2xl p-4 md:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs md:text-sm font-medium text-gray-600">مصمم فيديوهات</p>
                    <p class="text-2xl md:text-3xl font-bold text-red-600">{{ $employees->where('role', 'video_editor')->count() }}</p>
                </div>
                <div class="w-10 h-10 md:w-12 md:h-12 bg-red-100 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-video text-red-600 text-lg md:text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Page Managers -->
        <div class="card rounded-xl md:rounded-2xl p-4 md:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs md:text-sm font-medium text-gray-600">إدارة الصفحة</p>
                    <p class="text-2xl md:text-3xl font-bold text-yellow-600">{{ $employees->where('role', 'page_manager')->count() }}</p>
                </div>
                <div class="w-10 h-10 md:w-12 md:h-12 bg-yellow-100 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-file-alt text-yellow-600 text-lg md:text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Account Managers -->
        <div class="card rounded-xl md:rounded-2xl p-4 md:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs md:text-sm font-medium text-gray-600">أكونت منجر</p>
                    <p class="text-2xl md:text-3xl font-bold text-indigo-600">{{ $employees->where('role', 'account_manager')->count() }}</p>
                </div>
                <div class="w-10 h-10 md:w-12 md:h-12 bg-indigo-100 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-user-tie text-indigo-600 text-lg md:text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Monitors -->
        <div class="card rounded-xl md:rounded-2xl p-4 md:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs md:text-sm font-medium text-gray-600">مونتير</p>
                    <p class="text-2xl md:text-3xl font-bold text-pink-600">{{ $employees->where('role', 'monitor')->count() }}</p>
                </div>
                <div class="w-10 h-10 md:w-12 md:h-12 bg-pink-100 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-cut text-pink-600 text-lg md:text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Media Buyers -->
        <div class="card rounded-xl md:rounded-2xl p-4 md:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs md:text-sm font-medium text-gray-600">ميديا بايرز</p>
                    <p class="text-2xl md:text-3xl font-bold text-teal-600">{{ $employees->where('role', 'media_buyer')->count() }}</p>
                </div>
                <div class="w-10 h-10 md:w-12 md:h-12 bg-teal-100 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-shopping-cart text-teal-600 text-lg md:text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Employees Table -->
    <div class="card rounded-xl md:rounded-2xl overflow-hidden">
        @if($employees->count() > 0)
            <div class="overflow-x-auto p-3 md:p-6">
                <table id="employeesTable" class="w-full" dir="rtl">
                    <thead>
                        <tr>
                            <th class="text-right" style="text-align: justify; padding-right: 33px;">الاسم</th>
                            <th class="text-right" style="text-align: justify; padding-right: 33px;">البريد الإلكتروني</th>
                            <th class="text-right" style="text-align: justify; padding-right: 33px;">رقم الهاتف</th>
                            <th class="text-right" style="text-align: justify; padding-right: 33px;">الدور الوظيفي</th>
                            <th class="text-right" style="text-align: justify; padding-right: 33px;">الحالة</th>
                            <th class="text-center" style="text-align: justify; padding-right: 33px;">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($employees as $employee)
                            <tr>
                                <td>
                                    <div class="text-right">
                                        <div class="text-sm font-medium text-gray-900">{{ $employee->name }}</div>
                                    </div>
                                </td>
                                <td>
                                    <div class="text-sm text-gray-900 text-right">{{ $employee->email }}</div>
                                </td>
                                <td>
                                    <div class="text-sm text-gray-900 text-right">{{ $employee->phone ?? 'غير محدد' }}</div>
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
                                    <div class="flex items-center justify-center space-x-2 rtl:space-x-reverse">
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
            <div class="text-center py-8 md:py-12 px-4">
                <i class="fas fa-users-cog text-4xl md:text-6xl text-gray-300 mb-3 md:mb-4"></i>
                <h3 class="text-base md:text-lg font-medium text-gray-900 mb-2">لا يوجد موظفين</h3>
                <p class="text-sm md:text-base text-gray-500 mb-4 md:mb-6">ابدأ بإضافة موظف جديد</p>
                <a href="{{ route('employees.create') }}" class="btn-primary text-white px-4 md:px-6 py-2.5 md:py-3 rounded-xl inline-flex items-center hover:no-underline text-sm md:text-base">
                    <i class="fas fa-user-plus text-xs md:text-sm ml-2"></i>
                    إضافة موظف جديد
                </a>
            </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script>
var table;

$(document).ready(function() {
    table = $('#employeesTable').DataTable({
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
        order: [[0, 'asc']], // Sort by name ascending
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

