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
        body { font-family: 'Cairo', sans-serif; background: #f8fafc; }
    </style>
</head>
<body class="min-h-screen text-gray-800">
    <header class="bg-white border-b border-gray-200">
        <div class="max-w-5xl mx-auto px-4 py-4 flex items-center justify-between gap-3">
            <div class="flex items-center gap-2">
                <span class="material-icons text-indigo-600">share</span>
                <span class="font-bold text-gray-800">MarketLink · عرض عام</span>
            </div>
            <span class="text-xs text-gray-400">عرض فقط</span>
        </div>
    </header>
    <main class="max-w-5xl mx-auto px-4 py-6">
        @yield('content')
    </main>
</body>
</html>
