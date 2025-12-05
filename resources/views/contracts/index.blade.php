@extends('layouts.dashboard')

@section('title', 'إدارة العقود')
@section('page-title', 'إدارة العقود')
@section('page-description', 'عرض وإدارة قائمة العقود')

@section('content')
<div class="space-y-6">
    <!-- Header Actions -->
    <div class="card page-header rounded-2xl p-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <div class="w-12 h-12 logo-gradient rounded-2xl flex items-center justify-center shadow-lg icon-spacing ml-3">
                    <i class="fas fa-file-contract text-white text-xl"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">العقود</h2>
                    <p class="text-gray-600">إدارة قائمة العقود والاتفاقيات</p>
                </div>
            </div>
            <div class="flex items-center space-x-3 rtl:space-x-reverse">
                <a href="{{ route('contracts.create') }}" class="btn-primary text-white px-6 py-3 rounded-xl flex items-center hover:no-underline">
                    <i class="fas fa-plus text-sm ml-2"></i>
                    إضافة عقد جديد
                </a>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total Contracts -->
        <div class="card rounded-2xl p-6 hover:shadow-xl transition-all duration-300 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 mb-1">إجمالي العقود</p>
                    <p class="text-3xl font-bold text-blue-600">{{ number_format($stats['total_contracts']) }}</p>
                </div>
                <div class="w-14 h-14 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg">
                    <i class="fas fa-file-contract text-white text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Total Amount -->
        <div class="card rounded-2xl p-6 hover:shadow-xl transition-all duration-300 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 mb-1">إجمالي المبلغ</p>
                    <p class="text-3xl font-bold text-green-600">{{ number_format($stats['total_amount'], 2) }}</p>
                    <p class="text-xs text-gray-500 mt-1">ج.م</p>
                </div>
                <div class="w-14 h-14 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center shadow-lg">
                    <i class="fas fa-money-bill-wave text-white text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Active Contracts -->
        <div class="card rounded-2xl p-6 hover:shadow-xl transition-all duration-300 border-l-4 border-purple-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 mb-1">عقود نشطة</p>
                    <p class="text-3xl font-bold text-purple-600">{{ number_format($stats['active_contracts']) }}</p>
                </div>
                <div class="w-14 h-14 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg">
                    <i class="fas fa-check-circle text-white text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Active Amount -->
        <div class="card rounded-2xl p-6 hover:shadow-xl transition-all duration-300 border-l-4 border-orange-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 mb-1">مبلغ العقود النشطة</p>
                    <p class="text-3xl font-bold text-orange-600">{{ number_format($stats['active_amount'], 2) }}</p>
                    <p class="text-xs text-gray-500 mt-1">ج.م</p>
                </div>
                <div class="w-14 h-14 bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl flex items-center justify-center shadow-lg">
                    <i class="fas fa-coins text-white text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Contracts Table -->
    <div class="card rounded-2xl overflow-hidden">
        @if($contracts->count() > 0)
            <div class="overflow-x-auto p-6">
                <table id="contractsTable" class="w-full" dir="rtl">
                    <thead>
                        <tr>
                            <th class="text-right" style="text-align: justify; padding-right: 33px;">الموظف</th>
                            <th class="text-right" style="text-align: justify; padding-right: 33px;">نظام التعامل</th>
                            <th class="text-right" style="text-align: justify; padding-right: 33px;">المبلغ المتفق عليه</th>
                            <th class="text-right" style="text-align: justify; padding-right: 33px;">مدة العقد</th>
                            <th class="text-right" style="text-align: justify; padding-right: 33px;">تاريخ النهاية</th>
                            <th class="text-right" style="text-align: justify; padding-right: 33px;">الوقت المتبقي</th>
                            <th class="text-right" style="text-align: justify; padding-right: 33px;">الحالة</th>
                            <th class="text-center" style="text-align: justify; padding-right: 33px;">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($contracts as $contract)
                            @php
                                $today = \Carbon\Carbon::today();
                                $startDate = $contract->start_date;
                                $endDate = $contract->end_date;
                                
                                // حساب مدة العقد بالشهور
                                $contractDurationMonths = $startDate->diffInMonths($endDate);
                                $contractDurationDays = $startDate->diffInDays($endDate);
                                
                                // حساب الوقت المتبقي
                                $daysRemaining = $today->diffInDays($endDate, false); // false يعني القيمة السالبة إذا انتهى
                                $isExpired = $endDate->isPast();
                                $isExpiringSoon = !$isExpired && $daysRemaining <= 30; // أقل من 30 يوم
                                
                                // تحديد لون الوقت المتبقي
                                $remainingColor = 'text-gray-600';
                                $remainingBg = 'bg-gray-100';
                                if ($isExpired) {
                                    $remainingColor = 'text-red-600';
                                    $remainingBg = 'bg-red-100';
                                } elseif ($isExpiringSoon) {
                                    $remainingColor = 'text-orange-600';
                                    $remainingBg = 'bg-orange-100';
                                } elseif ($daysRemaining > 30) {
                                    $remainingColor = 'text-green-600';
                                    $remainingBg = 'bg-green-100';
                                }
                            @endphp
                            <tr class="{{ $isExpired ? 'bg-red-50' : ($isExpiringSoon ? 'bg-orange-50' : '') }}">
                                <td>
                                    <div class="text-right">
                                        <div class="text-sm font-medium text-gray-900">{{ $contract->employee->name ?? 'غير محدد' }}</div>
                                        @if($contract->employee && $contract->employee->email)
                                            <div class="text-xs text-gray-500">{{ $contract->employee->email }}</div>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <span class="px-3 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800">
                                        {{ $contract->payment_type_label }}
                                    </span>
                                </td>
                                <td>
                                    <div class="text-sm font-semibold text-gray-900 text-right">
                                        {{ number_format($contract->agreed_amount, 2) }} ج.م
                                    </div>
                                </td>
                                <td>
                                    <div class="text-sm text-gray-900 text-right">
                                        <div class="font-medium">{{ $contractDurationMonths }} شهر</div>
                                        <div class="text-xs text-gray-500">({{ $contractDurationDays }} يوم)</div>
                                    </div>
                                </td>
                                <td>
                                    <div class="text-sm text-gray-900 text-right">
                                        <div>{{ $endDate->format('Y-m-d') }}</div>
                                        @php
                                            $months = [1 => 'يناير', 2 => 'فبراير', 3 => 'مارس', 4 => 'أبريل', 5 => 'مايو', 6 => 'يونيو', 7 => 'يوليو', 8 => 'أغسطس', 9 => 'سبتمبر', 10 => 'أكتوبر', 11 => 'نوفمبر', 12 => 'ديسمبر'];
                                            $monthNum = $endDate->format('n');
                                        @endphp
                                        <div class="text-xs text-gray-500">{{ $months[$monthNum] }} {{ $endDate->format('Y') }}</div>
                                    </div>
                                </td>
                                <td>
                                    <div class="text-sm text-right">
                                        @if($isExpired)
                                            <span class="px-3 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800">
                                                <i class="fas fa-exclamation-circle ml-1"></i>
                                                انتهى العقد
                                            </span>
                                        @else
                                            <span class="px-3 py-1 text-xs font-medium rounded-full {{ $remainingBg }} {{ $remainingColor }}">
                                                @if($daysRemaining > 30)
                                                    <i class="fas fa-check-circle ml-1"></i>
                                                @else
                                                    <i class="fas fa-clock ml-1"></i>
                                                @endif
                                                @if($daysRemaining >= 30)
                                                    {{ floor($daysRemaining / 30) }} شهر
                                                    @if($daysRemaining % 30 > 0)
                                                        و {{ $daysRemaining % 30 }} يوم
                                                    @endif
                                                @else
                                                    {{ $daysRemaining }} يوم
                                                @endif
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <span class="status-badge
                                        @if($contract->status === 'active') status-active
                                        @elseif($contract->status === 'completed') status-completed
                                        @else status-cancelled @endif">
                                        {{ $contract->status_badge }}
                                    </span>
                                </td>
                                <td>
                                    <div class="flex items-center justify-center space-x-2 rtl:space-x-reverse">
                                        <a href="{{ route('contracts.show', $contract) }}" class="text-blue-600 hover:text-blue-900 p-1" title="عرض التفاصيل">
                                            <i class="fas fa-eye text-sm"></i>
                                        </a>
                                        <a href="{{ route('contracts.edit', $contract) }}" class="text-yellow-600 hover:text-yellow-900 p-1" title="تعديل">
                                            <i class="fas fa-edit text-sm"></i>
                                        </a>
                                        <button onclick="confirmDelete('{{ route('contracts.destroy', $contract) }}', 'تأكيد حذف العقد', 'هل أنت متأكد من حذف العقد الخاص بـ {{ $contract->employee->name ?? 'الموظف' }}؟')" class="text-red-600 hover:text-red-900 p-1" title="حذف">
                                            <i class="fas fa-trash text-sm"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $contracts->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <i class="fas fa-file-contract text-6xl text-gray-300 mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">لا يوجد عقود</h3>
                <p class="text-gray-500 mb-6">ابدأ بإضافة عقد جديد</p>
                <a href="{{ route('contracts.create') }}" class="btn-primary text-white px-6 py-3 rounded-xl inline-flex items-center hover:no-underline">
                    <i class="fas fa-plus text-sm ml-2"></i>
                    إضافة عقد جديد
                </a>
            </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    $('#contractsTable').DataTable({
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
        order: [[4, 'asc']] // Sort by end date ascending
    });
});
</script>
@endsection

