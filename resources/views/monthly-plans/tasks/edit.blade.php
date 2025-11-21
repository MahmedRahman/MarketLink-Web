@extends('layouts.dashboard')

@section('title', 'تعديل المهمة')
@section('page-title', 'تعديل المهمة')
@section('page-description', 'تعديل بيانات المهمة')

@section('content')
<div class="container mx-auto px-4" data-generate-description-url="{{ route('tasks.generate-description') }}">
    <div class="max-w-4xl mx-auto space-y-6">
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

            <form id="edit-task-form" method="POST" action="{{ route('monthly-plans.tasks.update', [$monthlyPlan->id, $task->id]) }}" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT')

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

                <!-- Idea -->
                <div class="hidden">
                    <label for="idea" class="block text-sm font-medium text-gray-700 mb-2">
                        الفكرة
                    </label>
                    <textarea 
                        id="idea" 
                        name="idea" 
                        rows="6"
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-colors"
                        placeholder="أدخل فكرة المهمة (اختياري)"
                    >{!! old('idea', $task->idea) !!}</textarea>
                    @error('idea')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <button 
                        type="button" 
                        id="generate-description-btn" 
                        class="mt-3 px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-700 transition-colors flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed"
                        onclick="generateDescription()"
                    >
                        <span class="material-icons text-sm" id="generate-icon">auto_awesome</span>
                        <span id="generate-text">إنشاء الوصف تلقائياً</span>
                        <span class="material-icons text-sm animate-spin hidden" id="generate-spinner">sync</span>
                    </button>
                </div>

                <!-- Description -->
                <div id="description-container">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                        الوصف
                    </label>
                    <div id="description-wrapper" class="relative">
                        <textarea 
                            id="description" 
                            name="description" 
                            rows="6"
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-colors"
                            placeholder="أدخل وصف المهمة (اختياري)"
                        >{!! old('description', $task->description) !!}</textarea>
                        <div id="description-shimmer" class="hidden absolute inset-0 bg-gradient-to-r from-transparent via-white/60 to-transparent animate-shimmer rounded-xl pointer-events-none"></div>
                    </div>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Goal -->
                    <div>
                        <label for="goal_id" class="block text-sm font-medium text-gray-700 mb-2">
                            الهدف
                        </label>
                        <select 
                            id="goal_id" 
                            name="goal_id"
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-colors"
                        >
                            <option value="">اختر الهدف (اختياري)</option>
                            @foreach($goals as $goal)
                                <option value="{{ $goal->id }}" {{ old('goal_id', $task->goal_id) == $goal->id ? 'selected' : '' }}>
                                    {{ $goal->goal_name }} ({{ $goal->target_value }})
                                </option>
                            @endforeach
                        </select>
                        @error('goal_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">ربط المهمة بهدف من أهداف الخطة</p>
                    </div>

                    <!-- Assigned To -->
                    <div>
                        <label for="assigned_to" class="block text-sm font-medium text-gray-700 mb-2">
                            تعيين إلى
                        </label>
                        <select 
                            id="assigned_to" 
                            name="assigned_to"
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-colors"
                        >
                            <option value="">قائمة المهام</option>
                            @foreach($employees as $employee)
                                <option value="{{ $employee->id }}" {{ old('assigned_to', $task->assigned_to) == $employee->id ? 'selected' : '' }}>
                                    {{ $employee->name }} - {{ $employee->role_badge }}
                                </option>
                            @endforeach
                        </select>
                        @error('assigned_to')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Due Date -->
                    <div>
                        <label for="due_date" class="block text-sm font-medium text-gray-700 mb-2">
                            تاريخ الانتهاء
                        </label>
                        <input 
                            type="date" 
                            id="due_date" 
                            name="due_date" 
                            value="{{ old('due_date', $task->due_date ? $task->due_date->format('Y-m-d') : '') }}"
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-colors"
                        >
                        @error('due_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Status -->
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                            الحالة <span class="text-red-500">*</span>
                        </label>
                        <select 
                            id="status" 
                            name="status"
                            required
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-colors"
                        >
                            <option value="todo" {{ old('status', $task->status) == 'todo' ? 'selected' : '' }}>مهام</option>
                            <option value="in_progress" {{ old('status', $task->status) == 'in_progress' ? 'selected' : '' }}>قيد التنفيذ</option>
                            <option value="review" {{ old('status', $task->status) == 'review' ? 'selected' : '' }}>قيد المراجعة</option>
                            <option value="done" {{ old('status', $task->status) == 'done' ? 'selected' : '' }}>مكتملة</option>
                            <option value="publish" {{ old('status', $task->status) == 'publish' ? 'selected' : '' }}>نشر</option>
                            <option value="archived" {{ old('status', $task->status) == 'archived' ? 'selected' : '' }}>أرشيف</option>
                        </select>
                        @error('status')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Color -->
                    <div>
                        <label for="color" class="block text-sm font-medium text-gray-700 mb-2">
                            اللون
                        </label>
                        <div class="flex items-center gap-3">
                            <input 
                                type="color" 
                                id="color" 
                                name="color" 
                                value="{{ old('color', $task->color ?? '#6366f1') }}"
                                class="w-20 h-12 border border-gray-300 rounded-xl cursor-pointer"
                            >
                            <input 
                                type="text" 
                                id="color-hex" 
                                value="{{ old('color', $task->color ?? '#6366f1') }}" 
                                placeholder="#6366f1"
                                class="flex-1 px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-colors"
                                pattern="^#[A-Fa-f0-9]{6}$"
                                name=""
                            >
                        </div>
                        @error('color')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Important Links Section -->
                <div class="form-section space-y-6">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800 mb-2">روابط هامة</h3>
                        <p class="text-sm text-gray-600 mb-4">يمكنك إضافة روابط مهمة متعلقة بالمهمة (اختياري)</p>
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
                    <button type="submit" form="edit-task-form" class="btn-primary text-white px-8 py-3 rounded-xl flex items-center">
                        <span class="material-icons text-sm ml-2">save</span>
                        حفظ التغييرات
                    </button>
                </div>
            </form>
        </div>
    </div>
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
        let ideaQuill = null;
        if (ideaTextarea) {
            ideaTextarea.style.display = 'none';
            
            // Create Quill editor container
            const ideaEditorContainer = document.createElement('div');
            ideaEditorContainer.id = 'idea-editor';
            ideaEditorContainer.className = 'ql-container ql-snow';
            ideaEditorContainer.style.height = '300px';
            ideaTextarea.parentNode.insertBefore(ideaEditorContainer, ideaTextarea);
            
            try {
                ideaQuill = new Quill('#idea-editor', {
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
                    placeholder: 'أدخل فكرة المهمة (اختياري)...'
                });
                
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
                if (window.descriptionQuill && descriptionTextarea) {
                    const descriptionContent = window.descriptionQuill.root.innerHTML;
                    if (descriptionContent === '<p><br></p>' || descriptionContent === '<p></p>' || !descriptionContent.trim()) {
                        descriptionTextarea.value = '';
                    } else {
                        descriptionTextarea.value = descriptionContent;
                    }
                }
                
                // تحديث محتوى idea textarea من Quill editor قبل الإرسال
                if (ideaQuill && ideaTextarea) {
                    const ideaContent = ideaQuill.root.innerHTML;
                    if (ideaContent === '<p><br></p>' || ideaContent === '<p></p>' || !ideaContent.trim()) {
                        ideaTextarea.value = '';
                    } else {
                        ideaTextarea.value = ideaContent;
                    }
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
</script>
@endsection

