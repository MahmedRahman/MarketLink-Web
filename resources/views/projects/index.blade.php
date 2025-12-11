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
                <a href="{{ route('projects.create') }}" class="btn-primary text-white px-6 py-3 rounded-xl flex items-center hover:no-underline">
                    <i class="fas fa-plus text-sm ml-2"></i>
                    إضافة مشروع جديد
                </a>
            </div>
        </div>
    </div>

    <!-- Responsibility Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
        <!-- Total Projects -->
        <div class="responsibility-card card rounded-2xl p-6 hover:shadow-xl transition-all duration-300 cursor-pointer {{ (!isset($selectedResponsibility) || $selectedResponsibility === 'all' || $selectedResponsibility === '') ? 'ring-2 ring-gray-400 shadow-lg' : '' }}" 
             data-responsibility="all" 
             onclick="filterByResponsibility('all')">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 mb-1">إجمالي المشاريع</p>
                    <p class="text-3xl font-bold text-gray-800">{{ number_format($responsibilityStats['total']) }}</p>
                </div>
                <div class="w-14 h-14 bg-gradient-to-br from-gray-500 to-gray-600 rounded-xl flex items-center justify-center shadow-lg">
                    <i class="fas fa-project-diagram text-white text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Full Management -->
        <div class="responsibility-card card rounded-2xl p-6 hover:shadow-xl transition-all duration-300 border-l-4 border-blue-500 cursor-pointer {{ (isset($selectedResponsibility) && $selectedResponsibility === 'full_management') ? 'ring-2 ring-blue-400 shadow-lg' : '' }}" 
             data-responsibility="full_management" 
             onclick="filterByResponsibility('full_management')">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 mb-1">إدارة كاملة</p>
                    <p class="text-3xl font-bold text-blue-600">{{ number_format($responsibilityStats['full_management']) }}</p>
                </div>
                <div class="w-14 h-14 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg">
                    <i class="fas fa-cogs text-white text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Media Buyer -->
        <div class="responsibility-card card rounded-2xl p-6 hover:shadow-xl transition-all duration-300 border-l-4 border-purple-500 cursor-pointer {{ (isset($selectedResponsibility) && $selectedResponsibility === 'media_buyer') ? 'ring-2 ring-purple-400 shadow-lg' : '' }}" 
             data-responsibility="media_buyer" 
             onclick="filterByResponsibility('media_buyer')">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 mb-1">ميديا بير</p>
                    <p class="text-3xl font-bold text-purple-600">{{ number_format($responsibilityStats['media_buyer']) }}</p>
                </div>
                <div class="w-14 h-14 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg">
                    <i class="fas fa-bullhorn text-white text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Account Manager -->
        <div class="responsibility-card card rounded-2xl p-6 hover:shadow-xl transition-all duration-300 border-l-4 border-green-500 cursor-pointer {{ (isset($selectedResponsibility) && $selectedResponsibility === 'account_manager') ? 'ring-2 ring-green-400 shadow-lg' : '' }}" 
             data-responsibility="account_manager" 
             onclick="filterByResponsibility('account_manager')">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 mb-1">Account Manager</p>
                    <p class="text-3xl font-bold text-green-600">{{ number_format($responsibilityStats['account_manager']) }}</p>
                </div>
                <div class="w-14 h-14 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center shadow-lg">
                    <i class="fas fa-user-tie text-white text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Design -->
        <div class="responsibility-card card rounded-2xl p-6 hover:shadow-xl transition-all duration-300 border-l-4 border-orange-500 cursor-pointer {{ (isset($selectedResponsibility) && $selectedResponsibility === 'design') ? 'ring-2 ring-orange-400 shadow-lg' : '' }}" 
             data-responsibility="design" 
             onclick="filterByResponsibility('design')">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 mb-1">تصميم</p>
                    <p class="text-3xl font-bold text-orange-600">{{ number_format($responsibilityStats['design']) }}</p>
                </div>
                <div class="w-14 h-14 bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl flex items-center justify-center shadow-lg">
                    <i class="fas fa-palette text-white text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Projects Table -->
    <div class="card rounded-2xl overflow-hidden">
        @if($projects->count() > 0)
            <div class="overflow-x-auto p-6">
                <table id="projectsTable" class="w-full" dir="rtl">
                    <thead>
                        <tr>
                            <th class="text-right" style="text-align: justify; padding-right: 33px;">تاريخ الإنشاء</th>
                            <th class="text-right" style="text-align: justify; padding-right: 33px;">اسم البيزنس</th>
                            <th class="text-right" style="text-align: justify; padding-right: 33px;">العميل</th>
                            <th class="text-right" style="text-align: justify; padding-right: 33px;">الموقع</th>
                            <th class="text-right" style="text-align: justify; padding-right: 33px;">عدد الموظفين</th>
                            <th class="text-right" style="text-align: justify; padding-right: 33px;">المسؤولية</th>
                            <th class="text-right" style="text-align: justify; padding-right: 33px;">الحالة</th>
                            <th class="text-center" style="text-align: justify; padding-right: 33px;">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($projects as $project)
                            <tr>
                                <td>
                                    <div class="text-right">
                                        <div class="text-sm text-gray-500">{{ $project->created_at->format('Y-m-d') }}</div>
                                        @php
                                            $months = [1 => 'يناير', 2 => 'فبراير', 3 => 'مارس', 4 => 'أبريل', 5 => 'مايو', 6 => 'يونيو', 7 => 'يوليو', 8 => 'أغسطس', 9 => 'سبتمبر', 10 => 'أكتوبر', 11 => 'نوفمبر', 12 => 'ديسمبر'];
                                            $month = $project->created_at->format('n');
                                        @endphp
                                        <div class="text-xs text-gray-400 mt-1">{{ $months[$month] }}</div>
                                    </div>
                                </td>
                                <td>
                                    <div class="text-right">
                                        <div class="text-sm font-medium text-gray-900">{{ $project->business_name }}</div>
                                        @if($project->business_description)
                                            <div class="text-xs text-gray-500">{{ Str::limit($project->business_description, 50) }}</div>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="text-right">
                                        <div class="text-sm text-gray-900">{{ $project->client->name ?? 'غير محدد' }}</div>
                                        @if($project->client)
                                            <div class="text-xs text-gray-500">{{ $project->client->email }}</div>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="text-sm text-gray-900 text-right">
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
                                    <div class="text-sm font-semibold text-gray-900 text-right">
                                        {{ $project->employees_count ?? 0 }}
                                    </div>
                                </td>
                                <td>
                                    <div class="flex flex-wrap gap-1 justify-end">
                                        @php
                                            $responsibilities = $project->responsibility ?? [];
                                            if (!is_array($responsibilities)) {
                                                $responsibilities = [];
                                            }
                                            $responsibilityLabels = [
                                                'full_management' => 'إدارة كاملة',
                                                'media_buyer' => 'ميديا بير',
                                                'account_manager' => 'Account Manager',
                                                'design' => 'تصميم'
                                            ];
                                            $responsibilityColors = [
                                                'full_management' => 'bg-blue-100 text-blue-800',
                                                'media_buyer' => 'bg-purple-100 text-purple-800',
                                                'account_manager' => 'bg-green-100 text-green-800',
                                                'design' => 'bg-orange-100 text-orange-800'
                                            ];
                                        @endphp
                                        @if(count($responsibilities) > 0)
                                            @foreach($responsibilities as $resp)
                                                @if(isset($responsibilityLabels[$resp]))
                                                    <span class="px-2 py-1 text-xs font-medium rounded-full {{ $responsibilityColors[$resp] ?? 'bg-gray-100 text-gray-800' }}">
                                                        {{ $responsibilityLabels[$resp] }}
                                                    </span>
                                                @endif
                                            @endforeach
                                        @else
                                            <span class="text-gray-400 text-sm">غير محدد</span>
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
                                    <div class="flex items-center justify-center space-x-2 rtl:space-x-reverse">
                                        <a href="{{ route('projects.show', $project) }}" class="text-blue-600 hover:text-blue-900 p-1" title="عرض التفاصيل">
                                            <i class="fas fa-eye text-sm"></i>
                                        </a>
                                        <a href="{{ route('projects.edit', $project) }}" class="text-yellow-600 hover:text-yellow-900 p-1" title="تعديل">
                                            <i class="fas fa-edit text-sm"></i>
                                        </a>
                                        <a href="{{ route('projects.revenues.index', $project) }}" class="text-green-600 hover:text-green-900 p-1" title="الإيرادات ({{ $project->revenues_count }})">
                                            <i class="fas fa-coins text-sm"></i>
                                        </a>
                                        <a href="{{ route('projects.expenses.index', $project) }}" class="text-red-600 hover:text-red-900 p-1" title="المصروفات ({{ $project->expenses_count }})">
                                            <i class="fas fa-credit-card text-sm"></i>
                                        </a>
                                        <a href="{{ route('projects.content.create', $project) }}" class="text-purple-600 hover:text-purple-900 p-1" title="إضافة محتوى منشور">
                                            <i class="fas fa-file-text text-sm"></i>
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
var table;

$(document).ready(function() {
    table = $('#projectsTable').DataTable({
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
                targets: [7], // Actions column
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
                    columns: [0, 1, 2, 3, 4, 5, 6]
                }
            },
            {
                extend: 'pdf',
                text: 'PDF',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5, 6]
                }
            },
            {
                extend: 'print',
                text: 'Print',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5, 6]
                }
            }
        ]
    });
});

function filterByResponsibility(responsibility) {
    // إزالة active state من جميع الكاردات
    $('.responsibility-card').removeClass('ring-2 shadow-lg');
    
    // إضافة active state للكارد المحدد
    if (responsibility === 'all') {
        $('.responsibility-card[data-responsibility="all"]').addClass('ring-2 ring-gray-400 shadow-lg');
    } else {
        $('.responsibility-card[data-responsibility="' + responsibility + '"]').addClass('ring-2 shadow-lg');
    }
    
    // إعادة تحميل الصفحة مع parameter الفلترة
    const url = new URL(window.location.href);
    if (responsibility === 'all') {
        url.searchParams.delete('responsibility');
    } else {
        url.searchParams.set('responsibility', responsibility);
    }
    window.location.href = url.toString();
}

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

