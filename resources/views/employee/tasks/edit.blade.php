@extends('layouts.employee')

@section('title', 'تعديل المهمة')
@section('page-title', 'تعديل المهمة')
@section('page-description', 'تعديل بيانات المهمة')

@section('content')
<div class="container mx-auto px-4">
    <div class="max-w-4xl mx-auto space-y-6">
        <!-- Header -->
        <div class="card p-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="w-12 h-12 logo-gradient rounded-xl flex items-center justify-center shadow-lg ml-3" style="background: {{ $task->color ?? '#6366f1' }};">
                        <span class="material-icons text-white text-xl">edit</span>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800">تعديل المهمة</h2>
                        <p class="text-gray-600">{{ $task->title }}</p>
                    </div>
                </div>
                <a href="{{ route('employee.dashboard') }}" class="flex items-center px-4 py-2 text-gray-600 hover:text-gray-800 hover:bg-gray-100 rounded-xl transition-colors">
                    <span class="material-icons text-sm ml-2">arrow_back</span>
                    العودة للوحة التحكم
                </a>
            </div>
        </div>

        <!-- Form Card -->
        <div class="card p-8">
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

            <form id="edit-task-form" method="POST" action="{{ route('employee.tasks.update', $task->id) }}" class="space-y-6" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                @if($isManager)
                    <!-- Title (Only for Managers) -->
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                            العنوان <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            id="title" 
                            name="title" 
                            value="{{ old('title', $task->title) }}"
                            required
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-colors"
                        />
                        @error('title')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                @else
                    <!-- Task Info -->
                    <div class="bg-gray-50 rounded-xl p-4 mb-6">
                        <h3 class="font-semibold text-gray-800 mb-3">معلومات المهمة</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-600">العنوان</p>
                                <p class="font-medium text-gray-800">{{ $task->title }}</p>
                            </div>
                            @if($task->monthlyPlan)
                                <div>
                                    <p class="text-sm text-gray-600">المشروع</p>
                                    <p class="font-medium text-gray-800">{{ $task->monthlyPlan->project->business_name ?? 'مشروع' }}</p>
                                </div>
                            @endif
                            @if($task->due_date)
                                <div>
                                    <p class="text-sm text-gray-600">تاريخ الانتهاء</p>
                                    <p class="font-medium text-gray-800">{{ $task->due_date->format('Y-m-d') }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

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

                <!-- Description -->
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                        الوصف
                    </label>
                    <div id="description-editor-container"></div>
                    <textarea 
                        id="description" 
                        name="description" 
                        rows="6"
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-colors"
                        placeholder="أدخل وصف المهمة (اختياري)"
                        style="display: none;"
                    >{{ old('description', $task->description) }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                @if($isManager)
                    <!-- Assigned To (Only for Managers) -->
                    <div>
                        <label for="assigned_to" class="block text-sm font-medium text-gray-700 mb-2">
                            المخصص إلى
                        </label>
                        <select 
                            id="assigned_to" 
                            name="assigned_to" 
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-colors"
                        >
                            <option value="">غير مخصص</option>
                            @foreach($employees as $employee)
                                <option value="{{ $employee->id }}" {{ old('assigned_to', $task->assigned_to) == $employee->id ? 'selected' : '' }}>
                                    {{ $employee->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('assigned_to')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Due Date (Only for Managers) -->
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
                        />
                        @error('due_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                @endif

                @if($isManager)
                    <!-- Important Links Section -->
                    <div class="form-section space-y-6 pt-6 border-t border-gray-200">
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
                    <div class="form-section space-y-6 pt-6 border-t border-gray-200">
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
                                                    href="{{ route('employee.tasks.files.download', [$task->id, $file->id]) }}" 
                                                    target="_blank"
                                                    class="text-green-600 hover:text-green-800 p-1"
                                                    title="تحميل"
                                                >
                                                    <span class="material-icons text-sm">download</span>
                                                </a>
                                                <form 
                                                    action="{{ route('employee.tasks.files.delete', [$task->id, $file->id]) }}" 
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
                @endif

                <!-- Important Links Section (for regular employees) -->
                @if(!$isManager)
                    <div class="form-section space-y-6 pt-6 border-t border-gray-200">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800 mb-2">روابط هامة</h3>
                            <p class="text-sm text-gray-600 mb-4">يمكنك إضافة روابط مهمة متعلقة بالمهمة (اختياري)</p>
                        </div>
                        
                        <div id="links-container-employee" class="space-y-4">
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
                        
                        <button type="button" id="add-link-btn-employee" class="text-primary hover:text-primary-700 font-medium flex items-center">
                            <span class="material-icons text-sm ml-2">add</span>
                            إضافة رابط آخر
                        </button>
                        @error('links.*')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Attachments Section (for regular employees) -->
                    <div class="form-section space-y-6 pt-6 border-t border-gray-200">
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
                                                    href="{{ route('employee.tasks.files.download', [$task->id, $file->id]) }}" 
                                                    target="_blank"
                                                    class="text-green-600 hover:text-green-800 p-1"
                                                    title="تحميل"
                                                >
                                                    <span class="material-icons text-sm">download</span>
                                                </a>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                        
                        <!-- New Attachments -->
                        <div>
                            <h4 class="text-sm font-medium text-gray-700 mb-3">إضافة مرفقات جديدة</h4>
                            <div id="attachments-container-employee" class="space-y-4">
                                <div class="attachment-item border-2 border-dashed border-gray-300 rounded-xl p-6 bg-gray-50">
                                    <div class="flex items-center justify-between">
                                        <div class="flex-1">
                                            <label for="attachments_employee_0" class="cursor-pointer">
                                                <div class="flex items-center justify-center py-4">
                                                    <div class="text-center">
                                                        <span class="material-icons text-5xl text-gray-400 mb-2">attach_file</span>
                                                        <p class="text-sm text-gray-600">اضغط لاختيار ملف أو اسحب الملف هنا</p>
                                                        <p class="text-xs text-gray-500 mt-1">الصور: JPG, PNG, GIF | الملفات: PDF, DOC, XLS, ZIP (حد أقصى 10MB)</p>
                                                    </div>
                                                </div>
                                                <input 
                                                    type="file" 
                                                    id="attachments_employee_0" 
                                                    name="attachments[]" 
                                                    class="hidden attachment-input"
                                                    accept=".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx,.xls,.xlsx,.zip,.rar"
                                                    data-index="employee_0"
                                                >
                                            </label>
                                        </div>
                                        <button type="button" onclick="removeAttachmentEmployee('employee_0')" class="text-red-500 hover:text-red-700 ml-4 remove-attachment-btn" style="display: none;">
                                            <span class="material-icons">delete</span>
                                        </button>
                                    </div>
                                    <div id="file-info-employee_0" class="mt-3 p-3 bg-white rounded-lg border border-gray-200 hidden">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center flex-1">
                                                <span class="material-icons text-blue-500 ml-2" id="file-icon-employee_0">attach_file</span>
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-sm font-medium text-gray-800 truncate" id="file-name-employee_0"></p>
                                                    <p class="text-xs text-gray-500" id="file-size-employee_0"></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <button type="button" id="add-attachment-btn-employee" class="mt-4 text-primary hover:text-primary-700 font-medium flex items-center">
                                <span class="material-icons text-sm ml-2">add</span>
                                إضافة مرفق آخر
                            </button>
                            @error('attachments.*')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                @endif

                <!-- Actions -->
                <div class="flex items-center justify-center space-x-4 space-x-reverse pt-6 border-t border-gray-200">
                    <button type="submit" class="btn-primary text-white px-8 py-3 rounded-xl flex items-center font-medium">
                        <span class="material-icons text-sm ml-2">save</span>
                        حفظ التغييرات
                    </button>
                    @if($isManager)
                        <form method="POST" action="{{ route('employee.tasks.destroy', $task->id) }}" class="inline" onsubmit="return confirm('هل أنت متأكد من حذف هذه المهمة؟');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-8 py-3 bg-red-600 text-white rounded-xl hover:bg-red-700 transition-colors font-medium">
                                <span class="material-icons text-sm ml-2">delete</span>
                                حذف المهمة
                            </button>
                        </form>
                    @endif
                    <a href="{{ route('employee.dashboard') }}" class="px-8 py-3 border border-gray-300 rounded-xl text-gray-700 hover:bg-gray-50 transition-colors font-medium">
                        إلغاء
                    </a>
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
    #description-editor-container .ql-container.ql-snow {
        border-top: none;
        border-bottom-right-radius: 0.75rem;
        border-bottom-left-radius: 0.75rem;
    }
</style>
@endsection

@section('scripts')
<!-- Quill Editor JS -->
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
        
        // Get the textarea and container
        const textarea = document.getElementById('description');
        const container = document.getElementById('description-editor-container');
        
        if (!textarea || !container) {
            console.error('Description elements not found!');
            return;
        }
        
        // Initialize Quill
        let quill;
        try {
            quill = new Quill('#description-editor-container', {
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
            const form = document.getElementById('edit-task-form');
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
                    
                    // إزالة حقول الملفات الفارغة قبل الإرسال
                    const fileInputs = form.querySelectorAll('input[type="file"][name="attachments[]"]');
                    fileInputs.forEach(input => {
                        if (!input.files || input.files.length === 0) {
                            // إزالة input من النموذج إذا كان فارغاً
                            input.remove();
                        }
                    });
                });
            }
        } catch (error) {
            console.error('Error initializing Quill:', error);
            // Show textarea if Quill fails
            textarea.style.display = 'block';
        }
    }
    
    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initQuill);
    } else {
        initQuill();
    }
})();

@if($isManager)
// Important Links Management
document.addEventListener('DOMContentLoaded', function() {
    const addLinkBtn = document.getElementById('add-link-btn');
    if (!addLinkBtn) return;
    
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
    addLinkBtn.addEventListener('click', function() {
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

// Attachments Management
document.addEventListener('DOMContentLoaded', function() {
    const addAttachmentBtn = document.getElementById('add-attachment-btn');
    if (!addAttachmentBtn) return;
    
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
                input.value = '';
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
            if (attachmentItem) {
                attachmentItem.classList.remove('border-blue-300', 'bg-blue-50');
                attachmentItem.classList.add('border-gray-300', 'bg-gray-50');
            }
        }
    }
    
    window.removeAttachment = function(index) {
        const attachmentInput = document.querySelector(`#attachments_${index}`);
        if (attachmentInput) {
            const attachmentItem = attachmentInput.closest('.attachment-item');
            if (attachmentItem) {
                attachmentInput.value = '';
                attachmentItem.remove();
            }
        }
    };
    
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
    addAttachmentBtn.addEventListener('click', function() {
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
                <div id="file-info-${attachmentIndex}" class="mt-3 p-3 bg-white rounded-lg border border-gray-200 hidden">
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
        attachFileEvents();
        attachmentIndex++;
    });
});
@endif

@if(!$isManager)
// Important Links Management (for regular employees)
document.addEventListener('DOMContentLoaded', function() {
    const addLinkBtn = document.getElementById('add-link-btn-employee');
    if (!addLinkBtn) return;
    
    let linkIndex = {{ count(old('links', $task->task_data['links'] ?? [])) }};
    
    function removeLink(index) {
        const linkItem = document.querySelector(`input[name="links[${index}][url]"]`)?.closest('.link-item');
        if (linkItem) {
            linkItem.remove();
            updateLinkNumbers();
        }
    }
    
    function updateLinkNumbers() {
        const linkItems = document.querySelectorAll('#links-container-employee .link-item');
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
    addLinkBtn.addEventListener('click', function() {
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
        document.getElementById('links-container-employee').insertAdjacentHTML('beforeend', newLinkHtml);
        linkIndex++;
        updateLinkNumbers();
    });
    
    // تحديث الأرقام عند التحميل
    updateLinkNumbers();
});

// Attachments Management (for regular employees)
document.addEventListener('DOMContentLoaded', function() {
    const addAttachmentBtn = document.getElementById('add-attachment-btn-employee');
    if (!addAttachmentBtn) return;
    
    let attachmentIndex = 1;
    
    // ربط أحداث لجميع حقول الملفات الموجودة
    function attachFileEvents() {
        document.querySelectorAll('#attachments-container-employee .attachment-input').forEach(input => {
            if (!input.dataset.eventAttached) {
                input.addEventListener('change', function() {
                    const index = this.dataset.index;
                    handleFileSelect(this, index);
                });
                input.dataset.eventAttached = 'true';
            }
        });
    }
    
    // استدعاء عند تحميل الصفحة
    attachFileEvents();
    
    function handleFileSelect(input, index) {
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
                input.value = '';
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
            if (attachmentItem) {
                attachmentItem.classList.remove('border-blue-300', 'bg-blue-50');
                attachmentItem.classList.add('border-gray-300', 'bg-gray-50');
            }
        }
    }
    
    window.removeAttachmentEmployee = function(index) {
        const attachmentInput = document.querySelector(`#attachments_${index}`);
        if (attachmentInput) {
            const attachmentItem = attachmentInput.closest('.attachment-item');
            if (attachmentItem) {
                attachmentInput.value = '';
                attachmentItem.remove();
            }
        }
    };
    
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
    addAttachmentBtn.addEventListener('click', function() {
        const newAttachmentHtml = `
            <div class="attachment-item border-2 border-dashed border-gray-300 rounded-xl p-6 bg-gray-50">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <label for="attachments_employee_${attachmentIndex}" class="cursor-pointer">
                            <div class="flex items-center justify-center py-4">
                                <div class="text-center">
                                    <span class="material-icons text-5xl text-gray-400 mb-2">attach_file</span>
                                    <p class="text-sm text-gray-600">اضغط لاختيار ملف أو اسحب الملف هنا</p>
                                    <p class="text-xs text-gray-500 mt-1">الصور: JPG, PNG, GIF | الملفات: PDF, DOC, XLS, ZIP (حد أقصى 10MB)</p>
                                </div>
                            </div>
                            <input 
                                type="file" 
                                id="attachments_employee_${attachmentIndex}" 
                                name="attachments[]" 
                                class="hidden attachment-input"
                                accept=".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx,.xls,.xlsx,.zip,.rar"
                                data-index="employee_${attachmentIndex}"
                            >
                        </label>
                    </div>
                    <button type="button" onclick="removeAttachmentEmployee('employee_${attachmentIndex}')" class="text-red-500 hover:text-red-700 ml-4 remove-attachment-btn" style="display: none;">
                        <span class="material-icons">delete</span>
                    </button>
                </div>
                <div id="file-info-employee_${attachmentIndex}" class="mt-3 p-3 bg-white rounded-lg border border-gray-200 hidden">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center flex-1">
                            <span class="material-icons text-blue-500 ml-2" id="file-icon-employee_${attachmentIndex}">attach_file</span>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-800 truncate" id="file-name-employee_${attachmentIndex}"></p>
                                <p class="text-xs text-gray-500" id="file-size-employee_${attachmentIndex}"></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
        document.getElementById('attachments-container-employee').insertAdjacentHTML('beforeend', newAttachmentHtml);
        attachFileEvents();
        attachmentIndex++;
    });
    
    // إزالة حقول الملفات الفارغة قبل إرسال النموذج
    const formEmployee = document.getElementById('edit-task-form');
    if (formEmployee) {
        formEmployee.addEventListener('submit', function(e) {
            const fileInputs = formEmployee.querySelectorAll('input[type="file"][name="attachments[]"]');
            fileInputs.forEach(input => {
                if (!input.files || input.files.length === 0) {
                    // إزالة input من النموذج إذا كان فارغاً
                    input.remove();
                }
            });
        });
    }
});
@endif
</script>
@endsection

