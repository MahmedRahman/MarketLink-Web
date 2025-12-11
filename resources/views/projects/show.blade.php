@extends('layouts.dashboard')

@section('title', 'عرض بيانات المشروع')
@section('page-title', 'عرض بيانات المشروع')
@section('page-description', 'عرض تفاصيل المشروع: ' . $project->business_name)

@section('content')
<div class="container mx-auto px-4">
    <div class="max-w-6xl mx-auto space-y-6">
        <!-- Success/Error Messages -->
        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg flex items-center">
                <span class="material-icons ml-2">check_circle</span>
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg flex items-center">
                <span class="material-icons ml-2">error</span>
                {{ session('error') }}
            </div>
        @endif

        <!-- Header -->
        <div class="card page-header rounded-2xl p-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="w-12 h-12 logo-gradient rounded-2xl flex items-center justify-center shadow-lg icon-spacing ml-3">
                        <i class="fas fa-project-diagram text-white text-xl"></i>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800">عرض بيانات المشروع</h2>
                        <p class="text-gray-600">تفاصيل المشروع: {{ $project->business_name }}</p>
                    </div>
                </div>
                <div class="flex items-center space-x-3 rtl:space-x-reverse">
                    <a href="{{ route('projects.analyze', $project) }}" class="flex items-center px-4 py-2 text-white bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 rounded-xl transition-colors icon-spacing shadow-lg">
                        <i class="fas fa-magic text-sm ml-2"></i>
                        تحليل المحتوى النصي
                    </a>
                    <a href="{{ route('projects.edit', $project) }}" class="flex items-center px-4 py-2 text-blue-600 hover:text-blue-700 hover:bg-blue-50 rounded-xl transition-colors icon-spacing">
                        <i class="fas fa-edit text-sm ml-2"></i>
                        تعديل
                    </a>
                    <a href="{{ route('projects.index') }}" class="flex items-center px-4 py-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-xl transition-colors icon-spacing">
                        العودة للقائمة
                    </a>
                </div>
            </div>
        </div>

        <!-- Project Information Cards -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Basic Information Card -->
            <div class="lg:col-span-2">
                <div class="card rounded-2xl p-6">
                    <div class="flex items-center mb-6">
                        <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center icon-spacing ml-3">
                            <i class="fas fa-building text-blue-600"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-800">المعلومات الأساسية</h3>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-2">اسم البيزنس</label>
                            <p class="text-lg font-semibold text-gray-900">{{ $project->business_name }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-2">العميل</label>
                            <p class="text-lg font-semibold text-gray-900">{{ $project->client->name ?? 'غير محدد' }}</p>
                        </div>
                        
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-500 mb-2">وصف البيزنس</label>
                            <p class="text-sm text-gray-700 leading-relaxed">{{ $project->business_description ?? 'لا يوجد وصف' }}</p>
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
                            <span class="status-badge status-{{ $project->status_color }}">
                                {{ $project->status_badge }}
                            </span>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-2">تاريخ الإنشاء</label>
                            <p class="text-sm text-gray-900">{{ $project->created_at->format('Y-m-d H:i') }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-2">آخر تحديث</label>
                            <p class="text-sm text-gray-900">{{ $project->updated_at->format('Y-m-d H:i') }}</p>
                        </div>
                        
                        <div class="pt-4 border-t border-gray-200">
                            <div class="flex flex-col space-y-2">
                                <a href="{{ route('projects.edit', $project) }}" class="w-full btn-primary text-white px-4 py-2 rounded-xl flex items-center justify-center hover:no-underline">
                                    <i class="fas fa-edit text-sm ml-2"></i>
                                    تعديل المشروع
                                </a>
                                <a href="{{ route('projects.revenues.index', $project) }}" class="w-full bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-xl flex items-center justify-center transition-colors">
                                    <i class="fas fa-money-bill-wave text-sm ml-2"></i>
                                    إدارة الإيرادات
                                </a>
                                <a href="{{ route('projects.expenses.index', $project) }}" class="w-full bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded-xl flex items-center justify-center transition-colors">
                                    <i class="fas fa-receipt text-sm ml-2"></i>
                                    إدارة المصروفات
                                </a>
                                <a href="{{ route('projects.financial-report', $project) }}" class="w-full bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-xl flex items-center justify-center transition-colors">
                                    <i class="fas fa-chart-line text-sm ml-2"></i>
                                    التقرير المالي الشهري
                                </a>
                                <button onclick="confirmDelete('{{ route('projects.destroy', $project) }}', 'تأكيد حذف المشروع', 'هل أنت متأكد من حذف المشروع {{ $project->business_name }}؟')" class="w-full bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-xl flex items-center justify-center transition-colors">
                                    <i class="fas fa-trash text-sm ml-2"></i>
                                    حذف المشروع
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Social Media Links Section -->
        @if($project->website_url || $project->facebook_url || $project->instagram_url || $project->twitter_url || $project->linkedin_url || $project->youtube_url || $project->tiktok_url)
        <div class="card rounded-2xl p-6">
            <div class="flex items-center mb-6">
                <div class="w-10 h-10 bg-purple-100 rounded-xl flex items-center justify-center icon-spacing ml-3">
                    <i class="fas fa-share-alt text-purple-600"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-800">روابط وسائل التواصل الاجتماعي</h3>
            </div>
            
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


        <!-- Employees Section -->
        <div class="card rounded-2xl p-6">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center icon-spacing ml-3">
                        <i class="fas fa-users-cog text-green-600"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800">الموظفين الخاصين بالمشروع</h3>
                        <p class="text-sm text-gray-600">الموظفين الذين يعملون على هذا المشروع</p>
                    </div>
                </div>
                <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-medium">
                    {{ $project->employees->count() }} موظف
                </span>
            </div>
            
            @if($project->employees->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($project->employees as $employee)
                    <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-xl p-4 border border-green-100">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center ml-3">
                                    <i class="fas fa-user text-white text-sm"></i>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-800">{{ $employee->name }}</h4>
                                    <span class="text-xs text-gray-500">{{ $employee->role_badge }}</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="space-y-2">
                            @if($employee->email)
                            <div class="bg-white rounded-lg p-2">
                                <div class="flex items-center">
                                    <i class="fas fa-envelope text-green-500 ml-2 text-xs"></i>
                                    <a href="mailto:{{ $employee->email }}" class="text-green-600 hover:text-green-700 text-xs break-all">
                                        {{ $employee->email }}
                                    </a>
                                </div>
                            </div>
                            @endif
                            
                            @if($employee->phone)
                            <div class="bg-white rounded-lg p-2">
                                <div class="flex items-center">
                                    <i class="fas fa-phone text-green-500 ml-2 text-xs"></i>
                                    <a href="tel:{{ $employee->phone }}" class="text-green-600 hover:text-green-700 text-xs font-medium">
                                        {{ $employee->phone }}
                                    </a>
                                </div>
                            </div>
                            @endif
                            
                            <div class="flex items-center justify-between pt-2 border-t border-green-100">
                                <span class="text-xs text-gray-500">
                                    <span class="px-2 py-1 rounded-full bg-{{ $employee->role_color }}-100 text-{{ $employee->role_color }}-800">
                                        {{ $employee->role_badge }}
                                    </span>
                                </span>
                                @if($employee->status === 'active')
                                    <span class="text-xs text-green-600 font-medium">
                                        <i class="fas fa-circle text-xs ml-1"></i>
                                        نشط
                                    </span>
                                @else
                                    <span class="text-xs text-gray-500">
                                        <i class="fas fa-circle text-xs ml-1"></i>
                                        غير نشط
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-users text-gray-400 text-xl"></i>
                    </div>
                    <h4 class="text-lg font-medium text-gray-900 mb-2">لا يوجد موظفين</h4>
                    <p class="text-gray-500 mb-4">لم يتم تعيين أي موظفين لهذا المشروع بعد</p>
                    <a href="{{ route('projects.edit', $project) }}" class="inline-flex items-center px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors">
                        <i class="fas fa-plus text-sm ml-2"></i>
                        إضافة موظفين
                    </a>
                </div>
            @endif
        </div>

        <!-- Authorized Persons Section -->
        <div class="card rounded-2xl p-6">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-indigo-100 rounded-xl flex items-center justify-center icon-spacing ml-3">
                        <i class="fas fa-users text-indigo-600"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800">الأشخاص الموثقين</h3>
                        <p class="text-sm text-gray-600">الأشخاص المخولين للعمل على هذا المشروع</p>
                    </div>
                </div>
                <span class="bg-indigo-100 text-indigo-800 px-3 py-1 rounded-full text-sm font-medium">
                    {{ $project->authorized_persons_count ?? 0 }} شخص
                </span>
            </div>
            
            @if($project->authorized_persons && count($project->authorized_persons) > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($project->authorized_persons as $index => $person)
                    <div class="bg-gradient-to-br from-indigo-50 to-blue-50 rounded-xl p-4 border border-indigo-100">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-indigo-500 rounded-full flex items-center justify-center ml-3">
                                    <i class="fas fa-user text-white text-sm"></i>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-800">{{ $person['name'] ?? 'غير محدد' }}</h4>
                                    <p class="text-xs text-gray-500">شخص موثق</p>
                                </div>
                            </div>
                            <span class="text-xs text-gray-400">#{{ $index + 1 }}</span>
                        </div>
                        
                        @if($person['phone'] ?? null)
                        <div class="flex items-center justify-between bg-white rounded-lg p-2">
                            <div class="flex items-center">
                                <i class="fas fa-phone text-indigo-500 ml-2"></i>
                                <span class="text-sm text-gray-600">رقم الهاتف</span>
                            </div>
                            <a href="tel:{{ $person['phone'] }}" class="text-indigo-600 hover:text-indigo-700 text-sm font-medium">
                                {{ $person['phone'] }}
                            </a>
                        </div>
                        @endif
                        
                        @if($person['added_at'] ?? null)
                        <div class="mt-2 text-xs text-gray-400">
                            <i class="fas fa-clock ml-1"></i>
                            تم الإضافة: {{ \Carbon\Carbon::parse($person['added_at'])->format('Y-m-d') }}
                        </div>
                        @endif
                    </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-users text-gray-400 text-xl"></i>
                    </div>
                    <h4 class="text-lg font-medium text-gray-900 mb-2">لا يوجد أشخاص موثقين</h4>
                    <p class="text-gray-500 mb-4">لم يتم إضافة أي أشخاص موثقين لهذا المشروع بعد</p>
                    <a href="{{ route('projects.edit', $project) }}" class="inline-flex items-center px-4 py-2 bg-indigo-500 text-white rounded-lg hover:bg-indigo-600 transition-colors">
                        <i class="fas fa-plus text-sm ml-2"></i>
                        إضافة أشخاص موثقين
                    </a>
                </div>
            @endif
        </div>

        <!-- Project Accounts Section -->
        <div class="card rounded-2xl p-6">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-teal-100 rounded-xl flex items-center justify-center icon-spacing ml-3">
                        <i class="fas fa-key text-teal-600"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800">الحسابات الخاصة بالمشروع</h3>
                        <p class="text-sm text-gray-600">حسابات المنصات والخدمات المرتبطة بهذا المشروع</p>
                    </div>
                </div>
                <span class="bg-teal-100 text-teal-800 px-3 py-1 rounded-full text-sm font-medium">
                    {{ $project->project_accounts_count ?? 0 }} حساب
                </span>
            </div>
            
            @if($project->project_accounts && count($project->project_accounts) > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($project->project_accounts as $index => $account)
                    <div class="bg-gradient-to-br from-teal-50 to-green-50 rounded-xl p-4 border border-teal-100">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-teal-500 rounded-full flex items-center justify-center ml-3">
                                    <i class="fas fa-user-circle text-white text-sm"></i>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-800">{{ $account['username'] ?? 'غير محدد' }}</h4>
                                    <p class="text-xs text-gray-500">
                                        @if($account['account_type'] ?? null)
                                            {{ $account['account_type'] }}
                                        @else
                                            حساب المشروع
                                        @endif
                                    </p>
                                </div>
                            </div>
                            <span class="text-xs text-gray-400">#{{ $index + 1 }}</span>
                        </div>
                        
                        <div class="space-y-3">
                            <!-- Password Section -->
                            <div class="bg-white rounded-lg p-3 border border-teal-200">
                                <div class="flex items-center justify-between mb-2">
                                    <label class="text-xs font-medium text-gray-500">كلمة المرور</label>
                                    <button onclick="togglePassword('password-{{ $loop->index }}')" class="text-teal-500 hover:text-teal-600 text-xs">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                <div class="flex items-center">
                                    <input 
                                        type="password" 
                                        id="password-{{ $loop->index }}" 
                                        value="{{ $account['password'] ?? '' }}" 
                                        readonly 
                                        class="flex-1 px-3 py-2 text-sm bg-gray-50 border border-gray-200 rounded-lg font-mono"
                                    >
                                    <button 
                                        onclick="copyPassword('password-{{ $loop->index }}')" 
                                        class="ml-2 px-3 py-2 bg-teal-500 text-white text-xs rounded-lg hover:bg-teal-600 transition-colors"
                                        title="نسخ كلمة المرور"
                                    >
                                        <i class="fas fa-copy"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <!-- URL Section -->
                            @if($account['url'] ?? null)
                            <div class="bg-white rounded-lg p-3 border border-teal-200">
                                <label class="block text-xs font-medium text-gray-500 mb-2">الرابط</label>
                                <div class="flex items-center">
                                    <a 
                                        href="{{ $account['url'] }}" 
                                        target="_blank" 
                                        class="flex-1 text-teal-600 hover:text-teal-700 text-sm break-all font-medium"
                                    >
                                        {{ $account['url'] }}
                                    </a>
                                    <a 
                                        href="{{ $account['url'] }}" 
                                        target="_blank" 
                                        class="ml-2 px-2 py-1 bg-teal-100 text-teal-600 text-xs rounded hover:bg-teal-200 transition-colors"
                                        title="فتح الرابط"
                                    >
                                        <i class="fas fa-external-link-alt"></i>
                                    </a>
                                </div>
                            </div>
                            @endif

                            <!-- Notes Section -->
                            @if($account['notes'] ?? null)
                            <div class="bg-white rounded-lg p-3 border border-teal-200">
                                <label class="block text-xs font-medium text-gray-500 mb-2">ملاحظات</label>
                                <p class="text-sm text-gray-700">{{ $account['notes'] }}</p>
                            </div>
                            @endif
                            
                            <!-- Added Date -->
                            @if($account['added_at'] ?? null)
                            <div class="text-xs text-gray-400">
                                <i class="fas fa-clock ml-1"></i>
                                تم الإضافة: {{ \Carbon\Carbon::parse($account['added_at'])->format('Y-m-d') }}
                            </div>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-key text-gray-400 text-xl"></i>
                    </div>
                    <h4 class="text-lg font-medium text-gray-900 mb-2">لا يوجد حسابات</h4>
                    <p class="text-gray-500 mb-4">لم يتم إضافة أي حسابات لهذا المشروع بعد</p>
                    <a href="{{ route('projects.edit', $project) }}" class="inline-flex items-center px-4 py-2 bg-teal-500 text-white rounded-lg hover:bg-teal-600 transition-colors">
                        <i class="fas fa-plus text-sm ml-2"></i>
                        إضافة حسابات
                    </a>
                </div>
            @endif
        </div>

        <!-- Project Files Section -->
        <div class="card rounded-2xl p-6">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-purple-100 rounded-xl flex items-center justify-center icon-spacing ml-3">
                        <span class="material-icons text-purple-600">attach_file</span>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800">الملفات المرجعية</h3>
                        <p class="text-sm text-gray-600">الملفات والوثائق المرجعية للمشروع</p>
                    </div>
                </div>
                <span class="bg-purple-100 text-purple-800 px-3 py-1 rounded-full text-sm font-medium">
                    {{ $project->files->count() }} ملف
                </span>
            </div>

            <!-- Upload File Form -->
            <div class="bg-gray-50 rounded-xl p-4 mb-6 border border-gray-200">
                <form action="{{ route('projects.files.upload', $project) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="md:col-span-2">
                            <label for="file" class="block text-sm font-medium text-gray-700 mb-2">
                                اختيار ملف
                            </label>
                            <input
                                type="file"
                                id="file"
                                name="file"
                                required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                                accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.jpg,.jpeg,.png,.gif,.zip,.rar,.txt"
                            />
                            <p class="mt-1 text-xs text-gray-500">الحجم الأقصى: 10MB</p>
                        </div>
                        <div>
                            <label for="file_description" class="block text-sm font-medium text-gray-700 mb-2">
                                وصف الملف (اختياري)
                            </label>
                            <input
                                type="text"
                                id="file_description"
                                name="description"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                                placeholder="وصف الملف..."
                            />
                        </div>
                    </div>
                    <button type="submit" class="btn-primary text-white px-6 py-2 rounded-lg inline-flex items-center">
                        <span class="material-icons text-sm ml-2">upload</span>
                        رفع الملف
                    </button>
                </form>
            </div>

            <!-- Files List -->
            @if($project->files->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($project->files as $file)
                        <div class="bg-white border border-gray-200 rounded-xl p-4 hover:shadow-lg transition-shadow">
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex items-center flex-1">
                                    <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center ml-3">
                                        <span class="material-icons text-purple-600 text-lg">{{ $file->file_icon }}</span>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h4 class="font-semibold text-gray-800 text-sm truncate" title="{{ $file->file_name }}">
                                            {{ $file->file_name }}
                                        </h4>
                                        <p class="text-xs text-gray-500 mt-1">
                                            {{ $file->formatted_file_size }}
                                        </p>
                                    </div>
                                </div>
                                <form action="{{ route('projects.files.delete', [$project, $file]) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذا الملف؟');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-700 p-1">
                                        <span class="material-icons text-sm">delete</span>
                                    </button>
                                </form>
                            </div>

                            @if($file->description)
                                <p class="text-xs text-gray-600 mb-3 line-clamp-2">{{ $file->description }}</p>
                            @endif

                            <div class="flex items-center justify-between pt-3 border-t border-gray-100">
                                <a 
                                    href="{{ route('projects.files.download', [$project, $file]) }}" 
                                    class="text-purple-600 hover:text-purple-700 text-sm font-medium flex items-center"
                                >
                                    <span class="material-icons text-sm ml-1">download</span>
                                    تحميل
                                </a>
                                <span class="text-xs text-gray-400">
                                    {{ $file->created_at->format('Y-m-d') }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <span class="material-icons text-gray-400 text-3xl">attach_file</span>
                    </div>
                    <h4 class="text-lg font-medium text-gray-900 mb-2">لا توجد ملفات</h4>
                    <p class="text-gray-500">لم يتم رفع أي ملفات مرجعية لهذا المشروع بعد</p>
                </div>
            @endif
        </div>

        <!-- Posts Section -->
        <div class="card rounded-2xl p-6">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-purple-100 rounded-xl flex items-center justify-center icon-spacing ml-3">
                        <i class="fas fa-file-alt text-purple-600"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-800">المحتوى المنشور</h3>
                </div>
                <div class="flex items-center space-x-3 rtl:space-x-reverse">
                    <a href="{{ route('projects.analyze', $project) }}" class="flex items-center px-4 py-2 text-white bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 rounded-xl transition-colors text-sm shadow-lg">
                        <i class="fas fa-magic text-sm ml-2"></i>
                        تحليل المحتوى
                    </a>
                    <a href="{{ route('projects.content.create', $project) }}" class="btn-primary text-white px-4 py-2 rounded-xl flex items-center hover:no-underline text-sm">
                        <i class="fas fa-plus text-sm ml-2"></i>
                        إضافة محتوى جديد
                    </a>
                </div>
            </div>

            @if(isset($posts) && $posts->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($posts as $post)
                        <div class="bg-gradient-to-br from-purple-50 to-indigo-50 rounded-xl p-5 border border-purple-100 hover:shadow-lg transition-all">
                            <!-- Header -->
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-purple-500 rounded-lg flex items-center justify-center ml-3">
                                        @if($post->content_type == 'post')
                                            <i class="fas fa-file-alt text-white text-sm"></i>
                                        @elseif($post->content_type == 'reels')
                                            <i class="fas fa-video text-white text-sm"></i>
                                        @else
                                            <i class="fas fa-align-left text-white text-sm"></i>
                                        @endif
                                    </div>
                                    <div>
                                        <h4 class="font-semibold text-gray-800 text-sm">{{ $post->content_type_label }}</h4>
                                        <p class="text-xs text-gray-500">{{ $post->created_at->format('Y-m-d') }}</p>
                                    </div>
                                </div>
                                @if($post->revenue && $post->revenue > 0)
                                    <div class="bg-green-100 text-green-700 px-2 py-1 rounded-lg text-xs font-semibold">
                                        {{ number_format($post->revenue, 2) }} {{ $post->currency ?? 'EGP' }}
                                    </div>
                                @endif
                            </div>

                            <!-- Content Preview -->
                            <div class="bg-white rounded-lg p-4 mb-4 border border-purple-100">
                                <p class="text-sm text-gray-700 line-clamp-4 leading-relaxed">
                                    {{ Str::limit($post->content, 150) }}
                                </p>
                            </div>

                            <!-- Footer -->
                            <div class="flex items-center justify-between pt-3 border-t border-purple-100">
                                <div class="flex items-center text-xs text-gray-500">
                                    @if($post->creator)
                                        <i class="fas fa-user ml-1"></i>
                                        {{ $post->creator->name }}
                                    @endif
                                </div>
                                <a href="{{ route('brand-style-extractors.show', $post) }}" class="text-purple-600 hover:text-purple-700 text-sm font-medium flex items-center">
                                    <i class="fas fa-eye text-xs ml-1"></i>
                                    عرض التفاصيل
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Stats -->
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="bg-blue-50 rounded-lg p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-600 mb-1">إجمالي المحتوى</p>
                                    <p class="text-2xl font-bold text-blue-600">{{ $posts->count() }}</p>
                                </div>
                                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-file-alt text-blue-600"></i>
                                </div>
                            </div>
                        </div>
                        <div class="bg-green-50 rounded-lg p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-600 mb-1">إجمالي العائد</p>
                                    <p class="text-2xl font-bold text-green-600">
                                        {{ number_format($posts->sum(function($post) { return $post->revenue ?? 0; }), 2) }} EGP
                                    </p>
                                </div>
                                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-coins text-green-600"></i>
                                </div>
                            </div>
                        </div>
                        <div class="bg-purple-50 rounded-lg p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-600 mb-1">البوستات</p>
                                    <p class="text-2xl font-bold text-purple-600">
                                        {{ $posts->where('content_type', 'post')->count() }}
                                    </p>
                                </div>
                                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-file-alt text-purple-600"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="text-center py-12">
                    <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-file-alt text-purple-400 text-2xl"></i>
                    </div>
                    <h4 class="text-lg font-medium text-gray-900 mb-2">لا يوجد محتوى منشور</h4>
                    <p class="text-gray-500 mb-6">لم يتم إضافة أي محتوى منشور لهذا المشروع بعد</p>
                    <a href="{{ route('projects.content.create', $project) }}" class="btn-primary text-white px-6 py-3 rounded-xl inline-flex items-center hover:no-underline">
                        <i class="fas fa-plus text-sm ml-2"></i>
                        إضافة محتوى جديد
                    </a>
                </div>
            @endif

            <!-- Brand Profile Section -->
            @if($project->brand_profile && is_array($project->brand_profile) && count($project->brand_profile) > 0)
                <div class="mt-8 pt-8 border-t border-gray-200">
                    <div class="flex items-center mb-6">
                        <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center icon-spacing ml-3">
                            <i class="fas fa-magic text-white"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-800">Brand Profile</h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @if(isset($project->brand_profile['voice']) && !empty($project->brand_profile['voice']))
                            <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl p-5 border border-blue-100">
                                <div class="flex items-center mb-3">
                                    <div class="w-8 h-8 bg-blue-500 rounded-lg flex items-center justify-center ml-3">
                                        <i class="fas fa-microphone text-white text-xs"></i>
                                    </div>
                                    <h4 class="font-semibold text-gray-800">Voice</h4>
                                </div>
                                <p class="text-sm text-gray-700 leading-relaxed">{{ $project->brand_profile['voice'] }}</p>
                            </div>
                        @endif

                        @if(isset($project->brand_profile['tone']) && !empty($project->brand_profile['tone']))
                            <div class="bg-gradient-to-br from-purple-50 to-pink-50 rounded-xl p-5 border border-purple-100">
                                <div class="flex items-center mb-3">
                                    <div class="w-8 h-8 bg-purple-500 rounded-lg flex items-center justify-center ml-3">
                                        <i class="fas fa-volume-up text-white text-xs"></i>
                                    </div>
                                    <h4 class="font-semibold text-gray-800">Tone</h4>
                                </div>
                                <p class="text-sm text-gray-700 leading-relaxed">{{ $project->brand_profile['tone'] }}</p>
                            </div>
                        @endif

                        @if(isset($project->brand_profile['structure']) && !empty($project->brand_profile['structure']))
                            <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-xl p-5 border border-green-100">
                                <div class="flex items-center mb-3">
                                    <div class="w-8 h-8 bg-green-500 rounded-lg flex items-center justify-center ml-3">
                                        <i class="fas fa-sitemap text-white text-xs"></i>
                                    </div>
                                    <h4 class="font-semibold text-gray-800">Structure</h4>
                                </div>
                                <p class="text-sm text-gray-700 leading-relaxed">{{ $project->brand_profile['structure'] }}</p>
                            </div>
                        @endif

                        @if(isset($project->brand_profile['language_style']) && !empty($project->brand_profile['language_style']))
                            <div class="bg-gradient-to-br from-yellow-50 to-orange-50 rounded-xl p-5 border border-yellow-100">
                                <div class="flex items-center mb-3">
                                    <div class="w-8 h-8 bg-yellow-500 rounded-lg flex items-center justify-center ml-3">
                                        <i class="fas fa-language text-white text-xs"></i>
                                    </div>
                                    <h4 class="font-semibold text-gray-800">Language Style</h4>
                                </div>
                                <p class="text-sm text-gray-700 leading-relaxed">{{ $project->brand_profile['language_style'] }}</p>
                            </div>
                        @endif

                        @if(isset($project->brand_profile['cta_style']) && !empty($project->brand_profile['cta_style']))
                            <div class="bg-gradient-to-br from-red-50 to-pink-50 rounded-xl p-5 border border-red-100">
                                <div class="flex items-center mb-3">
                                    <div class="w-8 h-8 bg-red-500 rounded-lg flex items-center justify-center ml-3">
                                        <i class="fas fa-hand-pointer text-white text-xs"></i>
                                    </div>
                                    <h4 class="font-semibold text-gray-800">CTA Style</h4>
                                </div>
                                <p class="text-sm text-gray-700 leading-relaxed">{{ $project->brand_profile['cta_style'] }}</p>
                            </div>
                        @endif

                        @if(isset($project->brand_profile['enemy']) && !empty($project->brand_profile['enemy']))
                            <div class="bg-gradient-to-br from-gray-50 to-slate-50 rounded-xl p-5 border border-gray-100">
                                <div class="flex items-center mb-3">
                                    <div class="w-8 h-8 bg-gray-500 rounded-lg flex items-center justify-center ml-3">
                                        <i class="fas fa-shield-alt text-white text-xs"></i>
                                    </div>
                                    <h4 class="font-semibold text-gray-800">Enemy</h4>
                                </div>
                                <p class="text-sm text-gray-700 leading-relaxed">{{ $project->brand_profile['enemy'] }}</p>
                            </div>
                        @endif

                        @if(isset($project->brand_profile['values']) && !empty($project->brand_profile['values']))
                            <div class="bg-gradient-to-br from-indigo-50 to-blue-50 rounded-xl p-5 border border-indigo-100">
                                <div class="flex items-center mb-3">
                                    <div class="w-8 h-8 bg-indigo-500 rounded-lg flex items-center justify-center ml-3">
                                        <i class="fas fa-heart text-white text-xs"></i>
                                    </div>
                                    <h4 class="font-semibold text-gray-800">Values</h4>
                                </div>
                                <p class="text-sm text-gray-700 leading-relaxed">{{ $project->brand_profile['values'] }}</p>
                            </div>
                        @endif

                        @if(isset($project->brand_profile['hook_patterns']) && !empty($project->brand_profile['hook_patterns']))
                            <div class="bg-gradient-to-br from-teal-50 to-cyan-50 rounded-xl p-5 border border-teal-100">
                                <div class="flex items-center mb-3">
                                    <div class="w-8 h-8 bg-teal-500 rounded-lg flex items-center justify-center ml-3">
                                        <i class="fas fa-fish text-white text-xs"></i>
                                    </div>
                                    <h4 class="font-semibold text-gray-800">Hook Patterns</h4>
                                </div>
                                <p class="text-sm text-gray-700 leading-relaxed">{{ $project->brand_profile['hook_patterns'] }}</p>
                            </div>
                        @endif

                        @if(isset($project->brand_profile['phrases']) && !empty($project->brand_profile['phrases']))
                            <div class="bg-gradient-to-br from-amber-50 to-yellow-50 rounded-xl p-5 border border-amber-100 md:col-span-2">
                                <div class="flex items-center mb-3">
                                    <div class="w-8 h-8 bg-amber-500 rounded-lg flex items-center justify-center ml-3">
                                        <i class="fas fa-quote-left text-white text-xs"></i>
                                    </div>
                                    <h4 class="font-semibold text-gray-800">Phrases</h4>
                                </div>
                                <p class="text-sm text-gray-700 leading-relaxed">{{ $project->brand_profile['phrases'] }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

@section('scripts')
<script>
// تعريف الدالة في النطاق العام قبل DOMContentLoaded
window.analyzeProjectContent = function(event) {
    console.log('analyzeProjectContent called', event);
    
    try {
        // الحصول على الزر الذي تم الضغط عليه
        let clickedBtn = null;
        if (event && event.target) {
            clickedBtn = event.target.closest('button');
        }
        if (!clickedBtn) {
            clickedBtn = document.getElementById('analyzeContentBtn') || document.getElementById('analyzeProjectContentBtn');
        }
        
        const projectId = {{ $project->id }};
        console.log('Project ID:', projectId);
        console.log('Clicked button:', clickedBtn);
        
        // تعطيل الزر إذا كان موجوداً
        if (clickedBtn) {
            clickedBtn.disabled = true;
            const originalHTML = clickedBtn.innerHTML;
            clickedBtn.innerHTML = '<i class="fas fa-spinner fa-spin text-sm ml-2"></i> جاري التحليل...';
            clickedBtn.dataset.originalHTML = originalHTML;
        }
        
        // تعطيل الزر في الهيدر أيضاً إذا كان موجوداً
        const headerBtn = document.getElementById('analyzeProjectContentBtn');
        const headerBtnText = document.getElementById('analyzeProjectBtnText');
        const headerBtnLoading = document.getElementById('analyzeProjectBtnLoading');
        
        if (headerBtn) {
            headerBtn.disabled = true;
            if (headerBtnText) headerBtnText.classList.add('hidden');
            if (headerBtnLoading) headerBtnLoading.classList.remove('hidden');
        }
        
        // الحصول على CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (!csrfToken) {
            throw new Error('CSRF token not found');
        }
        const token = csrfToken.getAttribute('content');
        console.log('CSRF Token found:', token ? 'Yes' : 'No');
        
        // إظهار رسالة تحميل
        Swal.fire({
            title: 'جاري التحليل...',
            text: 'يرجى الانتظار بينما نقوم بتحليل جميع المحتويات النصية للمشروع',
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        // استدعاء API
        console.log('Starting content analysis for project:', projectId);
        console.log('Fetch URL:', `/projects/${projectId}/analyze-content`);
        
        fetch(`/projects/${projectId}/analyze-content`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token,
                'Accept': 'application/json'
            }
        })
        .then(response => {
            console.log('Response status:', response.status);
            if (!response.ok) {
                return response.json().then(data => {
                    throw new Error(data.error || `HTTP error! status: ${response.status}`);
                });
            }
            return response.json();
        })
        .then(data => {
            console.log('Response data:', data);
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'تم التحليل بنجاح!',
                    text: 'تم تحليل جميع المحتويات النصية وحفظ Brand Profile على مستوى المشروع',
                    confirmButtonText: 'حسناً'
                }).then(() => {
                    // إعادة تحميل الصفحة لعرض البيانات المحدثة
                    location.reload();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'فشل التحليل',
                    text: data.error || 'حدث خطأ أثناء تحليل المحتوى',
                    confirmButtonText: 'حسناً'
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'حدث خطأ',
                text: error.message || 'حدث خطأ أثناء الاتصال بالخادم',
                confirmButtonText: 'حسناً'
            });
        })
        .finally(() => {
            // إعادة تفعيل الزر الذي تم الضغط عليه
            if (clickedBtn && clickedBtn.dataset.originalHTML) {
                clickedBtn.disabled = false;
                clickedBtn.innerHTML = clickedBtn.dataset.originalHTML;
            }
            
            // إعادة تفعيل الزر في الهيدر إذا كان موجوداً
            if (headerBtn) {
                headerBtn.disabled = false;
                if (headerBtnText) headerBtnText.classList.remove('hidden');
                if (headerBtnLoading) headerBtnLoading.classList.add('hidden');
            }
        });
    } catch (error) {
        console.error('Error in analyzeProjectContent:', error);
        Swal.fire({
            icon: 'error',
            title: 'حدث خطأ',
            text: error.message || 'حدث خطأ غير متوقع',
            confirmButtonText: 'حسناً'
        });
        
        // إعادة تفعيل الأزرار في حالة الخطأ
        const clickedBtn = document.getElementById('analyzeContentBtn') || document.getElementById('analyzeProjectContentBtn');
        if (clickedBtn && clickedBtn.dataset.originalHTML) {
            clickedBtn.disabled = false;
            clickedBtn.innerHTML = clickedBtn.dataset.originalHTML;
        }
    }
};

// إضافة event listener للأزرار بعد تحميل الصفحة
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, setting up event listeners');
    
    // زر في قسم البوستات
    const analyzeBtn = document.getElementById('analyzeContentBtn');
    if (analyzeBtn) {
        analyzeBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            console.log('Posts section button clicked!');
            if (typeof window.analyzeProjectContent === 'function') {
                window.analyzeProjectContent(e);
            } else {
                console.error('analyzeProjectContent function not found');
            }
        });
        console.log('Posts section analyze button event listener added');
    } else {
        console.warn('Posts section analyze button not found');
    }
    
    // زر في الهيدر
    const headerBtn = document.getElementById('analyzeProjectContentBtn');
    if (headerBtn) {
        headerBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            console.log('Header button clicked!');
            if (typeof window.analyzeProjectContent === 'function') {
                window.analyzeProjectContent(e);
            } else {
                console.error('analyzeProjectContent function not found');
            }
        });
        console.log('Header analyze button event listener added');
    } else {
        console.warn('Header analyze button not found');
    }
});

function togglePassword(inputId) {
    const input = document.getElementById(inputId);
    if (input.type === 'password') {
        input.type = 'text';
    } else {
        input.type = 'password';
    }
}

function copyPassword(inputId) {
    const input = document.getElementById(inputId);
    input.select();
    input.setSelectionRange(0, 99999);
    document.execCommand('copy');
    
    // Show success message
    Swal.fire({
        title: 'تم النسخ!',
        text: 'تم نسخ كلمة المرور إلى الحافظة',
        icon: 'success',
        timer: 2000,
        showConfirmButton: false
    });
}
@endsection
@endsection
