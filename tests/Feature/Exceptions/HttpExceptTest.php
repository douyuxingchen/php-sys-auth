<?php

namespace Tests\Feature\Exceptions;

use Douyuxingchen\PhpLibraryStateless\Exceptions\HttpException;
use PHPUnit\Framework\TestCase;

class HttpExceptTest extends TestCase
{
    public function testThrow()
    {
        $httpExcept = new HttpException(0, "success");
    }

}