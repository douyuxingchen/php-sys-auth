<?php
namespace Douyuxingchen\PhpSysAuth\Middle;

use Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode as Middleware;
use Closure;

class SysAuthMiddleware extends Middleware
{
    public function handle($request, Closure $next)
    {
        return $next($request);
    }

}