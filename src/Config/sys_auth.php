<?php
return [

    /*
    |--------------------------------------------------------------------------
    | 应用配置
    |--------------------------------------------------------------------------
    |
    | cache: 是否启用缓存，建议开启
    | exp：缓存过期时间配置
    |
    */
    'app' => [
        'cache' => true,
        'exp' => 3600,
    ],

    /*
    |--------------------------------------------------------------------------
    | IP限流控制
    |--------------------------------------------------------------------------
    |
    | ip_limit: 是否开启IP验证限制。如果开启，无法命中配置的IP要求，会被驳回
    |
    | status：状态开关
    | white_list： 白名单，房本开发者在开发环境中调试
    |
    */
    'ip_limit' => [
        'status' => true,
        'white_list' => [],
    ],

    /*
    |--------------------------------------------------------------------------
    | 路由限流控制
    |--------------------------------------------------------------------------
    |
    | 是否开启API路由请求白名单限制。如果开启，则不在白名单的请求路由，会被驳回
    |
    | status：状态开关
    | white_list： 白名单，房本开发者在开发环境中调试
    |
    */
    'route_limit' => [
        'status' => true,
        'white_list' => [],
    ],

    /*
    |--------------------------------------------------------------------------
    | 接口限流配置
    |--------------------------------------------------------------------------
    |
    | 是否开启API请求速率限制。如果开启，超过API要求的速率，会被驳回
    |
    */
    'api_rate' => [
        'status' => true,

        // 是否启用lua执行
        // 开启后，可以加快限流算法的效率
        'eval' => true,

        // 支持的算法类型
        // counter: 计数器
        // window: 时间窗口
        // bucket: 令牌桶
        'rate_type' => 'counter',
    ],

    /*
    |--------------------------------------------------------------------------
    | Mysql Redis Databases
    |--------------------------------------------------------------------------
    |
    | 有状态的服务配置
    | 配置不同的连接名称，则会使用不同的服务连接
    | 请在 config/database.php 文件中的 connections.mysql 和 connections.redis 确定不同的连接名称，修改下方的配置文件
    |
    */
    'mysql_connect_name' => 'mysql',
    'redis_connect_name' => 'default',

];