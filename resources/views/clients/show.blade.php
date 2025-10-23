@extends('layouts.dashboard')

@section('title', 'عرض بيانات العميل')
@section('page-title', 'عرض بيانات العميل')
@section('page-description', 'عرض تفاصيل العميل: ' . $client->name)

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
                        <h2 class="text-2xl font-bold text-gray-800">عرض بيانات العميل</h2>
                        <p class="text-gray-600">تفاصيل العميل: {{ $client->name }}</p>
                    </div>
                </div>
                <div class="flex items-center space-x-3 rtl:space-x-reverse">
                    <a href="{{ route('clients.edit', $client) }}" class="flex items-center px-4 py-2 text-blue-600 hover:text-blue-700 hover:bg-blue-50 rounded-xl transition-colors icon-spacing">
                        <i class="fas fa-edit text-sm mr-2"></i>
                        تعديل
                    </a>
                    <a href="{{ route('clients.index') }}" class="flex items-center px-4 py-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-xl transition-colors icon-spacing">
                        العودة للقائمة
                    </a>
                </div>
            </div>
        </div>

        <!-- Client Information Cards -->
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
                            <label class="block text-sm font-medium text-gray-500 mb-2">اسم العميل</label>
                            <p class="text-lg font-semibold text-gray-900">{{ $client->name }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-2">البريد الإلكتروني</label>
                            <p class="text-lg font-semibold text-gray-900">{{ $client->email }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-2">رقم الهاتف</label>
                            <p class="text-lg font-semibold text-gray-900">{{ $client->phone ?? 'غير محدد' }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-2">اسم الشركة</label>
                            <p class="text-lg font-semibold text-gray-900">{{ $client->company ?? 'غير محدد' }}</p>
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
                            <span class="status-badge status-{{ $client->status_color }}">
                                {{ $client->status_badge }}
                            </span>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-2">تاريخ الإنشاء</label>
                            <p class="text-sm text-gray-900">{{ $client->created_at->format('Y-m-d H:i') }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-2">آخر تحديث</label>
                            <p class="text-sm text-gray-900">{{ $client->updated_at->format('Y-m-d H:i') }}</p>
                        </div>
                        
                        <div class="pt-4 border-t border-gray-200">
                            <div class="flex flex-col space-y-2">
                                <a href="{{ route('clients.edit', $client) }}" class="w-full btn-primary text-white px-4 py-2 rounded-xl flex items-center justify-center hover:no-underline">
                                    <i class="fas fa-edit text-sm mr-2"></i>
                                    تعديل العميل
                                </a>
                                <button onclick="confirmDelete('{{ route('clients.destroy', $client) }}', 'تأكيد حذف العميل', 'هل أنت متأكد من حذف العميل {{ $client->name }}؟')" class="w-full bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-xl flex items-center justify-center transition-colors">
                                    <i class="fas fa-trash text-sm mr-2"></i>
                                    حذف العميل
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Notes Section -->
        @if($client->notes)
        <div class="card rounded-2xl p-6">
            <div class="flex items-center mb-4">
                <div class="w-10 h-10 bg-yellow-100 rounded-xl flex items-center justify-center icon-spacing ml-3">
                    <i class="fas fa-sticky-note text-yellow-600"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-800">الملاحظات</h3>
            </div>
            <div class="bg-gray-50 rounded-xl p-4">
                <p class="text-gray-700 leading-relaxed">{{ $client->notes }}</p>
            </div>
        </div>
        @endif

        <!-- Projects Section -->
        <div class="card rounded-2xl p-6">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-purple-100 rounded-xl flex items-center justify-center icon-spacing ml-3">
                        <i class="fas fa-project-diagram text-purple-600"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-800">مشاريع العميل</h3>
                </div>
                <a href="{{ route('projects.create', ['client_id' => $client->id]) }}" class="btn-primary text-white px-4 py-2 rounded-xl flex items-center hover:no-underline">
                    <i class="fas fa-plus text-sm mr-2"></i>
                    إضافة مشروع جديد
                </a>
            </div>
            
            @if($client->projects->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($client->projects as $project)
                        <div class="bg-gray-50 rounded-xl p-4 hover:bg-gray-100 transition-colors">
                            <div class="flex items-center justify-between mb-2">
                                <h4 class="font-semibold text-gray-900">{{ $project->business_name }}</h4>
                                <span class="status-badge status-{{ $project->status_color }}">
                                    {{ $project->status_badge }}
                                </span>
                            </div>
                            <p class="text-sm text-gray-600 mb-3">{{ Str::limit($project->business_description, 100) }}</p>
                            <div class="flex items-center justify-between">
                                <span class="text-xs text-gray-500">{{ $project->created_at->format('Y-m-d') }}</span>
                                <a href="{{ route('projects.show', $project) }}" class="text-blue-600 hover:text-blue-700 text-sm">
                                    <i class="fas fa-eye mr-1"></i>
                                    عرض
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <i class="fas fa-project-diagram text-4xl text-gray-300 mb-4"></i>
                    <h4 class="text-lg font-medium text-gray-900 mb-2">لا يوجد مشاريع</h4>
                    <p class="text-gray-500 mb-4">لم يتم إضافة أي مشاريع لهذا العميل بعد</p>
                    <a href="{{ route('projects.create', ['client_id' => $client->id]) }}" class="btn-primary text-white px-4 py-2 rounded-xl inline-flex items-center hover:no-underline">
                        <i class="fas fa-plus text-sm mr-2"></i>
                        إضافة مشروع جديد
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

