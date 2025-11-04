@extends('layouts.employee')

@section('title', 'تفاصيل الإيصال')
@section('page-title', 'تفاصيل الإيصال')
@section('page-description', $expense->title)

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="card p-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <div class="w-12 h-12 logo-gradient rounded-xl flex items-center justify-center shadow-lg ml-3">
                    <span class="material-icons text-white text-xl">receipt</span>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">{{ $expense->title }}</h2>
                    <p class="text-gray-600">{{ $expense->project->business_name ?? 'بدون مشروع' }}</p>
                </div>
            </div>
            <a href="{{ route('employee.expenses.index') }}" class="flex items-center px-4 py-2 text-gray-600 hover:text-gray-800 hover:bg-gray-100 rounded-xl transition-colors">
                <span class="material-icons text-sm ml-2">arrow_back</span>
                العودة
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Expense Details -->
            <div class="card p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">تفاصيل الإيصال</h3>
                
                @if($expense->description)
                    <div class="mb-6">
                        <p class="text-sm text-gray-600 mb-2">الوصف</p>
                        <p class="text-gray-800 whitespace-pre-wrap">{{ $expense->description }}</p>
                    </div>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-600">المبلغ</p>
                        <p class="text-2xl font-bold text-gray-800 mt-1">{{ number_format($expense->amount, 2) }} {{ $expense->currency }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-600">الحالة</p>
                        <span class="px-3 py-1 text-sm rounded-full bg-{{ $expense->status_color }}-100 text-{{ $expense->status_color }}-800 mt-1 inline-block">
                            {{ $expense->status_badge }}
                        </span>
                    </div>

                    <div>
                        <p class="text-sm text-gray-600">تاريخ الإيصال</p>
                        <p class="text-gray-800 font-medium mt-1">{{ $expense->expense_date->format('Y-m-d') }}</p>
                    </div>

                    @if($expense->category)
                        <div>
                            <p class="text-sm text-gray-600">الفئة</p>
                            <p class="text-gray-800 font-medium mt-1">{{ $expense->category_badge }}</p>
                        </div>
                    @endif

                    @if($expense->payment_method)
                        <div>
                            <p class="text-sm text-gray-600">طريقة الدفع</p>
                            <p class="text-gray-800 font-medium mt-1">{{ $expense->payment_method_badge }}</p>
                        </div>
                    @endif

                    @if($expense->payment_reference)
                        <div>
                            <p class="text-sm text-gray-600">رقم المرجع</p>
                            <p class="text-gray-800 font-medium mt-1">{{ $expense->payment_reference }}</p>
                        </div>
                    @endif
                </div>

                @if($expense->notes)
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <p class="text-sm text-gray-600 mb-2">ملاحظات</p>
                        <p class="text-gray-800 whitespace-pre-wrap">{{ $expense->notes }}</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Project Info -->
            @if($expense->project)
                <div class="card p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">المشروع</h3>
                    <div class="space-y-3">
                        <div>
                            <p class="text-sm text-gray-600">اسم المشروع</p>
                            <p class="text-gray-800 font-medium mt-1">{{ $expense->project->business_name }}</p>
                        </div>
                        @if($expense->project->client)
                            <div>
                                <p class="text-sm text-gray-600">العميل</p>
                                <p class="text-gray-800 font-medium mt-1">{{ $expense->project->client->name }}</p>
                            </div>
                        @endif
                        <a href="{{ route('employee.projects.show', $expense->project->id) }}" 
                           class="w-full btn-primary text-white px-4 py-3 rounded-xl flex items-center justify-center text-sm">
                            <span class="material-icons text-sm ml-2">folder</span>
                            عرض المشروع
                        </a>
                    </div>
                </div>
            @endif

            <!-- Expense Info -->
            <div class="card p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">معلومات إضافية</h3>
                <div class="space-y-3 text-sm">
                    <div>
                        <span class="text-gray-600">تاريخ الإنشاء:</span>
                        <span class="text-gray-800 font-medium">{{ $expense->created_at->format('Y-m-d') }}</span>
                    </div>
                    <div>
                        <span class="text-gray-600">آخر تحديث:</span>
                        <span class="text-gray-800 font-medium">{{ $expense->updated_at->format('Y-m-d') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

