@extends('layouts.dashboard')

@section('title', 'إضافة مشروع جديد')
@section('page-title', 'إضافة مشروع جديد')
@section('page-description', 'إضافة مشروع جديد إلى النظام')

@section('content')
<div class="container mx-auto px-4">
    <div class="max-w-4xl mx-auto space-y-6">
        <!-- Header -->
        <div class="card page-header rounded-2xl p-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="w-12 h-12 logo-gradient rounded-2xl flex items-center justify-center shadow-lg icon-spacing ml-3">
                        <i class="fas fa-project-diagram text-white text-xl"></i>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800">إضافة مشروع جديد</h2>
                        <p class="text-gray-600">املأ البيانات التالية لإضافة مشروع جديد إلى النظام</p>
                    </div>
                </div>
                <a href="{{ route('projects.index') }}" class="flex items-center px-4 py-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-xl transition-colors icon-spacing">
                    العودة للقائمة
                </a>
            </div>
        </div>

        <!-- Form Card -->
        <div class="card rounded-2xl p-8">
            <form method="POST" action="{{ route('projects.store') }}" class="space-y-8">
                @csrf
                
                <!-- Basic Information Section -->
                <div class="form-section space-y-6">
                    <h3 class="text-lg font-semibold text-gray-800">المعلومات الأساسية</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Client -->
                        <div>
                            <label for="client_id" class="block text-sm font-medium text-gray-700 mb-2">
                                العميل <span class="text-red-500">*</span>
                            </label>
                            <select
                                id="client_id"
                                name="client_id"
                                required
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-colors select2"
                            >
                                <option value="">اختر العميل</option>
                                @foreach($clients as $client)
                                    <option value="{{ $client->id }}" {{ old('client_id') == $client->id ? 'selected' : '' }}>
                                        {{ $client->name }} - {{ $client->email }}
                                    </option>
                                @endforeach
                            </select>
                            @error('client_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Business Name -->
                        <div>
                            <label for="business_name" class="block text-sm font-medium text-gray-700 mb-2">
                                اسم البيزنس <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="text"
                                id="business_name"
                                name="business_name"
                                value="{{ old('business_name') }}"
                                required
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-colors"
                                placeholder="أدخل اسم البيزنس"
                            />
                            @error('business_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Business Description -->
                    <div>
                        <label for="business_description" class="block text-sm font-medium text-gray-700 mb-2">
                            نبذة عن البيزنس
                        </label>
                        <textarea
                            id="business_description"
                            name="business_description"
                            rows="4"
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-colors"
                            placeholder="أدخل نبذة عن البيزنس"
                        >{{ old('business_description') }}</textarea>
                        @error('business_description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Website and Social Media Section -->
                <div class="form-section space-y-6">
                    <h3 class="text-lg font-semibold text-gray-800">الموقع ووسائل التواصل الاجتماعي</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Website -->
                        <div>
                            <label for="website_url" class="block text-sm font-medium text-gray-700 mb-2">
                                رابط الموقع
                            </label>
                            <input
                                type="url"
                                id="website_url"
                                name="website_url"
                                value="{{ old('website_url') }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-colors"
                                placeholder="https://example.com"
                            />
                            @error('website_url')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Facebook -->
                        <div>
                            <label for="facebook_url" class="block text-sm font-medium text-gray-700 mb-2">
                                رابط فيسبوك
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

                        <!-- Instagram -->
                        <div>
                            <label for="instagram_url" class="block text-sm font-medium text-gray-700 mb-2">
                                رابط إنستغرام
                            </label>
                            <input
                                type="url"
                                id="instagram_url"
                                name="instagram_url"
                                value="{{ old('instagram_url') }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-colors"
                                placeholder="https://instagram.com/username"
                            />
                            @error('instagram_url')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Twitter -->
                        <div>
                            <label for="twitter_url" class="block text-sm font-medium text-gray-700 mb-2">
                                رابط تويتر
                            </label>
                            <input
                                type="url"
                                id="twitter_url"
                                name="twitter_url"
                                value="{{ old('twitter_url') }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-colors"
                                placeholder="https://twitter.com/username"
                            />
                            @error('twitter_url')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- LinkedIn -->
                        <div>
                            <label for="linkedin_url" class="block text-sm font-medium text-gray-700 mb-2">
                                رابط لينكد إن
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

                        <!-- YouTube -->
                        <div>
                            <label for="youtube_url" class="block text-sm font-medium text-gray-700 mb-2">
                                رابط يوتيوب
                            </label>
                            <input
                                type="url"
                                id="youtube_url"
                                name="youtube_url"
                                value="{{ old('youtube_url') }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-colors"
                                placeholder="https://youtube.com/channel/username"
                            />
                            @error('youtube_url')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- TikTok -->
                        <div>
                            <label for="tiktok_url" class="block text-sm font-medium text-gray-700 mb-2">
                                رابط تيك توك
                            </label>
                            <input
                                type="url"
                                id="tiktok_url"
                                name="tiktok_url"
                                value="{{ old('tiktok_url') }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-colors"
                                placeholder="https://tiktok.com/@username"
                            />
                            @error('tiktok_url')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Contact Information Section -->
                <div class="form-section space-y-6">
                    <h3 class="text-lg font-semibold text-gray-800">معلومات الاتصال</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- WhatsApp -->
                        <div>
                            <label for="whatsapp_number" class="block text-sm font-medium text-gray-700 mb-2">
                                رقم الواتساب
                            </label>
                            <input
                                type="tel"
                                id="whatsapp_number"
                                name="whatsapp_number"
                                value="{{ old('whatsapp_number') }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-colors"
                                placeholder="مثال: +966501234567"
                            />
                            @error('whatsapp_number')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Phone -->
                        <div>
                            <label for="phone_number" class="block text-sm font-medium text-gray-700 mb-2">
                                رقم الهاتف
                            </label>
                            <input
                                type="tel"
                                id="phone_number"
                                name="phone_number"
                                value="{{ old('phone_number') }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-colors"
                                placeholder="مثال: +966501234567"
                            />
                            @error('phone_number')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                البريد الإلكتروني
                            </label>
                            <input
                                type="email"
                                id="email"
                                name="email"
                                value="{{ old('email') }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-colors"
                                placeholder="أدخل البريد الإلكتروني"
                            />
                            @error('email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Address -->
                        <div>
                            <label for="address" class="block text-sm font-medium text-gray-700 mb-2">
                                العنوان
                            </label>
                            <textarea
                                id="address"
                                name="address"
                                rows="3"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-colors"
                                placeholder="أدخل العنوان"
                            >{{ old('address') }}</textarea>
                            @error('address')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Status Section -->
                <div class="form-section space-y-6">
                    <h3 class="text-lg font-semibold text-gray-800">الحالة</h3>
                    
                    <div class="grid grid-cols-1">
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
                        <i class="fas fa-save text-lg mr-3"></i>
                        حفظ
                    </button>   
                    
                    <a href="{{ route('projects.index') }}" class="action-button cancel-button flex items-center px-8 py-4 rounded-2xl font-medium text-lg min-w-[140px] justify-center">
                        إلغاء
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
