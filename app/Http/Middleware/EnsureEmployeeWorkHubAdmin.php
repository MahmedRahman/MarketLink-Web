<?php

namespace App\Http\Middleware;

use App\Support\WorkHub;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureEmployeeWorkHubAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $employee = Auth::guard('employee')->user();

        abort_unless($employee && $employee->isWorkHubAdmin(), 403);

        WorkHub::shareContext($request);

        return $next($request);
    }
}
