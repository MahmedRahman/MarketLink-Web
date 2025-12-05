@extends('layouts.dashboard')

@section('title', 'تقويم الاجتماعات')
@section('page-title', 'تقويم الاجتماعات')
@section('page-description', 'عرض وإدارة اجتماعات الشهر الحالي')

@section('content')
<div class="space-y-6">
    <!-- Header Actions -->
    <div class="card page-header rounded-2xl p-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <div class="w-12 h-12 logo-gradient rounded-2xl flex items-center justify-center shadow-lg icon-spacing ml-3">
                    <i class="fas fa-calendar-alt text-white text-xl"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">تقويم الاجتماعات</h2>
                    <p class="text-gray-600">عرض اجتماعات الشهر الحالي</p>
                </div>
            </div>
            <div class="flex items-center space-x-3 rtl:space-x-reverse">
                <a href="{{ route('meetings.create') }}" class="btn-primary text-white px-6 py-3 rounded-xl flex items-center hover:no-underline">
                    <i class="fas fa-plus text-sm ml-2"></i>
                    إضافة اجتماع جديد
                </a>
            </div>
        </div>
    </div>

    <!-- Month Navigation and Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <!-- Month Navigation -->
        <div class="card rounded-2xl p-4">
            <div class="flex items-center justify-between">
                <button onclick="changeMonth('{{ \Carbon\Carbon::parse($selectedMonth)->subMonth()->format('Y-m') }}')" class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
                    <i class="fas fa-chevron-right text-gray-600"></i>
                </button>
                <div class="text-center">
                    @php
                        $months = [1 => 'يناير', 2 => 'فبراير', 3 => 'مارس', 4 => 'أبريل', 5 => 'مايو', 6 => 'يونيو', 7 => 'يوليو', 8 => 'أغسطس', 9 => 'سبتمبر', 10 => 'أكتوبر', 11 => 'نوفمبر', 12 => 'ديسمبر'];
                        $monthNum = $monthDate->format('n');
                        $year = $monthDate->format('Y');
                    @endphp
                    <h3 class="text-lg font-bold text-gray-800">{{ $months[$monthNum] }} {{ $year }}</h3>
                </div>
                <button onclick="changeMonth('{{ \Carbon\Carbon::parse($selectedMonth)->addMonth()->format('Y-m') }}')" class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
                    <i class="fas fa-chevron-left text-gray-600"></i>
                </button>
            </div>
        </div>

        <!-- Total Meetings -->
        <div class="card rounded-2xl p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">إجمالي الاجتماعات</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $totalMeetings }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-calendar text-blue-600"></i>
                </div>
            </div>
        </div>

        <!-- Today Meetings -->
        <div class="card rounded-2xl p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">اجتماعات اليوم</p>
                    <p class="text-2xl font-bold text-green-600">{{ $todayMeetings }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-clock text-green-600"></i>
                </div>
            </div>
        </div>

        <!-- Upcoming Meetings -->
        <div class="card rounded-2xl p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">اجتماعات قادمة</p>
                    <p class="text-2xl font-bold text-orange-600">{{ $upcomingMeetings }}</p>
                </div>
                <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-calendar-check text-orange-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Calendar View -->
    <div class="card rounded-2xl p-6">
        @php
            $startOfMonth = $monthDate->copy()->startOfMonth();
            $endOfMonth = $monthDate->copy()->endOfMonth();
            $startDate = $startOfMonth->copy()->startOfWeek(\Carbon\Carbon::SATURDAY); // بداية الأسبوع من السبت
            $endDate = $endOfMonth->copy()->endOfWeek(\Carbon\Carbon::FRIDAY); // نهاية الأسبوع من الجمعة
            
            $daysOfWeek = ['السبت', 'الأحد', 'الاثنين', 'الثلاثاء', 'الأربعاء', 'الخميس', 'الجمعة'];
            $currentDate = $startDate->copy();
        @endphp

        <!-- Calendar Grid -->
        <div class="overflow-x-auto">
            <table class="w-full border-collapse">
                <thead>
                    <tr>
                        @foreach($daysOfWeek as $day)
                            <th class="p-3 text-center font-semibold text-gray-700 bg-gray-50 border border-gray-200">
                                {{ $day }}
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @while($currentDate->lte($endDate))
                        <tr>
                            @for($i = 0; $i < 7; $i++)
                                @php
                                    $dateKey = $currentDate->format('Y-m-d');
                                    $isCurrentMonth = $currentDate->format('Y-m') === $selectedMonth;
                                    $isToday = $currentDate->isToday();
                                    $dayMeetings = $meetingsByDate[$dateKey] ?? [];
                                @endphp
                                <td class="border border-gray-200 p-2 align-top {{ $isCurrentMonth ? 'bg-white' : 'bg-gray-50' }} {{ $isToday ? 'ring-2 ring-primary' : '' }}" style="min-width: 120px; height: 120px;">
                                    <div class="flex items-center justify-between mb-1">
                                        <span class="text-sm font-medium {{ $isCurrentMonth ? 'text-gray-900' : 'text-gray-400' }} {{ $isToday ? 'bg-primary text-white rounded-full w-6 h-6 flex items-center justify-center' : '' }}">
                                            {{ $currentDate->format('d') }}
                                        </span>
                                        @if(count($dayMeetings) > 0)
                                            <span class="text-xs bg-primary text-white rounded-full px-2 py-0.5">
                                                {{ count($dayMeetings) }}
                                            </span>
                                        @endif
                                    </div>
                                    <div class="space-y-1 max-h-20 overflow-y-auto">
                                        @foreach($dayMeetings as $meeting)
                                            <div class="meeting-item p-1.5 rounded-lg bg-blue-50 border border-blue-200 hover:bg-blue-100 transition-colors cursor-pointer group" onclick="showMeetingDetails({{ $meeting->id }})">
                                                <div class="flex items-center justify-between">
                                                    <div class="flex-1 min-w-0">
                                                        <p class="text-xs font-medium text-blue-900 truncate">{{ $meeting->title }}</p>
                                                        <p class="text-xs text-blue-700">
                                                            <i class="fas fa-clock text-xs ml-1"></i>
                                                            {{ \Carbon\Carbon::parse($meeting->meeting_time)->format('H:i') }}
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </td>
                                @php
                                    $currentDate->addDay();
                                @endphp
                            @endfor
                        </tr>
                    @endwhile
                </tbody>
            </table>
        </div>
    </div>

    <!-- Meetings List (Optional - for mobile view) -->
    <div class="card rounded-2xl p-6 md:hidden">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">قائمة الاجتماعات</h3>
        <div class="space-y-3">
            @foreach($meetings as $meeting)
                <div class="p-4 border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors">
                    <div class="flex items-center justify-between mb-2">
                        <div>
                            <h4 class="font-semibold text-gray-900">{{ $meeting->title }}</h4>
                            <p class="text-sm text-gray-600">
                                {{ $meeting->meeting_date->format('Y-m-d') }} 
                                <span class="mx-2">•</span>
                                {{ \Carbon\Carbon::parse($meeting->meeting_time)->format('H:i') }}
                            </p>
                        </div>
                        <div class="flex items-center space-x-2 rtl:space-x-reverse">
                            <a href="{{ route('meetings.show', $meeting) }}" class="text-blue-600 hover:text-blue-900 p-1">
                                <i class="fas fa-eye text-sm"></i>
                            </a>
                            <a href="{{ route('meetings.edit', $meeting) }}" class="text-yellow-600 hover:text-yellow-900 p-1">
                                <i class="fas fa-edit text-sm"></i>
                            </a>
                        </div>
                    </div>
                    @if($meeting->project)
                        <p class="text-xs text-gray-500">
                            <i class="fas fa-project-diagram ml-1"></i>
                            {{ $meeting->project->business_name }}
                        </p>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</div>

<!-- Meeting Details Modal -->
<div id="meetingModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-xl font-bold text-gray-800">تفاصيل الاجتماع</h3>
            <button onclick="closeMeetingModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div id="meetingModalContent">
            <!-- Content will be loaded here -->
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function changeMonth(month) {
    window.location.href = '{{ route("meetings.index") }}?month=' + month;
}

function showMeetingDetails(meetingId) {
    // يمكن إضافة AJAX لجلب تفاصيل الاجتماع أو التوجيه مباشرة
    window.location.href = '/meetings/' + meetingId;
}

function closeMeetingModal() {
    document.getElementById('meetingModal').classList.add('hidden');
}

// Close modal on outside click
document.getElementById('meetingModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeMeetingModal();
    }
});
</script>

<style>
.meeting-item {
    font-size: 11px;
}

.meeting-item:hover {
    transform: scale(1.02);
}

/* Custom scrollbar for calendar cells */
td div.space-y-1::-webkit-scrollbar {
    width: 4px;
}

td div.space-y-1::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 2px;
}

td div.space-y-1::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 2px;
}

td div.space-y-1::-webkit-scrollbar-thumb:hover {
    background: #555;
}
</style>
@endsection
