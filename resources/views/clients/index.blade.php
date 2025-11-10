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
                <a href="{{ route('clients.create') }}" class="btn-primary text-white px-6 py-3 rounded-xl flex items-center hover:no-underline">
                    <i class="fas fa-user-plus text-sm ml-2"></i>
                    إضافة عميل جديد
                </a>
            </div>
        </div>
    </div>

    <!-- Clients Table -->
    <div class="card rounded-2xl overflow-hidden">
   
        
        @if($clients->count() > 0)
            <div class="overflow-x-auto p-6">
                <table id="clientsTable" class="w-full" dir="rtl">
                    <thead>
                        <tr>
                            <th class="text-right" style="text-align: justify; padding-right: 33px;">تاريخ الإضافة</th>
                            <th class="text-right" style="text-align: justify; padding-right: 33px;">العميل</th>
                            <th class="text-right" style="text-align: justify; padding-right: 33px;">الشركة</th>
                            <th class="text-right" style="text-align: justify; padding-right: 33px;">الهاتف</th>
                            <th class="text-right" style="text-align: justify; padding-right: 33px;">الحالة</th>
                            <th class="text-center" style="text-align: justify; padding-right: 33px;">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($clients as $client)
                            <tr>
                                <td>
                                    <div class="text-sm text-gray-500 text-right">{{ $client->created_at->format('Y-m-d') }}</div>
                                </td>
                                <td>
                                    <div class="text-right">
                                        <div class="text-sm font-medium text-gray-900">{{ $client->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $client->email }}</div>
                                    </div>
                                </td>
                                <td>
                                    <div class="text-sm text-gray-900 text-right">{{ $client->company ?? 'غير محدد' }}</div>
                                </td>
                                <td>
                                    <div class="text-sm text-gray-900 text-right">{{ $client->phone ?? 'غير محدد' }}</div>
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
                                    <div class="flex items-center justify-center space-x-2 rtl:space-x-reverse">
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
var table;

$(document).ready(function() {
    table = $('#clientsTable').DataTable({
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
        order: [[0, 'desc']], // Sort by date descending (first column now)
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
