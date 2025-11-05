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

            <form id="create-task-form" method="POST" action="{{ route('monthly-plans.tasks.store', $monthlyPlan) }}" enctype="multipart/form-data" class="space-y-6">
                @csrf
                <!-- Hidden field for list_type -->
                <input type="hidden" id="list_type" name="list_type" value="">

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
                                <option value="{{ $goal->id }}" {{ old('goal_id') == $goal->id ? 'selected' : '' }}>
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
                                <option value="{{ $employee->id }}" {{ old('assigned_to') == $employee->id ? 'selected' : '' }}>
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

                <!-- Important Links Section -->
                <div class="form-section space-y-6">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800 mb-2">روابط هامة</h3>
                        <p class="text-sm text-gray-600 mb-4">يمكنك إضافة روابط مهمة متعلقة بالمهمة (اختياري)</p>
                    </div>
                    
                    <div id="links-container" class="space-y-4">
                        <div class="link-item p-4 border border-gray-200 rounded-xl">
                            <div class="flex items-center justify-between mb-3">
                                <h4 class="text-sm font-medium text-gray-700">رابط 1</h4>
                                <button type="button" class="remove-link text-red-500 hover:text-red-700" style="display: none;">
                                    <span class="material-icons text-sm">delete</span>
                                </button>
                            </div>
                            <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">الرابط</label>
                                        <input
                                            type="url"
                                            name="links[0][url]"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
                                            placeholder="https://example.com"
                                        />
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">عنوان الرابط</label>
                                    <input
                                        type="text"
                                        name="links[0][title]"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
                                        placeholder="مثال: رابط الموقع"
                                    />
                                </div>
                            </div>
                        </div>
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
                    
                    <button type="button" id="add-attachment-btn" class="text-primary hover:text-primary-700 font-medium flex items-center">
                        <span class="material-icons text-sm ml-2">add</span>
                        إضافة مرفق آخر
                    </button>
                    @error('attachments.*')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
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
    
    // Update list_type when assigned_to changes
    const assignedToSelect = document.getElementById('assigned_to');
    const listTypeInput = document.getElementById('list_type');
    
    function updateListType() {
        if (assignedToSelect && listTypeInput) {
            const assignedTo = assignedToSelect.value;
            // إذا تم اختيار موظف، قم بتعيين list_type إلى 'employee'
            // وإلا قم بتعيينه إلى 'tasks'
            listTypeInput.value = assignedTo ? 'employee' : 'tasks';
        }
    }
    
    // تحديث list_type عند تغيير assigned_to
    if (assignedToSelect) {
        assignedToSelect.addEventListener('change', updateListType);
        // تحديث القيمة الأولية
        updateListType();
    }
    
    // Update textarea content before form submission
    const form = document.getElementById('create-task-form');
    if (form) {
        form.addEventListener('submit', function(e) {
            // تحديث list_type قبل الإرسال بناءً على assigned_to
            updateListType();
            
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
            
            // التحقق من أن المرفقات صحيحة
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
            
            // السماح للنموذج بالإرسال بشكل طبيعي
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

    // Important Links Management
    let linkIndex = 1;
    
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
});
</script>
@endsection

