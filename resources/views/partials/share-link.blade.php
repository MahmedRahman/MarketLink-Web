{{--
  متغيرات:
  - $label: عنوان الرابط
  - $url: الرابط
  - $hint: وصف اختياري
  - $inputId: id فريد للحقل
--}}
@php
    $inputId = $inputId ?? ('share-'.uniqid());
@endphp
<div class="rounded-xl border border-indigo-100 bg-indigo-50/50 p-3 space-y-2">
    <div class="flex items-center gap-1.5 text-xs font-semibold text-indigo-800">
        <span class="material-icons text-sm">share</span>
        <span>{{ $label }}</span>
    </div>
    @if(!empty($hint))
        <p class="text-[11px] text-indigo-600/80">{{ $hint }}</p>
    @endif
    <div class="flex gap-2">
        <input type="text" id="{{ $inputId }}" readonly value="{{ $url }}" dir="ltr"
               class="min-w-0 flex-1 px-3 py-2 rounded-lg border border-indigo-100 bg-white text-xs text-gray-700 focus:outline-none"
               onclick="this.select()">
        <button type="button"
                onclick="window.copyShareLink && window.copyShareLink('{{ $inputId }}', this)"
                class="shrink-0 px-3 py-2 rounded-lg bg-indigo-600 text-white text-xs font-semibold hover:bg-indigo-700 inline-flex items-center gap-1">
            <span class="material-icons text-sm">content_copy</span>
            نسخ
        </button>
    </div>
</div>
