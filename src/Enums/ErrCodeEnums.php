<?php
namespace Douyuxingchen\PhpSysAuth\Enums;

use Douyuxingchen\PhpSysAuth\Exceptions\ErrCodeException;
use Douyuxingchen\PhpSysAuth\Exceptions\TokenInvalidException;
use Douyuxingchen\PhpSysAuth\Exceptions\ValidationException;

class ErrCodeEnums
{
    /**
     * Token类
     * 此类错误码一律是签名校验失败，业务方需要进行签名验证失败的反馈处理
     *
     * @see ValidationException 签名参数不完整或错误
     * @see TokenInvalidException 签名认证失败
     */
    const ERR_TOKEN_DELETION = 10000; // Token信息不完整
    const ERR_TOKEN_FAILED = 10001; // Token校验失败
    const ERR_TOKEN_EXP = 10002; // Token已经过期
    const ERR_TOKEN_APP_KEY_EMPTY = 10003; // payload中appKey不能为空
    const ERR_TOKEN_EXP_EMPTY = 10004; // payload中exp不能为空
    const ERR_TOKEN_APP_KEY_NOT_MATCH = 10005; // appKey和payload中提供的payload不匹配
    const ERR_TOKEN_EXP_NOT_24_HOUR = 10006; // payload中exp不能超过24小时


    /**
     * App类
     * 此类错误码是业务错误，需要业务方根据不同的场景进行判定，返回给调用方进行修正处理
     *
     * @see ErrCodeException
     */
    const ERR_APP_KEY_EMPTY = 20000; // 您提供的appKey不存在
    const ERR_APP_STATUS_NOT_OPEN = 20001; // 该appKey不是启用的状态
    const ERR_IP_BLACK = 20005; // 在IP黑名单模式下，该IP被拦截
    const ERR_IP_WHITE = 20006; // 在IP白名单模式下，该IP被拦截
    const ERR_URI_UNAUTHORIZED = 20010; // 您请求的接口未被授权
    const ERR_REQUEST_FREQUENTLY = 20015; // 请求过于频繁

}