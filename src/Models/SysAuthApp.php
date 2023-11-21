<?php

namespace Douyuxingchen\PhpSysAuth\Models;

class SysAuthApp extends BaseModel
{
    public $table = 'sys_auth_app';
    protected $primaryKey = 'id';
    protected $guarded = [];

    // 状态
    const STATUS_PENDING = 0; // 待审核
    const STATUS_APPROVED = 1; // 通过
    const STATUS_REJECTED = 2; // 拒绝

    // IP限制模型
    const IP_WHITE = 0; // 白名单
    const IP_BLACK = 1; // 黑名单
}