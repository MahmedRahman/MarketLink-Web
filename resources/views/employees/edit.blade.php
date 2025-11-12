@extends('layouts.dashboard')

@section('title', 'تعديل بيانات الموظف')
@section('page-title', 'تعديل بيانات الموظف')
@section('page-description', 'تعديل بيانات الموظف: ' . $employee->name)

@section('content')
<div class="container mx-auto px-3 md:px-4">
    <div class="max-w-4xl mx-auto space-y-4 md:space-y-6">
        <!-- Header -->
        <div class="card page-header rounded-xl md:rounded-2xl p-4 md:p-6">
            <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                <div class="flex items-center flex-1">
                    <div class="w-10 h-10 md:w-12 md:h-12 logo-gradient rounded-xl md:rounded-2xl flex items-center justify-center shadow-lg icon-spacing ml-2 md:ml-3 flex-shrink-0">
                        <i class="fas fa-user-edit text-white text-lg md:text-xl"></i>
                    </div>
                    <div>
                        <h2 class="text-xl md:text-2xl font-bold text-gray-800">تعديل بيانات الموظف</h2>
                        <p class="text-sm md:text-base text-gray-600 hidden md:block">تعديل بيانات الموظف: {{ $employee->name }}</p>
                    </div>
                </div>
                <a href="{{ route('employees.index') }}" class="flex items-center px-3 md:px-4 py-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-xl transition-colors text-sm md:text-base w-full md:w-auto justify-center">
                    <i class="fas fa-arrow-right text-xs md:text-sm ml-2"></i>
                    العودة للقائمة
                </a>
            </div>
        </div>

        <!-- Form Card -->
        <div class="card rounded-xl md:rounded-2xl p-4 md:p-8">

            <form method="POST" action="{{ route('employees.update', $employee) }}" class="space-y-8">
                @csrf
                @method('PUT')

                <!-- Basic Information Section -->
                <div class="form-section space-y-4 md:space-y-6">
                    <h3 class="text-base md:text-lg font-semibold text-gray-800">المعلومات الأساسية</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                        <!-- Name -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                اسم الموظف <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="text"
                                id="name"
                                name="name"
                                value="{{ old('name', $employee->name) }}"
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
                                value="{{ old('email', $employee->email) }}"
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
                                value="{{ old('phone', $employee->phone) }}"
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
                                كلمة السر الجديدة
                            </label>
                            <input
                                type="password"
                                id="password"
                                name="password"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-colors"
                                placeholder="أدخل كلمة السر الجديدة (اتركها فارغة للحفاظ على الكلمة الحالية)"
                            />
                            @error('password')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Social Links -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 md:gap-6 mt-4 md:mt-6">
                        <!-- Facebook URL -->
                        <div>
                            <label for="facebook_url" class="block text-sm font-medium text-gray-700 mb-2">
                                رابط الفيس بوك
                            </label>
                            <input
                                type="url"
                                id="facebook_url"
                                name="facebook_url"
                                value="{{ old('facebook_url', $employee->facebook_url) }}"
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
                                value="{{ old('linkedin_url', $employee->linkedin_url) }}"
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
                                value="{{ old('portfolio_url', $employee->portfolio_url) }}"
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
                        >{{ old('notes', $employee->notes) }}</textarea>
                        @error('notes')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Payment Information Section -->
                <div class="form-section space-y-4 md:space-y-6">
                    <h3 class="text-base md:text-lg font-semibold text-gray-800">بيانات التحويل</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                        <!-- Instapay Number -->
                        <div>
                            <label for="instapay_number" class="block text-sm font-medium text-gray-700 mb-2">
                                رقم انستاباي (Instapay)
                            </label>
                            <input
                                type="text"
                                id="instapay_number"
                                name="instapay_number"
                                value="{{ old('instapay_number', $employee->instapay_number ?? '') }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-colors"
                                placeholder="أدخل رقم أو معلومات انستاباي (مثل: 01234567890 أو username@instapay)"
                            />
                            <p class="mt-1 text-xs text-gray-500">يمكن إدخال رقم أو أي نص آخر متعلق بانستاباي</p>
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
                                value="{{ old('vodafone_cash_number', $employee->vodafone_cash_number) }}"
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
                <div class="form-section space-y-4 md:space-y-6">
                    <h3 class="text-base md:text-lg font-semibold text-gray-800">الدور الوظيفي والحالة</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
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
                                <option value="content_writer" {{ old('role', $employee->role) === 'content_writer' ? 'selected' : '' }}>كاتب محتوى</option>
                                <option value="ad_manager" {{ old('role', $employee->role) === 'ad_manager' ? 'selected' : '' }}>إدارة إعلانات</option>
                                <option value="designer" {{ old('role', $employee->role) === 'designer' ? 'selected' : '' }}>مصمم</option>
                                <option value="video_editor" {{ old('role', $employee->role) === 'video_editor' ? 'selected' : '' }}>مصمم فيديوهات</option>
                                <option value="page_manager" {{ old('role', $employee->role) === 'page_manager' ? 'selected' : '' }}>إدارة الصفحة</option>
                                <option value="account_manager" {{ old('role', $employee->role) === 'account_manager' ? 'selected' : '' }}>أكونت منجر</option>
                                <option value="monitor" {{ old('role', $employee->role) === 'monitor' ? 'selected' : '' }}>مونتير</option>
                                <option value="media_buyer" {{ old('role', $employee->role) === 'media_buyer' ? 'selected' : '' }}>ميديا بايرز</option>
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
                                <option value="active" {{ old('status', $employee->status) === 'active' ? 'selected' : '' }}>نشط</option>
                                <option value="inactive" {{ old('status', $employee->status) === 'inactive' ? 'selected' : '' }}>غير نشط</option>
                                <option value="pending" {{ old('status', $employee->status) === 'pending' ? 'selected' : '' }}>في الانتظار</option>
                            </select>
                            @error('status')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Role Description Section -->
                <div class="form-section space-y-4 md:space-y-6">
                    <h3 class="text-base md:text-lg font-semibold text-gray-800">وصف الدور الوظيفي</h3>
                    
                    <div class="bg-gray-50 rounded-xl p-4 md:p-6">
                        <div id="role-description" class="text-sm text-gray-700">
                            <!-- سيتم تحديث هذا المحتوى بواسطة JavaScript -->
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-center gap-3 md:gap-4 pt-6 md:pt-8 border-t border-gray-200">
                    <button type="submit" class="action-button btn-primary text-white px-6 md:px-8 py-3 md:py-4 rounded-xl md:rounded-2xl flex items-center justify-center font-medium text-base md:text-lg w-full sm:w-auto sm:min-w-[160px]">
                        <i class="fas fa-save text-base md:text-lg ml-2 md:ml-3"></i>
                        حفظ التغييرات
                    </button>
                    <a href="{{ route('employees.index') }}" class="action-button cancel-button flex items-center justify-center px-6 md:px-8 py-3 md:py-4 rounded-xl md:rounded-2xl font-medium text-base md:text-lg w-full sm:w-auto sm:min-w-[140px]">
                        إلغاء
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

