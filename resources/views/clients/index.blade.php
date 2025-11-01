@extends('layouts.dashboard')

@section('title', 'إدارة العملاء')
@section('page-title', 'إدارة العملاء')
@section('page-description', 'عرض وإدارة قائمة العملاء')

@section('content')
<div class="space-y-6">
    <!-- Header Actions -->
    <div class="card rounded-2xl p-6 mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <div class="w-12 h-12 logo-gradient rounded-2xl flex items-center justify-center shadow-lg ml-4">
                    <i class="fas fa-users text-white text-xl"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">العملاء</h2>
                    <p class="text-gray-600">إدارة قائمة العملاء والشركات</p>
                </div>
            </div>
            <a href="{{ route('clients.create') }}" class="btn-primary text-white px-6 py-3 rounded-xl flex items-center hover:no-underline">
                <i class="fas fa-user-plus text-sm ml-2"></i>
                إضافة عميل جديد
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="card rounded-2xl p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">إجمالي العملاء</p>
                    <p class="text-3xl font-bold text-gray-800">{{ $clients->total() }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                    <span class="material-icons text-blue-600">people</span>
                </div>
            </div>
        </div>

        <div class="card rounded-2xl p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">العملاء النشطين</p>
                    <p class="text-3xl font-bold text-green-600">{{ $clients->where('status', 'active')->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                    <span class="material-icons text-green-600">check_circle</span>
                </div>
            </div>
        </div>

        <div class="card rounded-2xl p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">في الانتظار</p>
                    <p class="text-3xl font-bold text-yellow-600">{{ $clients->where('status', 'pending')->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center">
                    <span class="material-icons text-yellow-600">schedule</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Clients Table -->
    <div class="card rounded-2xl overflow-hidden">
   
        
        @if($clients->count() > 0)
            <div class="overflow-x-auto p-6">
                <table id="clientsTable" class="w-full">
                    <thead>
                        <tr>
                            <th>العميل</th>
                            <th>الشركة</th>
                            <th>الهاتف</th>
                            <th>الحالة</th>
                            <th>تاريخ الإضافة</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($clients as $client)
                            <tr>
                                <td>
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-gradient-to-r from-purple-500 to-blue-500 rounded-full flex items-center justify-center">
                                            <i class="fas fa-user text-white text-sm"></i>
                                        </div>
                                        <div class="mr-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $client->name }}</div>
                                            <div class="text-sm text-gray-500">{{ $client->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="text-sm text-gray-900">{{ $client->company ?? 'غير محدد' }}</div>
                                </td>
                                <td>
                                    <div class="text-sm text-gray-900">{{ $client->phone ?? 'غير محدد' }}</div>
                                </td>
                                <td>
                                    <span class="status-badge
                                        @if($client->status === 'active') status-active
                                        @elseif($client->status === 'inactive') status-inactive
                                        @else status-pending @endif">
                                        {{ $client->status_badge }}
                                    </span>
                                </td>
                                <td>
                                    <div class="text-sm text-gray-500">{{ $client->created_at->format('Y-m-d') }}</div>
                                </td>
                                <td>
                                    <div class="flex items-center space-x-2 rtl:space-x-reverse">
                                        <a href="{{ route('clients.show', $client) }}" class="text-blue-600 hover:text-blue-900 p-1" title="عرض التفاصيل">
                                            <i class="fas fa-eye text-sm"></i>
                                        </a>
                                        <a href="{{ route('clients.edit', $client) }}" class="text-yellow-600 hover:text-yellow-900 p-1" title="تعديل">
                                            <i class="fas fa-edit text-sm"></i>
                                        </a>
                                        <button onclick="confirmDelete('{{ route('clients.destroy', $client) }}', 'تأكيد حذف العميل', 'هل أنت متأكد من حذف العميل {{ $client->name }}؟')" class="text-red-600 hover:text-red-900 p-1" title="حذف">
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
                <span class="material-icons text-6xl text-gray-300 mb-4">people_outline</span>
                <h3 class="text-lg font-medium text-gray-900 mb-2">لا يوجد عملاء</h3>
                <p class="text-gray-500 mb-6">ابدأ بإضافة عميل جديد</p>
                <a href="{{ route('clients.create') }}" class="btn-primary text-white px-6 py-3 rounded-xl inline-flex items-center hover:no-underline">
                    <i class="fas fa-user-plus text-sm ml-2"></i>
                    إضافة عميل جديد
                </a>
            </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    $('#clientsTable').DataTable({
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
