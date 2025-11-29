@extends('layouts.dashboard')

@section('title', 'إجمالي التقرير المالي للموظفين')
@section('page-title', 'إجمالي التقرير المالي للموظفين')
@section('page-description', 'عرض إجمالي المبالغ المدفوعة والمستحقة لكل موظف')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="card page-header rounded-2xl p-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <div class="w-12 h-12 logo-gradient rounded-2xl flex items-center justify-center shadow-lg icon-spacing ml-3">
                    <i class="fas fa-users-cog text-white text-xl"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">إجمالي التقرير المالي للموظفين</h2>
                    <p class="text-gray-600">عرض إجمالي المبالغ المدفوعة والمستحقة لكل موظف</p>
                </div>
            </div>
            <div class="flex items-center space-x-3 rtl:space-x-reverse">
            </div>
        </div>
    </div>

    <!-- Monthly Records Cards -->
    <div class="card rounded-2xl p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4">السجلات الشهرية</h3>
        <div class="flex flex-wrap gap-4">
            <!-- All Card -->
            <a href="{{ route('reports.total-employees-financial') }}" class="monthly-record-card flex-1 min-w-[150px] max-w-[200px] p-4 rounded-xl border-2 transition-all cursor-pointer {{ !request('record_month') ? 'border-primary bg-primary-50' : 'border-gray-200 hover:border-primary hover:bg-gray-50' }}">
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
                    $recordMonthValue = $record['record_month_year'];
                    $months = [
                        1 => 'يناير', 2 => 'فبراير', 3 => 'مارس', 4 => 'أبريل',
                        5 => 'مايو', 6 => 'يونيو', 7 => 'يوليو', 8 => 'أغسطس',
                        9 => 'سبتمبر', 10 => 'أكتوبر', 11 => 'نوفمبر', 12 => 'ديسمبر'
                    ];
                    $parts = explode('-', $recordMonthValue);
                    $year = $parts[0] ?? '';
                    $monthNum = isset($parts[1]) ? (int)$parts[1] : 0;
                    $monthName = $months[$monthNum] ?? '';
                    $isActive = request('record_month') == $recordMonthValue;
                    $count = $record['count'];
                @endphp
                <a href="{{ route('reports.total-employees-financial', ['record_month' => $recordMonthValue]) }}" class="monthly-record-card flex-1 min-w-[150px] max-w-[200px] p-4 rounded-xl border-2 transition-all cursor-pointer {{ $isActive ? 'border-primary bg-primary-50' : 'border-gray-200 hover:border-primary hover:bg-gray-50' }}">
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
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="card rounded-2xl p-6">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center ml-4">
                    <i class="fas fa-check-circle text-green-600 text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">إجمالي المبالغ المدفوعة</p>
                    <p class="text-2xl font-bold text-gray-900">
                        {{ number_format($grandTotalPaid, 2) }} جنيه
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
                    <p class="text-sm text-gray-600">إجمالي المبالغ المستحقة</p>
                    <p class="text-2xl font-bold text-gray-900">
                        {{ number_format($grandTotalPending, 2) }} جنيه
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Employees Table -->
    <div class="card rounded-2xl p-6">
        @if($employeesData->count() > 0)
            <div class="overflow-x-auto">
                <table id="employeesFinancialTable" class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider" style="text-align: justify; padding-right: 33px;">
                                اسم الموظف
                            </th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider" style="text-align: justify; padding-right: 33px;">
                                عدد المشاريع
                            </th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider" style="text-align: justify; padding-right: 33px;">
                                إجمالي المبلغ المدفوع
                            </th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider" style="text-align: justify; padding-right: 33px;">
                                المبلغ المستحق
                            </th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider" style="text-align: justify; padding-right: 33px;">
                                الإجراءات
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($employeesData as $data)
                            <tr class="hover:bg-gray-50 {{ !$data['has_expenses'] ? 'bg-yellow-50' : '' }}">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-gradient-to-r from-purple-500 to-blue-500 rounded-xl flex items-center justify-center ml-3">
                                            <i class="fas fa-user text-white text-sm"></i>
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">{{ $data['employee']->name }}</div>
                                            <div class="text-xs text-gray-500">{{ $data['employee']->role_badge }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($data['projects_count'] > 0)
                                        <button onclick="showEmployeeProjects({{ $data['employee']->id }})" class="text-sm font-semibold text-blue-600 hover:text-blue-800 hover:underline cursor-pointer">
                                            {{ $data['projects_count'] }}
                                        </button>
                                    @else
                                        <div class="text-sm font-semibold text-gray-400">
                                        {{ $data['projects_count'] }}
                                    </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($data['total_paid'] > 0)
                                    <a href="{{ route('reports.employee-paid-expenses', ['employee' => $data['employee']->id, 'record_month' => $recordMonth]) }}" class="text-sm font-semibold text-green-600 hover:text-green-800 hover:underline cursor-pointer">
                                        {{ number_format($data['total_paid'], 2) }} جنيه
                                    </a>
                                    @else
                                    <div class="text-sm font-semibold text-gray-400">
                                        {{ number_format($data['total_paid'], 2) }} جنيه
                                    </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-semibold text-orange-600">
                                        {{ number_format($data['total_pending'], 2) }} جنيه
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                    <div class="flex items-center justify-center space-x-2 rtl:space-x-reverse">
                                        @if($data['total_pending'] > 0)
                                            <button onclick="showMarkAsPaidModal({{ $data['employee']->id }}, '{{ $data['employee']->name }}', {{ $data['total_pending'] }})" class="text-green-600 hover:text-green-900 p-1" title="تحويل المستحق إلى مدفوع">
                                                <i class="fas fa-check-circle text-sm"></i>
                                            </button>
                                        @endif
                                        <button onclick="showEmployeeTransferInfo({{ $data['employee']->id }})" class="text-purple-600 hover:text-purple-900 p-1" title="بيانات التحويل">
                                            <i class="fas fa-credit-card text-sm"></i>
                                        </button>
                                        <a href="{{ route('reports.employee-financial', ['employee_id' => $data['employee']->id, 'record_month' => $recordMonth]) }}" class="text-blue-600 hover:text-blue-900 p-1" title="عرض التفاصيل">
                                            <i class="fas fa-eye text-sm"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50">
                        <tr>
                            <td colspan="2" class="px-6 py-4 text-right font-bold text-gray-900">
                                الإجمالي
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-bold text-green-600">
                                    {{ number_format($grandTotalPaid, 2) }} جنيه
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-bold text-orange-600">
                                    {{ number_format($grandTotalPending, 2) }} جنيه
                                </div>
                            </td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @else
            <div class="text-center py-12">
                <i class="fas fa-users-cog text-6xl text-gray-300 mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">لا يوجد بيانات</h3>
                <p class="text-gray-500">لا توجد مصروفات للموظفين في الفترة المحددة</p>
            </div>
        @endif
    </div>
</div>

<!-- Modal for Employee Projects -->
<div id="employeeProjectsModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50 flex items-center justify-center">
    <div class="bg-white rounded-2xl p-6 max-w-2xl w-full mx-4 shadow-xl max-h-[80vh] overflow-y-auto">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-xl font-bold text-gray-800" id="modalEmployeeName">مشاريع الموظف</h3>
            <button onclick="closeEmployeeProjectsModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div id="modalProjectsList" class="space-y-3">
            <!-- Projects will be loaded here -->
        </div>
    </div>
</div>

<!-- Modal for Mark as Paid -->
<div id="markAsPaidModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50 flex items-center justify-center">
    <div class="bg-white rounded-2xl p-6 max-w-md w-full mx-4 shadow-xl">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-xl font-bold text-gray-800">تحويل المبلغ المستحق إلى مدفوع</h3>
            <button onclick="closeMarkAsPaidModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div class="mb-4">
            <p class="text-gray-700 mb-2">الموظف: <span id="markAsPaidEmployeeName" class="font-semibold"></span></p>
            <p class="text-gray-700 mb-2">المبلغ المستحق: <span id="markAsPaidAmount" class="font-semibold text-orange-600"></span> جنيه</p>
            <p class="text-sm text-gray-500">سيتم تحويل جميع المصروفات المستحقة لهذا الموظف إلى مدفوعة.</p>
        </div>
        <form id="markAsPaidForm" method="POST" action="">
            @csrf
            <input type="hidden" name="record_month" value="{{ $recordMonth }}">
            <div class="flex items-center justify-end space-x-3 rtl:space-x-reverse">
                <button type="button" onclick="closeMarkAsPaidModal()" class="px-4 py-2 text-gray-700 bg-gray-200 rounded-xl hover:bg-gray-300 transition-colors">
                    إلغاء
                </button>
                <button type="submit" class="px-4 py-2 text-white bg-green-600 rounded-xl hover:bg-green-700 transition-colors">
                    تأكيد التحويل
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal for Employee Transfer Info -->
<div id="employeeTransferModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50 flex items-center justify-center">
    <div class="bg-white rounded-2xl p-6 max-w-md w-full mx-4 shadow-xl">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-xl font-bold text-gray-800" id="transferModalEmployeeName">بيانات التحويل</h3>
            <button onclick="closeEmployeeTransferModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div id="transferInfoContent" class="space-y-4">
            <!-- Transfer info will be loaded here -->
        </div>
    </div>
</div>

<!-- Store projects data in JavaScript -->
<script>
var employeesProjectsData = {
    @foreach($employeesData as $data)
    {{ $data['employee']->id }}: {
        employeeName: "{{ $data['employee']->name }}",
        projects: [
            @foreach($data['projects'] as $project)
            {
                id: {{ $project->id }},
                name: "{{ $project->business_name }}",
                client: "{{ $project->client->name ?? 'غير محدد' }}",
                url: "{{ route('projects.show', $project) }}"
            }@if(!$loop->last),@endif
            @endforeach
        ]
    }@if(!$loop->last),@endif
    @endforeach
};

// Store transfer info data
var employeesTransferData = {
    @foreach($employeesData as $data)
    {{ $data['employee']->id }}: {
        employeeName: "{{ $data['employee']->name }}",
        instapayNumber: "{{ $data['employee']->instapay_number ?? '' }}",
        vodafoneCashNumber: "{{ $data['employee']->vodafone_cash_number ?? '' }}"
    }@if(!$loop->last),@endif
    @endforeach
};
</script>
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
    $('#employeesFinancialTable').DataTable({
        responsive: true,
        paging: true,
        searching: false,
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
        columnDefs: [
            {
                targets: [4], // Actions column
                orderable: false,
                searchable: false
            }
        ],
        order: [[2, 'desc']], // Sort by paid amount descending
        pageLength: 100,
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]]
    });
});

