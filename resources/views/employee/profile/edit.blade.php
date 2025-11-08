@extends('layouts.employee')

@section('title', 'الملف الشخصي')
@section('page-title', 'الملف الشخصي')
@section('page-description', 'إدارة بيانات حسابك وكلمة المرور')

@section('content')
<div class="space-y-6">
    <!-- Success Message -->
    @if (session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl">
            {{ session('success') }}
        </div>
    @endif

    <!-- Update Profile Information -->
    <div class="card p-8">
        <div class="flex items-center mb-6">
            <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center ml-3">
                <span class="material-icons text-blue-600">person</span>
            </div>
            <h3 class="text-lg font-semibold text-gray-800">تحديث بيانات الملف الشخصي</h3>
        </div>

        <form method="POST" action="{{ route('employee.profile.update') }}" class="space-y-6">
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
                    value="{{ old('name', $employee->name) }}" 
                    required
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors"
                >
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
                    value="{{ old('email', $employee->email) }}" 
                    required
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors"
                >
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
                    type="text" 
                    id="phone" 
                    name="phone" 
                    value="{{ old('phone', $employee->phone) }}" 
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors"
                >
                @error('phone')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Role (Read-only) -->
            <div>
                <label for="role" class="block text-sm font-medium text-gray-700 mb-2">
                    الدور
                </label>
                <input 
                    type="text" 
                    id="role" 
                    value="{{ $employee->role_badge }}" 
                    readonly
                    disabled
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl bg-gray-100 text-gray-600 cursor-not-allowed"
                >
            </div>

            <!-- Instapay Number -->
            <div>
                <label for="instapay_number" class="block text-sm font-medium text-gray-700 mb-2">
                    رقم Instapay
                </label>
                <input 
                    type="text" 
                    id="instapay_number" 
                    name="instapay_number" 
                    value="{{ old('instapay_number', $employee->instapay_number) }}" 
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors"
                >
                @error('instapay_number')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Vodafone Cash Number -->
            <div>
                <label for="vodafone_cash_number" class="block text-sm font-medium text-gray-700 mb-2">
                    رقم Vodafone Cash
                </label>
                <input 
                    type="text" 
                    id="vodafone_cash_number" 
                    name="vodafone_cash_number" 
                    value="{{ old('vodafone_cash_number', $employee->vodafone_cash_number) }}" 
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors"
                >
                @error('vodafone_cash_number')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Facebook URL -->
            <div>
                <label for="facebook_url" class="block text-sm font-medium text-gray-700 mb-2">
                    رابط Facebook
                </label>
                <input 
                    type="url" 
                    id="facebook_url" 
                    name="facebook_url" 
                    value="{{ old('facebook_url', $employee->facebook_url) }}" 
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors"
                >
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
                    value="{{ old('linkedin_url', $employee->linkedin_url) }}" 
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors"
                >
                @error('linkedin_url')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Portfolio URL -->
            <div>
                <label for="portfolio_url" class="block text-sm font-medium text-gray-700 mb-2">
                    رابط Portfolio
                </label>
                <input 
                    type="url" 
                    id="portfolio_url" 
                    name="portfolio_url" 
                    value="{{ old('portfolio_url', $employee->portfolio_url) }}" 
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors"
                >
                @error('portfolio_url')
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
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors"
                >{{ old('notes', $employee->notes) }}</textarea>
                @error('notes')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit Button -->
            <div class="flex items-center justify-end pt-4">
                <button type="submit" class="btn-primary text-white px-8 py-3 rounded-xl flex items-center">
                    <span class="material-icons text-sm ml-2">save</span>
                    حفظ التغييرات
                </button>
            </div>
        </form>
    </div>

    <!-- Update Password -->
    <div class="card p-8">
        <div class="flex items-center mb-6">
            <div class="w-10 h-10 bg-purple-100 rounded-xl flex items-center justify-center ml-3">
                <span class="material-icons text-purple-600">lock</span>
            </div>
            <div>
                <h3 class="text-lg font-semibold text-gray-800">تغيير كلمة المرور</h3>
                <p class="text-sm text-gray-600">تأكد من استخدام كلمة مرور قوية لحماية حسابك</p>
            </div>
        </div>

        <form method="POST" action="{{ route('employee.profile.password.update') }}" class="space-y-6">
            @csrf
            @method('PATCH')

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
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors"
                >
                @error('current_password')
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
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors"
                >
                @error('password')
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
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors"
                >
            </div>

            <!-- Submit Button -->
            <div class="flex items-center justify-end pt-4">
                <button type="submit" class="btn-primary text-white px-8 py-3 rounded-xl flex items-center">
                    <span class="material-icons text-sm ml-2">key</span>
                    تحديث كلمة المرور
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

