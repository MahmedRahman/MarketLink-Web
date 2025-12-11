@extends('layouts.dashboard')

@section('title', 'Brand Style Extractor')
@section('page-title', 'Brand Style Extractor')
@section('page-description', 'إدارة محتوى العلامة التجارية للمشاريع')

@section('content')
<div class="space-y-6">
    <!-- Header Actions -->
    <div class="card page-header rounded-2xl p-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <div class="w-12 h-12 logo-gradient rounded-2xl flex items-center justify-center shadow-lg icon-spacing ml-3">
                    <i class="fas fa-palette text-white text-xl"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">Brand Style Extractor</h2>
                    <p class="text-gray-600">إدارة محتوى العلامة التجارية للمشاريع</p>
                </div>
            </div>
            <div class="flex items-center space-x-3 rtl:space-x-reverse">
                <a href="{{ route('brand-style-extractors.create') }}" class="btn-primary text-white px-6 py-3 rounded-xl flex items-center hover:no-underline">
                    <i class="fas fa-plus text-sm ml-2"></i>
                    إضافة محتوى جديد
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-xl" role="alert">
            <div class="flex items-center">
                <i class="fas fa-check-circle ml-2"></i>
                <span>{{ session('success') }}</span>
            </div>
        </div>
    @endif

    @if($extractors->count() > 0)
        <!-- Group by Project -->
        @foreach($extractorsByProject as $projectId => $projectExtractors)
            @php
                $project = $projectExtractors->first()->project;
            @endphp
            <div class="card rounded-2xl overflow-hidden">
                <div class="bg-gradient-to-r from-primary to-secondary p-6">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center ml-3">
                                <i class="fas fa-project-diagram text-white"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-white">{{ $project->business_name ?? 'مشروع غير محدد' }}</h3>
                                <p class="text-white/80 text-sm">{{ $projectExtractors->count() }} نوع محتوى</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="p-6">
                    <div class="space-y-4">
                        @foreach($projectExtractors->groupBy('content_type') as $contentType => $typeExtractors)
                            <div class="border border-gray-200 rounded-xl p-4">
                                <div class="flex items-center justify-between mb-3">
                                    <h4 class="text-lg font-semibold text-gray-800 flex items-center">
                                        <i class="fas fa-tag text-primary ml-2"></i>
                                        {{ $typeExtractors->first()->content_type_label }}
                                    </h4>
                                    <span class="text-sm text-gray-500 bg-gray-100 px-3 py-1 rounded-full">
                                        {{ $typeExtractors->count() }} عنصر
                                    </span>
                                </div>
                                
                                <div class="space-y-3">
                                    @foreach($typeExtractors as $extractor)
                                        <div class="bg-gray-50 rounded-lg p-4 border-r-4 border-primary">
                                            <div class="flex items-start justify-between">
                                                <div class="flex-1">
                                                    <p class="text-gray-700 content-preview">{{ $extractor->content }}</p>
                                                    <p class="text-xs text-gray-500 mt-2">
                                                        <i class="fas fa-clock ml-1"></i>
                                                        {{ $extractor->created_at->format('Y-m-d H:i') }}
                                                        @if($extractor->creator)
                                                            | <i class="fas fa-user ml-1"></i> {{ $extractor->creator->name }}
                                                        @endif
                                                    </p>
                                                </div>
                                                <div class="flex items-center space-x-2 rtl:space-x-reverse mr-4">
                                                    <a href="{{ route('brand-style-extractors.show', $extractor) }}" class="text-blue-600 hover:text-blue-900 p-2" title="عرض التفاصيل">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('brand-style-extractors.edit', $extractor) }}" class="text-yellow-600 hover:text-yellow-900 p-2" title="تعديل">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <button onclick="confirmDelete('{{ route('brand-style-extractors.destroy', $extractor) }}', 'تأكيد حذف المحتوى', 'هل أنت متأكد من حذف هذا المحتوى؟')" class="text-red-600 hover:text-red-900 p-2" title="حذف">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endforeach
    @else
        <div class="card rounded-2xl p-12 text-center">
            <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-palette text-gray-400 text-3xl"></i>
            </div>
            <h3 class="text-xl font-semibold text-gray-800 mb-2">لا توجد محتويات</h3>
            <p class="text-gray-600 mb-6">ابدأ بإضافة محتوى جديد للعلامة التجارية</p>
            <a href="{{ route('brand-style-extractors.create') }}" class="btn-primary text-white px-6 py-3 rounded-xl inline-flex items-center hover:no-underline">
                <i class="fas fa-plus text-sm ml-2"></i>
                إضافة محتوى جديد
            </a>
        </div>
    @endif
</div>

<style>
.content-preview {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    text-overflow: ellipsis;
    line-height: 1.6;
    max-height: 3.2em; /* 2 lines * 1.6 line-height */
    word-wrap: break-word;
    white-space: pre-wrap;
}
</style>

<script>
function confirmDelete(url, title, message) {
    Swal.fire({
        title: title,
        text: message,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'نعم، احذف',
        cancelButtonText: 'إلغاء',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = url;
            
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            methodInput.value = 'DELETE';
            form.appendChild(methodInput);
            
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = csrfToken;
            form.appendChild(csrfInput);
            
            document.body.appendChild(form);
            form.submit();
        }
    });
}
</script>
@endsection

