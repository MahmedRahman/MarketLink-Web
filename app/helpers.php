<?php

use App\Support\WorkHub;
use Illuminate\Support\HtmlString;

if (! function_exists('work_route')) {
    /**
     * رابط مساحة العمل حسب السياق (أدمن أو أكونت منجر).
     */
    function work_route(string $name, mixed $parameters = [], bool $absolute = true): string
    {
        $prefix = view()->shared('workRoutePrefix') ?? WorkHub::routePrefix();

        return route($prefix.'.'.$name, $parameters, $absolute);
    }
}

if (! function_exists('linkify_text')) {
    /**
     * يحول الروابط داخل النص إلى <a> قابلة للضغط مع الحفاظ على باقي النص آمناً.
     */
    function linkify_text(?string $text): HtmlString
    {
        if ($text === null || $text === '') {
            return new HtmlString('');
        }

        $escaped = e($text);
        $pattern = '/(https?:\/\/[^\s<]+)|(www\.[^\s<]+)/iu';

        $linked = preg_replace_callback($pattern, function (array $matches) {
            $raw = $matches[0];
            $trailing = '';
            if (preg_match('/[.,);:!?؟]+$/u', $raw, $m)) {
                $trailing = $m[0];
                $raw = substr($raw, 0, -strlen($trailing));
            }

            $href = str_starts_with(strtolower($raw), 'www.') ? 'https://'.$raw : $raw;

            return '<a href="'.$href.'" target="_blank" rel="noopener noreferrer" class="text-indigo-600 underline break-all hover:text-indigo-800">'.$raw.'</a>'.$trailing;
        }, $escaped) ?? $escaped;

        return new HtmlString(nl2br($linked, false));
    }
}
