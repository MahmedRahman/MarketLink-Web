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
    </div>
</div>

@section('scripts')
<script>
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
</script>
@endsection
@endsection
