@extends('layouts.employee')

@section('title', 'تفاصيل المهمة')
@section('page-title', $task->title)
@section('page-description', 'عرض تفاصيل المهمة')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <!-- Action Buttons -->
    <div class="flex items-center justify-end space-x-3 space-x-reverse">
        <a href="{{ route('employee.tasks.edit', $task->id) }}" class="flex items-center px-4 py-2 text-purple-600 hover:text-purple-800 hover:bg-purple-50 rounded-xl transition-colors">
            <span class="material-icons text-sm ml-2">edit</span>
            تعديل
        </a>
        <a href="{{ route('employee.dashboard') }}" class="flex items-center px-4 py-2 text-gray-600 hover:text-gray-800 hover:bg-gray-100 rounded-xl transition-colors">
            <span class="material-icons text-sm ml-2">arrow_back</span>
            العودة
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg flex items-center">
            <span class="material-icons ml-2">check_circle</span>
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Task Details -->
            <div class="card p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">تفاصيل المهمة</h3>
                
                @if($task->description)
                    <div class="mb-6">
                        <h4 class="text-sm font-medium text-gray-700 mb-2">الوصف</h4>
                        <div class="text-gray-800 prose max-w-none">{!! $task->description !!}</div>
                    </div>
                @endif

                <!-- Important Links -->
                @if($task->task_data && isset($task->task_data['links']) && count($task->task_data['links']) > 0)
                    <div class="mb-6">
                        <h4 class="text-sm font-medium text-gray-700 mb-3">روابط هامة</h4>
                        <div class="space-y-2">
                            @foreach($task->task_data['links'] as $link)
                                <a href="{{ $link['url'] }}" target="_blank" class="flex items-center text-blue-600 hover:text-blue-800">
                                    <span class="material-icons text-sm ml-2">link</span>
                                    {{ $link['title'] ?: $link['url'] }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <h4 class="text-sm font-medium text-gray-700 mb-2">الحالة</h4>
                        <span class="px-3 py-1 text-sm rounded-full bg-{{ $task->status_color }}-100 text-{{ $task->status_color }}-800">
                            {{ $task->status_badge }}
                        </span>
                    </div>

                    @if($task->goal)
                        <div>
                            <h4 class="text-sm font-medium text-gray-700 mb-2">الهدف</h4>
                            <p class="text-gray-800">{{ $task->goal->goal_name }}</p>
                        </div>
                    @endif

                    @if($task->due_date)
                        <div>
                            <h4 class="text-sm font-medium text-gray-700 mb-2">تاريخ الانتهاء</h4>
                            <p class="text-gray-800 flex items-center">
                                <span class="material-icons text-sm ml-1">event</span>
                                {{ $task->due_date->format('Y-m-d') }}
                                @if($task->due_date->isPast() && $task->status !== 'done')
                                    <span class="text-red-500 mr-2">(متأخرة)</span>
                                @endif
                            </p>
                        </div>
                    @endif

                    @if($task->monthlyPlan)
                        <div>
                            <h4 class="text-sm font-medium text-gray-700 mb-2">الخطة الشهرية</h4>
                            <p class="text-gray-800">{{ $task->monthlyPlan->month }} {{ $task->monthlyPlan->year }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Files -->
            @if($task->files && $task->files->count() > 0)
                <div class="card p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">المرفقات</h3>
                    <div class="space-y-4">
                        @foreach($task->files as $file)
                            @if($file->isImage())
                                <!-- Image Preview -->
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <div class="flex items-center justify-between mb-3">
                                        <div class="flex items-center">
                                            <span class="material-icons text-gray-500 ml-2">image</span>
                                            <span class="text-sm text-gray-800">{{ $file->file_name }}</span>
                                        </div>
                                        <div class="flex items-center space-x-2 space-x-reverse">
                                            <a href="{{ route('employee.tasks.files.view', [$task->id, $file->id]) }}" 
                                               target="_blank"
                                               class="text-blue-500 hover:text-blue-700 text-sm flex items-center"
                                               onclick="event.preventDefault(); openImageModal('{{ route('employee.tasks.files.view', [$task->id, $file->id]) }}', '{{ $file->file_name }}');">
                                                <span class="material-icons text-sm ml-1">zoom_in</span>
                                                تكبير
                                            </a>
                                            <a href="{{ route('employee.tasks.files.download', [$task->id, $file->id]) }}" 
                                               class="text-green-500 hover:text-green-700 text-sm flex items-center">
                                                <span class="material-icons text-sm ml-1">download</span>
                                                تحميل
                                            </a>
                                        </div>
                                    </div>
                                    <div class="mt-3">
                                        <img src="{{ route('employee.tasks.files.view', [$task->id, $file->id]) }}" 
                                             alt="{{ $file->file_name }}"
                                             class="w-full h-48 object-cover rounded-lg cursor-pointer hover:opacity-90 transition-opacity"
                                             onclick="openImageModal('{{ route('employee.tasks.files.view', [$task->id, $file->id]) }}', '{{ $file->file_name }}')">
                                    </div>
                                </div>
                            @else
                                <!-- Regular File -->
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                    <div class="flex items-center">
                                        <span class="material-icons text-gray-500 ml-2">{{ $file->file_icon }}</span>
                                        <div class="mr-3">
                                            <span class="text-sm text-gray-800 block">{{ $file->file_name }}</span>
                                            <span class="text-xs text-gray-500">{{ $file->formatted_file_size }}</span>
                                        </div>
                                    </div>
                                    <a href="{{ route('employee.tasks.files.download', [$task->id, $file->id]) }}" 
                                       class="text-blue-500 hover:text-blue-700 text-sm flex items-center">
                                        <span class="material-icons text-sm ml-1">download</span>
                                        تحميل
                                    </a>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Quick Actions -->
            <div class="card p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">إجراءات سريعة</h3>
                <div class="space-y-3">
                    <a href="{{ route('employee.tasks.edit', $task->id) }}" 
                       class="w-full btn-primary text-white px-4 py-3 rounded-xl flex items-center justify-center">
                        <span class="material-icons text-sm ml-2">edit</span>
                        تعديل المهمة
                    </a>
                </div>
            </div>

            <!-- Task Info -->
            <div class="card p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">معلومات إضافية</h3>
                <div class="space-y-3 text-sm">
                    <div>
                        <span class="text-gray-600">تاريخ الإنشاء:</span>
                        <span class="text-gray-800 font-medium">{{ $task->created_at->format('Y-m-d') }}</span>
                    </div>
                    <div>
                        <span class="text-gray-600">آخر تحديث:</span>
                        <span class="text-gray-800 font-medium">{{ $task->updated_at->format('Y-m-d') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Image Modal -->
<div id="imageModal" class="fixed inset-0 bg-black bg-opacity-75 z-50 hidden flex items-center justify-center p-4" onclick="closeImageModal()">
    <div class="relative max-w-7xl max-h-full" onclick="event.stopPropagation()">
        <button onclick="closeImageModal()" class="absolute -top-10 left-0 text-white hover:text-gray-300 text-2xl font-bold">
            <span class="material-icons text-4xl">close</span>
        </button>
        <img id="modalImage" src="" alt="" class="max-w-full max-h-[90vh] object-contain rounded-lg">
        <p id="modalImageName" class="text-white text-center mt-4 text-sm"></p>
    </div>
</div>
@endsection

@section('scripts')
<script>
function openImageModal(imageUrl, imageName) {
    const modal = document.getElementById('imageModal');
    const modalImage = document.getElementById('modalImage');
    const modalImageName = document.getElementById('modalImageName');
    
    modalImage.src = imageUrl;
    modalImage.alt = imageName;
    modalImageName.textContent = imageName;
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeImageModal() {
    const modal = document.getElementById('imageModal');
    modal.classList.add('hidden');
    document.body.style.overflow = 'auto';
}

// Close modal on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeImageModal();
    }
});
</script>
@endsection

