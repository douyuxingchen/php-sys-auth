# php-sys-auth  
PHP外部系统授权签名验证库。这个SDK是一个用于外部系统授权签名验证的工具，它旨在帮助您确保来自外部系统的请求是合法和受信任的。

## 特性
- 支持外部系统请求的签名验证。
- 提供了简单而强大的 API，方便集成到您的 PHP 应用程序中。
- 帮助您保护您的应用程序免受未经授权的访问。

## 说明文档

### 安装 
```bash
composer require "douyuxingchen/php-sys-auth"
```

### 更新
```bash
composer update "douyuxingchen/php-sys-auth" --ignore-platform-reqs
```

### Laravel框架快速接入

#### 步骤1：注册服务提供者
在 config/app.php 文件中的 providers 数组中注册你的服务提供者。
```php
'providers' => [
  // 其他服务提供者...
  Douyuxingchen\PhpSysAuth\ConfigProvider::class,
],
```

#### 步骤2：生成配置文件
运行以下命令生成配置文件
```bash
php artisan vendor:publish --provider="Douyuxingchen\PhpSysAuth\ConfigProvider"
```
用户需要在每次更新包的时候都要覆盖资源，你可以使用 --force 标志。

#### 步骤3：创建中间件
```bash
php artisan make:middleware SysAuthMiddleware
```

#### 步骤4：注册中间件

在 `app/Http/Kernel.php` 文件的 `$routeMiddleware` 数组中，注册中间件：

```php
protected $routeMiddleware = [
    'api.sys.auth' => \App\Http\Middleware\SysAuthMiddleware::class,
];
```

#### 步骤5：中间件接入
```php
public function handle($request, Closure $next)
{
    $appKey = $request->header('AppKey');
    $authToken = $request->header('Authorization');
    
    if(!$appKey || !$authToken) {
        return response()->json(['message' => 'AppKey or Authorization not found'], 400);
    }

    try {
        (new AuthApi($appKey))->setToken($authToken)->verify();
    } catch (ConfigException $e){
        // TODO 配置文件错误
        $code = $e->getCode();
        $message = $e->getMessage();
        return response()->json('Config error', 500);
    } catch (ErrCodeException $e) {
        // TODO 业务错误
        $code = $e->getCode();
        $message = $e->getMessage();
        // 根据不同的错误信息，进行不同的业务处理
        return response()->json(['message' => $message]);
    } catch (ValidationException|TokenInvalidException $e) {
        // TODO 签名认证失败
        $code = $e->getCode();
        $message = $e->getMessage();
        // 签名验证失败，可以进行统一返回处理
        return response()->json(['message' => 'Unauthorized'], 401);
    }

    return $next($request);
}
```

#### 步骤6：路由中接入中间件
路由组允许你共享路由属性，例如中间件，这样不需要在每个单独的路由上定义那些属性。
```php
<?php
Route::middleware('api.sys.auth')->group(function () {
    Route::get('/', function () {
        // 使用 api.sys.auth 中间件...
    });

    Route::get('/user/profile', function () {
        // 使用 api.sys.auth 中间件...
    });
});
```

## 注意事项

### AppKey和AppSecret生成
关于`app_key`和`secret_key`的申请，该SDK提供了生成方法，请请求如下方法生成
```php
// 生成AppKey
Douyuxingchen\PhpSysAuth\Auth\AuthKey::genAppKey();
// 生成 AppSecret
Douyuxingchen\PhpSysAuth\Auth\AuthKey::genAppSecret();
```

### Token生成
在开发阶段，需要进行Token生成进行测试，你可以使用以下代码进行Token生成
```php
(new AuthApi('your_app_key'))->token('your_app_secret', ['exp'=>86400,'timestamp' => time()]);
```

### App缓存清理
如果您对App应用进行了配置修改，例如：黑白名单、IP更改、限流更改、接口白名单。那么您通常需要调用`flushCache`方法进行缓存清理。
```php
(new AuthApi('your_app_key'))->flushCache();
```

## 使用指南
请参阅我们的完整[文档](docs)以了解如何使用此库等更多详细信息。

## 版权和许可
本项目基于 [GPL-3.0] 许可证。请查阅 LICENSE 文件以获取更多信息。