@extends('layouts.admin')

@section('title', 'تعديل الخطة')
@section('page-title', 'تعديل خطة الاشتراك')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="card">
        <form method="POST" action="{{ route('admin.plans.update', $plan) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Name -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">اسم الخطة *</label>
                <input type="text" id="name" name="name" value="{{ old('name', $plan->name) }}" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">الوصف</label>
                <textarea id="description" name="description" rows="3"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">{{ old('description', $plan->description) }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Price & Duration -->
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="price_egp" class="block text-sm font-medium text-gray-700 mb-2">السعر (جنيه مصري) *</label>
                    <input type="number" id="price_egp" name="price_egp" value="{{ old('price_egp', $plan->price_egp) }}" step="0.01" min="0" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    @error('price_egp')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="duration_days" class="block text-sm font-medium text-gray-700 mb-2">مدة الاشتراك (يوم) *</label>
                    <input type="number" id="duration_days" name="duration_days" value="{{ old('duration_days', $plan->duration_days) }}" min="1" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    @error('duration_days')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Sort Order & Active -->
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="sort_order" class="block text-sm font-medium text-gray-700 mb-2">ترتيب العرض</label>
                    <input type="number" id="sort_order" name="sort_order" value="{{ old('sort_order', $plan->sort_order) }}" min="0"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    @error('sort_order')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center">
                    <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $plan->is_active) ? 'checked' : '' }}
                        class="w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                    <label for="is_active" class="mr-2 text-sm font-medium text-gray-700">الخطة نشطة</label>
                </div>
            </div>

            <!-- Features -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">الميزات *</label>
                <div id="features-container" class="space-y-2">
                    @php
                        $features = old('features', $plan->features->pluck('feature_name')->toArray());
                        if (empty($features)) {
                            $features = [''];
                        }
                    @endphp
                    @foreach($features as $index => $feature)
                        <div class="flex gap-2">
                            <input type="text" name="features[]" value="{{ $feature }}" placeholder="أدخل ميزة" required
                                class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                            <button type="button" onclick="removeFeature(this)" class="px-4 py-2 text-red-600 hover:bg-red-50 rounded-lg">
                                <span class="material-icons">delete</span>
                            </button>
                        </div>
                    @endforeach
                </div>
                <button type="button" onclick="addFeature()" class="mt-2 text-sm text-purple-600 hover:underline flex items-center">
                    <span class="material-icons text-sm ml-1">add</span>
                    إضافة ميزة أخرى
                </button>
                @error('features.*')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Actions -->
            <div class="flex gap-4 pt-4 border-t">
                <button type="submit" class="btn-primary">تحديث الخطة</button>
                <a href="{{ route('admin.plans.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                    إلغاء
                </a>
            </div>
        </form>
    </div>
</div>

<script>
    function addFeature() {
        const container = document.getElementById('features-container');
        const newFeature = document.createElement('div');
        newFeature.className = 'flex gap-2';
        newFeature.innerHTML = `
            <input type="text" name="features[]" placeholder="أدخل ميزة" required
                class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
            <button type="button" onclick="removeFeature(this)" class="px-4 py-2 text-red-600 hover:bg-red-50 rounded-lg">
                <span class="material-icons">delete</span>
            </button>
        `;
        container.appendChild(newFeature);
    }

    function removeFeature(button) {
        const container = document.getElementById('features-container');
        if (container.children.length > 1) {
            button.parentElement.remove();
        } else {
            alert('يجب وجود ميزة واحدة على الأقل');
        }
    }
</script>
@endsection

