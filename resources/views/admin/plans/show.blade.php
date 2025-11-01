@extends('layouts.admin')

@section('title', 'تفاصيل الخطة')
@section('page-title', 'تفاصيل خطة الاشتراك')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Plan Info -->
    <div class="card">
        <div class="flex justify-between items-start mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">{{ $plan->name }}</h2>
                @if($plan->description)
                    <p class="text-gray-600 mt-2">{{ $plan->description }}</p>
                @endif
            </div>
            <div class="flex gap-2">
                <a href="{{ route('admin.plans.edit', $plan) }}" class="btn-primary flex items-center">
                    <span class="material-icons ml-2 text-sm">edit</span>
                    تعديل
                </a>
                <form action="{{ route('admin.plans.destroy', $plan) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذه الخطة؟');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 flex items-center">
                        <span class="material-icons ml-2 text-sm">delete</span>
                        حذف
                    </button>
                </form>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">السعر</label>
                <p class="text-2xl font-bold text-purple-600">{{ number_format($plan->price_egp, 2) }} جنيه مصري</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">مدة الاشتراك</label>
                <p class="text-xl font-semibold text-gray-800">{{ $plan->duration_days }} يوم</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">الحالة</label>
                @if($plan->is_active)
                    <span class="px-3 py-1 text-sm font-semibold rounded-full bg-green-100 text-green-800">نشط</span>
                @else
                    <span class="px-3 py-1 text-sm font-semibold rounded-full bg-gray-100 text-gray-800">غير نشط</span>
                @endif
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">ترتيب العرض</label>
                <p class="text-lg font-semibold text-gray-800">{{ $plan->sort_order }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">عدد الميزات</label>
                <p class="text-lg font-semibold text-gray-800">{{ $plan->features->count() }} ميزة</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">عدد الاشتراكات</label>
                <p class="text-lg font-semibold text-gray-800">{{ $plan->subscriptions->count() }} اشتراك</p>
            </div>
        </div>
    </div>

    <!-- Features -->
    <div class="card">
        <h3 class="text-xl font-bold text-gray-800 mb-4">الميزات</h3>
        @if($plan->features->count() > 0)
            <ul class="space-y-2">
                @foreach($plan->features as $feature)
                    <li class="flex items-center text-gray-700">
                        <span class="material-icons text-green-500 ml-2">check_circle</span>
                        {{ $feature->feature_name }}
                    </li>
                @endforeach
            </ul>
        @else
            <p class="text-gray-500">لا توجد ميزات لهذه الخطة</p>
        @endif
    </div>

    <!-- Back Button -->
    <div>
        <a href="{{ route('admin.plans.index') }}" class="text-purple-600 hover:underline flex items-center">
            <span class="material-icons ml-2">arrow_back</span>
            العودة لقائمة الخطط
        </a>
    </div>
</div>
@endsection

