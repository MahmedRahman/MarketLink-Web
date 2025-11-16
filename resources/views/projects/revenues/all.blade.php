@extends('layouts.dashboard')

@section('title', 'إيرادات المشاريع')
@section('page-title', 'إيرادات المشاريع')
@section('page-description', 'عرض وإدارة جميع إيرادات المشاريع')

@section('content')
<div class="space-y-6">
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg flex items-center">
            <span class="material-icons ml-2">check_circle</span>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg flex items-center">
            <span class="material-icons ml-2">error</span>
            {{ session('error') }}
        </div>
    @endif

    <!-- Header Actions -->
    <div class="card page-header rounded-2xl p-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <div class="w-12 h-12 logo-gradient rounded-2xl flex items-center justify-center shadow-lg icon-spacing ml-3">
                    <i class="fas fa-money-bill-wave text-white text-xl"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">إيرادات المشاريع</h2>
                    <p class="text-gray-600">عرض وإدارة جميع إيرادات المشاريع</p>
                </div>
            </div>
            <div class="flex items-center space-x-3 rtl:space-x-reverse">
                <a href="{{ route('revenues.create') }}" class="btn-primary text-white px-6 py-3 rounded-xl flex items-center hover:no-underline">
                    <i class="fas fa-plus text-sm ml-2"></i>
                    إضافة إيراد جديد
                </a>
                <a href="{{ route('projects.index') }}" class="flex items-center px-4 py-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-xl transition-colors">
                    <i class="fas fa-arrow-right text-sm ml-2"></i>
                    المشاريع
                </a>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card rounded-2xl p-6">
        <form method="GET" action="{{ route('revenues.all') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div>
                <label for="project_id" class="block text-sm font-medium text-gray-700 mb-2">
                    المشروع
                </label>
                <select
                    id="project_id"
                    name="project_id"
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-colors select2"
                    onchange="this.form.submit()"
                >
                    <option value="">جميع المشاريع</option>
                    @foreach($projects as $project)
                        <option value="{{ $project->id }}" {{ request('project_id') == $project->id ? 'selected' : '' }}>
                            {{ $project->business_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="record_month_year" class="block text-sm font-medium text-gray-700 mb-2">
                    السجلات الشهرية
                </label>
                <input
                    type="month"
                    id="record_month_year"
                    name="record_month_year"
                    value="{{ request('record_month_year') ?? '' }}"
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-colors"
                    onchange="this.form.submit()"
                />
            </div>

            <div>
                <label for="month" class="block text-sm font-medium text-gray-700 mb-2">
                    الشهر (تاريخ الإيراد)
                </label>
                <input
                    type="month"
                    id="month"
                    name="month"
                    value="{{ request('month') ?? '' }}"
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-colors"
                    onchange="this.form.submit()"
                />
            </div>

            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                    الحالة
                </label>
                <select
                    id="status"
                    name="status"
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-colors select2"
                    onchange="this.form.submit()"
                >
                    <option value="">جميع الحالات</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>في الانتظار</option>
                    <option value="received" {{ request('status') == 'received' ? 'selected' : '' }}>تم الاستلام</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>ملغي</option>
                </select>
            </div>

            <div class="flex items-end">
                @if(request('project_id') || request('month') || request('record_month_year') || request('status'))
                    <a href="{{ route('revenues.all') }}" class="w-full btn-secondary text-white px-6 py-3 rounded-xl hover:no-underline text-center">
                        <i class="fas fa-times text-sm ml-2"></i>
                        إلغاء الفلتر
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="card rounded-2xl p-6">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center ml-4">
                    <i class="fas fa-coins text-blue-600 text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">إجمالي الإيرادات</p>
                    <p class="text-2xl font-bold text-gray-900">
                        {{ number_format($revenues->sum('amount'), 2) }} جنيه
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
                    <p class="text-sm text-gray-600">تم تحصيله</p>
                    <p class="text-2xl font-bold text-gray-900">
                        {{ number_format($revenues->sum('paid_amount'), 2) }} جنيه
                    </p>
                </div>
            </div>
        </div>

        <div class="card rounded-2xl p-6">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center ml-4">
                    <i class="fas fa-clock text-orange-600 text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">المتبقي</p>
                    <p class="text-2xl font-bold text-gray-900">
                        {{ number_format($revenues->sum(function($revenue) { return $revenue->calculated_remaining_amount; }), 2) }} جنيه
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Revenues Table -->
    <div class="card rounded-2xl p-6">
        @if($revenues->count() > 0)
            <div class="overflow-x-auto">
                <table id="revenuesTable" class="min-w-full divide-y divide-gray-200" dir="rtl">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                السجلات الشهرية
                            </th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                المشروع
                            </th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                تاريخ الاستحقاق
                            </th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                العنوان
                            </th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                المبلغ الإجمالي
                            </th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                المبلغ المدفوع
                            </th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                المبلغ المتبقي
                            </th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                الحالة
                            </th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                طريقة الدفع
                            </th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                الإجراءات
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($revenues as $revenue)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($revenue->record_month_year)
                                        @php
                                            $months = [
                                                1 => 'يناير', 2 => 'فبراير', 3 => 'مارس', 4 => 'أبريل',
                                                5 => 'مايو', 6 => 'يونيو', 7 => 'يوليو', 8 => 'أغسطس',
                                                9 => 'سبتمبر', 10 => 'أكتوبر', 11 => 'نوفمبر', 12 => 'ديسمبر'
                                            ];
                                            $parts = explode('-', $revenue->record_month_year);
                                            $year = $parts[0] ?? '';
                                            $monthNum = isset($parts[1]) ? (int)$parts[1] : 0;
                                            $monthName = $months[$monthNum] ?? '';
                                        @endphp
                                        <div class="text-sm font-medium text-gray-900">{{ $monthName }} {{ $year }}</div>
                                        <div class="text-xs text-gray-500">{{ $revenue->record_month_year }}</div>
                                    @else
                                        <div class="text-sm font-medium text-gray-400 italic">فاضي</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $revenue->project->business_name ?? 'غير محدد' }}</div>
                                    @if($revenue->project)
                                        <div class="text-xs text-gray-500">{{ $revenue->project->client->name ?? '' }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-500">{{ $revenue->revenue_date->format('Y-m-d') }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $revenue->title }}</div>
                                    @if($revenue->description)
                                        <div class="text-sm text-gray-500">{{ Str::limit($revenue->description, 50) }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-semibold text-gray-900">{{ $revenue->formatted_amount }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-semibold text-green-600">{{ $revenue->formatted_paid_amount }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-semibold text-orange-600">{{ $revenue->formatted_calculated_remaining_amount }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="status-badge status-{{ $revenue->status_color }}">
                                        {{ $revenue->status_badge }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $revenue->payment_method_badge }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                    <div class="flex items-center justify-center space-x-2 rtl:space-x-reverse">
                                        <a href="{{ route('revenues.edit', $revenue) }}" class="text-yellow-600 hover:text-yellow-900 p-1" title="تعديل">
                                            <i class="fas fa-edit text-sm"></i>
                                        </a>
                                        <form action="{{ route('revenues.duplicate', $revenue) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="text-purple-600 hover:text-purple-900 p-1" title="نسخ الإيراد" onclick="return confirm('هل تريد نسخ هذا الإيراد؟')">
                                                <i class="fas fa-copy text-sm"></i>
                                            </button>
                                        </form>
                                        <button onclick="confirmDelete('{{ route('revenues.destroy', $revenue) }}', 'تأكيد حذف الإيراد', 'هل أنت متأكد من حذف الإيراد {{ $revenue->title }}؟')" class="text-red-600 hover:text-red-900 p-1" title="حذف">
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
                <i class="fas fa-money-bill-wave text-6xl text-gray-300 mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">لا يوجد إيرادات</h3>
                <p class="text-gray-500 mb-6">ابدأ بإضافة إيراد جديد</p>
                <a href="{{ route('revenues.create') }}" class="btn-primary text-white px-6 py-3 rounded-xl inline-flex items-center hover:no-underline">
                    <i class="fas fa-plus text-sm ml-2"></i>
                    إضافة إيراد جديد
                </a>
            </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Initialize Select2
    $('.select2').select2({
        placeholder: 'اختر من القائمة',
        allowClear: true,
        dir: 'rtl',
        width: '100%',
        language: {
            noResults: function() {
                return 'لا توجد نتائج';
            },
            searching: function() {
                return 'جاري البحث...';
            }
        }
    });

    $('#revenuesTable').DataTable({
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
                    columns: [0, 1, 2, 3, 4, 5, 6, 7, 8]
                }
            },
            {
                extend: 'pdf',
                text: 'تصدير PDF',
                className: 'btn btn-danger',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5, 6, 7, 8]
                }
            },
            {
                extend: 'print',
                text: 'طباعة',
                className: 'btn btn-info',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5, 6, 7, 8]
                }
            }
        ],
        columnDefs: [
            {
                targets: [8], // Actions column
                orderable: false,
                searchable: false
            }
        ],
        order: [[2, 'desc']], // Sort by date descending
        pageLength: 10,
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]]
    });
});
</script>
@endsection

