@extends('layouts.dashboard')

@section('title', 'تعديل المشروع')
@section('page-title', 'تعديل المشروع')
@section('page-description', 'تعديل بيانات المشروع: ' . $project->business_name)

@section('content')
<div class="container mx-auto px-4">
    <div class="max-w-4xl mx-auto space-y-6">
        <!-- Header -->
        <div class="card page-header rounded-2xl p-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="w-12 h-12 logo-gradient rounded-2xl flex items-center justify-center shadow-lg icon-spacing ml-3">
                        <i class="fas fa-edit text-white text-xl"></i>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800">تعديل المشروع</h2>
                        <p class="text-gray-600">تعديل بيانات المشروع: {{ $project->business_name }}</p>
                    </div>
                </div>
                <a href="{{ route('projects.index') }}" class="flex items-center px-4 py-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-xl transition-colors icon-spacing">
                    العودة للقائمة
                </a>
            </div>
        </div>

        <!-- Form Card -->
        <div class="card rounded-2xl p-8">
            @if ($errors->any())
                <div class="mb-6 bg-red-50 border border-red-200 rounded-xl p-4">
                    <div class="flex items-center mb-2">
                        <i class="fas fa-exclamation-triangle text-red-500 ml-2"></i>
                        <h3 class="text-red-800 font-semibold">يرجى تصحيح الأخطاء التالية:</h3>
                    </div>
                    <ul class="text-red-700 text-sm space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>• {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (session('error'))
                <div class="mb-6 bg-red-50 border border-red-200 rounded-xl p-4">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-triangle text-red-500 ml-2"></i>
                        <span class="text-red-800">{{ session('error') }}</span>
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('projects.update', $project) }}" class="space-y-8">
                @csrf
                @method('PUT')

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
                                    <option value="{{ $client->id }}" {{ (old('client_id', $project->client_id) == $client->id) ? 'selected' : '' }}>
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
                                value="{{ old('business_name', $project->business_name) }}"
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
                            وصف البيزنس
                        </label>
                        <textarea
                            id="business_description"
                            name="business_description"
                            rows="4"
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-colors"
                            placeholder="أدخل وصفاً مختصراً للبيزنس"
                        >{{ old('business_description', $project->business_description) }}</textarea>
                        @error('business_description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Social Media Links Section -->
                <div class="form-section space-y-6">
                    <h3 class="text-lg font-semibold text-gray-800">روابط وسائل التواصل الاجتماعي</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Website URL -->
                        <div>
                            <label for="website_url" class="block text-sm font-medium text-gray-700 mb-2">
                                الموقع الإلكتروني
                            </label>
                            <input
                                type="url"
                                id="website_url"
                                name="website_url"
                                value="{{ old('website_url', $project->website_url) }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-colors"
                                placeholder="https://example.com"
                            />
                            @error('website_url')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Facebook URL -->
                        <div>
                            <label for="facebook_url" class="block text-sm font-medium text-gray-700 mb-2">
                                فيسبوك
                            </label>
                            <input
                                type="url"
                                id="facebook_url"
                                name="facebook_url"
                                value="{{ old('facebook_url', $project->facebook_url) }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-colors"
                                placeholder="https://facebook.com/username"
                            />
                            @error('facebook_url')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Instagram URL -->
                        <div>
                            <label for="instagram_url" class="block text-sm font-medium text-gray-700 mb-2">
                                انستغرام
                            </label>
                            <input
                                type="url"
                                id="instagram_url"
                                name="instagram_url"
                                value="{{ old('instagram_url', $project->instagram_url) }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-colors"
                                placeholder="https://instagram.com/username"
                            />
                            @error('instagram_url')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Twitter URL -->
                        <div>
                            <label for="twitter_url" class="block text-sm font-medium text-gray-700 mb-2">
                                تويتر
                            </label>
                            <input
                                type="url"
                                id="twitter_url"
                                name="twitter_url"
                                value="{{ old('twitter_url', $project->twitter_url) }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-colors"
                                placeholder="https://twitter.com/username"
                            />
                            @error('twitter_url')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- LinkedIn URL -->
                        <div>
                            <label for="linkedin_url" class="block text-sm font-medium text-gray-700 mb-2">
                                لينكدإن
                            </label>
                            <input
                                type="url"
                                id="linkedin_url"
                                name="linkedin_url"
                                value="{{ old('linkedin_url', $project->linkedin_url) }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-colors"
                                placeholder="https://linkedin.com/company/username"
                            />
                            @error('linkedin_url')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- YouTube URL -->
                        <div>
                            <label for="youtube_url" class="block text-sm font-medium text-gray-700 mb-2">
                                يوتيوب
                            </label>
                            <input
                                type="url"
                                id="youtube_url"
                                name="youtube_url"
                                value="{{ old('youtube_url', $project->youtube_url) }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-colors"
                                placeholder="https://youtube.com/channel/username"
                            />
                            @error('youtube_url')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- TikTok URL -->
                        <div>
                            <label for="tiktok_url" class="block text-sm font-medium text-gray-700 mb-2">
                                تيك توك
                            </label>
                            <input
                                type="url"
                                id="tiktok_url"
                                name="tiktok_url"
                                value="{{ old('tiktok_url', $project->tiktok_url) }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-colors"
                                placeholder="https://tiktok.com/@username"
                            />
                            @error('tiktok_url')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>


                <!-- Authorized Persons Section -->
                <div class="form-section space-y-6">
                    <h3 class="text-lg font-semibold text-gray-800">الأشخاص الموثقين</h3>
                    <p class="text-sm text-gray-600">أضف الأشخاص الموثقين للمشروع (اختياري)</p>
                    
                    <div id="authorized-persons-container">
                        @if($project->authorized_persons && count($project->authorized_persons) > 0)
                            @foreach($project->authorized_persons as $index => $person)
                                <div class="authorized-person-item grid grid-cols-1 md:grid-cols-2 gap-4 p-4 border border-gray-200 rounded-xl mb-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">اسم الشخص</label>
                                        <input
                                            type="text"
                                            name="authorized_persons[{{ $index }}][name]"
                                            value="{{ $person['name'] ?? '' }}"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition-colors"
                                            placeholder="أدخل اسم الشخص"
                                        />
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">رقم الهاتف</label>
                                        <div class="flex gap-2">
                                            <input
                                                type="tel"
                                                name="authorized_persons[{{ $index }}][phone]"
                                                value="{{ $person['phone'] ?? '' }}"
                                                class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition-colors"
                                                placeholder="مثال: 01234567890"
                                            />
                                            <button type="button" class="remove-authorized-person px-3 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="authorized-person-item grid grid-cols-1 md:grid-cols-2 gap-4 p-4 border border-gray-200 rounded-xl">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">اسم الشخص</label>
                                    <input
                                        type="text"
                                        name="authorized_persons[0][name]"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition-colors"
                                        placeholder="أدخل اسم الشخص"
                                    />
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">رقم الهاتف</label>
                                    <input
                                        type="tel"
                                        name="authorized_persons[0][phone]"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition-colors"
                                        placeholder="مثال: +966501234567"
                                    />
                                </div>
                            </div>
                        @endif
                    </div>
                    
                    <button type="button" id="add-authorized-person" class="btn-primary text-white px-4 py-2 rounded-lg flex items-center">
                        <i class="fas fa-plus text-sm ml-2"></i>
                        إضافة شخص آخر
                    </button>
                </div>

                <!-- Project Accounts Section -->
                <div class="form-section space-y-6">
                    <h3 class="text-lg font-semibold text-gray-800">الحسابات الخاصة بالمشروع</h3>
                    <p class="text-sm text-gray-600">أضف الحسابات الخاصة بالمشروع (اختياري)</p>
                    
                    <div id="project-accounts-container">
                        @if($project->project_accounts && count($project->project_accounts) > 0)
                            @foreach($project->project_accounts as $index => $account)
                                <div class="project-account-item grid grid-cols-1 md:grid-cols-3 gap-4 p-4 border border-gray-200 rounded-xl mb-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">اسم المستخدم</label>
                                        <input
                                            type="text"
                                            name="project_accounts[{{ $index }}][username]"
                                            value="{{ $account['username'] ?? '' }}"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition-colors"
                                            placeholder="أدخل اسم المستخدم"
                                        />
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">كلمة المرور</label>
                                        <input
                                            type="password"
                                            name="project_accounts[{{ $index }}][password]"
                                            value="{{ $account['password'] ?? '' }}"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition-colors"
                                            placeholder="أدخل كلمة المرور"
                                        />
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">الرابط</label>
                                        <div class="flex gap-2">
                                            <input
                                                type="url"
                                                name="project_accounts[{{ $index }}][url]"
                                                value="{{ $account['url'] ?? '' }}"
                                                class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition-colors"
                                                placeholder="https://example.com"
                                            />
                                            <button type="button" class="remove-project-account px-3 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="project-account-item grid grid-cols-1 md:grid-cols-3 gap-4 p-4 border border-gray-200 rounded-xl">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">اسم المستخدم</label>
                                    <input
                                        type="text"
                                        name="project_accounts[0][username]"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition-colors"
                                        placeholder="أدخل اسم المستخدم"
                                    />
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">كلمة المرور</label>
                                    <input
                                        type="password"
                                        name="project_accounts[0][password]"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition-colors"
                                        placeholder="أدخل كلمة المرور"
                                    />
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">الرابط</label>
                                    <input
                                        type="url"
                                        name="project_accounts[0][url]"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition-colors"
                                        placeholder="https://example.com"
                                    />
                                </div>
                            </div>
                        @endif
                    </div>
                    
                    <button type="button" id="add-project-account" class="btn-primary text-white px-4 py-2 rounded-lg flex items-center">
                        <i class="fas fa-plus text-sm ml-2"></i>
                        إضافة حساب آخر
                    </button>
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
                                <option value="active" {{ old('status', $project->status) === 'active' ? 'selected' : '' }}>نشط</option>
                                <option value="inactive" {{ old('status', $project->status) === 'inactive' ? 'selected' : '' }}>غير نشط</option>
                                <option value="pending" {{ old('status', $project->status) === 'pending' ? 'selected' : '' }}>في الانتظار</option>
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
                        حفظ التغييرات
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

@section('scripts')
<script>
$(document).ready(function() {
    console.log('Project Edit Script Loaded');
    
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

    // Add Authorized Person
    let authorizedPersonIndex = {{ $project->authorized_persons ? count($project->authorized_persons) : 1 }};
    $('#add-authorized-person').on('click', function(e) {
        e.preventDefault();
        console.log('Adding authorized person');
        const newPersonHtml = `
            <div class="authorized-person-item grid grid-cols-1 md:grid-cols-2 gap-4 p-4 border border-gray-200 rounded-xl mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">اسم الشخص</label>
                    <input
                        type="text"
                        name="authorized_persons[${authorizedPersonIndex}][name]"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition-colors"
                        placeholder="أدخل اسم الشخص"
                    />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">رقم الهاتف</label>
                    <div class="flex gap-2">
                        <input
                            type="tel"
                            name="authorized_persons[${authorizedPersonIndex}][phone]"
                            class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition-colors"
                            placeholder="مثال: +966501234567"
                        />
                        <button type="button" class="remove-authorized-person px-3 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;
        $('#authorized-persons-container').append(newPersonHtml);
        authorizedPersonIndex++;
    });

    // Remove Authorized Person
    $(document).on('click', '.remove-authorized-person', function(e) {
        e.preventDefault();
        console.log('Removing authorized person');
        $(this).closest('.authorized-person-item').remove();
    });

    // Add Project Account
    let projectAccountIndex = {{ $project->project_accounts ? count($project->project_accounts) : 1 }};
    $('#add-project-account').on('click', function(e) {
        e.preventDefault();
        console.log('Adding project account');
        const newAccountHtml = `
            <div class="project-account-item grid grid-cols-1 md:grid-cols-3 gap-4 p-4 border border-gray-200 rounded-xl mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">اسم المستخدم</label>
                    <input
                        type="text"
                        name="project_accounts[${projectAccountIndex}][username]"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition-colors"
                        placeholder="أدخل اسم المستخدم"
                    />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">كلمة المرور</label>
                    <input
                        type="password"
                        name="project_accounts[${projectAccountIndex}][password]"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition-colors"
                        placeholder="أدخل كلمة المرور"
                    />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">الرابط</label>
                    <div class="flex gap-2">
                        <input
                            type="url"
                            name="project_accounts[${projectAccountIndex}][url]"
                            class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition-colors"
                            placeholder="https://example.com"
                        />
                        <button type="button" class="remove-project-account px-3 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;
        $('#project-accounts-container').append(newAccountHtml);
        projectAccountIndex++;
    });

    // Remove Project Account
    $(document).on('click', '.remove-project-account', function(e) {
        e.preventDefault();
        console.log('Removing project account');
        $(this).closest('.project-account-item').remove();
    });
});
</script>
@endsection
