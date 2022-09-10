<?php

namespace App\Tests\Repository;

use App\Entity\User;
use App\Repository\UserRepository;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserRepositoryTest extends KernelTestCase
{

    public function testRemove()
    {
        $userRepository = $this->entityManager->getRepository(User::class);
        $userIdentifier = "itestremovefunction@todolist.fr";
        $expectedUser = (new User())->setEmail($userIdentifier)
            ->setUsername("Im")
            ->setPassword("motdepasse")
            ->setRoles(["ROLE_USER"]);
        $userRepository->add($expectedUser, true);
        $user = $userRepository->findOneBy(["email" => $userIdentifier]);

        $userRepository->remove($user, true);
        $nullAtUser = $userRepository->findOneBy(["email" => $userIdentifier]);


        $this->assertNull($nullAtUser);
        $this->assertInstanceOf(User::class, $user);
    }

    public function testUpgradePassword()
    {
        $userRepository = $this->entityManager->getRepository(User::class);
        $expectedPassword = "imTheNewPassword";
        $userIdentifier = "second@todolist.fr";
        $user = (new User())->setEmail($userIdentifier)
            ->setUsername("Im")
            ->setPassword("motdepasse")
            ->setRoles(["ROLE_USER"]);

        $userRepository->upgradePassword($user, $expectedPassword);
        $password = ($newUser = $userRepository->findOneBy(["email" => $userIdentifier]))->getPassword();
        $userRepository->remove($newUser, true);

        $this->assertEquals($expectedPassword, $password);
    }


    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }
}