// Show Employee Projects Modal
function showEmployeeProjects(employeeId) {
    const employeeData = employeesProjectsData[employeeId];
    if (!employeeData) {
        alert('لا توجد بيانات للموظف');
        return;
    }

    // Set employee name
    document.getElementById('modalEmployeeName').textContent = 'مشاريع: ' + employeeData.employeeName;

    // Clear and populate projects list
    const projectsList = document.getElementById('modalProjectsList');
    projectsList.innerHTML = '';

    if (employeeData.projects.length === 0) {
        projectsList.innerHTML = '<p class="text-gray-500 text-center py-4">لا يوجد مشاريع لهذا الموظف</p>';
    } else {
        employeeData.projects.forEach(function(project) {
            const projectItem = document.createElement('div');
            projectItem.className = 'p-4 border border-gray-200 rounded-xl hover:border-primary hover:bg-gray-50 transition-all';
            projectItem.innerHTML = `
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <a href="${project.url}" class="text-sm font-medium text-gray-900 hover:text-primary">
                            ${project.name}
                        </a>
                        <p class="text-xs text-gray-500 mt-1">العميل: ${project.client}</p>
                    </div>
                    <a href="${project.url}" class="text-blue-600 hover:text-blue-800 ml-3">
                        <i class="fas fa-external-link-alt"></i>
                    </a>
                </div>
            `;
            projectsList.appendChild(projectItem);
        });
    }

    // Show modal
    document.getElementById('employeeProjectsModal').classList.remove('hidden');
}

