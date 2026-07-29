@extends($ideasLayout ?? 'layouts.dashboard')

@section('title', 'تعديل فكرة')
@section('page-title', 'تعديل فكرة')
@section('page-description', 'تعديل البيانات للفكرة المقترحة')

@section('content')
<div class="max-w-3xl mx-auto space-y-5">
    <div class="flex items-center justify-between gap-3 flex-wrap">
        <a href="{{ route('ideas.index') }}" class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-primary">
            <span class="material-icons text-lg">arrow_back</span>
            رجوع للأفكار
        </a>
    </div>

    <div class="card rounded-2xl p-6">
        <h2 class="text-lg font-bold text-gray-900">تعديل فكرة</h2>

        <form method="POST" action="{{ route('ideas.update', $idea) }}" class="space-y-4 mt-5">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">العنوان</label>
                <input type="text" name="title" required value="{{ old('title', $idea->title) }}"
                       class="w-full px-3 py-2.5 rounded-xl border-2 border-gray-200 text-sm focus:border-primary focus:outline-none">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">وصف مختصر</label>
                <textarea name="description" rows="4"
                          class="w-full px-3 py-2.5 rounded-xl border-2 border-gray-200 text-sm focus:border-primary focus:outline-none">{{ old('description', $idea->description) }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">النوع المقترح (اختياري)</label>
                <select name="type" class="w-full px-3 py-2.5 rounded-xl border-2 border-gray-200 text-sm focus:border-primary focus:outline-none">
                    <option value="">بدون تحديد</option>
                    @foreach($types as $key => $label)
                        <option value="{{ $key }}" @selected(old('type', $idea->type) === $key)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex flex-wrap items-center gap-3 pt-2">
                <button type="submit" class="btn-primary text-white px-5 py-2.5 rounded-xl font-medium">
                    حفظ التعديل
                </button>
                <a href="{{ route('ideas.index') }}" class="px-5 py-2.5 rounded-xl font-medium bg-gray-100 text-gray-700 hover:bg-gray-200">
                    إلغاء
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

