<?php
namespace Douyuxingchen\PhpSysAuth;

use Douyuxingchen\PhpSysAuth\Exceptions\Exception;
use Illuminate\Support\ServiceProvider;

/**
 * --------------------------------------------------------------------------
 * 引导发布应用程序
 * --------------------------------------------------------------------------
 *
 * 在 config/app.php 文件中的 providers 数组中注册你的服务提供者。
 * 'providers' => [
 *      // 其他服务提供者...
 *      Douyuxingchen\PhpSysAuth\ConfigProvider::class,
 * ],
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
        $configPath = __DIR__ . '/Config/sys_auth.php';
        if (!file_exists($configPath)) {
            throw new Exception("Error: Unable to find the configuration file sys_auth.php");
        }

        $this->publishes([
            $configPath => config_path('sys_auth.php'),
        ]);
    }
}