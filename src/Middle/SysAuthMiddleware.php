<?php
namespace Douyuxingchen\PhpSysAuth\Middle;

use Douyuxingchen\PhpSysAuth\Auth\AuthApi;
use Douyuxingchen\PhpSysAuth\Exceptions\ErrCodeException;
use Douyuxingchen\PhpSysAuth\Exceptions\TokenInvalidException;
use Douyuxingchen\PhpSysAuth\Exceptions\ValidationException;
use Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode as Middleware;
use Closure;

/**
 * example
 *
 * 中间件接入该SDK演示代码
 */
class SysAuthMiddleware extends Middleware
{
    public function handle($request, Closure $next)
    {
        $appKey = $request->header('AppKey');
        $authToken = $request->header('Authorization');

        try {
            (new AuthApi($appKey))->setToken($authToken)->verify();
        } catch (ErrCodeException $e) {
            // TODO 业务错误
            $code = $e->getCode();
            $message = $e->getMessage();
            return response()->json([
                'code' => $code,
                'message' => $message,
            ]);
        } catch (ValidationException|TokenInvalidException $e) {
            // TODO 签名认证失败
            $code = $e->getCode();
            $message = $e->getMessage();
            return response()->json([
                'code' => $code,
                'message' => $message,
            ]);
        }

        return $next($request);
    }

}