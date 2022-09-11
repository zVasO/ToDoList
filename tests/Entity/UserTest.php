<?php

namespace App\Tests\Entity;

use App\Entity\Task;
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
        $this->user->setEmail($expectedUserIdentifier);
        $this->entityManager->getRepository(User::class)->add($this->user);


        //WHEN
        $userIdentifier = $this->user->getUserIdentifier();

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

    public function testGetPassword()
    {
        $user = $this->user;
        $expectedPassword = "testgetpassword";
        $user->setPassword($expectedPassword);

        $password = $user->getPassword();

        $this->assertEquals($expectedPassword, $password);
    }

    public function testGetTasks()
    {
        $user = $this->user;
        $task = (new Task())->setUser($user)
            ->setTitle("A simple test")
            ->setContent("What a test");
        $user->addTask($task);

        $tasks = $user->getTasks();

        $this->assertEquals($task, actual: $tasks[0]);
    }

    public function testRemoveTask()
    {
        $user = $this->user;
        $task = (new Task())->setTitle("A simple title")
            ->setContent("What a content ")
            ->setUser($user);
        $user->removeTask($task);

        $emptyTask = $user->getTasks();

        $this->assertEmpty($emptyTask);
    }

    public function testRemoveTaskWithoutUser()
    {
        //TODO check avec Antoine le rapport
        $user = new User();
        $uniqueTitle = "A simple title";
        $task = (new Task())->setTitle($uniqueTitle)
            ->setContent("What a content ")
            ->setUser(null);

        $user->removeTask($task);

        $emptyTask = $user->getTasks();

        $this->assertEmpty($emptyTask);
    }


    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->user = new User();
        $this->user->setEmail("test@gmail.com")
            ->setUsername("fullUserTest")
            ->setPassword("motdepasse")
            ->setRoles(["ROLE_USER"]);
    }
}
