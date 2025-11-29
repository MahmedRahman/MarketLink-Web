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
                                    <a href="{{ route('reports.employee-financial', ['employee_id' => $data['employee']->id, 'record_month' => $recordMonth]) }}" class="text-blue-600 hover:text-blue-900 p-1" title="عرض التفاصيل">
                                        <i class="fas fa-eye text-sm"></i>
                                    </a>
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
</script>
@endsection

