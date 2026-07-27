<?php

use App\Support\WorkHub;

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
