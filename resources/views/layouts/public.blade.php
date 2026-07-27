<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'MarketLink')</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        :root {
            --ml-ink: #0f172a;
            --ml-muted: #64748b;
            --ml-accent: #0d9488;
            --ml-accent-soft: #ccfbf1;
            --ml-surface: #ffffff;
            --ml-bg: #eef2f6;
        }
        body {
            font-family: 'Cairo', sans-serif;
            color: var(--ml-ink);
            background:
                radial-gradient(1200px 500px at 10% -10%, rgba(13, 148, 136, 0.12), transparent 55%),
                radial-gradient(900px 400px at 100% 0%, rgba(15, 23, 42, 0.06), transparent 50%),
                linear-gradient(180deg, #f4f7fa 0%, #eef2f6 40%, #e8eef4 100%);
            min-height: 100vh;
        }
        .share-shell { max-width: 56rem; }
        .share-panel {
            background: var(--ml-surface);
            border: 1px solid rgba(15, 23, 42, 0.06);
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.04);
        }
        .file-tile {
            transition: transform .18s ease, box-shadow .18s ease;
        }
        .file-tile:hover {
            transform: translateY(-2px);
            box-shadow: 0 14px 28px rgba(15, 23, 42, 0.1);
        }
        #files { scroll-margin-top: 5rem; }
    </style>
    @stack('head')
</head>
<body>
    <header class="sticky top-0 z-30 backdrop-blur-md bg-white/80 border-b border-slate-200/70">
        <div class="share-shell mx-auto px-4 py-3.5 flex items-center justify-between gap-3">
            <div class="flex items-center gap-2.5 min-w-0">
                <div class="w-9 h-9 rounded-xl bg-teal-600 text-white flex items-center justify-center shrink-0">
                    <span class="material-icons text-xl">hub</span>
                </div>
                <div class="min-w-0">
                    <p class="font-extrabold text-slate-900 leading-none tracking-tight">MarketLink</p>
                    <p class="text-[11px] text-slate-500 mt-0.5">عرض مشترك · بدون تسجيل</p>
                </div>
            </div>
            @hasSection('header-actions')
                <div class="shrink-0">@yield('header-actions')</div>
            @else
                <span class="text-[11px] font-semibold text-teal-700 bg-teal-50 border border-teal-100 px-2.5 py-1 rounded-lg">عرض فقط</span>
            @endif
        </div>
    </header>

    <main class="share-shell mx-auto px-4 py-6 md:py-8">
        @yield('content')
    </main>

    <footer class="share-shell mx-auto px-4 pb-8 pt-2 text-center text-[11px] text-slate-400">
        MarketLink · مشاركة آمنة للمحتوى والتصميم
    </footer>

    <script>
        window.copyShareText = async function (text, btn) {
            try {
                await navigator.clipboard.writeText(text);
                if (btn) {
                    const original = btn.innerHTML;
                    btn.innerHTML = '<span class="material-icons text-sm">check</span> تم النسخ';
                    setTimeout(function () { btn.innerHTML = original; }, 1600);
                }
            } catch (e) {
                prompt('انسخ الرابط:', text);
            }
        };
        window.copyShareLink = async function (inputId, btn) {
            const input = document.getElementById(inputId);
            if (!input) return;
            await window.copyShareText(input.value, btn);
        };
    </script>
    @stack('scripts')
</body>
</html>
