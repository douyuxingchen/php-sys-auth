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

#### 步骤1：创建中间件
```bash
php artisan make:middleware SysAuthMiddleware
```

#### 步骤2：注册中间件

在 `app/Http/Kernel.php` 文件的 `$routeMiddleware` 数组中，注册中间件：

```php
protected $routeMiddleware = [
    'api.sys.auth' => \App\Http\Middleware\SysAuthMiddleware::class,
];
```

#### 步骤3：中间件接入
```php

```

#### 步骤4：路由中接入中间件
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

## 使用指南
请参阅我们的完整[文档](docs)以了解如何使用此库、添加新服务类、处理状态和错误等更多详细信息。

## 版权和许可
本项目基于 [GPL-3.0] 许可证。请查阅 LICENSE 文件以获取更多信息。