@section('scripts')
<script>
$(document).ready(function() {
    // Initialize Select2
    $('.select2').select2({
        placeholder: 'اختر من القائمة',
        allowClear: true,
        dir: 'rtl',
        width: '100%',
        language: {
            noResults: function() {
                return 'لا توجد نتائج';
            },
            searching: function() {
                return 'جاري البحث...';
            }
        }
    });

    // Role descriptions
    const roleDescriptions = {
        'content_writer': 'مسؤول عن إنشاء المحتوى النصي والكتابة الإبداعية للمواقع والمنصات الرقمية',
        'ad_manager': 'مسؤول عن إدارة الحملات الإعلانية والتسويقية على المنصات الرقمية المختلفة',
        'designer': 'مسؤول عن التصميم الجرافيكي والهوية البصرية للمشاريع والعلامات التجارية',
        'video_editor': 'مسؤول عن تحرير وإنتاج المحتوى المرئي والفيديوهات التسويقية',
        'page_manager': 'مسؤول عن إدارة الصفحات والحسابات الرقمية على وسائل التواصل الاجتماعي',
        'account_manager': 'مسؤول عن إدارة الحسابات والعلاقات مع العملاء والمتابعة المستمرة',
        'monitor': 'مسؤول عن مراقبة وتحليل أداء الحملات الإعلانية والمحتوى الرقمي',
        'media_buyer': 'مسؤول عن شراء المساحات الإعلانية وإدارة الميزانيات التسويقية'
    };

    // Update role description when role changes
    $('#role').on('change', function() {
        const selectedRole = $(this).val();
        const description = roleDescriptions[selectedRole] || 'يرجى اختيار دور وظيفي لعرض الوصف';
        $('#role-description').text(description);
    });

    // Initialize with current role description
    const currentRole = $('#role').val();
    if (currentRole) {
        $('#role').trigger('change');
    }
});
</script>
@endsection
@endsection
