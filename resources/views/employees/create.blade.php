@extends('layouts.dashboard')

@section('title', 'إضافة موظف جديد')
@section('page-title', 'إضافة موظف جديد')
@section('page-description', 'إضافة موظف جديد إلى النظام')

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
                        <h2 class="text-2xl font-bold text-gray-800">إضافة موظف جديد</h2>
                        <p class="text-gray-600">املأ البيانات التالية لإضافة موظف جديد إلى النظام</p>
                    </div>
                </div>
                <a href="{{ route('employees.index') }}" class="flex items-center px-4 py-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-xl transition-colors icon-spacing">
                    العودة للقائمة
                </a>
            </div>
        </div>

        <!-- Form Card -->
        <div class="card rounded-2xl p-8">
            <form method="POST" action="{{ route('employees.store') }}" class="space-y-8">
                @csrf
                
                <!-- Basic Information Section -->
                <div class="form-section space-y-6">
                    <h3 class="text-lg font-semibold text-gray-800">المعلومات الأساسية</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Name -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                اسم الموظف <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="text"
                                id="name"
                                name="name"
                                value="{{ old('name') }}"
                                required
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-colors"
                                placeholder="أدخل اسم الموظف"
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
                                       placeholder="مثال: 01234567890"
                            />
                            @error('phone')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Password -->
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                                كلمة السر <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="password"
                                id="password"
                                name="password"
                                required
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-colors"
                                placeholder="أدخل كلمة السر"
                            />
                            @error('password')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Social Links -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
                        <!-- Facebook URL -->
                        <div>
                            <label for="facebook_url" class="block text-sm font-medium text-gray-700 mb-2">
                                رابط الفيس بوك
                            </label>
                            <input
                                type="url"
                                id="facebook_url"
                                name="facebook_url"
                                value="{{ old('facebook_url') }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-colors"
                                placeholder="https://facebook.com/username"
                            />
                            @error('facebook_url')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- LinkedIn URL -->
                        <div>
                            <label for="linkedin_url" class="block text-sm font-medium text-gray-700 mb-2">
                                رابط LinkedIn
                            </label>
                            <input
                                type="url"
                                id="linkedin_url"
                                name="linkedin_url"
                                value="{{ old('linkedin_url') }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-colors"
                                placeholder="https://linkedin.com/in/username"
                            />
                            @error('linkedin_url')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Portfolio URL -->
                        <div>
                            <label for="portfolio_url" class="block text-sm font-medium text-gray-700 mb-2">
                                رابط البورتفوليو
                            </label>
                            <input
                                type="url"
                                id="portfolio_url"
                                name="portfolio_url"
                                value="{{ old('portfolio_url') }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-colors"
                                placeholder="https://portfolio.com"
                            />
                            @error('portfolio_url')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Notes -->
                    <div class="mt-6">
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                            الملاحظات
                        </label>
                        <textarea
                            id="notes"
                            name="notes"
                            rows="4"
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-colors"
                            placeholder="أدخل ملاحظات عن الموظف (اختياري)"
                        >{{ old('notes') }}</textarea>
                        @error('notes')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Payment Information Section -->
                <div class="form-section space-y-6">
                    <h3 class="text-lg font-semibold text-gray-800">بيانات التحويل</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Instapay Number -->
                        <div>
                            <label for="instapay_number" class="block text-sm font-medium text-gray-700 mb-2">
                                رقم أنصاباي (Instapay)
                            </label>
                            <input
                                type="text"
                                id="instapay_number"
                                name="instapay_number"
                                value="{{ old('instapay_number') }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-colors"
                                placeholder="أدخل رقم أنصاباي"
                            />
                            @error('instapay_number')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Vodafone Cash Number -->
                        <div>
                            <label for="vodafone_cash_number" class="block text-sm font-medium text-gray-700 mb-2">
                                رقم فودافون كاش (Vodafone Cash)
                            </label>
                            <input
                                type="text"
                                id="vodafone_cash_number"
                                name="vodafone_cash_number"
                                value="{{ old('vodafone_cash_number') }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-colors"
                                placeholder="أدخل رقم فودافون كاش"
                            />
                            @error('vodafone_cash_number')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Role and Status Section -->
                <div class="form-section space-y-6">
                    <h3 class="text-lg font-semibold text-gray-800">الدور الوظيفي والحالة</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Role -->
                        <div>
                            <label for="role" class="block text-sm font-medium text-gray-700 mb-2">
                                الدور الوظيفي <span class="text-red-500">*</span>
                            </label>
                            <select
                                id="role"
                                name="role"
                                required
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-colors select2"
                            >
                                <option value="">اختر الدور الوظيفي</option>
                                <option value="content_writer" {{ old('role') === 'content_writer' ? 'selected' : '' }}>كاتب محتوى</option>
                                <option value="ad_manager" {{ old('role') === 'ad_manager' ? 'selected' : '' }}>إدارة إعلانات</option>
                                <option value="designer" {{ old('role') === 'designer' ? 'selected' : '' }}>مصمم</option>
                                <option value="video_editor" {{ old('role') === 'video_editor' ? 'selected' : '' }}>مصمم فيديوهات</option>
                                <option value="page_manager" {{ old('role') === 'page_manager' ? 'selected' : '' }}>إدارة الصفحة</option>
                                <option value="account_manager" {{ old('role') === 'account_manager' ? 'selected' : '' }}>أكونت منجر</option>
                                <option value="monitor" {{ old('role') === 'monitor' ? 'selected' : '' }}>مونتير</option>
                                <option value="media_buyer" {{ old('role') === 'media_buyer' ? 'selected' : '' }}>ميديا بايرز</option>
                            </select>
                            @error('role')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

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
                            @error('status')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex items-center justify-center rtl-spacing pt-8 border-t border-gray-200">
                    <button type="submit" class="action-button btn-primary text-white px-8 py-4 rounded-2xl flex items-center font-medium text-lg min-w-[160px] justify-center">
                        <i class="fas fa-save text-lg ml-3"></i>
                        حفظ
                    </button>   
                    
                    <a href="{{ route('employees.index') }}" class="action-button cancel-button flex items-center px-8 py-4 rounded-2xl font-medium text-lg min-w-[140px] justify-center">
                        إلغاء
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