// Close Employee Projects Modal
function closeEmployeeProjectsModal() {
    document.getElementById('employeeProjectsModal').classList.add('hidden');
}

// Close modal when clicking outside
document.getElementById('employeeProjectsModal').addEventListener('click', function(e) {
    if (e.target.id === 'employeeProjectsModal') {
        closeEmployeeProjectsModal();
    }
});

// Show Employee Transfer Info Modal
function showEmployeeTransferInfo(employeeId) {
    const employeeData = employeesTransferData[employeeId];
    if (!employeeData) {
        alert('لا توجد بيانات للموظف');
        return;
    }

    // Set employee name
    document.getElementById('transferModalEmployeeName').textContent = 'بيانات التحويل: ' + employeeData.employeeName;

    // Clear and populate transfer info
    const transferContent = document.getElementById('transferInfoContent');
    transferContent.innerHTML = '';

    let hasInfo = false;

    if (employeeData.instapayNumber) {
        hasInfo = true;
        const instapayDiv = document.createElement('div');
        instapayDiv.className = 'p-4 bg-blue-50 border border-blue-200 rounded-xl';
        instapayDiv.innerHTML = `
            <label class="block text-sm font-medium text-blue-700 mb-2">رقم Instapay:</label>
            <div class="flex items-center bg-white rounded-lg p-3 border border-blue-200">
                <span class="flex-1 text-sm text-gray-800 font-mono" id="instapay-number-${employeeId}">${employeeData.instapayNumber}</span>
                <button type="button" onclick="copyToClipboard('instapay-number-${employeeId}', 'instapay-copy-btn-${employeeId}')" id="instapay-copy-btn-${employeeId}" class="ml-2 text-blue-600 hover:text-blue-700" title="نسخ">
                    <i class="fas fa-copy text-sm"></i>
                </button>
            </div>
        `;
        transferContent.appendChild(instapayDiv);
    }

    if (employeeData.vodafoneCashNumber) {
        hasInfo = true;
        const vodafoneDiv = document.createElement('div');
        vodafoneDiv.className = 'p-4 bg-green-50 border border-green-200 rounded-xl';
        vodafoneDiv.innerHTML = `
            <label class="block text-sm font-medium text-green-700 mb-2">رقم فودافون كاش:</label>
            <div class="flex items-center bg-white rounded-lg p-3 border border-green-200">
                <span class="flex-1 text-sm text-gray-800 font-mono" id="vodafone-number-${employeeId}">${employeeData.vodafoneCashNumber}</span>
                <button type="button" onclick="copyToClipboard('vodafone-number-${employeeId}', 'vodafone-copy-btn-${employeeId}')" id="vodafone-copy-btn-${employeeId}" class="ml-2 text-green-600 hover:text-green-700" title="نسخ">
                    <i class="fas fa-copy text-sm"></i>
                </button>
            </div>
        `;
        transferContent.appendChild(vodafoneDiv);
    }

    if (!hasInfo) {
        transferContent.innerHTML = '<p class="text-gray-500 text-center py-4">لا توجد بيانات تحويل لهذا الموظف</p>';
    }

    // Show modal
    document.getElementById('employeeTransferModal').classList.remove('hidden');
}

