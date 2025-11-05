@extends('layouts.employee')

@section('title', 'تفاصيل المشروع')
@section('page-title', $project->business_name)
@section('page-description', 'تفاصيل المشروع')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="card p-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <div class="w-12 h-12 logo-gradient rounded-xl flex items-center justify-center shadow-lg ml-3">
                    <span class="material-icons text-white text-xl">folder</span>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">{{ $project->business_name }}</h2>
                </div>
            </div>
            <a href="{{ route('employee.projects.index') }}" class="flex items-center px-4 py-2 text-gray-600 hover:text-gray-800 hover:bg-gray-100 rounded-xl transition-colors">
                <span class="material-icons text-sm ml-2">arrow_back</span>
                العودة
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Project Info -->
            <div class="card p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">معلومات المشروع</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-600">اسم البيزنس</p>
                        <p class="text-gray-800 font-medium mt-1">{{ $project->business_name }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-600">الحالة</p>
                        <span class="px-3 py-1 text-sm rounded-full bg-{{ $project->status_color }}-100 text-{{ $project->status_color }}-800 mt-1 inline-block">
                            {{ $project->status_badge }}
                        </span>
                    </div>

                    <div>
                        <p class="text-sm text-gray-600">تاريخ الإنشاء</p>
                        <p class="text-gray-800 font-medium mt-1">{{ $project->created_at->format('Y-m-d') }}</p>
                    </div>
                </div>

                @if($project->business_description)
                    <div class="mt-6">
                        <p class="text-sm text-gray-600 mb-2">وصف البيزنس</p>
                        <p class="text-gray-800">{{ $project->business_description }}</p>
                    </div>
                @endif
            </div>

            <!-- Social Media Links Section -->
            @if($project->website_url || $project->facebook_url || $project->instagram_url || $project->twitter_url || $project->linkedin_url || $project->youtube_url || $project->tiktok_url)
            <div class="card p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">روابط وسائل التواصل الاجتماعي</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @if($project->website_url)
                    <div class="bg-gray-50 rounded-xl p-4">
                        <div class="flex items-center mb-2">
                            <i class="fas fa-globe text-blue-600 ml-2"></i>
                            <span class="font-medium text-gray-800">الموقع الإلكتروني</span>
                        </div>
                        <a href="{{ $project->website_url }}" target="_blank" class="text-blue-600 hover:text-blue-700 text-sm break-all">
                            {{ $project->website_url }}
                        </a>
                    </div>
                    @endif

                    @if($project->facebook_url)
                    <div class="bg-gray-50 rounded-xl p-4">
                        <div class="flex items-center mb-2">
                            <i class="fab fa-facebook text-blue-600 ml-2"></i>
                            <span class="font-medium text-gray-800">فيسبوك</span>
                        </div>
                        <a href="{{ $project->facebook_url }}" target="_blank" class="text-blue-600 hover:text-blue-700 text-sm break-all">
                            {{ $project->facebook_url }}
                        </a>
                    </div>
                    @endif

                    @if($project->instagram_url)
                    <div class="bg-gray-50 rounded-xl p-4">
                        <div class="flex items-center mb-2">
                            <i class="fab fa-instagram text-pink-600 ml-2"></i>
                            <span class="font-medium text-gray-800">انستغرام</span>
                        </div>
                        <a href="{{ $project->instagram_url }}" target="_blank" class="text-blue-600 hover:text-blue-700 text-sm break-all">
                            {{ $project->instagram_url }}
                        </a>
                    </div>
                    @endif

                    @if($project->twitter_url)
                    <div class="bg-gray-50 rounded-xl p-4">
                        <div class="flex items-center mb-2">
                            <i class="fab fa-twitter text-blue-400 ml-2"></i>
                            <span class="font-medium text-gray-800">تويتر</span>
                        </div>
                        <a href="{{ $project->twitter_url }}" target="_blank" class="text-blue-600 hover:text-blue-700 text-sm break-all">
                            {{ $project->twitter_url }}
                        </a>
                    </div>
                    @endif

                    @if($project->linkedin_url)
                    <div class="bg-gray-50 rounded-xl p-4">
                        <div class="flex items-center mb-2">
                            <i class="fab fa-linkedin text-blue-700 ml-2"></i>
                            <span class="font-medium text-gray-800">لينكدإن</span>
                        </div>
                        <a href="{{ $project->linkedin_url }}" target="_blank" class="text-blue-600 hover:text-blue-700 text-sm break-all">
                            {{ $project->linkedin_url }}
                        </a>
                    </div>
                    @endif

                    @if($project->youtube_url)
                    <div class="bg-gray-50 rounded-xl p-4">
                        <div class="flex items-center mb-2">
                            <i class="fab fa-youtube text-red-600 ml-2"></i>
                            <span class="font-medium text-gray-800">يوتيوب</span>
                        </div>
                        <a href="{{ $project->youtube_url }}" target="_blank" class="text-blue-600 hover:text-blue-700 text-sm break-all">
                            {{ $project->youtube_url }}
                        </a>
                    </div>
                    @endif

                    @if($project->tiktok_url)
                    <div class="bg-gray-50 rounded-xl p-4">
                        <div class="flex items-center mb-2">
                            <i class="fab fa-tiktok text-gray-800 ml-2"></i>
                            <span class="font-medium text-gray-800">تيك توك</span>
                        </div>
                        <a href="{{ $project->tiktok_url }}" target="_blank" class="text-blue-600 hover:text-blue-700 text-sm break-all">
                            {{ $project->tiktok_url }}
                        </a>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- My Expenses -->
            <div class="card p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">إيصالاتي في هذا المشروع</h3>
                
                @if($employeeExpenses->count() > 0)
                    <div class="space-y-3">
                        @foreach($employeeExpenses as $expense)
                            <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors">
                                <div class="flex items-center justify-between mb-2">
                                    <div class="flex-1">
                                        <h4 class="font-medium text-gray-800">{{ $expense->title }}</h4>
                                        @if($expense->description)
                                            <p class="text-sm text-gray-600 mt-1">{{ Str::limit($expense->description, 80) }}</p>
                                        @endif
                                    </div>
                                    <span class="px-3 py-1 text-xs rounded-full bg-{{ $expense->status_color }}-100 text-{{ $expense->status_color }}-800">
                                        {{ $expense->status_badge }}
                                    </span>
                                </div>
                                <div class="flex items-center justify-between text-sm">
                                    <div class="flex items-center space-x-4 space-x-reverse text-gray-600">
                                        <span class="font-medium text-gray-800">{{ number_format($expense->amount, 2) }} {{ $expense->currency }}</span>
                                        <span>{{ $expense->expense_date->format('Y-m-d') }}</span>
                                        @if($expense->category)
                                            <span>{{ $expense->category_badge }}</span>
                                        @endif
                                    </div>
                                    <a href="{{ route('employee.expenses.show', $expense->id) }}" 
                                       class="text-blue-500 hover:text-blue-700 text-xs">
                                        عرض التفاصيل
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 text-center py-8">لا توجد إيصالات مرتبطة بك في هذا المشروع</p>
                @endif
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Quick Actions -->
            <div class="card p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">إجراءات سريعة</h3>
                <div class="space-y-3">
                    <a href="{{ route('employee.expenses.index') }}" 
                       class="w-full btn-primary text-white px-4 py-3 rounded-xl flex items-center justify-center">
                        <span class="material-icons text-sm ml-2">receipt</span>
                        جميع الإيصالات
                    </a>
                </div>
            </div>

            <!-- Project Team -->
            @if($project->employees && $project->employees->count() > 0)
                <div class="card p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">فريق العمل</h3>
                    <div class="space-y-2">
                        @foreach($project->employees as $emp)
                            <div class="flex items-center justify-between p-2 bg-gray-50 rounded-lg">
                                <span class="text-sm text-gray-800">{{ $emp->name }}</span>
                                @if($emp->pivot->role === 'manager')
                                    <span class="px-2 py-1 text-xs rounded-full bg-orange-100 text-orange-700">
                                        مدير
                                    </span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

