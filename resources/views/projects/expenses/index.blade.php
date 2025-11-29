@extends('layouts.dashboard')

@section('title', 'مصروفات المشروع')
@section('page-title', 'مصروفات المشروع')
@section('page-description', 'إدارة مصروفات المشروع: ' . $project->business_name)

@section('content')
<div class="space-y-6">
    <!-- Header Actions -->
    <div class="card rounded-2xl p-6 mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <div class="w-12 h-12 logo-gradient rounded-2xl flex items-center justify-center shadow-lg ml-4">
                    <i class="fas fa-receipt text-white text-xl"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">مصروفات المشروع</h2>
                    <p class="text-gray-600">إدارة مصروفات المشروع: {{ $project->business_name }}</p>
                </div>
            </div>
            <div class="flex items-center space-x-3 rtl:space-x-reverse">
                <button id="bulkDuplicateBtn" class="btn-secondary text-white px-6 py-3 rounded-xl flex items-center hover:no-underline" disabled>
                    <i class="fas fa-copy text-sm ml-2"></i>
                    نسخ المحدد
                </button>
                <a href="{{ route('projects.expenses.create', $project) }}" class="btn-primary text-white px-6 py-3 rounded-xl flex items-center hover:no-underline">
                    <i class="fas fa-plus text-sm ml-2"></i>
                    إضافة مصروف جديد
                </a>
                <a href="{{ route('projects.show', $project) }}" class="flex items-center px-4 py-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-xl transition-colors">
                    العودة للمشروع
                </a>
            </div>
        </div>
    </div>

    <!-- Month Filter -->
    <div class="card rounded-2xl p-6 mb-6">
        <form method="GET" action="{{ route('projects.expenses.index', $project) }}" class="flex items-center gap-4">
            <div class="flex-1">
                <label for="month" class="block text-sm font-medium text-gray-700 mb-2">
                    فلترة بالشهر
                </label>
                <input
                    type="month"
                    id="month"
                    name="month"
                    value="{{ $selectedMonth ?? '' }}"
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-colors"
                    onchange="this.form.submit()"
                />
            </div>
            @if($selectedMonth)
            <div class="flex items-end">
                <a href="{{ route('projects.expenses.index', $project) }}" class="btn-secondary text-white px-6 py-3 rounded-xl hover:no-underline">
                    <i class="fas fa-times text-sm ml-2"></i>
                    إلغاء الفلتر
                </a>
            </div>
            @endif
        </form>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
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
                    <p class="text-sm text-gray-600">المبلغ المستحق</p>
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
                <table id="expensesTable" class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <input type="checkbox" id="selectAllExpenses" class="w-4 h-4 rounded border-gray-300 text-primary focus:ring-2 focus:ring-primary cursor-pointer">
                            </th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                السجلات الشهرية
                            </th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                التاريخ
                            </th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                اسم الموظف
                            </th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                العنوان
                            </th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                المبلغ
                            </th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                الحالة
                            </th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                الإجراءات
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($expenses as $expense)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <input type="checkbox" name="expense_ids[]" value="{{ $expense->id }}" class="expense-checkbox w-4 h-4 rounded border-gray-300 text-primary focus:ring-2 focus:ring-primary cursor-pointer">
                                </td>
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
                                        <div class="text-sm font-medium text-gray-400 italic">غير محدد</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-500">{{ $expense->expense_date->format('Y-m-d') }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        {{ $expense->employee ? $expense->employee->name : 'غير محدد' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $expense->title }}</div>
                                    @if($expense->description)
                                        <div class="text-sm text-gray-500">{{ Str::limit($expense->description, 50) }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-semibold text-gray-900">{{ $expense->formatted_amount }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="status-badge status-{{ $expense->status_color }}">
                                        {{ $expense->status_badge }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                    <div class="flex items-center justify-center space-x-2 rtl:space-x-reverse">
                                        <a href="{{ route('projects.expenses.show', [$project, $expense]) }}" class="text-blue-600 hover:text-blue-900 p-1" title="عرض التفاصيل">
                                            <i class="fas fa-eye text-sm"></i>
                                        </a>
                                        <a href="{{ route('projects.expenses.edit', [$project, $expense]) }}" class="text-yellow-600 hover:text-yellow-900 p-1" title="تعديل">
                                            <i class="fas fa-edit text-sm"></i>
                                        </a>
                                        <form action="{{ route('projects.expenses.duplicate', [$project, $expense]) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="text-purple-600 hover:text-purple-900 p-1" title="نسخ المصروف" onclick="return confirm('هل تريد نسخ هذا المصروف؟')">
                                                <i class="fas fa-copy text-sm"></i>
                                            </button>
                                        </form>
                                        <button onclick="confirmDelete('{{ route('projects.expenses.destroy', [$project, $expense]) }}', 'تأكيد حذف المصروف', 'هل أنت متأكد من حذف المصروف {{ $expense->title }}؟')" class="text-red-600 hover:text-red-900 p-1" title="حذف">
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
                <p class="text-gray-500 mb-6">ابدأ بإضافة مصروف جديد للمشروع</p>
                <a href="{{ route('projects.expenses.create', $project) }}" class="btn-primary text-white px-6 py-3 rounded-xl inline-flex items-center hover:no-underline">
                    <i class="fas fa-plus text-sm ml-2"></i>
                    إضافة مصروف جديد
                </a>
            </div>
        @endif
    </div>
</div>

<!-- Modal for Bulk Duplicate -->
<div id="bulkDuplicateModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50 flex items-center justify-center">
    <div class="bg-white rounded-2xl p-6 max-w-md w-full mx-4 shadow-xl">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-xl font-bold text-gray-800">نسخ المصروفات المحددة</h3>
            <button onclick="closeBulkDuplicateModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <form id="bulkDuplicateForm" method="POST" action="{{ route('projects.expenses.bulk-duplicate', $project) }}">
            @csrf
            <div class="mb-4">
                <label for="record_month_year" class="block text-sm font-medium text-gray-700 mb-2">
                    الشهر المحاسبي الجديد <span class="text-red-500">*</span>
                </label>
                <input
                    type="month"
                    id="record_month_year"
                    name="record_month_year"
                    value="{{ date('Y-m') }}"
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-colors"
                    required
                />
                <p class="text-xs text-gray-500 mt-1">اختر الشهر المحاسبي للسجلات المنسوخة</p>
            </div>
            <div class="mb-4">
                <p class="text-sm text-gray-600">
                    سيتم نسخ <span id="selectedCount" class="font-bold text-primary">0</span> مصروف
                </p>
            </div>
            <div class="flex items-center justify-end space-x-3 rtl:space-x-reverse">
                <button type="button" onclick="closeBulkDuplicateModal()" class="px-6 py-3 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 transition-colors">
                    إلغاء
                </button>
                <button type="submit" class="btn-primary text-white px-6 py-3 rounded-xl hover:no-underline">
                    <i class="fas fa-copy text-sm ml-2"></i>
                    نسخ
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    var table = $('#expensesTable').DataTable({
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
                    columns: [1, 2, 3, 4, 5, 6, 7] // Skip checkbox column (0) and actions column (7)
                }
            },
            {
                extend: 'pdf',
                text: 'تصدير PDF',
                className: 'btn btn-danger',
                exportOptions: {
                    columns: [1, 2, 3, 4, 5, 6, 7] // Skip checkbox column (0) and actions column (7)
                }
            },
            {
                extend: 'print',
                text: 'طباعة',
                className: 'btn btn-info',
                exportOptions: {
                    columns: [1, 2, 3, 4, 5, 6, 7] // Skip checkbox column (0) and actions column (7)
                }
            },
            {
                text: 'نسخ المحدد',
                className: 'btn btn-purple',
                action: function (e, dt, node, config) {
                    // Trigger the bulk duplicate button click
                    $('#bulkDuplicateBtn').trigger('click');
                }
            }
        ],
        columnDefs: [
            {
                targets: [0], // Checkbox column
                orderable: false,
                searchable: false
            },
            {
                targets: [7], // Actions column (last column, index 7)
                orderable: false,
                searchable: false
            }
        ],
        order: [[2, 'desc']], // Sort by date descending (newest first) - column 2 is date (after checkbox 0 and record_month_year 1)
        pageLength: 10,
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]]
    });

    // Store selected expense IDs across all pages
    var selectedExpenseIds = new Set();

    // Select All Checkbox - Use event delegation for DataTable
    $(document).on('change', '#selectAllExpenses', function() {
        var isChecked = $(this).is(':checked');
        var visibleCheckboxes = table.$('.expense-checkbox');
        
        visibleCheckboxes.each(function() {
            var expenseId = $(this).val();
            $(this).prop('checked', isChecked);
            
            if (isChecked) {
                selectedExpenseIds.add(expenseId);
            } else {
                selectedExpenseIds.delete(expenseId);
            }
        });
        
        updateBulkDuplicateButton();
    });

    // Individual Checkbox - Use event delegation for DataTable
    $(document).on('change', '.expense-checkbox', function() {
        var expenseId = $(this).val();
        
        if ($(this).is(':checked')) {
            selectedExpenseIds.add(expenseId);
        } else {
            selectedExpenseIds.delete(expenseId);
        }
        
        updateSelectAllCheckbox();
        updateBulkDuplicateButton();
    });

    // Update Select All Checkbox State
    function updateSelectAllCheckbox() {
        var visibleCheckboxes = table.$('.expense-checkbox');
        var allChecked = true;
        
        visibleCheckboxes.each(function() {
            if (!$(this).is(':checked')) {
                allChecked = false;
                return false; // break loop
            }
        });
        
        $('#selectAllExpenses').prop('checked', visibleCheckboxes.length > 0 && allChecked);
    }

    // Update Bulk Duplicate Button
    function updateBulkDuplicateButton() {
        const bulkBtn = $('#bulkDuplicateBtn');
        const duplicateTableBtn = $('.btn-purple'); // DataTable duplicate button
        
        if (selectedExpenseIds.size > 0) {
            bulkBtn.prop('disabled', false);
            bulkBtn.removeClass('opacity-50 cursor-not-allowed');
            // Enable DataTable button
            if (duplicateTableBtn.length) {
                duplicateTableBtn.prop('disabled', false);
                duplicateTableBtn.removeClass('disabled');
            }
        } else {
            bulkBtn.prop('disabled', true);
            bulkBtn.addClass('opacity-50 cursor-not-allowed');
            // Disable DataTable button
            if (duplicateTableBtn.length) {
                duplicateTableBtn.prop('disabled', true);
                duplicateTableBtn.addClass('disabled');
            }
        }
    }

    // Open Bulk Duplicate Modal
    $('#bulkDuplicateBtn').on('click', function(e) {
        e.preventDefault();
        
        if (selectedExpenseIds.size === 0) {
            alert('يرجى اختيار مصروف واحد على الأقل');
            return;
        }

        // Convert Set to Array
        var selectedIds = Array.from(selectedExpenseIds);

        // Add hidden inputs for selected expense IDs
        $('#bulkDuplicateForm').find('input[name="expense_ids[]"]').remove();
        selectedIds.forEach(function(id) {
            $('#bulkDuplicateForm').append('<input type="hidden" name="expense_ids[]" value="' + id + '">');
        });

        $('#selectedCount').text(selectedIds.length);
        $('#bulkDuplicateModal').removeClass('hidden');
    });

    // Close Bulk Duplicate Modal
    function closeBulkDuplicateModal() {
        $('#bulkDuplicateModal').addClass('hidden');
        // Reset form
        $('#bulkDuplicateForm').find('input[name="expense_ids[]"]').remove();
    }

    // Close modal when clicking outside
    $('#bulkDuplicateModal').on('click', function(e) {
        if ($(e.target).attr('id') === 'bulkDuplicateModal') {
            closeBulkDuplicateModal();
        }
    });

    // Update button state when DataTable redraws (pagination, search, etc.)
    table.on('draw', function() {
        // Restore checkbox states from selectedExpenseIds
        table.$('.expense-checkbox').each(function() {
            var expenseId = $(this).val();
            $(this).prop('checked', selectedExpenseIds.has(expenseId));
        });
        
        updateSelectAllCheckbox();
        updateBulkDuplicateButton();
    });

    // Initialize button state
    updateBulkDuplicateButton();
});
</script>
@endsection