// Close Employee Transfer Modal
function closeEmployeeTransferModal() {
    document.getElementById('employeeTransferModal').classList.add('hidden');
}

// Close modal when clicking outside
document.getElementById('employeeTransferModal').addEventListener('click', function(e) {
    if (e.target.id === 'employeeTransferModal') {
        closeEmployeeTransferModal();
    }
});

// Copy to clipboard function
function copyToClipboard(elementId, buttonId) {
    const element = document.getElementById(elementId);
    const text = element.textContent;
    
    navigator.clipboard.writeText(text).then(function() {
        const button = document.getElementById(buttonId);
        const originalHTML = button.innerHTML;
        button.innerHTML = '<i class="fas fa-check text-sm"></i>';
        button.classList.add('text-green-600');
        
        setTimeout(function() {
            button.innerHTML = originalHTML;
            button.classList.remove('text-green-600');
        }, 2000);
    }).catch(function(err) {
        console.error('Failed to copy:', err);
        alert('فشل نسخ النص');
    });
}

// Show Mark as Paid Modal
function showMarkAsPaidModal(employeeId, employeeName, totalPending) {
    document.getElementById('markAsPaidEmployeeName').textContent = employeeName;
    document.getElementById('markAsPaidAmount').textContent = totalPending.toLocaleString('ar-EG', {minimumFractionDigits: 2, maximumFractionDigits: 2});
    
    const form = document.getElementById('markAsPaidForm');
    const recordMonth = '{{ $recordMonth }}';
    let url = `/reports/total-employees-financial/${employeeId}/mark-paid`;
    if (recordMonth) {
        url += `?record_month=${recordMonth}`;
    }
    form.action = url;
    
    document.getElementById('markAsPaidModal').classList.remove('hidden');
}

// Close Mark as Paid Modal
function closeMarkAsPaidModal() {
    document.getElementById('markAsPaidModal').classList.add('hidden');
}

// Close modal when clicking outside
document.getElementById('markAsPaidModal').addEventListener('click', function(e) {
    if (e.target.id === 'markAsPaidModal') {
        closeMarkAsPaidModal();
    }
});
</script>
@endsection

