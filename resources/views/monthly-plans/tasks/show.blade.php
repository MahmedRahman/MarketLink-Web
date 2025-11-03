@extends('layouts.dashboard')

@section('title', 'تفاصيل المهمة')
@section('page-title', 'تفاصيل المهمة')
@section('page-description', 'عرض تفاصيل المهمة والتعليقات')

@section('content')
<div class="container mx-auto px-4">
    <div class="max-w-5xl mx-auto space-y-6">
        <!-- Header -->
        <div class="card page-header rounded-2xl p-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="w-12 h-12 logo-gradient rounded-2xl flex items-center justify-center shadow-lg icon-spacing ml-3" style="background: {{ $task->color ?? '#6366f1' }};">
                        <span class="material-icons text-white text-xl">task</span>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800">{{ $task->title }}</h2>
                        <p class="text-gray-600">تفاصيل المهمة والتعليقات</p>
                    </div>
                </div>
                <div class="flex items-center space-x-3 rtl:space-x-reverse">
                    <a href="{{ route('monthly-plans.tasks.edit', [$monthlyPlan, $task]) }}" class="flex items-center px-4 py-2 text-gray-600 hover:text-gray-800 hover:bg-gray-100 rounded-xl transition-colors">
                        <span class="material-icons text-sm ml-2">edit</span>
                        تعديل
                    </a>
                    <a href="{{ route('monthly-plans.show', $monthlyPlan) }}" class="flex items-center px-4 py-2 text-gray-600 hover:text-gray-800 hover:bg-gray-100 rounded-xl transition-colors">
                        <span class="material-icons text-sm ml-2">arrow_back</span>
                        العودة للخطة
                    </a>
                </div>
            </div>
        </div>

        <!-- Success/Error Messages -->
        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg flex items-center">
                <span class="material-icons ml-2">check_circle</span>
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg flex items-center">
                <span class="material-icons ml-2">error</span>
                {{ session('error') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Task Details -->
                <div class="card rounded-2xl p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">تفاصيل المهمة</h3>
                    
                    @if($task->description)
                        <div class="mb-6">
                            <h4 class="text-sm font-medium text-gray-700 mb-2">الوصف</h4>
                            <div class="prose max-w-none text-gray-800">{!! $task->description !!}</div>
                        </div>
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <div>
                            <h4 class="text-sm font-medium text-gray-700 mb-2">الحالة</h4>
                            <span class="px-3 py-1 text-sm rounded-full bg-{{ $task->status_color }}-100 text-{{ $task->status_color }}-800">
                                {{ $task->status_badge }}
                            </span>
                        </div>

                        @if($task->assignedEmployee)
                            <div>
                                <h4 class="text-sm font-medium text-gray-700 mb-2">المخصص إلى</h4>
                                <p class="text-gray-800">{{ $task->assignedEmployee->name }}</p>
                            </div>
                        @endif

                        @if($task->goal)
                            <div>
                                <h4 class="text-sm font-medium text-gray-700 mb-2">الهدف</h4>
                                <p class="text-gray-800">{{ $task->goal->goal_name }} ({{ $task->goal->target_value }} {{ $task->goal->unit ?? '' }})</p>
                                <p class="text-xs text-gray-500 mt-1">المحقق: {{ $task->goal->achieved_value }} / {{ $task->goal->target_value }}</p>
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

                        <div>
                            <h4 class="text-sm font-medium text-gray-700 mb-2">اللون</h4>
                            <div class="flex items-center">
                                <div class="w-8 h-8 rounded-lg border border-gray-300" style="background-color: {{ $task->color ?? '#6366f1' }};"></div>
                                <span class="mr-2 text-gray-800">{{ $task->color ?? '#6366f1' }}</span>
                            </div>
                        </div>
                    </div>

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

                    <!-- Attachments -->
                    @if($task->files && $task->files->count() > 0)
                        <div>
                            <h4 class="text-sm font-medium text-gray-700 mb-3">المرفقات</h4>
                            <div class="space-y-2">
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
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Comments Section -->
                <div class="card rounded-2xl p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">التعليقات</h3>

                    <!-- Add Comment Form -->
                    <form action="{{ route('monthly-plans.tasks.comments.store', [$monthlyPlan, $task]) }}" method="POST" class="mb-6">
                        @csrf
                        <div class="mb-4">
                            <label for="comment" class="block text-sm font-medium text-gray-700 mb-2">
                                إضافة تعليق
                            </label>
                            <textarea 
                                id="comment" 
                                name="comment" 
                                rows="4"
                                required
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-colors"
                                placeholder="اكتب تعليقك هنا..."
                            ></textarea>
                            @error('comment')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <button type="submit" class="btn-primary text-white px-6 py-2 rounded-xl flex items-center">
                            <span class="material-icons text-sm ml-2">send</span>
                            إرسال التعليق
                        </button>
                    </form>

                    <!-- Comments List -->
                    <div class="space-y-4">
                        @forelse($task->comments as $comment)
                            <div class="border-b border-gray-200 pb-4 last:border-b-0">
                                <div class="flex items-start justify-between mb-2">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-primary/10 rounded-full flex items-center justify-center ml-3">
                                            <span class="material-icons text-primary">person</span>
                                        </div>
                                        <div>
                                            <p class="text-sm font-semibold text-gray-800">{{ $comment->user->name }}</p>
                                            <p class="text-xs text-gray-500">{{ $comment->created_at->format('Y-m-d H:i') }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="mr-13">
                                    <p class="text-gray-800 whitespace-pre-wrap">{{ $comment->comment }}</p>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-8 text-gray-500">
                                <span class="material-icons text-5xl mb-2 block">comment</span>
                                <p>لا توجد تعليقات بعد</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Task Info -->
                <div class="card rounded-2xl p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">معلومات المهمة</h3>
                    <div class="space-y-4">
                        <div>
                            <p class="text-xs text-gray-500 mb-1">تاريخ الإنشاء</p>
                            <p class="text-sm font-medium text-gray-800">{{ $task->created_at->format('Y-m-d H:i') }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 mb-1">آخر تحديث</p>
                            <p class="text-sm font-medium text-gray-800">{{ $task->updated_at->format('Y-m-d H:i') }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 mb-1">عدد المرفقات</p>
                            <p class="text-sm font-medium text-gray-800">{{ $task->files->count() }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 mb-1">عدد التعليقات</p>
                            <p class="text-sm font-medium text-gray-800">{{ $task->comments->count() }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

