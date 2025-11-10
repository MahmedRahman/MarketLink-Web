@extends('layouts.dashboard')

@section('title', 'إدارة المشاريع')
@section('page-title', 'إدارة المشاريع')
@section('page-description', 'عرض وإدارة قائمة المشاريع')

@section('content')
<div class="space-y-6">
    <!-- Header Actions -->
    <div class="card page-header rounded-2xl p-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <div class="w-12 h-12 logo-gradient rounded-2xl flex items-center justify-center shadow-lg icon-spacing ml-3">
                    <i class="fas fa-project-diagram text-white text-xl"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">المشاريع</h2>
                    <p class="text-gray-600">إدارة قائمة المشاريع والبيزنس</p>
                </div>
            </div>
            <a href="{{ route('projects.create') }}" class="btn-primary text-white px-6 py-3 rounded-xl flex items-center hover:no-underline">
                <i class="fas fa-plus text-sm ml-2"></i>
                إضافة مشروع جديد
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="card rounded-2xl p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">إجمالي المشاريع</p>
                    <p class="text-3xl font-bold text-gray-800">{{ $projects->total() }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-project-diagram text-blue-600"></i>
                </div>
            </div>
        </div>

        <div class="card rounded-2xl p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">المشاريع النشطة</p>
                    <p class="text-3xl font-bold text-green-600">{{ $projects->where('status', 'active')->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-check-circle text-green-600"></i>
                </div>
            </div>
        </div>

        <div class="card rounded-2xl p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">في الانتظار</p>
                    <p class="text-3xl font-bold text-yellow-600">{{ $projects->where('status', 'pending')->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-clock text-yellow-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Projects Table -->
    <div class="card rounded-2xl p-6">
        @if($projects->count() > 0)
            <div class="overflow-x-auto">
                <table id="projectsTable" class="w-full">
                    <thead>
                        <tr class="border-b border-gray-200">
                            <th class="text-right py-3 px-4 font-medium text-gray-700">اسم البيزنس</th>
                            <th class="text-right py-3 px-4 font-medium text-gray-700">العميل</th>
                            <th class="text-right py-3 px-4 font-medium text-gray-700">الموقع</th>
                            <th class="text-right py-3 px-4 font-medium text-gray-700">الحالة</th>
                            <th class="text-right py-3 px-4 font-medium text-gray-700">تاريخ الإنشاء</th>
                            <th class="text-right py-3 px-4 font-medium text-gray-700">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($projects as $project)
                            <tr class="border-b border-gray-100 hover:bg-gray-50">
                                <td class="py-4 px-4">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-gradient-to-r from-purple-500 to-blue-500 rounded-xl flex items-center justify-center ml-3">
                                            <i class="fas fa-building text-white text-sm"></i>
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">{{ $project->business_name }}</div>
                                            <div class="text-xs text-gray-500">{{ Str::limit($project->business_description, 50) }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="text-sm text-gray-900">{{ $project->client->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $project->client->email }}</div>
                                </td>
                                <td>
                                    <div class="text-sm text-gray-900">
                                        @if($project->website_url)
                                            <a href="{{ $project->website_url }}" target="_blank" class="text-blue-600 hover:text-blue-900">
                                                <i class="fas fa-globe text-sm ml-1"></i>
                                                الموقع
                                            </a>
                                        @else
                                            <span class="text-gray-400">غير محدد</span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <span class="status-badge
                                        @if($project->status === 'active') status-active
                                        @elseif($project->status === 'inactive') status-inactive
                                        @else status-pending @endif">
                                        {{ $project->status_badge }}
                                    </span>
                                </td>
                                <td>
                                    <div class="text-sm text-gray-500">{{ $project->created_at->format('Y-m-d') }}</div>
                                </td>
                                <td>
                                    <div class="flex items-center space-x-2 rtl:space-x-reverse">
                                        <a href="{{ route('projects.show', $project) }}" class="text-blue-600 hover:text-blue-900 p-1" title="عرض التفاصيل">
                                            <i class="fas fa-eye text-sm"></i>
                                        </a>
                                        <a href="{{ route('projects.edit', $project) }}" class="text-yellow-600 hover:text-yellow-900 p-1" title="تعديل">
                                            <i class="fas fa-edit text-sm"></i>
                                        </a>
                                        <a href="{{ route('projects.revenues.index', $project) }}" class="text-green-600 hover:text-green-900 p-1" title="الإيرادات ({{ $project->revenues_count }})">
                                            <i class="fas fa-arrow-up text-sm"></i>
                                        </a>
                                        <a href="{{ route('projects.expenses.index', $project) }}" class="text-red-600 hover:text-red-900 p-1" title="المصروفات ({{ $project->expenses_count }})">
                                            <i class="fas fa-arrow-down text-sm"></i>
                                        </a>
                                        <button onclick="confirmDelete('{{ route('projects.destroy', $project) }}', 'تأكيد حذف المشروع', 'هل أنت متأكد من حذف المشروع {{ $project->business_name }}؟')" class="text-red-600 hover:text-red-900 p-1" title="حذف">
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
                <i class="fas fa-project-diagram text-6xl text-gray-300 mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">لا يوجد مشاريع</h3>
                <p class="text-gray-500 mb-6">ابدأ بإضافة مشروع جديد</p>
                <a href="{{ route('projects.create') }}" class="btn-primary text-white px-6 py-3 rounded-xl inline-flex items-center hover:no-underline">
                    <i class="fas fa-plus text-sm ml-2"></i>
                    إضافة مشروع جديد
                </a>
            </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    $('#projectsTable').DataTable({
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
                    columns: [0, 1, 2, 3, 4]
                }
            },
            {
                extend: 'pdf',
                text: 'تصدير PDF',
                className: 'btn btn-danger',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4]
                }
            },
            {
                extend: 'print',
                text: 'طباعة',
                className: 'btn btn-info',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4]
                }
            }
        ],
        columnDefs: [
            {
                targets: [5], // Actions column
                orderable: false,
                searchable: false
            }
        ],
        order: [[4, 'desc']], // Sort by date descending
        pageLength: 10,
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]]
    });
});
</script>
@endsection
