<?php

namespace App\Tests;

use App\Entity\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testGetUsername(): void
    {
        //GIVEN
        $expectedUsername = "dylan2";
        $user = (new User())->setUsername($expectedUsername);

        //WHEN
        $username = $user->getUsername();

        //THEN
        $this->assertEquals($expectedUsername, $username);
    }

    public function testGetUsernameNull(): void
    {
        //GIVEN
        $user = new User();

        //WHEN
        $username = $user->getUsername();

        //THEN
        $this->assertNull($username);
    }
}
