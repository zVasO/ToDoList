<?php

namespace App\Tests\Exception;

use App\Exception\AccessException;
use PHPUnit\Framework\TestCase;

class AccessExceptionTest extends TestCase
{

    public function test__construct()
    {
        $expectedMessage = "Im the test message !";
        $expectedCode = "200";
        $accessException = new AccessException($expectedMessage, $expectedCode);

        $message = $accessException->getMessage();
        $code = $accessException->getCode();

        $this->assertEquals($expectedMessage, $message);
        $this->assertEquals($expectedCode, $code);
    }
}
