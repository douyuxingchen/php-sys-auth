<?php

namespace Douyuxingchen\PhpSysAuth\Auth;

class AuthKey
{
    /**
     * 生成应用Key
     *
     * @param string $prefix 指定字符串前缀
     * @return string
     */
    public static function genAppKey(string $prefix = ''): string
    {
        return self::generateCode(16);
    }

    /**
     * 生成应用私钥
     *
     * @param string $prefix 指定字符串前缀
     * @return string
     */
    public static function genAppSecret(string $prefix = ''): string
    {
        return self::generateCode(32);
    }

    private static function generateCode(int $length, string $prefix = ''): string
    {
        $alphaNumericCharacters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $alphaCharacters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';

        $code = $prefix;

        if (empty($prefix) || !ctype_alpha($prefix)) {
            $randomAlphaIndex = mt_rand(0, strlen($alphaCharacters) - 1);
            $code .= $alphaCharacters[$randomAlphaIndex];
        }
        for ($i = strlen($code); $i < $length; $i++) {
            $randomIndex = mt_rand(0, strlen($alphaNumericCharacters) - 1);
            $code .= $alphaNumericCharacters[$randomIndex];
        }

        return $code;
    }
}