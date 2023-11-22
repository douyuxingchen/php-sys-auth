<?php
namespace Douyuxingchen\PhpSysAuth;

use Douyuxingchen\PhpSysAuth\Exceptions\Exception;
use Illuminate\Support\ServiceProvider;

/**
 * --------------------------------------------------------------------------
 * 引导发布应用程序
 * --------------------------------------------------------------------------
 *
 * php artisan vendor:publish --provider="Douyuxingchen\PhpSysAuth\ConfigProvider"
 * 用户需要在每次更新包的时候都要覆盖资源，你可以使用 --force 标志。
 *
 * @throws Exception
 */
class ConfigProvider extends ServiceProvider
{
    /**
     * @throws Exception
     */
    public function boot()
    {
        $config_path = realpath(__DIR__.'/Config/sys_auth.php');
        if(!$config_path) {
            throw new Exception("Error: Unable to obtain the path to the configuration file sys_auth.php");
        }

        $this->publishes([
            $config_path => config_path('sys_auth.php'),
        ]);
    }
}