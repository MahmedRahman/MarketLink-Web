@extends('layouts.dashboard')

@section('title', 'مصروفات المشاريع')
@section('page-title', 'مصروفات المشاريع')
@section('page-description', 'عرض وإدارة جميع مصروفات المشاريع')

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
                    <i class="fas fa-receipt text-white text-xl"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">مصروفات المشاريع</h2>
                    <p class="text-gray-600">عرض وإدارة جميع مصروفات المشاريع</p>
                </div>
            </div>
            <div class="flex items-center space-x-3 rtl:space-x-reverse">
                <a href="{{ route('expenses.create') }}" class="btn-primary text-white px-6 py-3 rounded-xl flex items-center hover:no-underline">
                    <i class="fas fa-plus text-sm ml-2"></i>
                    إضافة مصروف جديد
                </a>
                <a href="{{ route('projects.index') }}" class="flex items-center px-4 py-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-xl transition-colors">
                    <i class="fas fa-arrow-right text-sm ml-2"></i>
                    المشاريع
                </a>
            </div>
        </div>
    </div>

    <!-- Monthly Records Cards -->
    <div class="card rounded-2xl p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4">السجلات الشهرية</h3>
        <div class="flex flex-wrap gap-4">
            <!-- All Card -->
            <a href="{{ route('expenses.all') }}" class="monthly-record-card flex-1 min-w-[150px] max-w-[200px] p-4 rounded-xl border-2 transition-all cursor-pointer {{ !request('record_month_year') ? 'border-primary bg-primary-50' : 'border-gray-200 hover:border-primary hover:bg-gray-50' }}">
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
                    $recordMonth = $record['record_month_year'];
                    $months = [
                        1 => 'يناير', 2 => 'فبراير', 3 => 'مارس', 4 => 'أبريل',
                        5 => 'مايو', 6 => 'يونيو', 7 => 'يوليو', 8 => 'أغسطس',
                        9 => 'سبتمبر', 10 => 'أكتوبر', 11 => 'نوفمبر', 12 => 'ديسمبر'
                    ];
                    $parts = explode('-', $recordMonth);
                    $year = $parts[0] ?? '';
                    $monthNum = isset($parts[1]) ? (int)$parts[1] : 0;
                    $monthName = $months[$monthNum] ?? '';
                    $isActive = request('record_month_year') == $recordMonth;
                    $count = $record['count'];
                @endphp
                <a href="{{ route('expenses.all', ['record_month_year' => $recordMonth]) }}" class="monthly-record-card flex-1 min-w-[150px] max-w-[200px] p-4 rounded-xl border-2 transition-all cursor-pointer {{ $isActive ? 'border-primary bg-primary-50' : 'border-gray-200 hover:border-primary hover:bg-gray-50' }}">
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
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="card rounded-2xl p-6">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center ml-4">
                    <i class="fas fa-coins text-red-600 text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">إجمالي المصروفات</p>
                    <p class="text-2xl font-bold text-gray-900">
                        {{ number_format($expenses->sum('amount'), 2) }} جنيه
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
                    <p class="text-sm text-gray-600">ما تم دفعه</p>
                    <p class="text-2xl font-bold text-gray-900">
                        {{ number_format($expenses->where('status', 'paid')->sum('amount'), 2) }} جنيه
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
                    <p class="text-sm text-gray-600">المستحق</p>
                    <p class="text-2xl font-bold text-gray-900">
                        {{ number_format($expenses->where('status', 'pending')->sum('amount'), 2) }} جنيه
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Expenses Table -->
    <div class="card rounded-2xl p-6">
        @if($expenses->count() > 0)
            <div class="overflow-x-auto">
                <table id="expensesTable" class="min-w-full divide-y divide-gray-200" dir="rtl">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                السجلات الشهرية
                            </th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                المشروع
                            </th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                تاريخ المصروف
                            </th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                العنوان
                            </th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                الموظف
                            </th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                الفئة
                            </th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                المبلغ
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
                        @foreach($expenses as $expense)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($expense->record_month_year)
                                        @php
                                            $months = [
                                                1 => 'يناير', 2 => 'فبراير', 3 => 'مارس', 4 => 'أبريل',
                                                5 => 'مايو', 6 => 'يونيو', 7 => 'يوليو', 8 => 'أغسطس',
                                                9 => 'سبتمبر', 10 => 'أكتوبر', 11 => 'نوفمبر', 12 => 'ديسمبر'
                                            ];
                                            $parts = explode('-', $expense->record_month_year);
                                            $year = $parts[0] ?? '';
                                            $monthNum = isset($parts[1]) ? (int)$parts[1] : 0;
                                            $monthName = $months[$monthNum] ?? '';
                                        @endphp
                                        <div class="text-sm font-medium text-gray-900">{{ $monthName }} {{ $year }}</div>
                                        <div class="text-xs text-gray-500">{{ $expense->record_month_year }}</div>
                                    @else
                                        <div class="text-sm font-medium text-gray-400 italic">فاضي</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $expense->project->business_name ?? 'غير محدد' }}</div>
                                    @if($expense->project)
                                        <div class="text-xs text-gray-500">{{ $expense->project->client->name ?? '' }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-500">{{ $expense->expense_date->format('Y-m-d') }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $expense->title }}</div>
                                    @if($expense->description)
                                        <div class="text-sm text-gray-500">{{ Str::limit($expense->description, 50) }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        {{ $expense->employee ? $expense->employee->name : 'غير محدد' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ $expense->category_badge }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-semibold text-gray-900">{{ $expense->formatted_amount }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="status-badge status-{{ $expense->status_color }}">
                                        {{ $expense->status_badge }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $expense->payment_method_badge }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                    <div class="flex items-center justify-center space-x-2 rtl:space-x-reverse">
                                        <a href="{{ route('expenses.edit', $expense) }}" class="text-yellow-600 hover:text-yellow-900 p-1" title="تعديل">
                                            <i class="fas fa-edit text-sm"></i>
                                        </a>
                                        <form action="{{ route('expenses.duplicate', $expense) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="text-purple-600 hover:text-purple-900 p-1" title="نسخ المصروف" onclick="return confirm('هل تريد نسخ هذا المصروف؟')">
                                                <i class="fas fa-copy text-sm"></i>
                                            </button>
                                        </form>
                                        <button onclick="confirmDelete('{{ route('expenses.destroy', $expense) }}', 'تأكيد حذف المصروف', 'هل أنت متأكد من حذف المصروف {{ $expense->title }}؟')" class="text-red-600 hover:text-red-900 p-1" title="حذف">
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
                <i class="fas fa-receipt text-6xl text-gray-300 mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">لا يوجد مصروفات</h3>
                <p class="text-gray-500 mb-6">ابدأ بإضافة مصروف جديد</p>
                <a href="{{ route('expenses.create') }}" class="btn-primary text-white px-6 py-3 rounded-xl inline-flex items-center hover:no-underline">
                    <i class="fas fa-plus text-sm ml-2"></i>
                    إضافة مصروف جديد
                </a>
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
    $('#expensesTable').DataTable({
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
        dom: 'rtip',
        searching: false,
        columnDefs: [
            {
                targets: [9], // Actions column
                orderable: false,
                searchable: false
            }
        ],
        order: [[2, 'desc']], // Sort by date descending
        pageLength: 100,
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]]
    });
});
</script>
@endsection

