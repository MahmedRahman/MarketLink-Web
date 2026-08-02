{{-- كارت بوست واحد في PDF --}}
<div class="post">
    <div class="post-head">
        <div class="post-title">
            @if($post['post_number'])
                بوست {{ $post['post_number'] }} —
            @endif
            {{ $post['title'] }}
        </div>
        <div class="chips">
            @if($post['type_label']){{ $post['type_label'] }} · @endif
            @if($post['is_carousel'])كروسيل · @endif
            @if($post['designer'])تصميم: {{ $post['designer'] }} · @endif
            @if($post['stage_label']){{ $post['stage_label'] }}@endif
            @if($post['publish_label'])
                · موعد النشر: {{ $post['publish_label'] }}
            @endif
            @if(!empty($post['platforms']))
                · {{ implode(' / ', $post['platforms']) }}
            @endif
        </div>
    </div>

    @foreach($post['images'] as $slideIndex => $image)
        @if($image['kind'] === 'video')
            <div class="video-note">
                فيديو مرفق: {{ $image['name'] ?: ('شريحة '.($slideIndex + 1)) }} — افتح المعرض لمشاهدته
            </div>
        @elseif(!empty($image['path']))
            <div class="img-wrap">
                <img src="{{ $image['path'] }}" alt="{{ $post['title'] }}" width="520">
                @if(count($post['images']) > 1)
                    <div class="slide-label">شريحة {{ $slideIndex + 1 }} / {{ count($post['images']) }}</div>
                @endif
            </div>
        @endif
    @endforeach

    <div class="label">الكابشن / المحتوى اللي هينزل</div>
    @if(filled($post['caption']))
        <div class="caption">{{ $post['caption'] }}</div>
    @else
        <div class="muted">مفيش كابشن مكتوب</div>
    @endif

    @if(filled($post['idea']))
        <div class="label">الفكرة</div>
        <div class="caption">{{ $post['idea'] }}</div>
    @endif

    @if(filled($post['tov']))
        <div class="label">Tone of Voice</div>
        <div class="caption">{{ $post['tov'] }}</div>
    @endif
</div>
