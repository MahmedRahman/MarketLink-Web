@extends('layouts.dashboard')

@section('title', 'إضافة عميل جديد')
@section('page-title', 'إضافة عميل جديد')
@section('page-description', 'إضافة عميل جديد إلى النظام')

@section('content')
<div class="container mx-auto px-4">
    <div class="max-w-4xl mx-auto space-y-6">
        <!-- Header -->
        <div class="card page-header rounded-2xl p-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="w-12 h-12 logo-gradient rounded-2xl flex items-center justify-center shadow-lg icon-spacing ml-3">
                        <i class="fas fa-user-plus text-white text-xl"></i>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800">إضافة عميل جديد</h2>
                        <p class="text-gray-600">املأ البيانات التالية لإضافة عميل جديد إلى النظام</p>
                    </div>
                </div>
                <a href="{{ route('clients.index') }}" class="flex items-center px-4 py-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-xl transition-colors icon-spacing">
                    العودة للقائمة

                </a>
            </div>
        </div>

    <!-- Form Card -->
    <div class="card rounded-2xl p-8">

        <form method="POST" action="{{ route('clients.store') }}" class="space-y-8">
            @csrf
            
            <!-- Basic Information Section -->
            <div class="form-section space-y-6">
                <h3 class="text-lg font-semibold text-gray-800">المعلومات الأساسية</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            اسم العميل <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            id="name" 
                            name="name" 
                            value="{{ old('name') }}"
                            required
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-colors"
                            placeholder="أدخل اسم العميل"
                        />
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                            البريد الإلكتروني <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            value="{{ old('email') }}"
                            required
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-colors"
                            placeholder="أدخل البريد الإلكتروني"
                        />
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                            كلمة السر
                        </label>
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            value="{{ old('password') }}"
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-colors"
                            placeholder="أدخل كلمة السر (اختياري)"
                        />
                        @error('password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Contact Information Section -->
            <div class="form-section space-y-6">
                <h3 class="text-lg font-semibold text-gray-800">معلومات الاتصال</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Phone -->
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                            رقم الهاتف
                        </label>
                        <input 
                            type="tel" 
                            id="phone" 
                            name="phone" 
                            value="{{ old('phone') }}"
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-colors"
                            placeholder="مثال: +966501234567"
                        />
                        @error('phone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Company -->
                    <div>
                        <label for="company" class="block text-sm font-medium text-gray-700 mb-2">
                            اسم الشركة
                        </label>
                        <input 
                            type="text" 
                            id="company" 
                            name="company" 
                            value="{{ old('company') }}"
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-colors"
                            placeholder="أدخل اسم الشركة"
                        />
                        @error('company')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Status and Notes Section -->
            <div class="form-section space-y-6">
                <h3 class="text-lg font-semibold text-gray-800">الحالة والملاحظات</h3>
                
                <div class="grid grid-cols-1 ">
                    <!-- Status -->
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                            الحالة <span class="text-red-500">*</span>
                        </label>
                        <select 
                            id="status" 
                            name="status" 
                            required
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-colors select2"
                        >
                                <option value="">اختر الحالة</option>
                                <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>نشط</option>
                                <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>غير نشط</option>
                                <option value="pending" {{ old('status') === 'pending' ? 'selected' : '' }}>في الانتظار</option>
                            </select>
                        </div>
                        @error('status')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Notes -->
                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                            ملاحظات
                        </label>
                        <textarea 
                            id="notes" 
                            name="notes" 
                            rows="4"
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-colors"
                            placeholder="أدخل أي ملاحظات إضافية"
                        >{{ old('notes') }}</textarea>
                        @error('notes')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-center rtl-spacing pt-8 border-t border-gray-200">
            <button type="submit" class="action-button btn-primary text-white px-8 py-4 rounded-2xl flex items-center font-medium text-lg min-w-[160px] justify-center">
                    <i class="fas fa-save text-lg mr-3"></i>
                    حفظ 
                </button>   
            
            <a href="{{ route('clients.index') }}" class="action-button cancel-button flex items-center px-8 py-4 rounded-2xl font-medium text-lg min-w-[140px] justify-center">
                    إلغاء
                </a>
              
            </div>
        </form>
    </div>
  
</div>
@endsection
