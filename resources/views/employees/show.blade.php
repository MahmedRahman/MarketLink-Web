@extends('layouts.dashboard')

@section('title', 'عرض بيانات الموظف')
@section('page-title', 'عرض بيانات الموظف')
@section('page-description', 'عرض تفاصيل الموظف: ' . $employee->name)

@section('content')
<div class="container mx-auto px-4">
    <div class="max-w-6xl mx-auto space-y-6">
        <!-- Header -->
        <div class="card page-header rounded-2xl p-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="w-12 h-12 logo-gradient rounded-2xl flex items-center justify-center shadow-lg icon-spacing ml-3">
                        <i class="fas fa-user text-white text-xl"></i>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800">عرض بيانات الموظف</h2>
                        <p class="text-gray-600">تفاصيل الموظف: {{ $employee->name }}</p>
                    </div>
                </div>
                <div class="flex items-center space-x-3 rtl:space-x-reverse">
                    <a href="{{ route('employees.edit', $employee) }}" class="flex items-center px-4 py-2 text-blue-600 hover:text-blue-700 hover:bg-blue-50 rounded-xl transition-colors icon-spacing">
                        <i class="fas fa-edit text-sm ml-2"></i>
                        تعديل
                    </a>
                    <a href="{{ route('employees.index') }}" class="flex items-center px-4 py-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-xl transition-colors icon-spacing">
                        العودة للقائمة
                    </a>
                </div>
            </div>
        </div>

        <!-- Employee Information Cards -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Basic Information Card -->
            <div class="lg:col-span-2">
                <div class="card rounded-2xl p-6">
                    <div class="flex items-center mb-6">
                        <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center icon-spacing ml-3">
                            <i class="fas fa-user text-blue-600"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-800">المعلومات الأساسية</h3>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-2">اسم الموظف</label>
                            <p class="text-lg font-semibold text-gray-900">{{ $employee->name }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-2">البريد الإلكتروني</label>
                            <p class="text-lg font-semibold text-gray-900">{{ $employee->email }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-2">رقم الهاتف</label>
                            <p class="text-lg font-semibold text-gray-900">{{ $employee->phone ?? 'غير محدد' }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-2">الدور الوظيفي</label>
                            <span class="role-badge role-{{ $employee->role_color }}">
                                {{ $employee->role_badge }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Status and Actions Card -->
            <div class="lg:col-span-1">
                <div class="card rounded-2xl p-6">
                    <div class="flex items-center mb-6">
                        <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center icon-spacing ml-3">
                            <i class="fas fa-info-circle text-green-600"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-800">الحالة والإجراءات</h3>
                    </div>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-2">الحالة</label>
                            <span class="status-badge status-{{ $employee->status_color }}">
                                {{ $employee->status_badge }}
                            </span>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-2">تاريخ الإنشاء</label>
                            <p class="text-sm text-gray-900">{{ $employee->created_at->format('Y-m-d H:i') }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-2">آخر تحديث</label>
                            <p class="text-sm text-gray-900">{{ $employee->updated_at->format('Y-m-d H:i') }}</p>
                        </div>
                        
                        <div class="pt-4 border-t border-gray-200">
                            <div class="flex flex-col space-y-2">
                                <a href="{{ route('employees.edit', $employee) }}" class="w-full btn-primary text-white px-4 py-2 rounded-xl flex items-center justify-center hover:no-underline">
                                    <i class="fas fa-edit text-sm ml-2"></i>
                                    تعديل الموظف
                                </a>
                                <button onclick="confirmDelete('{{ route('employees.destroy', $employee) }}', 'تأكيد حذف الموظف', 'هل أنت متأكد من حذف الموظف {{ $employee->name }}؟')" class="w-full bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-xl flex items-center justify-center transition-colors">
                                    <i class="fas fa-trash text-sm ml-2"></i>
                                    حذف الموظف
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Role Details Section -->
        <div class="card rounded-2xl p-6">
            <div class="flex items-center mb-6">
                <div class="w-10 h-10 bg-purple-100 rounded-xl flex items-center justify-center icon-spacing ml-3">
                    <i class="fas fa-briefcase text-purple-600"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-800">تفاصيل الدور الوظيفي</h3>
            </div>
            
            <div class="bg-gray-50 rounded-xl p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-2">نوع الدور</label>
                        <p class="text-lg font-semibold text-gray-900">{{ $employee->role_badge }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-2">وصف الدور</label>
                        <p class="text-sm text-gray-700">
                            @switch($employee->role)
                                @case('content_writer')
                                    مسؤول عن إنشاء المحتوى النصي والكتابة الإبداعية
                                    @break
                                @case('ad_manager')
                                    مسؤول عن إدارة الحملات الإعلانية والتسويقية
                                    @break
                                @case('designer')
                                    مسؤول عن التصميم الجرافيكي والهوية البصرية
                                    @break
                                @case('video_editor')
                                    مسؤول عن تحرير وإنتاج المحتوى المرئي
                                    @break
                                @case('page_manager')
                                    مسؤول عن إدارة الصفحات والحسابات الرقمية
                                    @break
                                @case('account_manager')
                                    مسؤول عن إدارة الحسابات والعلاقات مع العملاء
                                    @break
                                @case('monitor')
                                    مسؤول عن مراقبة وتحليل أداء الحملات الإعلانية والمحتوى الرقمي
                                    @break
                                @case('media_buyer')
                                    مسؤول عن شراء المساحات الإعلانية وإدارة الميزانيات التسويقية
                                    @break
                                @default
                                    دور وظيفي غير محدد
                            @endswitch
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contact Information Section -->
        <div class="card rounded-2xl p-6">
            <div class="flex items-center mb-6">
                <div class="w-10 h-10 bg-orange-100 rounded-xl flex items-center justify-center icon-spacing ml-3">
                    <i class="fas fa-phone text-orange-600"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-800">معلومات الاتصال</h3>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-2">البريد الإلكتروني</label>
                    <div class="flex items-center">
                        <i class="fas fa-envelope text-gray-400 ml-2"></i>
                        <a href="mailto:{{ $employee->email }}" class="text-blue-600 hover:text-blue-700">
                            {{ $employee->email }}
                        </a>
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-2">رقم الهاتف</label>
                    <div class="flex items-center">
                        <i class="fas fa-phone text-gray-400 ml-2"></i>
                        @if($employee->phone)
                            <a href="tel:{{ $employee->phone }}" class="text-blue-600 hover:text-blue-700">
                                {{ $employee->phone }}
                            </a>
                        @else
                            <span class="text-gray-500">غير محدد</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Social Links Section -->
        @if($employee->facebook_url || $employee->linkedin_url || $employee->portfolio_url)
        <div class="card rounded-2xl p-6">
            <div class="flex items-center mb-6">
                <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center icon-spacing ml-3">
                    <i class="fas fa-share-alt text-blue-600"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-800">روابط التواصل الاجتماعي</h3>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @if($employee->facebook_url)
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-2">رابط الفيس بوك</label>
                    <div class="flex items-center">
                        <i class="fab fa-facebook text-blue-600 text-xl ml-2"></i>
                        <a href="{{ $employee->facebook_url }}" target="_blank" class="text-blue-600 hover:text-blue-700 break-all">
                            {{ $employee->facebook_url }}
                        </a>
                    </div>
                </div>
                @endif
                
                @if($employee->linkedin_url)
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-2">رابط LinkedIn</label>
                    <div class="flex items-center">
                        <i class="fab fa-linkedin text-blue-700 text-xl ml-2"></i>
                        <a href="{{ $employee->linkedin_url }}" target="_blank" class="text-blue-600 hover:text-blue-700 break-all">
                            {{ $employee->linkedin_url }}
                        </a>
                    </div>
                </div>
                @endif
                
                @if($employee->portfolio_url)
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-2">رابط البورتفوليو</label>
                    <div class="flex items-center">
                        <i class="fas fa-briefcase text-purple-600 text-xl ml-2"></i>
                        <a href="{{ $employee->portfolio_url }}" target="_blank" class="text-blue-600 hover:text-blue-700 break-all">
                            {{ $employee->portfolio_url }}
                        </a>
                    </div>
                </div>
                @endif
            </div>
        </div>
        @endif

        <!-- Notes Section -->
        @if($employee->notes)
        <div class="card rounded-2xl p-6">
            <div class="flex items-center mb-6">
                <div class="w-10 h-10 bg-yellow-100 rounded-xl flex items-center justify-center icon-spacing ml-3">
                    <i class="fas fa-sticky-note text-yellow-600"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-800">الملاحظات</h3>
            </div>
            
            <div class="bg-gray-50 rounded-xl p-4">
                <p class="text-gray-700 whitespace-pre-line">{{ $employee->notes }}</p>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
