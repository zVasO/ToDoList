<?php

namespace App\Tests\Service;

use App\Entity\User;
use App\Service\UserService;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserServiceTest extends KernelTestCase
{

    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
        $this->userRepository = $this->entityManager->getRepository(User::class);
        $this->userService = new UserService($this->userRepository);

    }

    public function testGetAllUsers()
    {
        $expectedUsers = $this->userRepository->findAll();

        $users = $this->userService->getAllUsers();

        $this->assertEquals($expectedUsers, $users);
    }

    public function testCreateUser()
    {
        $randomEmail = random_int(0, 10000)."@todolist.fr";
        $user = new User();
        $user->setPassword("motdepasse")
            ->setEmail($randomEmail)
            ->setRoles(["ROLE_USER"])
            ->setUsername("CreatedUser".random_int(0, 100));
        $this->userService->createUser($user);

        $userFromRepository = $this->userRepository->findOneBy(["email"=>$randomEmail]);
        $this->assertEquals($user, $userFromRepository);
        //we delete the user
        $this->entityManager->remove($userFromRepository);
        $this->entityManager->flush();
    }

    public function testEditUser()
    {
        $users = $this->userService->getAllUsers();
        $userToEdit = $users[0];
        $expectedRandomName = (string)random_int(0, 10000);
        $userToEdit->setUsername($expectedRandomName);

        $this->userService->editUser($userToEdit);

        $this->assertEquals($userToEdit, $this->userRepository->find($userToEdit->getId()));



    }
}
