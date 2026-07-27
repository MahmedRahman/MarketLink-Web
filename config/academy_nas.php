<?php

return [
    'enabled' => (bool) env('ACADEMY_NAS_ENABLED', false),
    'host' => env('ACADEMY_NAS_HOST', '192.168.68.80'),
    'port' => (int) env('ACADEMY_NAS_PORT', 22),
    'username' => env('ACADEMY_NAS_USER', 'webadmin'),
    'password' => env('ACADEMY_NAS_PASSWORD', ''),
    'base_path' => env(
        'ACADEMY_NAS_BASE',
        '/home/webadmin/shared/Maha Elkhadry Academy/03_Social_Content'
    ),
    // جذر File Browser على NAS (مجلد shared)
    'shared_root' => env(
        'ACADEMY_NAS_SHARED_ROOT',
        '/home/webadmin/shared'
    ),
    'public_base_url' => env('ACADEMY_NAS_PUBLIC_URL', 'https://nas.marketlink.app'),
];
