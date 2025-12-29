@extends('layouts.dashboard')

@section('title', 'تعديل المهمة')
@section('page-title', 'تعديل المهمة')
@section('page-description', 'تعديل بيانات المهمة')

@section('content')
<div class="container mx-auto px-4" data-generate-description-url="{{ route('tasks.generate-description') }}">
    <form id="edit-task-form" method="POST" action="{{ route('monthly-plans.tasks.update', [$monthlyPlan->id, $task->id]) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <div class="flex gap-6 relative">
            <!-- Fixed Sidebar -->
            <div class="hidden lg:block w-80 flex-shrink-0">
                <div class="sticky top-6 space-y-4">
                    <div class="card rounded-2xl p-6 bg-gradient-to-br from-indigo-50 to-purple-50 border border-indigo-200">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                            <span class="material-icons text-indigo-600 ml-2">info</span>
                            معلومات المهمة
                        </h3>
                        
                        <div class="space-y-4">
                        <!-- الهدف -->
                        <div>
                            <label for="goal_id" class="block text-sm font-medium text-gray-700 mb-2">
                                الهدف
                            </label>
                            <select 
                                id="goal_id" 
                                name="goal_id"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-colors text-sm"
                            >
                                <option value="">اختر الهدف (اختياري)</option>
                                @foreach($goals as $goal)
                                    <option value="{{ $goal->id }}" {{ old('goal_id', $task->goal_id) == $goal->id ? 'selected' : '' }}>
                                        {{ $goal->goal_name }} ({{ $goal->target_value }})
                                    </option>
                                @endforeach
                            </select>
                            @error('goal_id')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <!-- تعيين إلى -->
                        <div>
                            <label for="assigned_to" class="block text-sm font-medium text-gray-700 mb-2">
                                تعيين إلى
                            </label>
                            <select 
                                id="assigned_to" 
                                name="assigned_to"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-colors text-sm"
                            >
                                <option value="">قائمة المهام</option>
                                @foreach($employees as $employee)
                                    <option value="{{ $employee->id }}" {{ old('assigned_to', $task->assigned_to) == $employee->id ? 'selected' : '' }}>
                                        {{ $employee->name }} - {{ $employee->role_badge }}
                                    </option>
                                @endforeach
                            </select>
                            @error('assigned_to')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <!-- تاريخ الانتهاء -->
                        <div>
                            <label for="due_date" class="block text-sm font-medium text-gray-700 mb-2">
                                تاريخ الانتهاء
                            </label>
                            <input 
                                type="date" 
                                id="due_date" 
                                name="due_date" 
                                value="{{ old('due_date', $task->due_date ? $task->due_date->format('Y-m-d') : '') }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-colors text-sm"
                            >
                            @error('due_date')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <!-- تاريخ النشر -->
                        <div>
                            <label for="publish_date" class="block text-sm font-medium text-gray-700 mb-2">
                                تاريخ النشر
                            </label>
                            <input 
                                type="date" 
                                id="publish_date" 
                                name="publish_date" 
                                value="{{ old('publish_date', $task->publish_date ? $task->publish_date->format('Y-m-d') : '') }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-colors text-sm"
                            >
                            @error('publish_date')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <!-- الحالة -->
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                                الحالة <span class="text-red-500">*</span>
                            </label>
                            <select 
                                id="status" 
                                name="status"
                                required
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-colors text-sm"
                            >
                                <option value="todo" {{ old('status', $task->status) == 'todo' ? 'selected' : '' }}>مهام</option>
                                <option value="in_progress" {{ old('status', $task->status) == 'in_progress' ? 'selected' : '' }}>قيد التنفيذ</option>
                                <option value="review" {{ old('status', $task->status) == 'review' ? 'selected' : '' }}>قيد المراجعة</option>
                                <option value="done" {{ old('status', $task->status) == 'done' ? 'selected' : '' }}>مكتملة</option>
                                <option value="publish" {{ old('status', $task->status) == 'publish' ? 'selected' : '' }}>نشر</option>
                                <option value="archived" {{ old('status', $task->status) == 'archived' ? 'selected' : '' }}>أرشيف</option>
                            </select>
                            @error('status')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <!-- اللون -->
                        <div>
                            <label for="color" class="block text-sm font-medium text-gray-700 mb-2">
                                اللون
                            </label>
                            <div class="flex items-center gap-2">
                                <input 
                                    type="color" 
                                    id="color" 
                                    name="color" 
                                    value="{{ old('color', $task->color ?? '#6366f1') }}"
                                    class="w-16 h-10 border border-gray-300 rounded-xl cursor-pointer"
                                >
                                <input 
                                    type="text" 
                                    id="color-hex" 
                                    value="{{ old('color', $task->color ?? '#6366f1') }}" 
                                    placeholder="#6366f1"
                                    class="flex-1 px-3 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-colors text-sm"
                                    pattern="^#[A-Fa-f0-9]{6}$"
                                >
                            </div>
                            @error('color')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="flex-1 max-w-4xl space-y-6">
        <!-- Success/Error Messages at Top -->
        @if (session('success'))
            <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg flex items-center shadow-sm">
                <span class="material-icons ml-2">check_circle</span>
                <span class="font-semibold">{{ session('success') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg flex items-center shadow-sm">
                <span class="material-icons ml-2">error</span>
                <span class="font-semibold">{{ session('error') }}</span>
            </div>
        @endif

        <!-- Header -->
        <div class="card page-header rounded-2xl p-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="w-12 h-12 logo-gradient rounded-2xl flex items-center justify-center shadow-lg icon-spacing ml-3">
                        <span class="material-icons text-white text-xl">edit</span>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800">تعديل المهمة</h2>
                        <p class="text-gray-600">تعديل بيانات المهمة: {{ $task->title }}</p>
                    </div>
                </div>
                <a href="{{ route('monthly-plans.show', $monthlyPlan) }}" class="flex items-center px-4 py-2 text-gray-600 hover:text-gray-800 hover:bg-gray-100 rounded-xl transition-colors">
                    <span class="material-icons text-sm ml-2">arrow_back</span>
                    العودة للخطة
                </a>
            </div>
        </div>

        <!-- Form Card -->
        <div class="card rounded-2xl p-8">
            @if (session('success'))
                <div class="mb-6 bg-green-50 border border-green-200 rounded-xl p-4">
                    <div class="flex items-center">
                        <span class="material-icons text-green-500 ml-2">check_circle</span>
                        <span class="text-green-800 font-semibold">{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            @if (session('error'))
                <div class="mb-6 bg-red-50 border border-red-200 rounded-xl p-4">
                    <div class="flex items-center">
                        <span class="material-icons text-red-500 ml-2">error</span>
                        <span class="text-red-800 font-semibold">{{ session('error') }}</span>
                    </div>
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-6 bg-red-50 border border-red-200 rounded-xl p-4">
                    <div class="flex items-center mb-2">
                        <span class="material-icons text-red-500 ml-2">error</span>
                        <h3 class="text-red-800 font-semibold">يرجى تصحيح الأخطاء التالية:</h3>
                    </div>
                    <ul class="text-red-700 text-sm space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>• {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

                <!-- Title -->
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                        عنوان المهمة <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="title" 
                        name="title" 
                        value="{{ old('title', $task->title) }}" 
                        required
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-colors"
                        placeholder="أدخل عنوان المهمة"
                    >
                    @error('title')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Idea Group -->
                <div class="bg-gray-50 rounded-2xl p-6 border border-gray-200 mt-4">
                    <div class="flex items-center justify-between mb-4 cursor-pointer" onclick="toggleIdeaSection()">
                        <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                            <span class="material-icons text-purple-600 ml-2">lightbulb</span>
                            الفكرة والتوصيات
                        </h3>
                        <button type="button" class="text-gray-600 hover:text-gray-800 transition-colors" id="idea-section-toggle">
                            <span class="material-icons" id="idea-section-icon">expand_less</span>
                        </button>
                    </div>
                    
                    <div id="idea-section-content">
                    <!-- توصيات الفكرة -->
                    <div class="mb-4">
                        <label for="idea_recommendations" class="block text-sm font-medium text-gray-700 mb-2">
                            توصيات الفكرة
                    </label>
                    <textarea 
                            id="idea_recommendations" 
                            name="idea_recommendations" 
                            rows="4"
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-colors"
                            placeholder="توصيات أو ملاحظات حول الفكرة (اختياري)"
                        >{!! old('idea_recommendations', $task->task_data['idea_recommendations'] ?? '') !!}</textarea>
                        @error('idea_recommendations')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    </div>
                    
                    <!-- الأزرار -->
                    <div class="mb-4 flex items-center gap-2 flex-wrap">
                    <button 
                        type="button" 
                            id="show-prompt-btn" 
                            class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed text-sm"
                            onclick="showPrompt()"
                        >
                            <span class="material-icons text-sm" id="show-prompt-icon">visibility</span>
                            <span id="show-prompt-text">اظهر البرومبت</span>
                            <span class="material-icons text-sm animate-spin hidden" id="show-prompt-spinner">sync</span>
                        </button>
                        <button 
                            type="button" 
                            id="copy-idea-btn" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed text-sm"
                            onclick="copyIdea()"
                            title="نسخ الفكرة"
                        >
                            <span class="material-icons text-sm" id="copy-idea-icon">content_copy</span>
                            <span id="copy-idea-text">نسخ الفكرة</span>
                        </button>
                        <button 
                            type="button" 
                            id="suggest-ideas-btn" 
                            class="px-4 py-2 bg-gradient-to-r from-purple-600 to-indigo-600 text-white rounded-lg hover:from-purple-700 hover:to-indigo-700 transition-colors flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed text-sm"
                            onclick="suggestIdeas()"
                        >
                            <span class="material-icons text-sm" id="suggest-icon">lightbulb</span>
                            <span id="suggest-text">اقتراح أفكار</span>
                            <span class="material-icons text-sm animate-spin hidden" id="suggest-spinner">sync</span>
                        </button>
                        <button 
                            type="button" 
                            id="view-idea-btn" 
                            class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed text-sm"
                            onclick="viewIdea()"
                            title="عرض الفكرة في نافذة منفصلة"
                        >
                            <span class="material-icons text-sm" id="view-idea-icon">zoom_in</span>
                            <span id="view-idea-text">عرض الفكرة</span>
                    </button>
                </div>

                    <!-- الفكرة -->
                    <div>
                        <label for="idea" class="block text-sm font-medium text-gray-700 mb-2">
                            الفكرة
                    </label>
                        <textarea 
                            id="idea" 
                            name="idea" 
                            rows="6"
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-colors text-lg leading-relaxed"
                            style="font-size: 1.5rem; line-height: 2; font-family: 'Cairo', 'Segoe UI', Tahoma, sans-serif;"
                            placeholder="أدخل فكرة المهمة هنا... يمكنك كتابة الفكرة بشكل مفصل مع مراعاة التوصيات المذكورة أعلاه"
                        >{!! old('idea', $task->idea) !!}</textarea>
                        @error('idea')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    </div>
                </div>

                <!-- Post and Recommendations -->
                <div class="bg-gradient-to-br from-purple-50 to-indigo-50 rounded-2xl p-6 border border-purple-200 mt-6">
                    <div class="flex items-center justify-between mb-4 cursor-pointer" onclick="togglePostSection()">
                        <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                            <span class="material-icons text-purple-600 ml-2">article</span>
                            البوست والتوصيات
                        </h3>
                        <button type="button" class="text-gray-600 hover:text-gray-800 transition-colors" id="post-section-toggle">
                            <span class="material-icons" id="post-section-icon">expand_less</span>
                        </button>
                    </div>
                    
                    <div id="post-section-content">
                    <!-- توصيات البوست -->
                    <div class="mb-4">
                        <label for="post_recommendations" class="block text-sm font-medium text-gray-700 mb-2">
                            توصيات البوست
                        </label>
                        <textarea 
                            id="post_recommendations" 
                            name="post_recommendations" 
                            rows="4"
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors"
                            placeholder="توصيات أو ملاحظات حول البوست (اختياري)"
                        >{!! old('post_recommendations', $task->task_data['post_recommendations'] ?? '') !!}</textarea>
                        @error('post_recommendations')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                    <!-- خيارات البوست -->
                    <div class="space-y-6 mb-4">
                        <!-- المنصة -->
                    <div>
                            <label class="block text-sm font-medium text-gray-700 mb-3">
                                المنصة
                        </label>
                            <div class="flex flex-wrap gap-3">
                                <input type="hidden" id="post_platform" name="post_platform" value="{{ old('post_platform', $task->task_data['post_platform'] ?? '') }}">
                                <button 
                                    type="button"
                                    class="platform-card px-6 py-4 border-2 rounded-xl transition-all flex items-center gap-3 {{ old('post_platform', $task->task_data['post_platform'] ?? '') == 'facebook' ? 'border-blue-500 bg-blue-50' : 'border-gray-200 hover:border-purple-300 bg-white' }}"
                                    data-platform="facebook"
                                    onclick="selectPlatform('facebook')"
                                >
                                    <span class="material-icons text-blue-600">facebook</span>
                                    <span class="font-medium text-gray-800">فيسبوك</span>
                                </button>
                                <button 
                                    type="button"
                                    class="platform-card px-6 py-4 border-2 rounded-xl transition-all flex items-center gap-3 {{ old('post_platform', $task->task_data['post_platform'] ?? '') == 'linkedin' ? 'border-blue-500 bg-blue-50' : 'border-gray-200 hover:border-purple-300 bg-white' }}"
                                    data-platform="linkedin"
                                    onclick="selectPlatform('linkedin')"
                                >
                                    <span class="material-icons text-blue-700">work</span>
                                    <span class="font-medium text-gray-800">لينكد إن</span>
                                </button>
                                <button 
                                    type="button"
                                    class="platform-card px-6 py-4 border-2 rounded-xl transition-all flex items-center gap-3 {{ old('post_platform', $task->task_data['post_platform'] ?? '') == 'tiktok' ? 'border-blue-500 bg-blue-50' : 'border-gray-200 hover:border-purple-300 bg-white' }}"
                                    data-platform="tiktok"
                                    onclick="selectPlatform('tiktok')"
                                >
                                    <span class="material-icons text-gray-900">music_note</span>
                                    <span class="font-medium text-gray-800">تيك توك</span>
                                </button>
                                <button 
                                    type="button"
                                    class="platform-card px-6 py-4 border-2 rounded-xl transition-all flex items-center gap-3 {{ old('post_platform', $task->task_data['post_platform'] ?? '') == 'instagram' ? 'border-blue-500 bg-blue-50' : 'border-gray-200 hover:border-purple-300 bg-white' }}"
                                    data-platform="instagram"
                                    onclick="selectPlatform('instagram')"
                                >
                                    <span class="material-icons text-pink-600">photo_camera</span>
                                    <span class="font-medium text-gray-800">إنستجرام</span>
                                </button>
                                <button 
                                    type="button"
                                    class="platform-card px-6 py-4 border-2 rounded-xl transition-all flex items-center gap-3 {{ old('post_platform', $task->task_data['post_platform'] ?? '') == 'twitter' ? 'border-blue-500 bg-blue-50' : 'border-gray-200 hover:border-purple-300 bg-white' }}"
                                    data-platform="twitter"
                                    onclick="selectPlatform('twitter')"
                                >
                                    <span class="material-icons text-blue-400">chat</span>
                                    <span class="font-medium text-gray-800">تويتر</span>
                                </button>
                            </div>
                    </div>

                        <!-- نوع البوست -->
                    <div>
                            <label class="block text-sm font-medium text-gray-700 mb-3">
                                نوع البوست
                        </label>
                            <div class="flex flex-wrap gap-3">
                                <input type="hidden" id="post_type" name="post_type" value="{{ old('post_type', $task->task_data['post_type'] ?? '') }}">
                                <button 
                                    type="button"
                                    class="post-type-card px-6 py-4 border-2 rounded-xl transition-all flex items-center gap-3 {{ old('post_type', $task->task_data['post_type'] ?? '') == 'text' ? 'border-purple-500 bg-purple-50' : 'border-gray-200 hover:border-purple-300 bg-white' }}"
                                    data-type="text"
                                    onclick="selectPostType('text')"
                                >
                                    <span class="material-icons text-purple-600">article</span>
                                    <span class="font-medium text-gray-800">نص</span>
                                </button>
                                <button 
                                    type="button"
                                    class="post-type-card px-6 py-4 border-2 rounded-xl transition-all flex items-center gap-3 {{ old('post_type', $task->task_data['post_type'] ?? '') == 'video' ? 'border-purple-500 bg-purple-50' : 'border-gray-200 hover:border-purple-300 bg-white' }}"
                                    data-type="video"
                                    onclick="selectPostType('video')"
                                >
                                    <span class="material-icons text-red-600">videocam</span>
                                    <span class="font-medium text-gray-800">فيديو</span>
                                </button>
                                <button 
                                    type="button"
                                    class="post-type-card px-6 py-4 border-2 rounded-xl transition-all flex items-center gap-3 {{ old('post_type', $task->task_data['post_type'] ?? '') == 'carousel' ? 'border-purple-500 bg-purple-50' : 'border-gray-200 hover:border-purple-300 bg-white' }}"
                                    data-type="carousel"
                                    onclick="selectPostType('carousel')"
                                >
                                    <span class="material-icons text-orange-600">view_carousel</span>
                                    <span class="font-medium text-gray-800">كروسر</span>
                                </button>
                                <button 
                                    type="button"
                                    class="post-type-card px-6 py-4 border-2 rounded-xl transition-all flex items-center gap-3 {{ old('post_type', $task->task_data['post_type'] ?? '') == 'reels' ? 'border-purple-500 bg-purple-50' : 'border-gray-200 hover:border-purple-300 bg-white' }}"
                                    data-type="reels"
                                    onclick="selectPostType('reels')"
                                >
                                    <span class="material-icons text-pink-600">movie</span>
                                    <span class="font-medium text-gray-800">ريلز</span>
                                </button>
                                <button 
                                    type="button"
                                    class="post-type-card px-6 py-4 border-2 rounded-xl transition-all flex items-center gap-3 {{ old('post_type', $task->task_data['post_type'] ?? '') == 'story' ? 'border-purple-500 bg-purple-50' : 'border-gray-200 hover:border-purple-300 bg-white' }}"
                                    data-type="story"
                                    onclick="selectPostType('story')"
                                >
                                    <span class="material-icons text-indigo-600">auto_stories</span>
                                    <span class="font-medium text-gray-800">ستوري</span>
                                </button>
                    </div>
                </div>

                        <!-- عدد الكلمات -->
                    <div>
                            <label for="post_word_count" class="block text-sm font-medium text-gray-700 mb-3">
                                عدد الكلمات (حد أقصى 700 كلمة)
                        </label>
                            <div class="bg-white border-2 border-gray-200 rounded-xl p-4 hover:border-purple-300 transition-colors">
                        <input 
                                    type="number" 
                                    id="post_word_count" 
                                    name="post_word_count"
                                    min="0"
                                    max="700"
                                    value="{{ old('post_word_count', $task->task_data['post_word_count'] ?? '') }}"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors text-lg font-medium"
                                    placeholder="أدخل عدد الكلمات (0-700)"
                                />
                                <p class="text-xs text-gray-500 mt-2">الحد الأقصى: 700 كلمة</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- الأزرار -->
                    <div class="mb-4 flex items-center gap-2 flex-wrap">
                        <button 
                            type="button" 
                            id="show-post-prompt-btn" 
                            class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed text-sm"
                            onclick="showPostPrompt()"
                        >
                            <span class="material-icons text-sm" id="show-post-prompt-icon">visibility</span>
                            <span id="show-post-prompt-text">اظهر البرومبت</span>
                            <span class="material-icons text-sm animate-spin hidden" id="show-post-prompt-spinner">sync</span>
                        </button>
                        <button 
                            type="button" 
                            id="suggest-post-btn" 
                            class="px-4 py-2 bg-gradient-to-r from-purple-600 to-indigo-600 text-white rounded-lg hover:from-purple-700 hover:to-indigo-700 transition-colors flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed text-sm"
                            onclick="suggestPost()"
                        >
                            <span class="material-icons text-sm" id="suggest-post-icon">auto_awesome</span>
                            <span id="suggest-post-text">اقتراح البوست</span>
                            <span class="material-icons text-sm animate-spin hidden" id="suggest-post-spinner">sync</span>
                        </button>
                        <button 
                            type="button" 
                            id="copy-post-btn" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed text-sm"
                            onclick="copyPost()"
                            title="نسخ البوست"
                        >
                            <span class="material-icons text-sm" id="copy-post-icon">content_copy</span>
                            <span id="copy-post-text">نسخ البوست</span>
                        </button>
                    </div>
                    
                    <!-- البوست -->
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label for="description" class="block text-sm font-medium text-gray-700">
                                البوست
                            </label>
                            <button 
                                type="button" 
                                id="view-post-btn" 
                                class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed text-sm"
                                onclick="viewPost()"
                                title="عرض البوست في نافذة منفصلة"
                            >
                                <span class="material-icons text-sm" id="view-post-icon">zoom_in</span>
                                <span id="view-post-text">عرض البوست</span>
                            </button>
                        </div>
                        <div id="description-wrapper" class="relative">
                            <textarea 
                                id="description" 
                                name="description" 
                                rows="6"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors text-lg leading-relaxed"
                                style="font-size: 1.125rem; line-height: 1.75; font-family: 'Cairo', 'Segoe UI', Tahoma, sans-serif;"
                                placeholder="أدخل البوست هنا..."
                            >{!! old('description', $task->description) !!}</textarea>
                        </div>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    </div>
                </div>

                <!-- التصميم -->
                <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-2xl p-6 border border-green-200 mt-6">
                    <div class="flex items-center justify-between mb-4 cursor-pointer" onclick="toggleDesignSection()">
                        <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                            <span class="material-icons text-green-600 ml-2">palette</span>
                            التصميم
                        </h3>
                        <button type="button" class="text-gray-600 hover:text-gray-800 transition-colors" id="design-section-toggle">
                            <span class="material-icons" id="design-section-icon">expand_less</span>
                        </button>
                    </div>
                    
                    <div id="design-section-content">
                    <!-- توصيات التصميم -->
                    <div class="mb-4">
                        <label for="design_recommendations" class="block text-sm font-medium text-gray-700 mb-2">
                            توصيات التصميم
                        </label>
                        <textarea 
                            id="design_recommendations" 
                            name="design_recommendations" 
                            rows="4"
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors"
                            placeholder="توصيات أو ملاحظات حول التصميم (اختياري)"
                        >{!! old('design_recommendations', $task->task_data['design_recommendations'] ?? '') !!}</textarea>
                        @error('design_recommendations')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- الأزرار -->
                    <div class="mb-4 flex items-center gap-2 flex-wrap">
                        <button 
                            type="button" 
                            id="show-design-prompt-btn" 
                            class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed text-sm"
                            onclick="showDesignPrompt()"
                        >
                            <span class="material-icons text-sm" id="show-design-prompt-icon">visibility</span>
                            <span id="show-design-prompt-text">اظهر البرومبت</span>
                            <span class="material-icons text-sm animate-spin hidden" id="show-design-prompt-spinner">sync</span>
                        </button>
                        <button 
                            type="button" 
                            id="suggest-design-btn" 
                            class="px-4 py-2 bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-lg hover:from-green-700 hover:to-emerald-700 transition-colors flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed text-sm"
                            onclick="suggestDesign()"
                        >
                            <span class="material-icons text-sm" id="suggest-design-icon">auto_awesome</span>
                            <span id="suggest-design-text">اقترح تصميم</span>
                            <span class="material-icons text-sm animate-spin hidden" id="suggest-design-spinner">sync</span>
                        </button>
                        <button 
                            type="button" 
                            id="copy-design-btn" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed text-sm"
                            onclick="copyDesign()"
                            title="نسخ التصميم"
                        >
                            <span class="material-icons text-sm" id="copy-design-icon">content_copy</span>
                            <span id="copy-design-text">نسخ التصميم</span>
                        </button>
                    </div>
                    
                    <!-- التصميم -->
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label for="design" class="block text-sm font-medium text-gray-700">
                                التصميم
                        </label>
                            <button 
                                type="button" 
                                id="view-design-btn" 
                                class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed text-sm"
                                onclick="viewDesign()"
                                title="عرض التصميم في نافذة منفصلة"
                            >
                                <span class="material-icons text-sm" id="view-design-icon">zoom_in</span>
                                <span id="view-design-text">عرض التصميم</span>
                            </button>
                        </div>
                        <div id="design-wrapper" class="relative">
                            <textarea 
                                id="design" 
                                name="design" 
                                rows="6"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors text-lg leading-relaxed"
                                style="font-size: 1.125rem; line-height: 1.75; font-family: 'Cairo', 'Segoe UI', Tahoma, sans-serif;"
                                placeholder="أدخل التصميم هنا..."
                            >{!! old('design', $task->design) !!}</textarea>
                        </div>
                        @error('design')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    </div>
                </div>

                <!-- Work Design Button -->
                <div class="form-section space-y-6 mb-6">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                            <span class="material-icons text-blue-600 ml-2">link</span>
                            روابط هامة
                        </h3>
                        <button
                            type="button"
                            id="work-design-btn"
                            class="px-4 py-2 bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-lg hover:from-green-700 hover:to-emerald-700 transition-colors flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed text-sm"
                            onclick="generateDesignImage()"
                        >
                            <span class="material-icons text-sm" id="work-design-icon">auto_awesome</span>
                            <span id="work-design-text">عمل التصميم</span>
                            <span class="material-icons text-sm animate-spin hidden" id="work-design-spinner">sync</span>
                        </button>
                    </div>
                </div>

                <!-- Important Links Section -->
                <div class="form-section space-y-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800 mb-2">روابط هامة</h3>
                            <p class="text-sm text-gray-600 mb-4">يمكنك إضافة روابط مهمة متعلقة بالمهمة (اختياري)</p>
                        </div>
                    </div>
                    
                    <div id="links-container" class="space-y-4">
                        @php
                            $existingLinks = old('links', $task->task_data['links'] ?? []);
                            if (empty($existingLinks)) {
                                $existingLinks = [['title' => '', 'url' => '']];
                            }
                        @endphp
                        @foreach($existingLinks as $index => $link)
                            <div class="link-item p-4 border border-gray-200 rounded-xl">
                                <div class="flex items-center justify-between mb-3">
                                    <h4 class="text-sm font-medium text-gray-700">رابط {{ $index + 1 }}</h4>
                                    <button type="button" class="remove-link text-red-500 hover:text-red-700" style="{{ count($existingLinks) > 1 ? '' : 'display: none;' }}">
                                        <span class="material-icons text-sm">delete</span>
                                    </button>
                                </div>
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">الرابط</label>
                                        <input
                                            type="url"
                                            name="links[{{ $index }}][url]"
                                            value="{{ old("links.{$index}.url", $link['url'] ?? '') }}"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
                                            placeholder="https://example.com"
                                        />
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">عنوان الرابط</label>
                                        <input
                                            type="text"
                                            name="links[{{ $index }}][title]"
                                            value="{{ old("links.{$index}.title", $link['title'] ?? '') }}"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
                                            placeholder="مثال: رابط الموقع"
                                        />
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    <button type="button" id="add-link-btn" class="text-primary hover:text-primary-700 font-medium flex items-center">
                        <span class="material-icons text-sm ml-2">add</span>
                        إضافة رابط آخر
                    </button>
                    @error('links.*')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Attachments Section -->
                <div class="form-section space-y-6">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800 mb-2">المرفقات</h3>
                        <p class="text-sm text-gray-600 mb-4">يمكنك إرفاق صور أو ملفات بالمهمة (اختياري)</p>
                    </div>
                    
                    <!-- Existing Attachments -->
                    @if($task->files && $task->files->count() > 0)
                        <div class="mb-6">
                            <h4 class="text-sm font-medium text-gray-700 mb-3">المرفقات الحالية</h4>
                            <div class="space-y-3">
                                @foreach($task->files as $file)
                                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border border-gray-200">
                                        <div class="flex items-center flex-1 min-w-0">
                                            <span class="material-icons text-blue-500 ml-3">{{ $file->file_icon }}</span>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm font-medium text-gray-800 truncate">{{ $file->file_name }}</p>
                                                <p class="text-xs text-gray-500">{{ $file->formatted_file_size }}</p>
                                            </div>
                                        </div>
                                        <div class="flex items-center space-x-2 rtl:space-x-reverse">
                                            <a 
                                                href="{{ route('monthly-plans.tasks.files.view', [$monthlyPlan, $task, $file]) }}" 
                                                target="_blank"
                                                class="text-green-600 hover:text-green-800 p-1"
                                                title="عرض"
                                            >
                                                <span class="material-icons text-sm">visibility</span>
                                            </a>
                                            <a 
                                                href="{{ route('monthly-plans.tasks.files.download', [$monthlyPlan, $task, $file]) }}" 
                                                class="text-blue-600 hover:text-blue-800 p-1"
                                                title="تحميل"
                                            >
                                                <span class="material-icons text-sm">download</span>
                                            </a>
                                            <form 
                                                action="{{ route('monthly-plans.tasks.files.delete', [$monthlyPlan, $task, $file]) }}" 
                                                method="POST" 
                                                class="inline"
                                                onsubmit="return confirm('هل أنت متأكد من حذف هذا الملف؟');"
                                            >
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-800 p-1" title="حذف">
                                                    <span class="material-icons text-sm">delete</span>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                    
                    <!-- New Attachments -->
                    <div>
                        <h4 class="text-sm font-medium text-gray-700 mb-3">إضافة مرفقات جديدة</h4>
                        <div id="attachments-container" class="space-y-4">
                            <div class="attachment-item border-2 border-dashed border-gray-300 rounded-xl p-6 bg-gray-50">
                                <div class="flex items-center justify-between">
                                    <div class="flex-1">
                                        <label for="attachments_0" class="cursor-pointer">
                                            <div class="flex items-center justify-center py-4">
                                                <div class="text-center">
                                                    <span class="material-icons text-5xl text-gray-400 mb-2">attach_file</span>
                                                    <p class="text-sm text-gray-600">اضغط لاختيار ملف أو اسحب الملف هنا</p>
                                                    <p class="text-xs text-gray-500 mt-1">الصور: JPG, PNG, GIF | الملفات: PDF, DOC, XLS, ZIP (حد أقصى 10MB)</p>
                                                </div>
                                            </div>
                                            <input 
                                                type="file" 
                                                id="attachments_0" 
                                                name="attachments[]" 
                                                class="hidden attachment-input"
                                                accept=".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx,.xls,.xlsx,.zip,.rar"
                                                data-index="0"
                                            >
                                        </label>
                                    </div>
                                    <button type="button" onclick="removeAttachment(0)" class="text-red-500 hover:text-red-700 ml-4 remove-attachment-btn" style="display: none;">
                                        <span class="material-icons">delete</span>
                                    </button>
                                </div>
                                <div id="file-info-0" class="mt-3 p-3 bg-white rounded-lg border border-gray-200 hidden">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center flex-1">
                                            <span class="material-icons text-blue-500 ml-2" id="file-icon-0">attach_file</span>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm font-medium text-gray-800 truncate" id="file-name-0"></p>
                                                <p class="text-xs text-gray-500" id="file-size-0"></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <button type="button" id="add-attachment-btn" class="mt-4 text-primary hover:text-primary-700 font-medium flex items-center">
                            <span class="material-icons text-sm ml-2">add</span>
                            إضافة مرفق آخر
                        </button>
                        @error('attachments.*')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex items-center justify-between pt-6 border-t border-gray-200 rtl-spacing">
                    <a href="{{ route('monthly-plans.show', $monthlyPlan) }}" class="px-6 py-3 border border-gray-300 rounded-xl text-gray-700 hover:bg-gray-50 transition-colors">
                        إلغاء
                    </a>
                    <button type="submit" class="btn-primary text-white px-8 py-3 rounded-xl flex items-center">
                        <span class="material-icons text-sm ml-2">save</span>
                        حفظ التغييرات
                    </button>
                </div>
        </div>
    </div>
    </form>
</div>

@endsection

@section('styles')
<!-- Quill Editor CSS -->
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<style>
    .ql-container.ql-snow {
        direction: rtl;
        font-family: 'Cairo', Arial, sans-serif;
        border: 1px solid #e5e7eb;
        border-radius: 0.75rem;
    }
    .ql-editor {
        min-height: 300px;
        font-family: 'Cairo', Arial, sans-serif;
        direction: rtl;
        text-align: right;
    }
    .ql-container {
        direction: rtl;
        font-family: 'Cairo', Arial, sans-serif;
    }
    .ql-toolbar.ql-snow {
        direction: rtl;
        border: 1px solid #e5e7eb;
        border-top-right-radius: 0.75rem;
        border-top-left-radius: 0.75rem;
        border-bottom: none;
    }
    #description-editor .ql-container.ql-snow {
        border-top: none;
        border-bottom-right-radius: 0.75rem;
        border-bottom-left-radius: 0.75rem;
    }
    #idea-editor .ql-container.ql-snow {
        border-top: none;
        border-bottom-right-radius: 0.75rem;
        border-bottom-left-radius: 0.75rem;
    }
    
    /* تحسينات خاصة بحقل الفكرة */
    #idea-editor .ql-editor {
        font-size: 1.5rem !important;
        line-height: 2.2 !important;
        font-family: 'Cairo', 'Segoe UI', 'Tahoma', sans-serif !important;
        direction: rtl !important;
        text-align: right !important;
        padding: 1.25rem !important;
        min-height: 250px !important;
        letter-spacing: 0.01em;
    }
    
    #idea-editor .ql-editor p {
        margin-bottom: 1.25rem !important;
        margin-top: 0.75rem !important;
        font-size: 1.5rem !important;
        line-height: 2.2 !important;
        font-family: 'Cairo', 'Segoe UI', 'Tahoma', sans-serif !important;
    }
    
    #idea-editor .ql-editor p:first-child {
        margin-top: 0 !important;
    }
    
    #idea-editor .ql-editor p:last-child {
        margin-bottom: 0 !important;
    }
    
    #idea-editor .ql-editor.ql-blank::before {
        font-size: 1.125rem !important;
        font-style: normal !important;
        color: #9ca3af !important;
        font-family: 'Cairo', 'Segoe UI', 'Tahoma', sans-serif !important;
        direction: rtl !important;
        text-align: right !important;
        left: auto !important;
        right: 1.25rem !important;
    }
    
    #idea-editor .ql-editor ul,
    #idea-editor .ql-editor ol {
        padding-right: 1.5rem;
        padding-left: 0;
        margin-bottom: 1.25rem;
    }
    
    #idea-editor .ql-editor li {
        margin-bottom: 0.75rem;
        font-size: 1.5rem;
        line-height: 2.2;
    }
    
    /* تحسين حجم الخط في textarea الخاص بالفكرة */
    #idea {
        font-size: 1.5rem !important;
        line-height: 2 !important;
    }
    
    /* تحسينات خاصة بحقل البوست (post) */
    #post-wrapper textarea {
        font-size: 1.25rem !important;
        line-height: 2.2 !important;
        font-family: 'Cairo', 'Segoe UI', 'Tahoma', sans-serif !important;
        direction: rtl !important;
        text-align: right !important;
        padding: 1.25rem !important;
        letter-spacing: 0.01em !important;
        font-weight: 400 !important;
        color: #1f2937 !important;
    }
    
    #post-wrapper textarea::placeholder {
        font-size: 1.125rem !important;
        font-family: 'Cairo', 'Segoe UI', 'Tahoma', sans-serif !important;
        color: #9ca3af !important;
        direction: rtl !important;
        text-align: right !important;
    }
    
    /* تحسينات خاصة بحقل التصميم */
    #design-wrapper textarea {
        font-size: 1.25rem !important;
        line-height: 2.2 !important;
        font-family: 'Cairo', 'Segoe UI', 'Tahoma', sans-serif !important;
        direction: rtl !important;
        text-align: right !important;
        padding: 1.25rem !important;
        letter-spacing: 0.01em !important;
        font-weight: 400 !important;
        color: #1f2937 !important;
    }
    
    #design-wrapper textarea::placeholder {
        font-size: 1.125rem !important;
        font-family: 'Cairo', 'Segoe UI', 'Tahoma', sans-serif !important;
        color: #9ca3af !important;
        direction: rtl !important;
        text-align: right !important;
    }
    
    /* Shimmer Effect */
    @keyframes shimmer {
        0% {
            transform: translateX(-100%);
        }
        100% {
            transform: translateX(100%);
        }
    }
    
    .animate-shimmer {
        animation: shimmer 2s infinite;
    }
    
    #description-wrapper.shimmer-active {
        position: relative;
        overflow: hidden;
    }
    
    #description-wrapper.shimmer-active textarea {
        background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
        background-size: 200% 100%;
        animation: shimmer-bg 1.5s infinite;
    }
    
    @keyframes shimmer-bg {
        0% {
            background-position: 200% 0;
        }
        100% {
            background-position: -200% 0;
        }
    }
    
    /* تحسينات خاصة بحقل الوصف (description-wrapper) */
    #description-wrapper textarea {
        font-size: 1.25rem !important;
        line-height: 2.2 !important;
        font-family: 'Cairo', 'Segoe UI', 'Tahoma', sans-serif !important;
        direction: rtl !important;
        text-align: right !important;
        padding: 1.25rem !important;
        letter-spacing: 0.01em !important;
        font-weight: 400 !important;
        color: #1f2937 !important;
    }
    
    #description-wrapper textarea::placeholder {
        font-size: 1.125rem !important;
        font-family: 'Cairo', 'Segoe UI', 'Tahoma', sans-serif !important;
        color: #9ca3af !important;
        direction: rtl !important;
        text-align: right !important;
    }
    
    /* تحسينات خاصة بـ description-editor (Quill) */
    #description-editor .ql-editor {
        font-size: 1.25rem !important;
        line-height: 2.2 !important;
        font-family: 'Cairo', 'Segoe UI', 'Tahoma', sans-serif !important;
        direction: rtl !important;
        text-align: right !important;
        padding: 1.25rem !important;
        min-height: 250px !important;
        letter-spacing: 0.01em !important;
        font-weight: 400 !important;
        color: #1f2937 !important;
    }
    
    #description-editor .ql-editor p {
        margin-bottom: 1.25rem !important;
        margin-top: 0.75rem !important;
        font-size: 1.25rem !important;
        line-height: 2.2 !important;
        font-family: 'Cairo', 'Segoe UI', 'Tahoma', sans-serif !important;
    }
    
    #description-editor .ql-editor p:first-child {
        margin-top: 0 !important;
    }
    
    #description-editor .ql-editor p:last-child {
        margin-bottom: 0 !important;
    }
    
    #description-editor .ql-editor.ql-blank::before {
        font-size: 1.125rem !important;
        font-style: normal !important;
        color: #9ca3af !important;
        font-family: 'Cairo', 'Segoe UI', 'Tahoma', sans-serif !important;
        direction: rtl !important;
        text-align: right !important;
        left: auto !important;
        right: 1.25rem !important;
    }
    
    #description-editor .ql-editor ul,
    #description-editor .ql-editor ol {
        padding-right: 1.5rem;
        padding-left: 0;
        margin-bottom: 1.25rem;
    }
    
    #description-editor .ql-editor li {
        margin-bottom: 0.75rem;
        font-size: 1.25rem;
        line-height: 2.2;
    }
</style>
@endsection

@section('scripts')
<!-- Quill Editor JS -->
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
<script>
(function() {
    // Wait for Quill to load
    function initQuill() {
        // Check if Quill is loaded
        if (typeof Quill === 'undefined') {
            console.error('Quill is not loaded! Retrying...');
            setTimeout(initQuill, 100);
            return;
        }
        
        // Initialize Description Quill Editor
        const descriptionTextarea = document.getElementById('description');
        window.descriptionQuill = null;
        if (descriptionTextarea) {
            descriptionTextarea.style.display = 'none';
            
            // Create Quill editor container
            const descriptionEditorContainer = document.createElement('div');
            descriptionEditorContainer.id = 'description-editor';
            descriptionEditorContainer.className = 'ql-container ql-snow';
            descriptionEditorContainer.style.height = '300px';
            descriptionTextarea.parentNode.insertBefore(descriptionEditorContainer, descriptionTextarea);
            
            try {
                window.descriptionQuill = new Quill('#description-editor', {
                    theme: 'snow',
                    modules: {
                        toolbar: [
                            [{ 'header': [1, 2, 3, false] }],
                            ['bold', 'italic', 'underline', 'strike'],
                            [{ 'color': [] }, { 'background': [] }],
                            [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                            [{ 'align': [] }],
                            ['link', 'image'],
                            ['clean']
                        ]
                    },
                    placeholder: 'أدخل وصف المهمة (اختياري)...'
                });
                
                // Set initial content if there's old input
                const descriptionInitialContent = descriptionTextarea.value || '';
                if (descriptionInitialContent) {
                    const tempDiv = document.createElement('div');
                    tempDiv.innerHTML = descriptionInitialContent;
                    if (tempDiv.children.length > 0 || descriptionInitialContent.includes('<')) {
                        window.descriptionQuill.root.innerHTML = descriptionInitialContent;
                    } else {
                        window.descriptionQuill.setText(descriptionInitialContent);
                    }
                }
            } catch (error) {
                console.error('Error initializing Description Quill:', error);
                descriptionTextarea.style.display = 'block';
            }
        }
        
        // Initialize Idea Quill Editor
        const ideaTextarea = document.getElementById('idea');
        window.ideaQuill = null;
        if (ideaTextarea) {
            ideaTextarea.style.display = 'none';
            
            // Create Quill editor container
            const ideaEditorContainer = document.createElement('div');
            ideaEditorContainer.id = 'idea-editor';
            ideaEditorContainer.className = 'ql-container ql-snow';
            ideaEditorContainer.style.height = '300px';
            ideaTextarea.parentNode.insertBefore(ideaEditorContainer, ideaTextarea);
            
            try {
                window.ideaQuill = new Quill('#idea-editor', {
                    theme: 'snow',
                    modules: {
                        toolbar: [
                            [{ 'header': [1, 2, 3, false] }],
                            ['bold', 'italic', 'underline', 'strike'],
                            [{ 'color': [] }, { 'background': [] }],
                            [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                            [{ 'align': [] }],
                            ['link', 'image'],
                            ['clean']
                        ]
                    },
                    placeholder: 'أدخل فكرة المهمة هنا... يمكنك كتابة الفكرة بشكل مفصل مع مراعاة التوصيات المذكورة أدناه',
                    formats: ['bold', 'italic', 'underline', 'strike', 'header', 'color', 'background', 'list', 'align', 'link', 'image']
                });
                
                // تحسين الخط والمسافات بعد التهيئة
                setTimeout(() => {
                    const ideaEditor = document.querySelector('#idea-editor .ql-editor');
                    if (ideaEditor) {
                        ideaEditor.style.fontSize = '1.25rem';
                        ideaEditor.style.lineHeight = '2';
                        ideaEditor.style.fontFamily = "'Cairo', 'Segoe UI', 'Tahoma', sans-serif";
                        ideaEditor.style.direction = 'rtl';
                        ideaEditor.style.textAlign = 'right';
                    }
                }, 100);
                
                // Set initial content if there's old input
                const ideaInitialContent = ideaTextarea.value || '';
                if (ideaInitialContent) {
                    const tempDiv = document.createElement('div');
                    tempDiv.innerHTML = ideaInitialContent;
                    if (tempDiv.children.length > 0 || ideaInitialContent.includes('<')) {
                        ideaQuill.root.innerHTML = ideaInitialContent;
                    } else {
                        ideaQuill.setText(ideaInitialContent);
                    }
                }
            } catch (error) {
                console.error('Error initializing Idea Quill:', error);
                ideaTextarea.style.display = 'block';
            }
        }
        
        // Update textarea content before form submission
        const form = document.getElementById('edit-task-form');
        if (form) {
            form.addEventListener('submit', function(e) {
                // تحديث محتوى description textarea من Quill editor قبل الإرسال
                const descriptionTextarea = document.getElementById('description');
                if (window.descriptionQuill && descriptionTextarea) {
                    const descriptionContent = window.descriptionQuill.root.innerHTML;
                    console.log('Updating description from Quill:', {
                        has_quill: !!window.descriptionQuill,
                        has_textarea: !!descriptionTextarea,
                        content_length: descriptionContent ? descriptionContent.length : 0,
                        content_preview: descriptionContent ? descriptionContent.substring(0, 100) : 'empty'
                    });
                    if (descriptionContent === '<p><br></p>' || descriptionContent === '<p></p>' || !descriptionContent.trim()) {
                        descriptionTextarea.value = '';
                    } else {
                        descriptionTextarea.value = descriptionContent;
                    }
                    console.log('Description textarea value after update:', {
                        value_length: descriptionTextarea.value ? descriptionTextarea.value.length : 0,
                        value_preview: descriptionTextarea.value ? descriptionTextarea.value.substring(0, 100) : 'empty'
                    });
                } else {
                    console.warn('Cannot update description: Quill or textarea not found', {
                        has_quill: !!window.descriptionQuill,
                        has_textarea: !!descriptionTextarea
                    });
                }
                
                // تحديث محتوى idea textarea من Quill editor قبل الإرسال
                if (window.ideaQuill && ideaTextarea) {
                    const ideaContent = window.ideaQuill.root.innerHTML;
                    if (ideaContent === '<p><br></p>' || ideaContent === '<p></p>' || !ideaContent.trim()) {
                        ideaTextarea.value = '';
                    } else {
                        ideaTextarea.value = ideaContent;
                    }
                }
                
                // التحقق من أن توصيات الفكرة موجودة في الفورم
                const ideaRecommendationsInput = document.getElementById('idea_recommendations');
                if (ideaRecommendationsInput) {
                    const recommendationsValue = ideaRecommendationsInput.value;
                    console.log('Idea Recommendations being submitted:', {
                        has_value: !!recommendationsValue,
                        value_length: recommendationsValue ? recommendationsValue.length : 0,
                        value_preview: recommendationsValue ? recommendationsValue.substring(0, 100) : 'empty'
                    });
                } else {
                    console.error('idea_recommendations input not found in form!');
                }
                // تحديث قيمة اللون من hex input إذا كانت مختلفة
                const colorHex = document.getElementById('color-hex');
                const colorPicker = document.getElementById('color');
                if (colorHex && colorPicker && colorHex.value) {
                    if (colorHex.value !== colorPicker.value) {
                        colorPicker.value = colorHex.value;
                    }
                }
                // التأكد من أن value موجودة في color input
                if (!colorPicker.value) {
                    colorPicker.value = '#6366f1';
                }
                
                // إظهار رسالة تحميل
                const submitButton = form.querySelector('button[type="submit"]');
                if (submitButton) {
                    submitButton.disabled = true;
                    submitButton.innerHTML = '<span class="material-icons text-sm ml-2 animate-spin">sync</span> جاري الحفظ...';
                }
            });
        }
        // No catch needed here as we handle errors individually
    }
    
    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initQuill);
    } else {
        initQuill();
    }
})();

// Sync color picker with hex input (run after DOM is ready)
document.addEventListener('DOMContentLoaded', function() {
    const colorInput = document.getElementById('color');
    const colorHexInput = document.getElementById('color-hex');
    
    if (colorInput && colorHexInput) {
        colorInput.addEventListener('input', function() {
            colorHexInput.value = this.value;
        });

        colorHexInput.addEventListener('input', function() {
            const hexValue = this.value;
            if (/^#[A-Fa-f0-9]{6}$/.test(hexValue)) {
                colorInput.value = hexValue;
            }
        });
    }
});

// Suggest Ideas Function
async function suggestIdeas() {
    const titleInput = document.getElementById('title');
    const descriptionTextarea = document.getElementById('description');
    const ideaTextarea = document.getElementById('idea');
    const suggestBtn = document.getElementById('suggest-ideas-btn');
    const suggestIcon = document.getElementById('suggest-icon');
    const suggestText = document.getElementById('suggest-text');
    const suggestSpinner = document.getElementById('suggest-spinner');
    
    // Get task ID from URL or form
    const taskId = {{ $task->id }};
    
    // Get title
    const title = titleInput ? titleInput.value.trim() : '';
    
    // Get description content from Quill editor if it exists
    let description = '';
    const descriptionEditor = document.querySelector('#description-editor .ql-editor');
    if (descriptionEditor && window.descriptionQuill) {
        description = descriptionEditor.innerHTML;
        // Convert HTML to plain text if needed
        if (description) {
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = description;
            description = tempDiv.textContent || tempDiv.innerText || '';
        }
    } else if (descriptionTextarea) {
        description = descriptionTextarea.value;
    }
    
    // Get idea recommendations directly from input field
    let ideaRecommendations = '';
    const ideaRecommendationsInput = document.getElementById('idea_recommendations');
    if (ideaRecommendationsInput) {
        ideaRecommendations = ideaRecommendationsInput.value.trim() || '';
    }
    
    console.log('Sending idea recommendations:', {
        has_value: !!ideaRecommendations,
        value_length: ideaRecommendations ? ideaRecommendations.length : 0,
        value_preview: ideaRecommendations ? ideaRecommendations.substring(0, 100) : 'empty'
    });
    
    // Disable button and show loading
    suggestBtn.disabled = true;
    suggestIcon.classList.add('hidden');
    suggestText.textContent = 'جاري اقتراح الفكرة...';
    suggestSpinner.classList.remove('hidden');
    
    try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        
        const response = await fetch('/tasks/suggest-ideas', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
            body: JSON.stringify({
                task_id: taskId,
                title: title,
                description: description,
                idea_recommendations: ideaRecommendations
            })
        });
        
        if (!response.ok) {
            const errorData = await response.json().catch(() => ({ error: 'فشل في اقتراح الأفكار' }));
            throw new Error(errorData.error || `فشل في اقتراح الأفكار (${response.status})`);
        }
        
        const data = await response.json();
        
        if (!data.success) {
            throw new Error(data.error || 'فشل في اقتراح الأفكار');
        }
        
        const suggestedIdeas = data.ideas;
        
        if (!suggestedIdeas || suggestedIdeas.trim() === '') {
            throw new Error('لم يتم اقتراح أي أفكار');
        }
        
        // Update idea field
        // If Quill editor exists for idea, update it
        const ideaEditor = document.querySelector('#idea-editor .ql-editor');
        if (ideaEditor && window.ideaQuill) {
            // Convert plain text to HTML paragraphs (preserve line breaks)
            const ideasArray = suggestedIdeas.split('\n').filter(line => line.trim());
            const htmlIdeas = ideasArray.map(idea => `<p>${idea.trim()}</p>`).join('');
            window.ideaQuill.root.innerHTML = htmlIdeas;
            ideaTextarea.value = htmlIdeas;
        } else {
            ideaTextarea.value = suggestedIdeas;
        }
        
        // Show success message
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'success',
                title: 'تم بنجاح!',
                text: 'تم اقتراح الفكرة بنجاح',
                confirmButtonText: 'حسناً',
                timer: 2000
            });
        }
        
    } catch (error) {
        console.error('Error suggesting ideas:', error);
        let errorMessage = 'حدث خطأ أثناء اقتراح الأفكار. يرجى المحاولة مرة أخرى.';
        if (error.message) {
            errorMessage = error.message;
        }
        
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'error',
                title: 'خطأ',
                text: errorMessage,
                confirmButtonText: 'حسناً'
            });
        } else {
            alert(errorMessage);
        }
    } finally {
        // Re-enable button
        suggestBtn.disabled = false;
        suggestIcon.classList.remove('hidden');
        suggestText.textContent = 'اقتراح أفكار';
        suggestSpinner.classList.add('hidden');
    }
}

// Show Prompt Function
async function showPrompt() {
    const titleInput = document.getElementById('title');
    const descriptionTextarea = document.getElementById('description');
    const ideaRecommendationsInput = document.getElementById('idea_recommendations');
    const showPromptBtn = document.getElementById('show-prompt-btn');
    const showPromptIcon = document.getElementById('show-prompt-icon');
    const showPromptText = document.getElementById('show-prompt-text');
    const showPromptSpinner = document.getElementById('show-prompt-spinner');
    
    // Get task ID from URL or form
    const taskId = {{ $task->id }};
    
    // Get title
    const title = titleInput ? titleInput.value.trim() : '';
    
    // Get description content from Quill editor if it exists
    let description = '';
    const descriptionEditor = document.querySelector('#description-editor .ql-editor');
    if (descriptionEditor && window.descriptionQuill) {
        description = descriptionEditor.innerHTML;
        // Convert HTML to plain text if needed
        if (description) {
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = description;
            description = tempDiv.textContent || tempDiv.innerText || '';
        }
    } else if (descriptionTextarea) {
        description = descriptionTextarea.value;
    }
    
    // Get idea recommendations directly from input field
    let ideaRecommendations = '';
    if (ideaRecommendationsInput) {
        ideaRecommendations = ideaRecommendationsInput.value.trim() || '';
    }
    
    // Disable button and show loading
    showPromptBtn.disabled = true;
    showPromptIcon.classList.add('hidden');
    showPromptText.textContent = 'جاري إنشاء البرومبت...';
    showPromptSpinner.classList.remove('hidden');
    
    try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        
        const response = await fetch('/tasks/show-prompt', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
            body: JSON.stringify({
                task_id: taskId,
                title: title,
                description: description,
                idea_recommendations: ideaRecommendations
            })
        });
        
        if (!response.ok) {
            const errorData = await response.json().catch(() => ({ error: 'فشل في إنشاء البرومبت' }));
            throw new Error(errorData.error || `فشل في إنشاء البرومبت (${response.status})`);
        }
        
        const data = await response.json();
        
        if (!data.success) {
            throw new Error(data.error || 'فشل في إنشاء البرومبت');
        }
        
        const prompt = data.prompt;
        
        // حفظ البرومبت في متغير عام للنسخ لاحقاً
        window.lastPrompt = prompt;
        
        // عرض البرومبت في console
        console.log('=== البرومبت المرسل إلى DeepSeek ===');
        console.log(prompt);
        console.log('=== نهاية البرومبت ===');
        
        // عرض البرومبت في نافذة منبثقة
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'info',
                title: 'البرومبت المرسل إلى DeepSeek',
                html: `<div style="text-align: right; direction: rtl; max-height: 500px; overflow-y: auto;">
                    <pre style="white-space: pre-wrap; word-wrap: break-word; background: #f5f5f5; padding: 15px; border-radius: 8px; font-size: 12px; text-align: right; font-family: 'Cairo', Arial, sans-serif;">${prompt.replace(/\n/g, '<br>')}</pre>
                </div>`,
                width: '90%',
                confirmButtonText: 'حسناً',
                didOpen: () => {
                    // نسخ البرومبت إلى الحافظة عند الضغط على زر النسخ
                    const copyBtn = document.createElement('button');
                    copyBtn.textContent = 'نسخ البرومبت';
                    copyBtn.className = 'swal2-confirm swal2-styled';
                    copyBtn.style.marginTop = '10px';
                    copyBtn.style.marginLeft = '10px';
                    copyBtn.onclick = () => {
                        navigator.clipboard.writeText(prompt).then(() => {
                            Swal.fire({
                                icon: 'success',
                                title: 'تم النسخ!',
                                text: 'تم نسخ البرومبت إلى الحافظة',
                                timer: 2000,
                                showConfirmButton: false
                            });
                        });
                    };
                    document.querySelector('.swal2-actions').appendChild(copyBtn);
                }
            });
        } else {
            // Fallback إذا لم يكن Swal متاحاً
            alert('البرومبت:\n\n' + prompt);
        }
        
    } catch (error) {
        console.error('Error showing prompt:', error);
        let errorMessage = 'حدث خطأ أثناء إنشاء البرومبت. يرجى المحاولة مرة أخرى.';
        if (error.message) {
            errorMessage = error.message;
        }
        
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'error',
                title: 'خطأ',
                text: errorMessage,
                confirmButtonText: 'حسناً'
            });
        } else {
            alert(errorMessage);
        }
    } finally {
        // Re-enable button
        showPromptBtn.disabled = false;
        showPromptIcon.classList.remove('hidden');
        showPromptText.textContent = 'اظهر البرومبت';
        showPromptSpinner.classList.add('hidden');
    }
}

// View Idea Function - عرض الفكرة في نافذة منبثقة
window.viewIdea = function viewIdea() {
    try {
        let ideaContent = '';
        
        // محاولة جلب المحتوى من Quill editor أولاً
        const ideaEditor = document.querySelector('#idea-editor .ql-editor');
        if (ideaEditor && window.ideaQuill) {
            // الحصول على المحتوى كـ HTML
            ideaContent = window.ideaQuill.root.innerHTML;
        } else {
            // إذا لم يكن Quill موجوداً، استخدم textarea
            const ideaTextarea = document.getElementById('idea');
            if (ideaTextarea) {
                ideaContent = ideaTextarea.value || '';
                // تحويل النص العادي إلى HTML مع الحفاظ على الأسطر
                if (ideaContent) {
                    ideaContent = ideaContent.split('\n').map(line => {
                        const trimmed = line.trim();
                        return trimmed ? `<p style="margin-bottom: 1rem; font-size: 1.5rem; line-height: 2;">${trimmed}</p>` : '<br>';
                    }).join('');
                }
            }
        }
        
        // تنظيف المحتوى
        ideaContent = ideaContent.trim();
        
        if (!ideaContent || ideaContent === '' || ideaContent === '<p></p>' || ideaContent === '<br>') {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'warning',
                    title: 'لا يوجد محتوى',
                    text: 'لا يوجد نص في حقل الفكرة لعرضه.',
                    confirmButtonText: 'حسناً'
                });
            } else {
                alert('لا يوجد نص في حقل الفكرة لعرضه.');
            }
            return;
        }
        
        // عرض الفكرة في نافذة منبثقة بحجم كبير
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'info',
                title: 'الفكرة',
                html: `<div style="text-align: right; direction: rtl; max-height: 70vh; overflow-y: auto; padding: 1rem;">
                    <div style="font-size: 1.75rem; line-height: 2.5; font-family: 'Cairo', 'Segoe UI', Tahoma, sans-serif; color: #1f2937; font-weight: 400; letter-spacing: 0.01em;">
                        ${ideaContent}
                    </div>
                </div>`,
                width: '90%',
                maxWidth: '900px',
                confirmButtonText: 'إغلاق',
                customClass: {
                    popup: 'swal2-popup-large',
                    htmlContainer: 'swal2-html-container-large'
                },
                didOpen: () => {
                    // تحسين التمرير
                    const content = document.querySelector('.swal2-html-container');
                    if (content) {
                        content.style.maxHeight = '70vh';
                        content.style.overflowY = 'auto';
                    }
                }
            });
        } else {
            // Fallback إذا لم يكن Swal متاحاً
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = ideaContent;
            const plainText = tempDiv.textContent || tempDiv.innerText || ideaContent;
            alert('الفكرة:\n\n' + plainText);
        }
    } catch (error) {
        console.error('Error viewing idea:', error);
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'error',
                title: 'خطأ',
                text: 'حدث خطأ أثناء عرض الفكرة: ' + error.message,
                confirmButtonText: 'حسناً'
            });
        } else {
            alert('حدث خطأ أثناء عرض الفكرة: ' + error.message);
        }
    }
}

// View Post Function - عرض البوست في نافذة منبثقة
window.viewPost = function viewPost() {
    try {
        let postContent = '';
        
        // محاولة جلب المحتوى من Quill editor أولاً
        const descriptionEditor = document.querySelector('#description-editor .ql-editor');
        if (descriptionEditor && window.descriptionQuill) {
            // الحصول على المحتوى كـ HTML
            postContent = window.descriptionQuill.root.innerHTML;
        } else {
            // إذا لم يكن Quill موجوداً، استخدم textarea
            const descriptionTextarea = document.getElementById('description');
            if (descriptionTextarea) {
                postContent = descriptionTextarea.value || '';
                // تحويل النص العادي إلى HTML مع الحفاظ على الأسطر
                if (postContent) {
                    postContent = postContent.split('\n').map(line => {
                        const trimmed = line.trim();
                        return trimmed ? `<p style="margin-bottom: 1rem; font-size: 1.5rem; line-height: 2;">${trimmed}</p>` : '<br>';
                    }).join('');
                }
            }
        }
        
        // تنظيف المحتوى
        postContent = postContent.trim();
        
        if (!postContent || postContent === '' || postContent === '<p></p>' || postContent === '<br>') {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'warning',
                    title: 'لا يوجد محتوى',
                    text: 'لا يوجد نص في حقل البوست لعرضه.',
                    confirmButtonText: 'حسناً'
                });
            } else {
                alert('لا يوجد نص في حقل البوست لعرضه.');
            }
            return;
        }
        
        // عرض البوست في نافذة منبثقة بحجم كبير
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'info',
                title: 'البوست',
                html: `<div style="text-align: right; direction: rtl; max-height: 70vh; overflow-y: auto; padding: 1rem;">
                    <div style="font-size: 1.75rem; line-height: 2.5; font-family: 'Cairo', 'Segoe UI', Tahoma, sans-serif; color: #1f2937; font-weight: 400; letter-spacing: 0.01em;">
                        ${postContent}
                    </div>
                </div>`,
                width: '90%',
                maxWidth: '900px',
                confirmButtonText: 'إغلاق',
                customClass: {
                    popup: 'swal2-popup-large',
                    htmlContainer: 'swal2-html-container-large'
                },
                didOpen: () => {
                    // تحسين التمرير
                    const content = document.querySelector('.swal2-html-container');
                    if (content) {
                        content.style.maxHeight = '70vh';
                        content.style.overflowY = 'auto';
                    }
                }
            });
        } else {
            // Fallback إذا لم يكن Swal متاحاً
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = postContent;
            const plainText = tempDiv.textContent || tempDiv.innerText || postContent;
            alert('البوست:\n\n' + plainText);
        }
    } catch (error) {
        console.error('Error viewing post:', error);
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'error',
                title: 'خطأ',
                text: 'حدث خطأ أثناء عرض البوست: ' + error.message,
                confirmButtonText: 'حسناً'
            });
        } else {
            alert('حدث خطأ أثناء عرض البوست: ' + error.message);
        }
    }
}

// View Design Function - عرض التصميم في نافذة منبثقة
window.viewDesign = function viewDesign() {
    try {
        let designContent = '';
        
        // محاولة جلب المحتوى من textarea
        const designTextarea = document.getElementById('design');
        if (designTextarea) {
            designContent = designTextarea.value || '';
            // تحويل النص العادي إلى HTML مع الحفاظ على الأسطر
            if (designContent) {
                designContent = designContent.split('\n').map(line => {
                    const trimmed = line.trim();
                    return trimmed ? `<p style="margin-bottom: 1rem; font-size: 1.5rem; line-height: 2;">${trimmed}</p>` : '<br>';
                }).join('');
            }
        }
        
        // تنظيف المحتوى
        designContent = designContent.trim();
        
        if (!designContent || designContent === '' || designContent === '<p></p>' || designContent === '<br>') {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'warning',
                    title: 'لا يوجد محتوى',
                    text: 'لا يوجد نص في حقل التصميم لعرضه.',
                    confirmButtonText: 'حسناً'
                });
            } else {
                alert('لا يوجد نص في حقل التصميم لعرضه.');
            }
            return;
        }
        
        // عرض التصميم في نافذة منبثقة بحجم كبير
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'info',
                title: 'التصميم',
                html: `<div style="text-align: right; direction: rtl; max-height: 70vh; overflow-y: auto; padding: 1rem;">
                    <div style="font-size: 1.75rem; line-height: 2.5; font-family: 'Cairo', 'Segoe UI', Tahoma, sans-serif; color: #1f2937; font-weight: 400; letter-spacing: 0.01em;">
                        ${designContent}
                    </div>
                </div>`,
                width: '90%',
                maxWidth: '900px',
                confirmButtonText: 'إغلاق',
                customClass: {
                    popup: 'swal2-popup-large',
                    htmlContainer: 'swal2-html-container-large'
                },
                didOpen: () => {
                    // تحسين التمرير
                    const content = document.querySelector('.swal2-html-container');
                    if (content) {
                        content.style.maxHeight = '70vh';
                        content.style.overflowY = 'auto';
                    }
                }
            });
        } else {
            // Fallback إذا لم يكن Swal متاحاً
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = designContent;
            const plainText = tempDiv.textContent || tempDiv.innerText || designContent;
            alert('التصميم:\n\n' + plainText);
        }
    } catch (error) {
        console.error('Error viewing design:', error);
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'error',
                title: 'خطأ',
                text: 'حدث خطأ أثناء عرض التصميم: ' + error.message,
                confirmButtonText: 'حسناً'
            });
        } else {
            alert('حدث خطأ أثناء عرض التصميم: ' + error.message);
        }
    }
}

// Copy Idea Function
async function copyIdea() {
    const copyIdeaBtn = document.getElementById('copy-idea-btn');
    const copyIdeaIcon = document.getElementById('copy-idea-icon');
    const copyIdeaText = document.getElementById('copy-idea-text');
    
    // Disable button
    copyIdeaBtn.disabled = true;
    copyIdeaText.textContent = 'جاري النسخ...';
    
    try {
        let ideaContent = '';
        
        // محاولة جلب المحتوى من Quill editor أولاً
        const ideaEditor = document.querySelector('#idea-editor .ql-editor');
        if (ideaEditor && window.ideaQuill) {
            // الحصول على المحتوى كـ HTML
            ideaContent = window.ideaQuill.root.innerHTML;
            
            // تحويل HTML إلى نص عادي
            if (ideaContent) {
                const tempDiv = document.createElement('div');
                tempDiv.innerHTML = ideaContent;
                ideaContent = tempDiv.textContent || tempDiv.innerText || '';
            }
        } else {
            // إذا لم يكن Quill موجوداً، استخدم textarea
            const ideaTextarea = document.getElementById('idea');
            if (ideaTextarea) {
                ideaContent = ideaTextarea.value || '';
            }
        }
        
        // تنظيف المحتوى
        ideaContent = ideaContent.trim();
        
        if (!ideaContent || ideaContent === '') {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'warning',
                    title: 'لا يوجد محتوى',
                    text: 'حقل الفكرة فارغ',
                    confirmButtonText: 'حسناً'
                });
            } else {
                alert('حقل الفكرة فارغ');
            }
            return;
        }
        
        // نسخ الفكرة إلى الحافظة
        await navigator.clipboard.writeText(ideaContent);
        
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'success',
                title: 'تم النسخ!',
                text: 'تم نسخ الفكرة إلى الحافظة',
                timer: 2000,
                showConfirmButton: false
            });
        } else {
            alert('تم نسخ الفكرة إلى الحافظة');
        }
        
    } catch (error) {
        console.error('Error copying idea:', error);
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'error',
                title: 'خطأ',
                text: 'فشل في نسخ الفكرة: ' + error.message,
                confirmButtonText: 'حسناً'
            });
        } else {
            alert('فشل في نسخ الفكرة: ' + error.message);
        }
    } finally {
        // Re-enable button
        copyIdeaBtn.disabled = false;
        copyIdeaText.textContent = 'نسخ الفكرة';
    }
}

// Suggest Post Function
async function suggestPost() {
    console.log('suggestPost function called');
    
    try {
        const descriptionTextarea = document.getElementById('description');
        const ideaTextarea = document.getElementById('idea');
        const postRecommendationsInput = document.getElementById('post_recommendations');
        const postPlatformInput = document.getElementById('post_platform');
        const postTypeInput = document.getElementById('post_type');
        const postWordCountInput = document.getElementById('post_word_count');
        const suggestPostBtn = document.getElementById('suggest-post-btn');
        const suggestPostIcon = document.getElementById('suggest-post-icon');
        const suggestPostText = document.getElementById('suggest-post-text');
        const suggestPostSpinner = document.getElementById('suggest-post-spinner');
        
        if (!suggestPostBtn) {
            console.error('suggest-post-btn not found');
            alert('خطأ: لم يتم العثور على الزر');
            return;
        }
        
        // Get task ID
        const taskId = {{ $task->id }};
        console.log('Task ID:', taskId);
        
        // Get description content from Quill editor if it exists
        let description = '';
        try {
            const descriptionEditor = document.querySelector('#description-editor .ql-editor');
            if (descriptionEditor && descriptionEditor.innerHTML && window.descriptionQuill) {
                description = descriptionEditor.innerHTML;
                // Convert HTML to plain text if needed
                if (description) {
                    const tempDiv = document.createElement('div');
                    tempDiv.innerHTML = description;
                    description = tempDiv.textContent || tempDiv.innerText || '';
                }
            } else if (descriptionTextarea && descriptionTextarea.value) {
                description = descriptionTextarea.value;
            }
        } catch (e) {
            console.warn('Error getting description:', e);
            if (descriptionTextarea && descriptionTextarea.value) {
                description = descriptionTextarea.value;
            }
        }
        
        // Get idea content from Quill editor if it exists
        let idea = '';
        try {
            const ideaEditor = document.querySelector('#idea-editor .ql-editor');
            if (ideaEditor && window.ideaQuill && window.ideaQuill.root && window.ideaQuill.root.innerHTML) {
                idea = window.ideaQuill.root.innerHTML;
                if (idea) {
                    const tempDiv = document.createElement('div');
                    tempDiv.innerHTML = idea;
                    idea = tempDiv.textContent || tempDiv.innerText || '';
                }
            } else if (ideaTextarea && ideaTextarea.value) {
                idea = ideaTextarea.value;
            }
        } catch (e) {
            console.warn('Error getting idea:', e);
            if (ideaTextarea && ideaTextarea.value) {
                idea = ideaTextarea.value;
            }
        }
        
        // Get post recommendations
        let postRecommendations = '';
        if (postRecommendationsInput) {
            postRecommendations = postRecommendationsInput.value.trim() || '';
        }
        
        // Get post options
        const postPlatform = postPlatformInput ? postPlatformInput.value : '';
        const postType = postTypeInput ? postTypeInput.value : '';
        const postWordCount = postWordCountInput ? postWordCountInput.value : '';
        
        console.log('Data to send:', {
            task_id: taskId,
            has_description: !!description,
            has_idea: !!idea,
            has_recommendations: !!postRecommendations,
            platform: postPlatform,
            platform: postPlatform,
            type: postType,
            word_count: postWordCount
        });
        
        // Disable button and show loading
        suggestPostBtn.disabled = true;
        suggestPostIcon.classList.add('hidden');
        suggestPostText.textContent = 'جاري اقتراح البوست...';
        suggestPostSpinner.classList.remove('hidden');
        
        const csrfTokenElement = document.querySelector('meta[name="csrf-token"]');
        if (!csrfTokenElement) {
            throw new Error('CSRF token not found');
        }
        const csrfToken = csrfTokenElement.getAttribute('content');
        if (!csrfToken) {
            throw new Error('CSRF token content is empty');
        }
        
        console.log('Sending request to /tasks/suggest-post');
        
        const response = await fetch('/tasks/suggest-post', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify({
                task_id: taskId,
                description: description,
                idea: idea,
                post_recommendations: postRecommendations,
                post_platform: postPlatform,
                post_type: postType,
                post_word_count: postWordCount
            })
        });
        
        if (!response.ok) {
            const errorData = await response.json().catch(() => ({ error: 'فشل في اقتراح البوست' }));
            throw new Error(errorData.error || `فشل في اقتراح البوست (${response.status})`);
        }
        
        const data = await response.json();
        
        if (!data.success) {
            throw new Error(data.error || 'فشل في اقتراح البوست');
        }
        
        const suggestedPost = data.post;
        
        if (!suggestedPost || suggestedPost.trim() === '') {
            throw new Error('لم يتم اقتراح بوست');
        }
        
        console.log('Post received:', suggestedPost.substring(0, 100) + '...');
        
        // Update description field with the suggested post (the one in Post section)
        if (descriptionTextarea) {
            // Remove HTML tags if any and set plain text
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = suggestedPost;
            const plainText = tempDiv.textContent || tempDiv.innerText || suggestedPost;
            descriptionTextarea.value = plainText;
            
            // Trigger input event to notify any listeners
            descriptionTextarea.dispatchEvent(new Event('input', { bubbles: true }));
        }
        
        // Also try to update Quill editor if it exists
        try {
            if (window.descriptionQuill && window.descriptionQuill.root) {
                // Convert plain text to HTML paragraphs (preserve line breaks)
                const postArray = suggestedPost.split('\n').filter(line => line.trim());
                const htmlPost = postArray.map(line => `<p>${line.trim()}</p>`).join('');
                window.descriptionQuill.root.innerHTML = htmlPost;
                // Also update the textarea value
                if (descriptionTextarea) {
                    descriptionTextarea.value = htmlPost;
                }
            } else {
                const descriptionEditor = document.querySelector('#description-editor .ql-editor');
                if (descriptionEditor) {
                    // Convert plain text to HTML paragraphs (preserve line breaks)
                    const postArray = suggestedPost.split('\n').filter(line => line.trim());
                    const htmlPost = postArray.map(line => `<p>${line.trim()}</p>`).join('');
                    descriptionEditor.innerHTML = htmlPost;
                    if (descriptionTextarea) {
                        descriptionTextarea.value = htmlPost;
                    }
                }
            }
        } catch (e) {
            console.warn('Could not update Quill editor:', e);
        }
        
        // Re-enable button and hide loading AFTER updating the content
        if (suggestPostBtn) {
            suggestPostBtn.disabled = false;
        }
        if (suggestPostIcon) {
            suggestPostIcon.classList.remove('hidden');
        }
        if (suggestPostText) {
            suggestPostText.textContent = 'اقتراح البوست';
        }
        if (suggestPostSpinner) {
            suggestPostSpinner.classList.add('hidden');
        }
        
        // Show success message
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'success',
                title: 'تم بنجاح!',
                text: 'تم اقتراح البوست بنجاح وتم عرضه في حقل الوصف',
                confirmButtonText: 'حسناً',
                timer: 2000
            });
        } else {
            alert('تم اقتراح البوست بنجاح');
        }
        
    } catch (error) {
        console.error('Error suggesting post:', error);
        console.error('Error stack:', error.stack);
        let errorMessage = 'حدث خطأ أثناء اقتراح البوست. يرجى المحاولة مرة أخرى.';
        if (error.message) {
            errorMessage = error.message;
        }
        
        // Re-enable button in case of error
        const suggestPostBtn = document.getElementById('suggest-post-btn');
        const suggestPostIcon = document.getElementById('suggest-post-icon');
        const suggestPostText = document.getElementById('suggest-post-text');
        const suggestPostSpinner = document.getElementById('suggest-post-spinner');
        
        if (suggestPostBtn) {
            suggestPostBtn.disabled = false;
        }
        if (suggestPostIcon) {
            suggestPostIcon.classList.remove('hidden');
        }
        if (suggestPostText) {
            suggestPostText.textContent = 'اقتراح البوست';
        }
        if (suggestPostSpinner) {
            suggestPostSpinner.classList.add('hidden');
        }
        
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'error',
                title: 'خطأ',
                text: errorMessage,
                confirmButtonText: 'حسناً'
            });
        } else {
            alert(errorMessage);
        }
    }
}

// Copy Post Function
async function copyPost() {
    const copyPostBtn = document.getElementById('copy-post-btn');
    const copyPostIcon = document.getElementById('copy-post-icon');
    const copyPostText = document.getElementById('copy-post-text');
    
    // Disable button
    copyPostBtn.disabled = true;
    copyPostText.textContent = 'جاري النسخ...';
    
    try {
        let postContent = '';
        
        // محاولة جلب المحتوى من Quill editor أولاً
        const descriptionEditor = document.querySelector('#description-editor .ql-editor');
        if (descriptionEditor && window.descriptionQuill) {
            // الحصول على المحتوى كـ HTML
            postContent = window.descriptionQuill.root.innerHTML;
            
            // تحويل HTML إلى نص عادي
            if (postContent) {
                const tempDiv = document.createElement('div');
                tempDiv.innerHTML = postContent;
                postContent = tempDiv.textContent || tempDiv.innerText || '';
            }
        } else {
            // إذا لم يكن Quill موجوداً، استخدم textarea
            const descriptionTextarea = document.getElementById('description');
            if (descriptionTextarea) {
                postContent = descriptionTextarea.value || '';
            }
        }
        
        // تنظيف المحتوى
        postContent = postContent.trim();
        
        if (!postContent || postContent === '') {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'warning',
                    title: 'لا يوجد محتوى',
                    text: 'حقل الوصف فارغ',
                    confirmButtonText: 'حسناً'
                });
            } else {
                alert('حقل الوصف فارغ');
            }
            return;
        }
        
        // نسخ البوست إلى الحافظة
        await navigator.clipboard.writeText(postContent);
        
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'success',
                title: 'تم النسخ!',
                text: 'تم نسخ البوست إلى الحافظة',
                timer: 2000,
                showConfirmButton: false
            });
        } else {
            alert('تم نسخ البوست إلى الحافظة');
        }
        
    } catch (error) {
        console.error('Error copying post:', error);
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'error',
                title: 'خطأ',
                text: 'فشل في نسخ البوست: ' + error.message,
                confirmButtonText: 'حسناً'
            });
        } else {
            alert('فشل في نسخ البوست: ' + error.message);
        }
    } finally {
        // Re-enable button
        copyPostBtn.disabled = false;
        copyPostText.textContent = 'نسخ البوست';
    }
}

// Generate Description Function
async function generateDescription() {
    const ideaTextarea = document.getElementById('idea');
    const descriptionTextarea = document.getElementById('description');
    const descriptionWrapper = document.getElementById('description-wrapper');
    const descriptionShimmer = document.getElementById('description-shimmer');
    const generateBtn = document.getElementById('generate-description-btn');
    const generateIcon = document.getElementById('generate-icon');
    const generateText = document.getElementById('generate-text');
    const generateSpinner = document.getElementById('generate-spinner');
    
    // Get idea content from Quill editor if it exists
    let ideaContent = '';
    const ideaEditor = document.querySelector('#idea-editor .ql-editor');
    if (ideaEditor) {
        ideaContent = ideaEditor.innerHTML;
        // Convert HTML to plain text if needed
        if (ideaContent) {
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = ideaContent;
            ideaContent = tempDiv.textContent || tempDiv.innerText || '';
        }
    } else if (ideaTextarea) {
        ideaContent = ideaTextarea.value;
    }
    
    if (!ideaContent || ideaContent.trim() === '') {
        alert('يرجى إدخال فكرة أولاً');
        return;
    }
    
    // Disable button and show loading
    generateBtn.disabled = true;
    generateIcon.classList.add('hidden');
    generateText.textContent = 'جاري الإنشاء...';
    generateSpinner.classList.remove('hidden');
    
    // Show shimmer effect
    descriptionWrapper.classList.add('shimmer-active');
    descriptionShimmer.classList.remove('hidden');
    
    try {
        const url = document.querySelector('[data-generate-description-url]')?.dataset.generateDescriptionUrl || '/tasks/generate-description';
        console.log('Request URL:', url);
        
        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify({
                idea: ideaContent
            })
        });
        
        // Check if response is ok
        if (!response.ok) {
            const errorData = await response.json().catch(() => ({ error: 'فشل في إنشاء الوصف' }));
            console.error('Response error:', response.status, errorData);
            throw new Error(errorData.error || `فشل في إنشاء الوصف (${response.status})`);
        }
        
        const data = await response.json();
        console.log('Response data:', data);
        
        if (!data.success) {
            throw new Error(data.error || 'فشل في إنشاء الوصف');
        }
        
        const generatedDescription = data.description;
        
        if (!generatedDescription || generatedDescription.trim() === '') {
            throw new Error('لم يتم إرجاع وصف من الخادم');
        }
        
        // Update description field
        // If Quill editor exists, update it
        const descriptionEditor = document.querySelector('#description-editor .ql-editor');
        if (descriptionEditor && window.descriptionQuill) {
            window.descriptionQuill.root.innerHTML = generatedDescription;
            descriptionTextarea.value = generatedDescription;
        } else {
            descriptionTextarea.value = generatedDescription;
        }
        
    } catch (error) {
        console.error('Error generating description:', error);
        let errorMessage = 'حدث خطأ أثناء إنشاء الوصف. يرجى المحاولة مرة أخرى.';
        if (error.message) {
            errorMessage = error.message;
        }
        alert(errorMessage);
    } finally {
        // Hide shimmer and re-enable button
        descriptionWrapper.classList.remove('shimmer-active');
        descriptionShimmer.classList.add('hidden');
        generateBtn.disabled = false;
        generateIcon.classList.remove('hidden');
        generateText.textContent = 'إنشاء الوصف تلقائياً';
        generateSpinner.classList.add('hidden');
    }
}

// Important Links Management
document.addEventListener('DOMContentLoaded', function() {
    let linkIndex = {{ count(old('links', $task->task_data['links'] ?? [])) }};
    
    function removeLink(index) {
        const linkItem = document.querySelector(`input[name="links[${index}][url]"]`)?.closest('.link-item');
        if (linkItem) {
            linkItem.remove();
            updateLinkNumbers();
        }
    }
    
    function updateLinkNumbers() {
        const linkItems = document.querySelectorAll('.link-item');
        linkItems.forEach((item, index) => {
            const titleLabel = item.querySelector('h4');
            if (titleLabel) {
                titleLabel.textContent = `رابط ${index + 1}`;
            }
            
            // تحديث أسماء الحقول
            const titleInput = item.querySelector('input[name^="links["][name*="[title]"]');
            const urlInput = item.querySelector('input[name^="links["][name*="[url]"]');
            
            if (titleInput) {
                titleInput.name = `links[${index}][title]`;
            }
            if (urlInput) {
                urlInput.name = `links[${index}][url]`;
            }
            
            // إظهار/إخفاء زر الحذف
            const removeBtn = item.querySelector('.remove-link');
            if (removeBtn) {
                removeBtn.style.display = linkItems.length > 1 ? 'block' : 'none';
                removeBtn.onclick = function() {
                    removeLink(index);
                };
            }
        });
    }
    
    // إضافة رابط جديد
    document.getElementById('add-link-btn').addEventListener('click', function() {
        const newLinkHtml = `
            <div class="link-item p-4 border border-gray-200 rounded-xl">
                <div class="flex items-center justify-between mb-3">
                    <h4 class="text-sm font-medium text-gray-700">رابط جديد</h4>
                    <button type="button" class="remove-link text-red-500 hover:text-red-700">
                        <span class="material-icons text-sm">delete</span>
                    </button>
                </div>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">الرابط</label>
                        <input
                            type="url"
                            name="links[${linkIndex}][url]"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
                            placeholder="https://example.com"
                        />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">عنوان الرابط</label>
                        <input
                            type="text"
                            name="links[${linkIndex}][title]"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
                            placeholder="مثال: رابط الموقع"
                        />
                    </div>
                </div>
            </div>
        `;
        document.getElementById('links-container').insertAdjacentHTML('beforeend', newLinkHtml);
        linkIndex++;
        updateLinkNumbers();
    });
    
    // تحديث الأرقام عند التحميل
    updateLinkNumbers();

    // Attachments Management
    let attachmentIndex = 1;
    
    // ربط أحداث لجميع حقول الملفات الموجودة
    function attachFileEvents() {
        document.querySelectorAll('.attachment-input').forEach(input => {
            if (!input.dataset.eventAttached) {
                input.addEventListener('change', function() {
                    const index = parseInt(this.dataset.index);
                    handleFileSelect(this, index);
                });
                input.dataset.eventAttached = 'true';
            }
        });
    }
    
    // استدعاء عند تحميل الصفحة
    attachFileEvents();
    
    function handleFileSelect(input, index) {
        console.log('File selected:', index, input.files); // للتصحيح
        if (input.files && input.files[0]) {
            const file = input.files[0];
            const fileInfo = document.getElementById(`file-info-${index}`);
            const fileName = document.getElementById(`file-name-${index}`);
            const fileSize = document.getElementById(`file-size-${index}`);
            const fileIcon = document.getElementById(`file-icon-${index}`);
            const attachmentItem = input.closest('.attachment-item');
            const removeBtn = attachmentItem ? attachmentItem.querySelector('.remove-attachment-btn') : null;
            
            // التحقق من حجم الملف (10MB)
            const maxSize = 10 * 1024 * 1024; // 10MB
            if (file.size > maxSize) {
                alert('حجم الملف أكبر من 10MB. يرجى اختيار ملف أصغر.');
                input.value = ''; // إعادة تعيين input
                return;
            }
            
            // Show file info
            if (fileName) {
                fileName.textContent = file.name;
            }
            if (fileSize) {
                fileSize.textContent = formatFileSize(file.size);
            }
            if (fileIcon) {
                fileIcon.textContent = getFileIcon(file.name);
            }
            if (fileInfo) {
                fileInfo.classList.remove('hidden');
                fileInfo.style.display = 'block';
            }
            
            // Show remove button
            if (removeBtn) {
                removeBtn.style.display = 'block';
            }
            
            // تغيير مظهر منطقة المرفق
            if (attachmentItem) {
                attachmentItem.classList.add('border-blue-300', 'bg-blue-50');
                attachmentItem.classList.remove('border-gray-300', 'bg-gray-50');
            }
        } else {
            // Hide file info if no file selected
            const fileInfo = document.getElementById(`file-info-${index}`);
            if (fileInfo) {
                fileInfo.classList.add('hidden');
                fileInfo.style.display = 'none';
            }
            const attachmentItem = input.closest('.attachment-item');
            const removeBtn = attachmentItem ? attachmentItem.querySelector('.remove-attachment-btn') : null;
            if (removeBtn) {
                removeBtn.style.display = 'none';
            }
            // إعادة المظهر الأصلي
            if (attachmentItem) {
                attachmentItem.classList.remove('border-blue-300', 'bg-blue-50');
                attachmentItem.classList.add('border-gray-300', 'bg-gray-50');
            }
        }
    }
    
    function removeAttachment(index) {
        const attachmentInput = document.querySelector(`#attachments_${index}`);
        if (attachmentInput) {
            const attachmentItem = attachmentInput.closest('.attachment-item');
            if (attachmentItem) {
                // إعادة تعيين input قبل الحذف
                attachmentInput.value = '';
                attachmentItem.remove();
            }
        }
    }
    
    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
    }
    
    function getFileIcon(fileName) {
        const extension = fileName.split('.').pop().toLowerCase();
        const iconMap = {
            'pdf': 'picture_as_pdf',
            'doc': 'description',
            'docx': 'description',
            'xls': 'table_chart',
            'xlsx': 'table_chart',
            'jpg': 'image',
            'jpeg': 'image',
            'png': 'image',
            'gif': 'image',
            'zip': 'folder_zip',
            'rar': 'folder_zip',
        };
        return iconMap[extension] || 'attach_file';
    }
    
    // Add new attachment field
    document.getElementById('add-attachment-btn').addEventListener('click', function() {
        const newAttachmentHtml = `
            <div class="attachment-item border-2 border-dashed border-gray-300 rounded-xl p-6 bg-gray-50">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <label for="attachments_${attachmentIndex}" class="cursor-pointer">
                            <div class="flex items-center justify-center py-4">
                                <div class="text-center">
                                    <span class="material-icons text-5xl text-gray-400 mb-2">attach_file</span>
                                    <p class="text-sm text-gray-600">اضغط لاختيار ملف أو اسحب الملف هنا</p>
                                    <p class="text-xs text-gray-500 mt-1">الصور: JPG, PNG, GIF | الملفات: PDF, DOC, XLS, ZIP (حد أقصى 10MB)</p>
                                </div>
                            </div>
                            <input 
                                type="file" 
                                id="attachments_${attachmentIndex}" 
                                name="attachments[]" 
                                class="hidden attachment-input"
                                accept=".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx,.xls,.xlsx,.zip,.rar"
                                data-index="${attachmentIndex}"
                            >
                        </label>
                    </div>
                    <button type="button" onclick="removeAttachment(${attachmentIndex})" class="text-red-500 hover:text-red-700 ml-4 remove-attachment-btn" style="display: none;">
                        <span class="material-icons">delete</span>
                    </button>
                </div>
                <div id="file-info-${attachmentIndex}" class="hidden mt-3 p-3 bg-white rounded-lg border border-gray-200">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center flex-1">
                            <span class="material-icons text-blue-500 ml-2" id="file-icon-${attachmentIndex}">attach_file</span>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-800 truncate" id="file-name-${attachmentIndex}"></p>
                                <p class="text-xs text-gray-500" id="file-size-${attachmentIndex}"></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
        document.getElementById('attachments-container').insertAdjacentHTML('beforeend', newAttachmentHtml);
        
        // ربط حدث للملف الجديد
        const newInput = document.getElementById(`attachments_${attachmentIndex}`);
        if (newInput) {
            newInput.addEventListener('change', function() {
                const index = parseInt(this.dataset.index);
                handleFileSelect(this, index);
            });
        }
        
        attachmentIndex++;
    });

    // التحقق من حجم الملفات قبل الإرسال
    const form = document.getElementById('edit-task-form');
    if (form) {
        form.addEventListener('submit', function(e) {
            const attachmentInputs = document.querySelectorAll('input[type="file"][name="attachments[]"]');
            let hasValidAttachments = true;
            attachmentInputs.forEach((input, index) => {
                if (input.files && input.files[0]) {
                    const file = input.files[0];
                    const maxSize = 10 * 1024 * 1024; // 10MB
                    if (file.size > maxSize) {
                        alert(`الملف "${file.name}" أكبر من 10MB. يرجى اختيار ملف أصغر.`);
                        hasValidAttachments = false;
                        e.preventDefault();
                        return false;
                    }
                }
            });
            
            if (!hasValidAttachments) {
                e.preventDefault();
                return false;
            }
        });
    }
});

// إظهار رسائل النجاح والخطأ باستخدام SweetAlert إذا كان متاحاً
@if (session('success'))
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'تم بنجاح!',
                text: '{{ session('success') }}',
                icon: 'success',
                confirmButtonText: 'حسناً'
            });
        }
    });
@endif

@if (session('error'))
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'خطأ!',
                text: '{{ session('error') }}',
                icon: 'error',
                confirmButtonText: 'حسناً'
            });
        }
    });
@endif

// Select Platform Function
function selectPlatform(platform) {
    const hiddenInput = document.getElementById('post_platform');
    const cards = document.querySelectorAll('.platform-card');
    
    hiddenInput.value = platform;
    
    cards.forEach(card => {
        if (card.dataset.platform === platform) {
            card.classList.remove('border-gray-200', 'bg-white');
            card.classList.add('border-blue-500', 'bg-blue-50');
        } else {
            card.classList.remove('border-blue-500', 'bg-blue-50');
            card.classList.add('border-gray-200', 'bg-white');
        }
    });
}

// Select Post Type Function
function selectPostType(type) {
    const hiddenInput = document.getElementById('post_type');
    const cards = document.querySelectorAll('.post-type-card');
    
    hiddenInput.value = type;
    
    cards.forEach(card => {
        if (card.dataset.type === type) {
            card.classList.remove('border-gray-200', 'bg-white');
            card.classList.add('border-purple-500', 'bg-purple-50');
        } else {
            card.classList.remove('border-purple-500', 'bg-purple-50');
            card.classList.add('border-gray-200', 'bg-white');
        }
    });
}

// Show Post Prompt Function
async function showPostPrompt() {
    console.log('showPostPrompt function called');
    
    try {
        const descriptionTextarea = document.getElementById('description');
        const ideaTextarea = document.getElementById('idea');
        const postRecommendationsInput = document.getElementById('post_recommendations');
        const postPlatformInput = document.getElementById('post_platform');
        const postTypeInput = document.getElementById('post_type');
        const postWordCountInput = document.getElementById('post_word_count');
        const showPostPromptBtn = document.getElementById('show-post-prompt-btn');
        const showPostPromptIcon = document.getElementById('show-post-prompt-icon');
        const showPostPromptText = document.getElementById('show-post-prompt-text');
        const showPostPromptSpinner = document.getElementById('show-post-prompt-spinner');
        
        if (!showPostPromptBtn) {
            console.error('show-post-prompt-btn not found');
            alert('خطأ: لم يتم العثور على الزر');
            return;
        }
        
        // Get task ID from URL or form
        const taskId = {{ $task->id }};
        console.log('Task ID:', taskId);
        
        // Get description content from Quill editor if it exists
        let description = '';
        try {
            if (window.descriptionQuill && window.descriptionQuill.root) {
                description = window.descriptionQuill.root.innerHTML;
                // Convert HTML to plain text if needed
                if (description) {
                    const tempDiv = document.createElement('div');
                    tempDiv.innerHTML = description;
                    description = tempDiv.textContent || tempDiv.innerText || '';
                }
            } else {
                const descriptionEditor = document.querySelector('#description-editor .ql-editor');
                if (descriptionEditor && descriptionEditor.innerHTML) {
                    description = descriptionEditor.innerHTML;
                    // Convert HTML to plain text if needed
                    if (description) {
                        const tempDiv = document.createElement('div');
                        tempDiv.innerHTML = description;
                        description = tempDiv.textContent || tempDiv.innerText || '';
                    }
                } else if (descriptionTextarea && descriptionTextarea.value) {
                    description = descriptionTextarea.value;
                }
            }
        } catch (e) {
            console.warn('Error getting description:', e);
            if (descriptionTextarea && descriptionTextarea.value) {
                description = descriptionTextarea.value;
            }
        }
        
        console.log('Description content:', {
            has_quill: !!window.descriptionQuill,
            description_length: description ? description.length : 0,
            description_preview: description ? description.substring(0, 100) : 'empty'
        });
        
        // Get idea content from Quill editor if it exists
        let idea = '';
        try {
            const ideaEditor = document.querySelector('#idea-editor .ql-editor');
            if (ideaEditor && window.ideaQuill && window.ideaQuill.root && window.ideaQuill.root.innerHTML) {
                idea = window.ideaQuill.root.innerHTML;
                if (idea) {
                    const tempDiv = document.createElement('div');
                    tempDiv.innerHTML = idea;
                    idea = tempDiv.textContent || tempDiv.innerText || '';
                }
            } else if (ideaTextarea && ideaTextarea.value) {
                idea = ideaTextarea.value;
            }
        } catch (e) {
            console.warn('Error getting idea:', e);
            if (ideaTextarea && ideaTextarea.value) {
                idea = ideaTextarea.value;
            }
        }
        
        // Get post recommendations directly from input field
        let postRecommendations = '';
        if (postRecommendationsInput) {
            postRecommendations = postRecommendationsInput.value.trim() || '';
        }
        
        // Get post options
        const postPlatform = postPlatformInput ? postPlatformInput.value : '';
        const postType = postTypeInput ? postTypeInput.value : '';
        const postWordCount = postWordCountInput ? postWordCountInput.value : '';
        
        console.log('Data to send:', {
            task_id: taskId,
            has_description: !!description,
            has_idea: !!idea,
            has_recommendations: !!postRecommendations,
            platform: postPlatform,
            type: postType,
            word_count: postWordCount
        });
        
        // Disable button and show loading
        showPostPromptBtn.disabled = true;
        showPostPromptIcon.classList.add('hidden');
        showPostPromptText.textContent = 'جاري إنشاء البرومبت...';
        showPostPromptSpinner.classList.remove('hidden');
        
        const csrfTokenElement = document.querySelector('meta[name="csrf-token"]');
        if (!csrfTokenElement) {
            throw new Error('CSRF token not found');
        }
        const csrfToken = csrfTokenElement.getAttribute('content');
        if (!csrfToken) {
            throw new Error('CSRF token content is empty');
        }
        
        console.log('Sending request to /tasks/show-post-prompt');
        console.log('CSRF Token:', csrfToken ? 'Found' : 'Not found');
        
        const response = await fetch('/tasks/show-post-prompt', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify({
                task_id: taskId,
                description: description,
                idea: idea,
                post_recommendations: postRecommendations,
                post_platform: postPlatform,
                post_type: postType,
                post_word_count: postWordCount
            })
        });
        
        if (!response.ok) {
            const errorData = await response.json().catch(() => ({ error: 'فشل في إنشاء البرومبت' }));
            throw new Error(errorData.error || `فشل في إنشاء البرومبت (${response.status})`);
        }
        
        const data = await response.json();
        
        if (!data.success) {
            throw new Error(data.error || 'فشل في إنشاء البرومبت');
        }
        
        const prompt = data.prompt;
        
        // حفظ البرومبت في متغير عام للنسخ لاحقاً
        window.lastPostPrompt = prompt;
        
        // عرض البرومبت في console
        console.log('=== البرومبت المرسل إلى DeepSeek للبوست ===');
        console.log(prompt);
        console.log('=== نهاية البرومبت ===');
        
        // عرض البرومبت في نافذة منبثقة
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'info',
                title: 'البرومبت المرسل إلى DeepSeek للبوست',
                html: `<div style="text-align: right; direction: rtl; max-height: 500px; overflow-y: auto;">
                    <pre style="white-space: pre-wrap; word-wrap: break-word; background: #f5f5f5; padding: 15px; border-radius: 8px; font-size: 12px; text-align: right; font-family: 'Cairo', Arial, sans-serif;">${prompt.replace(/\n/g, '<br>')}</pre>
                </div>`,
                width: '90%',
                confirmButtonText: 'نسخ البرومبت',
                cancelButtonText: 'إغلاق',
                showCancelButton: true,
                reverseButtons: true,
                customClass: {
                    confirmButton: 'swal2-confirm swal2-styled',
                    cancelButton: 'swal2-cancel swal2-styled'
                },
                didOpen: (toast) => {
                    const confirmButton = Swal.getConfirmButton();
                    if (confirmButton) {
                        confirmButton.onclick = () => {
                            navigator.clipboard.writeText(prompt).then(() => {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'تم النسخ!',
                                    text: 'تم نسخ البرومبت إلى الحافظة',
                                    timer: 2000,
                                    showConfirmButton: false
                                });
                            }).catch(err => {
                                console.error('Failed to copy prompt: ', err);
                                Swal.fire({
                                    icon: 'error',
                                    title: 'خطأ في النسخ',
                                    text: 'فشل نسخ البرومبت إلى الحافظة',
                                    confirmButtonText: 'حسناً'
                                });
                            });
                        };
                    }
                }
            });
        } else {
            // Fallback إذا لم يكن Swal متاحاً
            alert('البرومبت:\n\n' + prompt);
        }
        
        // Re-enable button after success
        if (showPostPromptBtn) {
            showPostPromptBtn.disabled = false;
        }
        if (showPostPromptIcon) {
            showPostPromptIcon.classList.remove('hidden');
        }
        if (showPostPromptText) {
            showPostPromptText.textContent = 'اظهر البرومبت';
        }
        if (showPostPromptSpinner) {
            showPostPromptSpinner.classList.add('hidden');
        }
        
    } catch (error) {
        console.error('Error showing post prompt:', error);
        console.error('Error stack:', error.stack);
        let errorMessage = 'حدث خطأ أثناء إنشاء البرومبت. يرجى المحاولة مرة أخرى.';
        if (error.message) {
            errorMessage = error.message;
        }
        
        // Re-enable button in case of error
        const showPostPromptBtn = document.getElementById('show-post-prompt-btn');
        const showPostPromptIcon = document.getElementById('show-post-prompt-icon');
        const showPostPromptText = document.getElementById('show-post-prompt-text');
        const showPostPromptSpinner = document.getElementById('show-post-prompt-spinner');
        
        if (showPostPromptBtn) {
            showPostPromptBtn.disabled = false;
        }
        if (showPostPromptIcon) {
            showPostPromptIcon.classList.remove('hidden');
        }
        if (showPostPromptText) {
            showPostPromptText.textContent = 'اظهر البرومبت';
        }
        if (showPostPromptSpinner) {
            showPostPromptSpinner.classList.add('hidden');
        }
        
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'error',
                title: 'خطأ',
                text: errorMessage,
                confirmButtonText: 'حسناً'
            });
        } else {
            alert(errorMessage);
        }
    }
}

// Show Design Prompt Function
window.showDesignPrompt = async function showDesignPrompt() {
    console.log('showDesignPrompt function called');
    
    try {
        const designTextarea = document.getElementById('design');
        const designRecommendationsInput = document.getElementById('design_recommendations');
        const showDesignPromptBtn = document.getElementById('show-design-prompt-btn');
        const showDesignPromptIcon = document.getElementById('show-design-prompt-icon');
        const showDesignPromptText = document.getElementById('show-design-prompt-text');
        const showDesignPromptSpinner = document.getElementById('show-design-prompt-spinner');
        
        console.log('Elements found:', {
            designTextarea: !!designTextarea,
            designRecommendationsInput: !!designRecommendationsInput,
            showDesignPromptBtn: !!showDesignPromptBtn,
            showDesignPromptIcon: !!showDesignPromptIcon,
            showDesignPromptText: !!showDesignPromptText,
            showDesignPromptSpinner: !!showDesignPromptSpinner
        });
        
        if (!showDesignPromptBtn) {
            console.error('show-design-prompt-btn not found');
            alert('خطأ: لم يتم العثور على الزر');
            return;
        }
        
        // Get task ID
        const taskId = {{ $task->id }};
        
        // Get design content
        let design = '';
        if (designTextarea && designTextarea.value) {
            design = designTextarea.value;
        }
        
        // Get design recommendations
        let designRecommendations = '';
        if (designRecommendationsInput) {
            designRecommendations = designRecommendationsInput.value.trim() || '';
        }
        
        // Disable button and show loading
        showDesignPromptBtn.disabled = true;
        showDesignPromptIcon.classList.add('hidden');
        showDesignPromptText.textContent = 'جاري إنشاء البرومبت...';
        showDesignPromptSpinner.classList.remove('hidden');
        
        const csrfTokenElement = document.querySelector('meta[name="csrf-token"]');
        if (!csrfTokenElement) {
            throw new Error('CSRF token not found');
        }
        const csrfToken = csrfTokenElement.getAttribute('content');
        if (!csrfToken) {
            throw new Error('CSRF token content is empty');
        }
        
        console.log('Sending request to /tasks/show-design-prompt', {
            task_id: taskId,
            has_design: !!design,
            has_recommendations: !!designRecommendations
        });
        
        const response = await fetch('/tasks/show-design-prompt', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify({
                task_id: taskId,
                design: design,
                design_recommendations: designRecommendations
            })
        });
        
        console.log('Response status:', response.status);
        console.log('Response ok:', response.ok);
        
        if (!response.ok) {
            const errorText = await response.text();
            console.error('Error response:', errorText);
            let errorData;
            try {
                errorData = JSON.parse(errorText);
            } catch (e) {
                errorData = { error: errorText || `فشل في إنشاء البرومبت (${response.status})` };
            }
            throw new Error(errorData.error || errorData.message || `فشل في إنشاء البرومبت (${response.status})`);
        }
        
        const data = await response.json();
        
        if (!data.success) {
            throw new Error(data.error || 'فشل في إنشاء البرومبت');
        }
        
        const prompt = data.prompt;
        
        // عرض البرومبت في نافذة منبثقة
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'info',
                title: 'البرومبت المرسل إلى DeepSeek للتصميم',
                html: `<div style="text-align: right; direction: rtl; max-height: 500px; overflow-y: auto;">
                    <pre style="white-space: pre-wrap; word-wrap: break-word; background: #f5f5f5; padding: 15px; border-radius: 8px; font-size: 12px; text-align: right; font-family: 'Cairo', Arial, sans-serif;">${prompt.replace(/\n/g, '<br>')}</pre>
                </div>`,
                width: '90%',
                confirmButtonText: 'نسخ البرومبت',
                cancelButtonText: 'إغلاق',
                showCancelButton: true,
                reverseButtons: true,
                didOpen: (toast) => {
                    const confirmButton = Swal.getConfirmButton();
                    if (confirmButton) {
                        confirmButton.onclick = () => {
                            navigator.clipboard.writeText(prompt).then(() => {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'تم النسخ!',
                                    text: 'تم نسخ البرومبت إلى الحافظة',
                                    timer: 2000,
                                    showConfirmButton: false
                                });
                            }).catch(err => {
                                console.error('Failed to copy prompt: ', err);
                                Swal.fire({
                                    icon: 'error',
                                    title: 'خطأ في النسخ',
                                    text: 'فشل نسخ البرومبت إلى الحافظة',
                                    confirmButtonText: 'حسناً'
                                });
                            });
                        };
                    }
                }
            });
        } else {
            alert('البرومبت:\n\n' + prompt);
        }
        
    } catch (error) {
        console.error('Error showing design prompt:', error);
        let errorMessage = 'حدث خطأ أثناء إنشاء البرومبت. يرجى المحاولة مرة أخرى.';
        if (error.message) {
            errorMessage = error.message;
        }
        
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'error',
                title: 'خطأ',
                text: errorMessage,
                confirmButtonText: 'حسناً'
            });
        } else {
            alert(errorMessage);
        }
    } finally {
        // Re-enable button
        const showDesignPromptBtn = document.getElementById('show-design-prompt-btn');
        const showDesignPromptIcon = document.getElementById('show-design-prompt-icon');
        const showDesignPromptText = document.getElementById('show-design-prompt-text');
        const showDesignPromptSpinner = document.getElementById('show-design-prompt-spinner');
        
        if (showDesignPromptBtn) {
            showDesignPromptBtn.disabled = false;
        }
        if (showDesignPromptIcon) {
            showDesignPromptIcon.classList.remove('hidden');
        }
        if (showDesignPromptText) {
            showDesignPromptText.textContent = 'اظهر البرومبت';
        }
        if (showDesignPromptSpinner) {
            showDesignPromptSpinner.classList.add('hidden');
        }
    }
}

// Suggest Design Function
window.suggestDesign = async function suggestDesign() {
    console.log('suggestDesign function called');
    
    try {
        const designTextarea = document.getElementById('design');
        const designRecommendationsInput = document.getElementById('design_recommendations');
        const suggestDesignBtn = document.getElementById('suggest-design-btn');
        const suggestDesignIcon = document.getElementById('suggest-design-icon');
        const suggestDesignText = document.getElementById('suggest-design-text');
        const suggestDesignSpinner = document.getElementById('suggest-design-spinner');
        
        // Also handle the create-design-btn
        const createDesignBtn = document.getElementById('create-design-btn');
        const createDesignIcon = document.getElementById('create-design-icon');
        const createDesignText = document.getElementById('create-design-text');
        const createDesignSpinner = document.getElementById('create-design-spinner');
        
        const activeBtn = suggestDesignBtn || createDesignBtn;
        if (!activeBtn) {
            console.error('Design button not found');
            alert('خطأ: لم يتم العثور على الزر');
            return;
        }
        
        // Get task ID
        const taskId = {{ $task->id }};
        
        // Get design content
        let design = '';
        if (designTextarea && designTextarea.value) {
            design = designTextarea.value;
        }
        
        // Get design recommendations
        let designRecommendations = '';
        if (designRecommendationsInput) {
            designRecommendations = designRecommendationsInput.value.trim() || '';
        }
        
        // Disable button and show loading
        // Disable both buttons
        if (suggestDesignBtn) {
            suggestDesignBtn.disabled = true;
            if (suggestDesignIcon) suggestDesignIcon.classList.add('hidden');
            if (suggestDesignText) suggestDesignText.textContent = 'جاري اقتراح التصميم...';
            if (suggestDesignSpinner) suggestDesignSpinner.classList.remove('hidden');
        }
        if (createDesignBtn) {
            createDesignBtn.disabled = true;
            if (createDesignIcon) createDesignIcon.classList.add('hidden');
            if (createDesignText) createDesignText.textContent = 'جاري عمل التصميم...';
            if (createDesignSpinner) createDesignSpinner.classList.remove('hidden');
        }
        
        const csrfTokenElement = document.querySelector('meta[name="csrf-token"]');
        if (!csrfTokenElement) {
            throw new Error('CSRF token not found');
        }
        const csrfToken = csrfTokenElement.getAttribute('content');
        if (!csrfToken) {
            throw new Error('CSRF token content is empty');
        }
        
        console.log('Sending request to /tasks/suggest-design', {
            task_id: taskId,
            has_design: !!design,
            has_recommendations: !!designRecommendations
        });
        
        const response = await fetch('/tasks/suggest-design', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify({
                task_id: taskId,
                design: design,
                design_recommendations: designRecommendations
            })
        });
        
        console.log('Response status:', response.status);
        console.log('Response ok:', response.ok);
        
        if (!response.ok) {
            const errorText = await response.text();
            console.error('Error response:', errorText);
            let errorData;
            try {
                errorData = JSON.parse(errorText);
            } catch (e) {
                errorData = { error: errorText || `فشل في اقتراح التصميم (${response.status})` };
            }
            throw new Error(errorData.error || errorData.message || `فشل في اقتراح التصميم (${response.status})`);
        }
        
        const data = await response.json();
        
        if (!data.success) {
            throw new Error(data.error || 'فشل في اقتراح التصميم');
        }
        
        const suggestedDesign = data.design;
        
        if (!suggestedDesign || suggestedDesign.trim() === '') {
            throw new Error('لم يتم اقتراح تصميم');
        }
        
        // Update design field
        if (designTextarea) {
            designTextarea.value = suggestedDesign;
            designTextarea.dispatchEvent(new Event('input', { bubbles: true }));
        }
        
        // Re-enable buttons and hide loading
        if (suggestDesignBtn) {
            suggestDesignBtn.disabled = false;
            if (suggestDesignIcon) suggestDesignIcon.classList.remove('hidden');
            if (suggestDesignText) suggestDesignText.textContent = 'اقترح تصميم';
            if (suggestDesignSpinner) suggestDesignSpinner.classList.add('hidden');
        }
        if (createDesignBtn) {
            createDesignBtn.disabled = false;
            if (createDesignIcon) createDesignIcon.classList.remove('hidden');
            if (createDesignText) createDesignText.textContent = 'عمل التصميم';
            if (createDesignSpinner) createDesignSpinner.classList.add('hidden');
        }
        
        // Show success message
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'success',
                title: 'تم بنجاح!',
                text: 'تم اقتراح التصميم بنجاح',
                confirmButtonText: 'حسناً',
                timer: 2000
            });
        }
        
    } catch (error) {
        console.error('Error suggesting design:', error);
        let errorMessage = 'حدث خطأ أثناء اقتراح التصميم. يرجى المحاولة مرة أخرى.';
        if (error.message) {
            errorMessage = error.message;
        }
        
        // Re-enable buttons in case of error
        const suggestDesignBtn = document.getElementById('suggest-design-btn');
        const suggestDesignIcon = document.getElementById('suggest-design-icon');
        const suggestDesignText = document.getElementById('suggest-design-text');
        const suggestDesignSpinner = document.getElementById('suggest-design-spinner');
        
        const createDesignBtn = document.getElementById('create-design-btn');
        const createDesignIcon = document.getElementById('create-design-icon');
        const createDesignText = document.getElementById('create-design-text');
        const createDesignSpinner = document.getElementById('create-design-spinner');
        
        if (suggestDesignBtn) {
            suggestDesignBtn.disabled = false;
            if (suggestDesignIcon) suggestDesignIcon.classList.remove('hidden');
            if (suggestDesignText) suggestDesignText.textContent = 'اقترح تصميم';
            if (suggestDesignSpinner) suggestDesignSpinner.classList.add('hidden');
        }
        if (createDesignBtn) {
            createDesignBtn.disabled = false;
            if (createDesignIcon) createDesignIcon.classList.remove('hidden');
            if (createDesignText) createDesignText.textContent = 'عمل التصميم';
            if (createDesignSpinner) createDesignSpinner.classList.add('hidden');
        }
        
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'error',
                title: 'خطأ',
                text: errorMessage,
                confirmButtonText: 'حسناً'
            });
        } else {
            alert(errorMessage);
        }
    }
}

// Generate Design Image Function
window.generateDesignImage = async function generateDesignImage() {
    console.log('generateDesignImage function called');
    
    try {
        const designTextarea = document.getElementById('design');
        const workDesignBtn = document.getElementById('work-design-btn');
        const workDesignIcon = document.getElementById('work-design-icon');
        const workDesignText = document.getElementById('work-design-text');
        const workDesignSpinner = document.getElementById('work-design-spinner');
        
        if (!workDesignBtn) {
            console.error('work-design-btn not found');
            alert('خطأ: لم يتم العثور على الزر');
            return;
        }
        
        // Get task ID
        const taskId = {{ $task->id }};
        
        // Get design content
        let design = '';
        if (designTextarea && designTextarea.value) {
            design = designTextarea.value.trim();
        }
        
        if (!design || design === '') {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'warning',
                    title: 'تنبيه',
                    text: 'يرجى إدخال محتوى التصميم أولاً',
                    confirmButtonText: 'حسناً'
                });
            } else {
                alert('يرجى إدخال محتوى التصميم أولاً');
            }
            return;
        }
        
        // Disable button and show loading
        workDesignBtn.disabled = true;
        if (workDesignIcon) workDesignIcon.classList.add('hidden');
        if (workDesignText) workDesignText.textContent = 'جاري إنشاء الصورة...';
        if (workDesignSpinner) workDesignSpinner.classList.remove('hidden');
        
        const csrfTokenElement = document.querySelector('meta[name="csrf-token"]');
        if (!csrfTokenElement) {
            throw new Error('CSRF token not found');
        }
        const csrfToken = csrfTokenElement.getAttribute('content');
        if (!csrfToken) {
            throw new Error('CSRF token content is empty');
        }
        
        console.log('Sending request to /tasks/generate-design-image', {
            task_id: taskId,
            has_design: !!design
        });
        
        const response = await fetch('/tasks/generate-design-image', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify({
                task_id: taskId,
                design: design
            })
        });
        
        console.log('Response status:', response.status);
        console.log('Response ok:', response.ok);
        
        if (!response.ok) {
            const errorText = await response.text();
            console.error('Error response:', errorText);
            let errorData;
            try {
                errorData = JSON.parse(errorText);
            } catch (e) {
                errorData = { error: errorText || `فشل في إنشاء الصورة (${response.status})` };
            }
            throw new Error(errorData.error || errorData.message || `فشل في إنشاء الصورة (${response.status})`);
        }
        
        const data = await response.json();
        
        if (!data.success) {
            throw new Error(data.error || 'فشل في إنشاء الصورة');
        }
        
        const imageData = data.image;
        
        if (!imageData || imageData.trim() === '') {
            throw new Error('لم يتم إنشاء صورة');
        }
        
        // Re-enable button and hide loading
        workDesignBtn.disabled = false;
        if (workDesignIcon) workDesignIcon.classList.remove('hidden');
        if (workDesignText) workDesignText.textContent = 'عمل التصميم';
        if (workDesignSpinner) workDesignSpinner.classList.add('hidden');
        
        // Display image in modal
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'تم إنشاء الصورة بنجاح!',
                html: `<div style="text-align: center;">
                    <img src="data:image/jpeg;base64,${imageData}" style="max-width: 100%; max-height: 70vh; border-radius: 8px; margin: 10px 0;" alt="Generated Design" />
                    <div style="margin-top: 15px;">
                        <button onclick="downloadDesignImage('${imageData}')" class="swal2-confirm swal2-styled" style="background-color: #3085d6; margin-left: 10px;">
                            تحميل الصورة
                        </button>
                    </div>
                </div>`,
                width: '90%',
                showConfirmButton: true,
                confirmButtonText: 'إغلاق',
                customClass: {
                    popup: 'swal2-popup-large'
                }
            });
        } else {
            // Fallback: open image in new window
            const imageWindow = window.open();
            imageWindow.document.write(`<img src="data:image/jpeg;base64,${imageData}" style="max-width: 100%;" />`);
        }
        
    } catch (error) {
        console.error('Error generating design image:', error);
        let errorMessage = 'حدث خطأ أثناء إنشاء الصورة. يرجى المحاولة مرة أخرى.';
        if (error.message) {
            errorMessage = error.message;
        }
        
        // Re-enable button in case of error
        const workDesignBtn = document.getElementById('work-design-btn');
        const workDesignIcon = document.getElementById('work-design-icon');
        const workDesignText = document.getElementById('work-design-text');
        const workDesignSpinner = document.getElementById('work-design-spinner');
        
        if (workDesignBtn) workDesignBtn.disabled = false;
        if (workDesignIcon) workDesignIcon.classList.remove('hidden');
        if (workDesignText) workDesignText.textContent = 'عمل التصميم';
        if (workDesignSpinner) workDesignSpinner.classList.add('hidden');
        
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'error',
                title: 'خطأ',
                text: errorMessage,
                confirmButtonText: 'حسناً'
            });
        } else {
            alert(errorMessage);
        }
    }
}

// Download Design Image Function
window.downloadDesignImage = function downloadDesignImage(base64Data) {
    try {
        const link = document.createElement('a');
        link.href = 'data:image/jpeg;base64,' + base64Data;
        link.download = 'design-' + Date.now() + '.jpg';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    } catch (error) {
        console.error('Error downloading image:', error);
        alert('حدث خطأ أثناء تحميل الصورة');
    }
}

// Copy Design Function
async function copyDesign() {
    const copyDesignBtn = document.getElementById('copy-design-btn');
    const copyDesignIcon = document.getElementById('copy-design-icon');
    const copyDesignText = document.getElementById('copy-design-text');
    
    if (!copyDesignBtn) {
        console.error('copy-design-btn not found');
        alert('خطأ: لم يتم العثور على زر النسخ');
        return;
    }
    
    copyDesignBtn.disabled = true;
    copyDesignText.textContent = 'جاري النسخ...';
    
    try {
        const designTextarea = document.getElementById('design');
        let designContent = '';
        
        if (designTextarea && designTextarea.value) {
            designContent = designTextarea.value.trim();
        }
        
        if (!designContent || designContent === '') {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'warning',
                    title: 'تنبيه',
                    text: 'لا يوجد محتوى لنسخه في حقل التصميم.',
                    confirmButtonText: 'حسناً'
                });
            } else {
                alert('لا يوجد محتوى لنسخه في حقل التصميم.');
            }
            return;
        }
        
        await navigator.clipboard.writeText(designContent);
        
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'success',
                title: 'تم النسخ!',
                text: 'تم نسخ التصميم إلى الحافظة',
                timer: 2000,
                showConfirmButton: false
            });
        } else {
            alert('تم نسخ التصميم إلى الحافظة');
        }
        
    } catch (error) {
        console.error('Error copying design:', error);
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'error',
                title: 'خطأ',
                text: 'فشل في نسخ التصميم: ' + error.message,
                confirmButtonText: 'حسناً'
            });
        } else {
            alert('فشل في نسخ التصميم: ' + error.message);
        }
    } finally {
        copyDesignBtn.disabled = false;
        copyDesignText.textContent = 'نسخ التصميم';
    }
}

// Toggle Idea Section Function
function toggleIdeaSection() {
    const content = document.getElementById('idea-section-content');
    const icon = document.getElementById('idea-section-icon');
    
    if (content && icon) {
        const isHidden = content.style.display === 'none';
        
        if (isHidden) {
            content.style.display = 'block';
            icon.textContent = 'expand_less';
            localStorage.setItem('idea-section-collapsed', 'false');
        } else {
            content.style.display = 'none';
            icon.textContent = 'expand_more';
            localStorage.setItem('idea-section-collapsed', 'true');
        }
    }
}

// Toggle Post Section Function
function togglePostSection() {
    const content = document.getElementById('post-section-content');
    const icon = document.getElementById('post-section-icon');
    
    if (content && icon) {
        const isHidden = content.style.display === 'none';
        
        if (isHidden) {
            content.style.display = 'block';
            icon.textContent = 'expand_less';
            localStorage.setItem('post-section-collapsed', 'false');
        } else {
            content.style.display = 'none';
            icon.textContent = 'expand_more';
            localStorage.setItem('post-section-collapsed', 'true');
        }
    }
}

// Toggle Design Section Function
function toggleDesignSection() {
    const content = document.getElementById('design-section-content');
    const icon = document.getElementById('design-section-icon');
    
    if (content && icon) {
        const isHidden = content.style.display === 'none';
        
        if (isHidden) {
            content.style.display = 'block';
            icon.textContent = 'expand_less';
            localStorage.setItem('design-section-collapsed', 'false');
        } else {
            content.style.display = 'none';
            icon.textContent = 'expand_more';
            localStorage.setItem('design-section-collapsed', 'true');
        }
    }
}

// Initialize Sections State
document.addEventListener('DOMContentLoaded', function() {
    // Idea Section
    const ideaContent = document.getElementById('idea-section-content');
    const ideaIcon = document.getElementById('idea-section-icon');
    const ideaCollapsed = localStorage.getItem('idea-section-collapsed') === 'true';
    
    if (ideaContent && ideaIcon) {
        if (ideaCollapsed) {
            ideaContent.style.display = 'none';
            ideaIcon.textContent = 'expand_more';
        } else {
            ideaContent.style.display = 'block';
            ideaIcon.textContent = 'expand_less';
        }
    }
    
    // Post Section
    const postContent = document.getElementById('post-section-content');
    const postIcon = document.getElementById('post-section-icon');
    const postCollapsed = localStorage.getItem('post-section-collapsed') === 'true';
    
    if (postContent && postIcon) {
        if (postCollapsed) {
            postContent.style.display = 'none';
            postIcon.textContent = 'expand_more';
        } else {
            postContent.style.display = 'block';
            postIcon.textContent = 'expand_less';
        }
    }
    
    // Design Section
    const designContent = document.getElementById('design-section-content');
    const designIcon = document.getElementById('design-section-icon');
    const designCollapsed = localStorage.getItem('design-section-collapsed') === 'true';
    
    if (designContent && designIcon) {
        if (designCollapsed) {
            designContent.style.display = 'none';
            designIcon.textContent = 'expand_more';
        } else {
            designContent.style.display = 'block';
            designIcon.textContent = 'expand_less';
        }
    }
});
</script>
@endsection

