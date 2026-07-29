@extends($ideasLayout ?? 'layouts.dashboard')

@section('title', 'الأفكار المقترحة')
@section('page-title', 'الأفكار المقترحة')
@section('page-description', 'Inbox منفصل لتجميع الأفكار قبل تحويلها لمشروع في مساحة العمل')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-xl flex items-center">
            <span class="material-icons ml-2">check_circle</span>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl flex items-center">
            <span class="material-icons ml-2">error</span>
            {{ session('error') }}
        </div>
    @endif

    {{-- فورم فكرة جديدة --}}
    <div class="card rounded-2xl p-6">
        <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
            <span class="material-icons text-indigo-600">lightbulb</span>
            فكرة جديدة
        </h2>

        <form method="POST" action="{{ route('ideas.store') }}" class="space-y-4 mt-5">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">العنوان</label>
                <input type="text" name="title" required value="{{ old('title') }}"
                       class="w-full px-3 py-2.5 rounded-xl border-2 border-gray-200 text-sm focus:border-primary focus:outline-none"
                       placeholder="مثال: أفكار حملة سوشيال لشهر يوليو">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">وصف مختصر</label>
                <textarea name="description" rows="3"
                          class="w-full px-3 py-2.5 rounded-xl border-2 border-gray-200 text-sm focus:border-primary focus:outline-none"
                          placeholder="تفاصيل الفكرة...">{{ old('description') }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">النوع المقترح (اختياري)</label>
                <select name="type" class="w-full px-3 py-2.5 rounded-xl border-2 border-gray-200 text-sm focus:border-primary focus:outline-none">
                    <option value="">بدون تحديد</option>
                    @foreach($types as $key => $label)
                        <option value="{{ $key }}" @selected(old('type') === $key)>{{ $label }}</option>
                    @endforeach
                </select>
                <p class="text-[11px] text-gray-500 mt-1">القيم لازم تطابق enum الموجود في `/work` حرفيًا.</p>
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <button type="submit" class="btn-primary text-white px-5 py-2.5 rounded-xl font-medium flex items-center gap-2">
                    <span class="material-icons text-base">add</span>
                    إضافة
                </button>
            </div>
        </form>
    </div>

    {{-- الأفكار المقترحة --}}
    <div class="space-y-3">
        <h3 class="font-bold text-gray-800 flex items-center gap-2">
            <span class="material-icons text-indigo-600 text-base">inbox</span>
            الأفكار المقترحة ({{ $suggestedIdeas->count() }})
        </h3>

        @if($suggestedIdeas->isEmpty())
            <div class="card rounded-2xl p-10 text-center text-gray-500">
                <span class="material-icons text-5xl text-gray-300 block">inbox</span>
                <p class="mt-3 font-bold text-gray-700">لا توجد أفكار مقترحة</p>
                <p class="text-sm mt-1">أضف فكرة جديدة من فورم “فكرة جديدة”.</p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($suggestedIdeas as $idea)
                    @php
                        $owner = $isOwner($idea);
                        $canEdit = $canManage || $owner;
                    @endphp
                    <div class="card rounded-2xl p-5">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <p class="text-sm text-gray-500">{{ $idea->creator_name }}</p>
                                <h4 class="font-bold text-gray-900 mt-1 truncate">{{ $idea->title }}</h4>
                            </div>
                            @if($idea->type && array_key_exists($idea->type, $types))
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg bg-indigo-50 text-indigo-800 border border-indigo-100 text-[11px] font-bold">
                                    {{ $types[$idea->type] }}
                                </span>
                            @endif
                        </div>

                        @if($idea->description)
                            <p class="text-sm text-gray-600 mt-3 whitespace-pre-line">
                                {{ \Illuminate\Support\Str::limit($idea->description, 180) }}
                            </p>
                        @endif

                        <p class="text-xs text-gray-400 mt-3">
                            {{ $idea->created_at?->format('Y/m/d') }}
                        </p>

                        <div class="flex flex-wrap items-center gap-2 mt-4">
                            @if($canConvertIdea)
                                <form method="POST" action="{{ route('ideas.convert', $idea) }}" onsubmit="return confirm('هل أنت متأكد؟ هنفتح فورم نشاط جديد في /work مملي ببيانات الفكرة.');" class="inline">
                                    @csrf
                                    <button type="submit"
                                            class="px-3 py-2 rounded-xl bg-emerald-50 text-emerald-700 hover:bg-emerald-100 text-xs font-semibold inline-flex items-center gap-1">
                                        <span class="material-icons text-sm">swap_horiz</span>
                                        تحويل لمشروع
                                    </button>
                                </form>
                            @endif

                            @if($canEdit)
                                <a href="{{ route('ideas.edit', $idea) }}"
                                   class="px-3 py-2 rounded-xl bg-indigo-50 text-indigo-700 hover:bg-indigo-100 text-xs font-semibold inline-flex items-center gap-1">
                                    <span class="material-icons text-sm">edit</span>
                                    تعديل
                                </a>

                                <form method="POST" action="{{ route('ideas.archive', $idea) }}" class="inline">
                                    @csrf
                                    <button type="submit"
                                            class="px-3 py-2 rounded-xl bg-amber-50 text-amber-700 hover:bg-amber-100 text-xs font-semibold inline-flex items-center gap-1">
                                        <span class="material-icons text-sm">schedule</span>
                                        تأجيل
                                    </button>
                                </form>

                                <form method="POST" action="{{ route('ideas.destroy', $idea) }}" onsubmit="return confirm('حذف الفكرة نهائيًا؟');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="px-3 py-2 rounded-xl bg-red-50 text-red-700 hover:bg-red-100 text-xs font-semibold inline-flex items-center gap-1">
                                        <span class="material-icons text-sm">delete</span>
                                        حذف
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- الأرشيف --}}
    <div class="space-y-3 pt-6">
        <h3 class="font-bold text-gray-800 flex items-center gap-2">
            <span class="material-icons text-gray-500 text-base">archive</span>
            أفكار مؤجلة/مؤرشفة ({{ $archivedIdeas->count() }})
        </h3>

        @if($archivedIdeas->isEmpty())
            <div class="text-sm text-gray-500">مفيش أفكار مؤرشفة</div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($archivedIdeas as $idea)
                    <div class="card rounded-2xl p-5">
                        <div class="min-w-0">
                            <p class="text-sm text-gray-500">{{ $idea->creator_name }}</p>
                            <h4 class="font-bold text-gray-900 mt-1 truncate">{{ $idea->title }}</h4>
                            @if($idea->description)
                                <p class="text-sm text-gray-600 mt-3 whitespace-pre-line">
                                    {{ \Illuminate\Support\Str::limit($idea->description, 160) }}
                                </p>
                            @endif
                            <p class="text-xs text-gray-400 mt-3">
                                {{ $idea->created_at?->format('Y/m/d') }}
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection

