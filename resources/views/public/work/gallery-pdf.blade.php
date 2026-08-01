<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: dejavusans, sans-serif;
            font-size: 11pt;
            color: #0f172a;
            line-height: 1.55;
        }
        .cover {
            text-align: center;
            padding: 28px 12px 18px;
            border-bottom: 2px solid #0d9488;
            margin-bottom: 18px;
        }
        .cover-kicker {
            color: #0d9488;
            font-size: 10pt;
            font-weight: bold;
            margin-bottom: 6px;
        }
        .cover h1 {
            font-size: 20pt;
            margin: 0 0 8px;
        }
        .cover-meta {
            color: #64748b;
            font-size: 9pt;
        }
        .stats {
            margin-top: 10px;
            font-size: 9pt;
            color: #334155;
        }
        .post {
            page-break-inside: avoid;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 12px;
            margin: 0 0 16px;
        }
        .post-head {
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 8px;
            margin-bottom: 10px;
        }
        .post-title {
            font-size: 13pt;
            font-weight: bold;
            margin: 0 0 4px;
        }
        .chips {
            color: #475569;
            font-size: 8.5pt;
        }
        .label {
            color: #0d9488;
            font-size: 8.5pt;
            font-weight: bold;
            margin: 8px 0 3px;
        }
        .caption {
            white-space: pre-wrap;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 8px 10px;
            font-size: 10pt;
        }
        .muted {
            color: #94a3b8;
            font-size: 9pt;
        }
        .img-wrap {
            text-align: center;
            margin: 8px 0;
            background: #f1f5f9;
            padding: 8px;
            border-radius: 6px;
        }
        .img-wrap img {
            max-width: 100%;
            max-height: 320px;
            height: auto;
        }
        .slide-label {
            font-size: 8pt;
            color: #64748b;
            margin-top: 3px;
        }
        .video-note {
            background: #fff1f2;
            border: 1px solid #fecdd3;
            color: #9f1239;
            padding: 8px;
            border-radius: 6px;
            font-size: 9pt;
            margin: 6px 0;
        }
        .footer {
            margin-top: 20px;
            text-align: center;
            color: #94a3b8;
            font-size: 8pt;
            border-top: 1px solid #e2e8f0;
            padding-top: 8px;
        }
    </style>
</head>
<body>
    <div class="cover">
        <div class="cover-kicker">ملف مراجعة الحملة قبل النشر</div>
        <h1>{{ $activity->title }}</h1>
        <div class="cover-meta">MarketLink · {{ $generatedAt }}</div>
        <div class="stats">{{ $posts->count() }} بوست جاهز للمراجعة (تصميم + كابشن)</div>
    </div>

    @foreach($posts as $index => $post)
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
                @elseif(!empty($image['data_uri']))
                    <div class="img-wrap">
                        <img src="{{ $image['data_uri'] }}" alt="{{ $post['title'] }}">
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
    @endforeach

    <div class="footer">MarketLink · ملف مراجعة داخلي قبل النشر</div>
</body>
</html>
