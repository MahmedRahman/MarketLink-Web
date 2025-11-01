@extends('layouts.admin')

@section('title', 'خطط الاشتراك')
@section('page-title', 'إدارة خطط الاشتراك')

@section('content')
<div class="space-y-6">
    <!-- Header Actions -->
    <div class="flex justify-between items-center">
        <h2 class="text-2xl font-bold text-gray-800">خطط الاشتراك</h2>
        <a href="{{ route('admin.plans.create') }}" class="btn-primary flex items-center">
            <span class="material-icons ml-2 text-sm">add</span>
            إضافة خطة جديدة
        </a>
    </div>

    <!-- Success Message -->
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg flex items-center">
            <span class="material-icons ml-2">check_circle</span>
            {{ session('success') }}
        </div>
    @endif

    <!-- Error Message -->
    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg flex items-center">
            <span class="material-icons ml-2">error</span>
            {{ session('error') }}
        </div>
    @endif

    <!-- Plans Table -->
    <div class="card">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الاسم</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">السعر (جنيه)</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">المدة (يوم)</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الميزات</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الحالة</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">ترتيب العرض</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">إجراءات</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($plans as $plan)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $plan->name }}</div>
                                @if($plan->description)
                                    <div class="text-xs text-gray-500 mt-1">{{ Str::limit($plan->description, 50) }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-bold text-purple-600">{{ number_format($plan->price_egp, 2) }} ج.م</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-500">{{ $plan->duration_days }} يوم</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-500">
                                    {{ $plan->features->count() }} ميزة
                                    @if($plan->features->count() > 0)
                                        <div class="mt-1 text-xs text-gray-400">
                                            {{ Str::limit($plan->features->first()->feature_name, 30) }}
                                            @if($plan->features->count() > 1)
                                                <span>+{{ $plan->features->count() - 1 }}</span>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($plan->is_active)
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">نشط</span>
                                @else
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">غير نشط</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-500">{{ $plan->sort_order }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex gap-2">
                                    <a href="{{ route('admin.plans.show', $plan) }}" class="text-blue-600 hover:text-blue-900">
                                        <span class="material-icons text-sm">visibility</span>
                                    </a>
                                    <a href="{{ route('admin.plans.edit', $plan) }}" class="text-purple-600 hover:text-purple-900">
                                        <span class="material-icons text-sm">edit</span>
                                    </a>
                                    <form action="{{ route('admin.plans.destroy', $plan) }}" method="POST" class="inline" onsubmit="return confirm('هل أنت متأكد من حذف هذه الخطة؟');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900">
                                            <span class="material-icons text-sm">delete</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                                لا توجد خطط متاحة. <a href="{{ route('admin.plans.create') }}" class="text-purple-600 hover:underline">إضافة خطة جديدة</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($plans->hasPages())
            <div class="mt-4">
                {{ $plans->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

