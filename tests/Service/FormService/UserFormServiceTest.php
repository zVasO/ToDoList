<?php

namespace App\Tests\Service\FormService;

use App\Entity\User;
use App\Service\FormService\UserFormService;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFormServiceTest extends KernelTestCase
{

    public function testEditUser()
    {
        $expectedEmail = "editTest" . rand(0, 100) . "@todolist.com";
        $userFormService = new UserFormService($this->userService, $this->userPasswordHasherStub);
        $user = $this->userRepository->findAll()[0];
        $oldEmail = $user->getEmail();
        $user->setEmail($expectedEmail);

        $userFormService->editUser($user);
        $updatedUser = $this->userRepository->find($user->getId());

        $this->assertNotNull($updatedUser);
        $this->assertInstanceOf(User::class, $updatedUser);
        $this->assertEquals($expectedEmail, $updatedUser->getEmail());
        $userFormService->editUser(($user->setEmail($oldEmail)));
    }

    public function testCreateUser()
    {
        $userFormService = new UserFormService($this->userService, $this->userPasswordHasherStub);
        $email = "userformtestcreate@todolist.fr";
        $user = (new User())->setEmail($email)
            ->setRoles(["ROLE_USER"])
            ->setPassword("motdepsse")
            ->setUsername("userformtestcreate");

        $userFormService->createUser($user);
        $createdUser = $this->userRepository->findOneBy(["email" => $email]);
        $this->userRepository->remove($createdUser, true);

        $this->assertNotNull($createdUser);
        $this->assertInstanceOf(User::class, $createdUser);

    }

    protected function setUp(): void
    {
        $kernel = self::bootKernel();
        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->userRepository = $this->entityManager->getRepository(User::class);
        $this->userService = new UserService($this->userRepository);


        // Créer un bouchon pour la classe SomeClass.
        $this->userPasswordHasherStub = $this->getMockBuilder(UserPasswordHasherInterface::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();

        // Configurer le bouchon (retourn motdepasse en hash)
        $this->userPasswordHasherStub->method('hashPassword')
            ->willReturn('$2y$13$oF//BbcOTbTzd4RIOkIuVut6Qlomr297Wzd6j6uHUWFAvxURGbUM.');
    }
}
