@extends('layouts.employee')

@section('title', 'الإيصالات')
@section('page-title', 'الإيصالات')
@section('page-description', 'إيصالات الدفع المرتبطة بك')

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
                    <h2 class="text-2xl font-bold text-gray-800">الإيصالات</h2>
                    <p class="text-gray-600">إيصالات الدفع المرتبطة بك</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="card p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">إجمالي الإيصالات</p>
                    <p class="text-2xl font-bold text-gray-800 mt-1">{{ $stats['count'] }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <span class="material-icons text-blue-600">receipt</span>
                </div>
            </div>
        </div>

        <div class="card p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">إجمالي المبلغ</p>
                    <p class="text-2xl font-bold text-gray-800 mt-1">{{ number_format($stats['total'], 2) }}</p>
                    <p class="text-xs text-gray-500">جنيه</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <span class="material-icons text-purple-600">attach_money</span>
                </div>
            </div>
        </div>

        <div class="card p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">تم الدفع</p>
                    <p class="text-2xl font-bold text-green-600 mt-1">{{ number_format($stats['paid'], 2) }}</p>
                    <p class="text-xs text-gray-500">جنيه</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <span class="material-icons text-green-600">check_circle</span>
                </div>
            </div>
        </div>

        <div class="card p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">في الانتظار</p>
                    <p class="text-2xl font-bold text-yellow-600 mt-1">{{ number_format($stats['pending'], 2) }}</p>
                    <p class="text-xs text-gray-500">جنيه</p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <span class="material-icons text-yellow-600">schedule</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Expenses List -->
    @if($expenses->count() > 0)
        <div class="card p-6">
            <div class="space-y-4">
                @foreach($expenses as $expense)
                    <div class="border border-gray-200 rounded-xl p-4 hover:bg-gray-50 transition-colors">
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex-1">
                                <h3 class="font-semibold text-gray-800 mb-1">{{ $expense->title }}</h3>
                                @if($expense->project)
                                    <p class="text-sm text-gray-600">
                                        <span class="material-icons text-xs align-middle ml-1">folder</span>
                                        {{ $expense->project->business_name }}
                                    </p>
                                @endif
                                @if($expense->description)
                                    <p class="text-sm text-gray-600 mt-2">{{ Str::limit($expense->description, 100) }}</p>
                                @endif
                            </div>
                            <span class="px-3 py-1 text-xs rounded-full bg-{{ $expense->status_color }}-100 text-{{ $expense->status_color }}-800">
                                {{ $expense->status_badge }}
                            </span>
                        </div>
                        
                        <div class="flex items-center justify-between pt-3 border-t border-gray-100">
                            <div class="flex items-center space-x-4 space-x-reverse text-sm text-gray-600">
                                <span class="font-bold text-gray-800 text-lg">{{ number_format($expense->amount, 2) }} {{ $expense->currency }}</span>
                                <span class="flex items-center">
                                    <span class="material-icons text-xs ml-1">calendar_today</span>
                                    {{ $expense->expense_date->format('Y-m-d') }}
                                </span>
                                @if($expense->category)
                                    <span>{{ $expense->category_badge }}</span>
                                @endif
                                @if($expense->payment_method)
                                    <span>{{ $expense->payment_method_badge }}</span>
                                @endif
                            </div>
                            <a href="{{ route('employee.expenses.show', $expense->id) }}" 
                               class="text-blue-500 hover:text-blue-700 text-sm font-medium flex items-center">
                                عرض التفاصيل
                                <span class="material-icons text-sm mr-1">arrow_forward</span>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-6">
                {{ $expenses->links() }}
            </div>
        </div>
    @else
        <div class="card p-12 text-center">
            <span class="material-icons text-gray-400 text-6xl mb-4">receipt_long</span>
            <p class="text-gray-600 text-lg">لا توجد إيصالات مرتبطة بك</p>
        </div>
    @endif
</div>
@endsection

