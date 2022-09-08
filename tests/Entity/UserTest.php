<?php

namespace App\Tests\Entity;

use App\Entity\User;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserTest extends KernelTestCase
{

    /**
     * @return void
     */
    public function testGetId(): void
    {
        //GIVEN
        $expectedId = 1;
        $user = (new User())->setId($expectedId);

        //WHEN
        $id = $user->getId();

        //THEN
        $this->assertEquals($expectedId, $id);
    }

    /**
     * @return void
     */
    public function testGetEmail(): void
    {
            //GIVEN
            $expectedEmail = "ilovetest@todolist.fr";
            $user = (new User())->setEmail($expectedEmail);

            //WHEN
            $email = $user->getEmail();

            //THEN
            $this->assertEquals($expectedEmail, $email);
        }

    /**
     * @return void
     */
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

    /**
     * @return void
     */
    public function testGetUserIdentifier(): void
    {
        //GIVEN
        $expectedUserIdentifier = "ilovetest@todolist.fr";
        $user = (new User())->setUsername($expectedUserIdentifier);

        /**
        $userRepository = $this->entityManager
            ->getRepository(User::class);
        $userRepository->add($user, true);
        */

        //WHEN
        $userIdentifier = $user->getUserIdentifier();

        //THEN
        $this->assertEquals($expectedUserIdentifier, $userIdentifier);
    }

    public function testGetRoles()
    {
        //GIVEN
        $expectedRoles = ["ROLE_USER", "ROLE_ADMIN"];
        $user = (new User())->setRoles($expectedRoles);

        //WHEN
        $roles = $user->getRoles();

        //THEN
        $this->assertEquals($expectedRoles, $roles);

    }
}
