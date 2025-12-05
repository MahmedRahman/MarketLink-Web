@extends('layouts.dashboard')

@section('title', 'تفاصيل الاجتماع')
@section('page-title', 'تفاصيل الاجتماع')
@section('page-description', $meeting->title)

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="card page-header rounded-2xl p-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <div class="w-12 h-12 logo-gradient rounded-2xl flex items-center justify-center shadow-lg icon-spacing ml-3">
                    <i class="fas fa-calendar-alt text-white text-xl"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">{{ $meeting->title }}</h2>
                    <p class="text-gray-600">تفاصيل الاجتماع</p>
                </div>
            </div>
            <div class="flex items-center space-x-3 rtl:space-x-reverse">
                <a href="{{ route('meetings.edit', $meeting) }}" class="btn-primary text-white px-6 py-3 rounded-xl flex items-center hover:no-underline">
                    <i class="fas fa-edit text-sm ml-2"></i>
                    تعديل
                </a>
                <a href="{{ route('meetings.index') }}" class="flex items-center px-4 py-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-xl transition-colors icon-spacing">
                    العودة للقائمة
                </a>
            </div>
        </div>
    </div>

    <!-- Meeting Details -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Information -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Basic Information -->
            <div class="card rounded-2xl p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">المعلومات الأساسية</h3>
                <div class="space-y-4">
                    <div>
                        <label class="text-sm font-medium text-gray-600">تاريخ الاجتماع</label>
                        <p class="text-lg text-gray-900 mt-1">
                            {{ $meeting->meeting_date->format('Y-m-d') }}
                            @php
                                $months = [1 => 'يناير', 2 => 'فبراير', 3 => 'مارس', 4 => 'أبريل', 5 => 'مايو', 6 => 'يونيو', 7 => 'يوليو', 8 => 'أغسطس', 9 => 'سبتمبر', 10 => 'أكتوبر', 11 => 'نوفمبر', 12 => 'ديسمبر'];
                                $month = $meeting->meeting_date->format('n');
                            @endphp
                            <span class="text-gray-500">({{ $months[$month] }})</span>
                        </p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-600">معاد الاجتماع</label>
                        <p class="text-lg text-gray-900 mt-1">
                            {{ \Carbon\Carbon::parse($meeting->meeting_time)->format('H:i') }}
                        </p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-600">عنوان الاجتماع</label>
                        <p class="text-lg text-gray-900 mt-1">{{ $meeting->title }}</p>
                    </div>
                    @if($meeting->objective)
                    <div>
                        <label class="text-sm font-medium text-gray-600">الهدف من الاجتماع</label>
                        <p class="text-gray-900 mt-1">{{ $meeting->objective }}</p>
                    </div>
                    @endif
                    <div>
                        <label class="text-sm font-medium text-gray-600">الموظف المسؤول</label>
                        <p class="text-lg text-gray-900 mt-1">
                            @if($meeting->responsibleEmployee)
                                <div class="flex items-center">
                                    <i class="fas fa-user text-gray-400 text-sm ml-2"></i>
                                    <span>{{ $meeting->responsibleEmployee->name }}</span>
                                </div>
                            @else
                                <span class="text-gray-400">غير محدد</span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            <!-- Project Information -->
            @if($meeting->project)
            <div class="card rounded-2xl p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">المشروع</h3>
                <div>
                    <a href="{{ route('projects.show', $meeting->project) }}" class="text-blue-600 hover:text-blue-900 font-medium">
                        {{ $meeting->project->business_name }}
                    </a>
                </div>
            </div>
            @endif

            <!-- Meeting Link -->
            @if($meeting->meeting_link)
            <div class="card rounded-2xl p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">رابط الاجتماع</h3>
                <div>
                    <a href="{{ $meeting->meeting_link }}" target="_blank" class="text-blue-600 hover:text-blue-900 break-all">
                        <i class="fas fa-link text-sm ml-2"></i>
                        {{ $meeting->meeting_link }}
                    </a>
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Attendees -->
            <div class="card rounded-2xl p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">الحضور</h3>
                @if($attendees && $attendees->count() > 0)
                    <div class="space-y-2">
                        @foreach($attendees as $employee)
                            <div class="flex items-center p-2 bg-gray-50 rounded-lg">
                                <i class="fas fa-user text-gray-400 ml-2"></i>
                                <span class="text-gray-900">{{ $employee->name }}</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500">لا يوجد حضور محدد</p>
                @endif
            </div>

            <!-- Actions -->
            <div class="card rounded-2xl p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">الإجراءات</h3>
                <div class="space-y-2">
                    <a href="{{ route('meetings.edit', $meeting) }}" class="w-full btn-primary text-white px-4 py-2 rounded-xl flex items-center justify-center hover:no-underline">
                        <i class="fas fa-edit text-sm ml-2"></i>
                        تعديل
                    </a>
                    <button onclick="confirmDelete('{{ route('meetings.destroy', $meeting) }}', 'تأكيد حذف الاجتماع', 'هل أنت متأكد من حذف الاجتماع {{ $meeting->title }}؟')" class="w-full bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-xl flex items-center justify-center transition-colors">
                        <i class="fas fa-trash text-sm ml-2"></i>
                        حذف
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

