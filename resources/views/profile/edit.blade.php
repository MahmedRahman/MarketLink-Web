@extends('layouts.dashboard')

@section('title', 'الإعدادات')
@section('page-title', 'الإعدادات')
@section('page-description', 'إدارة بيانات حسابك وكلمة المرور')

@section('content')
<div class="container mx-auto px-4">
    <div class="max-w-4xl mx-auto space-y-6">
        <!-- Header -->
        <div class="card page-header rounded-2xl p-6">
            <div class="flex items-center">
                <div class="w-12 h-12 logo-gradient rounded-2xl flex items-center justify-center shadow-lg icon-spacing ml-3">
                    <i class="fas fa-cog text-white text-xl"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">الإعدادات</h2>
                    <p class="text-gray-600">إدارة بيانات حسابك وكلمة المرور</p>
                </div>
            </div>
        </div>

        <!-- Success Message -->
        @if (session('status') === 'profile-updated')
            <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl mb-6">
                تم تحديث بيانات البروفايل بنجاح
            </div>
        @endif

        @if (session('status') === 'password-updated')
            <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl mb-6">
                تم تحديث كلمة المرور بنجاح
            </div>
        @endif

        <!-- Update Profile Information -->
        <div class="card rounded-2xl p-8">
            <div class="flex items-center mb-6">
                <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center icon-spacing ml-3">
                    <i class="fas fa-user text-blue-600"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-800">تحديث بيانات البروفايل</h3>
            </div>

            <form method="POST" action="{{ route('profile.update') }}" class="space-y-6">
                @csrf
                @method('PATCH')

                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        الاسم <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="name" 
                        name="name" 
                        value="{{ old('name', $user->name) }}" 
                        required
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-colors"
                    >
                    @error('name')
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
                        value="{{ old('email', $user->email) }}" 
                        readonly
                        disabled
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl bg-gray-100 text-gray-600 cursor-not-allowed"
                    >
                    <p class="mt-1 text-xs text-gray-500">لا يمكن تعديل البريد الإلكتروني</p>
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Auto Follow Tasks -->
                <div class="flex items-center">
                    <input 
                        type="checkbox" 
                        id="auto_follow_tasks" 
                        name="auto_follow_tasks" 
                        value="1"
                        {{ old('auto_follow_tasks', $user->auto_follow_tasks) ? 'checked' : '' }}
                        class="w-4 h-4 text-primary bg-gray-100 border-gray-300 rounded focus:ring-primary focus:ring-2"
                    >
                    <label for="auto_follow_tasks" class="mr-2 text-sm font-medium text-gray-700 cursor-pointer">
                        متابعة التاسكات بشكل تلقائي
                    </label>
                </div>
                <p class="text-xs text-gray-500 mb-4">عند تفعيل هذا الخيار، سيتم متابعة التاسكات الجديدة تلقائياً</p>
                @error('auto_follow_tasks')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror

                <!-- Submit Button -->
                <div class="flex items-center justify-end rtl-spacing pt-4">
                    <button type="submit" class="btn-primary text-white px-8 py-3 rounded-xl flex items-center">
                        <i class="fas fa-save text-sm ml-2"></i>
                        حفظ التغييرات
                    </button>
                </div>
            </form>
        </div>

        <!-- Update Password -->
        <div class="card rounded-2xl p-8">
            <div class="flex items-center mb-6">
                <div class="w-10 h-10 bg-purple-100 rounded-xl flex items-center justify-center icon-spacing ml-3">
                    <i class="fas fa-lock text-purple-600"></i>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-800">تغيير كلمة المرور</h3>
                    <p class="text-sm text-gray-600">تأكد من استخدام كلمة مرور قوية لحماية حسابك</p>
                </div>
            </div>

            <form method="POST" action="{{ route('password.update') }}" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Current Password -->
                <div>
                    <label for="current_password" class="block text-sm font-medium text-gray-700 mb-2">
                        كلمة المرور الحالية <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="password" 
                        id="current_password" 
                        name="current_password" 
                        required
                        autocomplete="current-password"
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-colors"
                    >
                    @error('current_password', 'updatePassword')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- New Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                        كلمة المرور الجديدة <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        required
                        autocomplete="new-password"
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-colors"
                    >
                    @error('password', 'updatePassword')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Confirm Password -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                        تأكيد كلمة المرور <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="password" 
                        id="password_confirmation" 
                        name="password_confirmation" 
                        required
                        autocomplete="new-password"
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-colors"
                    >
                    @error('password_confirmation', 'updatePassword')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit Button -->
                <div class="flex items-center justify-end rtl-spacing pt-4">
                    <button type="submit" class="btn-primary text-white px-8 py-3 rounded-xl flex items-center">
                        <i class="fas fa-key text-sm ml-2"></i>
                        تحديث كلمة المرور
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
