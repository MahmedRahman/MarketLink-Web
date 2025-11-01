@extends('layouts.dashboard')

@section('title', 'خطط الاشتراك')
@section('page-title', 'خطط الاشتراك')
@section('page-description', 'اختر الخطة المناسبة لك')

@section('content')
<div class="space-y-6">
    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg flex items-center">
            <span class="material-icons ml-2">error</span>
            {{ session('error') }}
        </div>
    @endif

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg flex items-center">
            <span class="material-icons ml-2">check_circle</span>
            {{ session('success') }}
        </div>
    @endif

    @if($plans->isEmpty())
        <div class="bg-white rounded-2xl shadow-lg p-12 text-center">
            <span class="material-icons text-gray-400 text-6xl mb-4">card_membership</span>
            <h3 class="text-xl font-semibold text-gray-800 mb-2">لا توجد خطط متاحة حالياً</h3>
            <p class="text-gray-600">يرجى المحاولة مرة أخرى لاحقاً</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($plans as $plan)
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden border-2 {{ $loop->index === 1 && $plans->count() >= 3 ? 'border-purple-400 transform scale-105 relative' : 'border-gray-200' }}">
                    @if($loop->index === 1 && $plans->count() >= 3)
                        <div class="absolute top-0 left-1/2 transform -translate-x-1/2 -translate-y-1/2 bg-yellow-400 text-gray-800 px-4 py-1 rounded-full text-sm font-bold z-10">
                            الأكثر شعبية
                        </div>
                    @endif
                    
                    <div class="p-8">
                        <h3 class="text-2xl font-bold text-gray-800 mb-2">{{ $plan->name }}</h3>
                        
                        @if($plan->description)
                            <p class="text-gray-600 text-sm mb-4">{{ $plan->description }}</p>
                        @endif
                        
                        <div class="mb-6 pb-6 border-b border-gray-200">
                            <div class="flex items-baseline">
                                <span class="text-4xl font-bold text-purple-600">{{ number_format($plan->price_egp, 0) }}</span>
                                <span class="text-gray-600 mr-2"> جنيه مصري</span>
                            </div>
                            <div class="text-sm text-gray-500 mt-1">لـ {{ $plan->duration_days }} يوم</div>
                        </div>
                        
                        @if($plan->features->count() > 0)
                            <ul class="space-y-3 mb-8 min-h-[200px]">
                                @foreach($plan->features as $feature)
                                    <li class="flex items-start">
                                        <span class="material-icons text-green-500 ml-2 text-sm flex-shrink-0">check_circle</span>
                                        <span class="text-gray-700">{{ $feature->feature_name }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <div class="mb-8 min-h-[200px]">
                                <p class="text-gray-500 text-sm">لا توجد ميزات لهذه الخطة</p>
                            </div>
                        @endif
                        
                        <form action="{{ route('subscription.subscribe') }}" method="POST" class="mt-auto">
                            @csrf
                            <input type="hidden" name="plan_id" value="{{ $plan->id }}">
                            <button type="submit" class="w-full btn-primary text-white py-3 rounded-xl text-center block hover:no-underline transition-all hover:shadow-lg">
                                <span class="flex items-center justify-center">
                                    <span class="material-icons ml-2 text-sm">payment</span>
                                    اشترك الآن
                                </span>
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection

