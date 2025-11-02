@extends('layouts.dashboard')

@section('title', 'إضافة مهمة جديدة')
@section('page-title', 'إضافة مهمة جديدة')
@section('page-description', 'إضافة مهمة جديدة للخطة الشهرية')

@section('content')
<div class="container mx-auto px-4">
    <div class="max-w-4xl mx-auto space-y-6">
        <!-- Header -->
        <div class="card page-header rounded-2xl p-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="w-12 h-12 logo-gradient rounded-2xl flex items-center justify-center shadow-lg icon-spacing ml-3">
                        <span class="material-icons text-white text-xl">add_task</span>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800">إضافة مهمة جديدة</h2>
                        <p class="text-gray-600">إضافة مهمة جديدة للخطة: {{ $monthlyPlan->month }} {{ $monthlyPlan->year }}</p>
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

            <form id="create-task-form" method="POST" action="{{ route('monthly-plans.tasks.store', $monthlyPlan) }}" class="space-y-6">
                @csrf

                <!-- Title -->
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                        عنوان المهمة <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="title" 
                        name="title" 
                        value="{{ old('title') }}" 
                        required
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-colors"
                        placeholder="أدخل عنوان المهمة"
                    >
                    @error('title')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                        الوصف
                    </label>
                    <textarea 
                        id="description" 
                        name="description" 
                        rows="6"
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-colors"
                        placeholder="أدخل وصف المهمة (اختياري)"
                    >{!! old('description') !!}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
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
                                <option value="{{ $employee->id }}" {{ old('assigned_to') == $employee->id ? 'selected' : '' }}>
                                    {{ $employee->name }} - {{ $employee->role_badge }}
                                </option>
                            @endforeach
                        </select>
                        @error('assigned_to')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Due Date -->
                    <div>
                        <label for="due_date" class="block text-sm font-medium text-gray-700 mb-2">
                            تاريخ الانتهاء
                        </label>
                        <input 
                            type="date" 
                            id="due_date" 
                            name="due_date" 
                            value="{{ old('due_date') }}"
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
                            الحالة
                        </label>
                        <select 
                            id="status" 
                            name="status"
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-colors"
                        >
                            <option value="todo" {{ old('status', 'todo') == 'todo' ? 'selected' : '' }}>مهام</option>
                            <option value="in_progress" {{ old('status') == 'in_progress' ? 'selected' : '' }}>قيد التنفيذ</option>
                            <option value="review" {{ old('status') == 'review' ? 'selected' : '' }}>قيد المراجعة</option>
                            <option value="done" {{ old('status') == 'done' ? 'selected' : '' }}>مكتملة</option>
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
                                value="{{ old('color', '#6366f1') }}"
                                class="w-20 h-12 border border-gray-300 rounded-xl cursor-pointer"
                            >
                            <input 
                                type="text" 
                                id="color-hex" 
                                value="{{ old('color', '#6366f1') }}" 
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

                <!-- Actions -->
                <div class="flex items-center justify-between pt-6 border-t border-gray-200 rtl-spacing">
                    <a href="{{ route('monthly-plans.show', $monthlyPlan) }}" class="px-6 py-3 border border-gray-300 rounded-xl text-gray-700 hover:bg-gray-50 transition-colors">
                        إلغاء
                    </a>
                    <button type="submit" form="create-task-form" class="btn-primary text-white px-8 py-3 rounded-xl flex items-center">
                        <span class="material-icons text-sm ml-2">add</span>
                        إضافة المهمة
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
    .ql-toolbar {
        direction: rtl;
        border-top-right-radius: 0.75rem;
        border-top-left-radius: 0.75rem;
    }
    #description-editor {
        border-bottom-right-radius: 0.75rem;
        border-bottom-left-radius: 0.75rem;
    }
</style>
@endsection

@section('scripts')
<!-- Quill Editor JS -->
<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Hide the original textarea
    const textarea = document.getElementById('description');
    textarea.style.display = 'none';
    
    // Create Quill editor container
    const editorContainer = document.createElement('div');
    editorContainer.id = 'description-editor';
    editorContainer.style.height = '300px';
    textarea.parentNode.insertBefore(editorContainer, textarea);
    
    // Initialize Quill
    const quill = new Quill('#description-editor', {
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
    const initialContent = textarea.value || '';
    if (initialContent) {
        // Check if content is HTML or plain text
        const tempDiv = document.createElement('div');
        tempDiv.innerHTML = initialContent;
        if (tempDiv.children.length > 0 || initialContent.includes('<')) {
            quill.root.innerHTML = initialContent;
        } else {
            quill.setText(initialContent);
        }
    }
    
    // Update textarea content before form submission
    const form = document.getElementById('create-task-form');
    if (form) {
        form.addEventListener('submit', function(e) {
            // تحديث محتوى textarea من Quill editor قبل الإرسال
            if (typeof quill !== 'undefined' && quill) {
                const content = quill.root.innerHTML;
                // تنظيف المحتوى الفارغ (Quill يضيف <p><br></p> إذا كان فارغاً)
                if (content === '<p><br></p>' || content === '<p></p>' || !content.trim()) {
                    textarea.value = '';
                } else {
                    textarea.value = content;
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
            // السماح للنموذج بالإرسال بشكل طبيعي - لا نمنع default behavior
        });
    }

    // Sync color picker with hex input
    document.getElementById('color').addEventListener('input', function() {
        document.getElementById('color-hex').value = this.value;
    });

    document.getElementById('color-hex').addEventListener('input', function() {
        const hexValue = this.value;
        if (/^#[A-Fa-f0-9]{6}$/.test(hexValue)) {
            document.getElementById('color').value = hexValue;
        }
    });
});
</script>
@endsection

