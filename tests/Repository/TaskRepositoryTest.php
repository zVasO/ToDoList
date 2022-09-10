<?php

namespace App\Tests\Repository;

use App\Entity\Task;
use App\Entity\User;
use App\Repository\TaskRepository;
use App\Service\UserService;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TaskRepositoryTest extends KernelTestCase
{

    public function testAdd()
    {
        $task = (new Task())->setUser($this->user)
            ->setTitle("Test Add Title")
            ->setContent("Im just a test")
            ->setCreatedAt(new \DateTime());
        $taskAmount = count($this->taskRepository->findAll());
        $this->taskRepository->add($task, true);

        $newTaskAmount = count($this->taskRepository->findAll());
        $this->taskRepository->remove($task, true);

        $this->assertGreaterThan($taskAmount, $newTaskAmount);
    }

    public function testRemove(){
        $task = (new Task())->setUser($this->user)
            ->setTitle("Test Add Title")
            ->setContent("Im just a test")
            ->setCreatedAt(new \DateTime());
        $this->taskRepository->add($task, true);
        $taskAmount = count($this->taskRepository->findAll());

        $this->taskRepository->remove($task, true);
        $newTaskAmount = count($this->taskRepository->findAll());

        $this->assertLessThan($taskAmount, $newTaskAmount);
    }

    public function testGetAnonymeUser()
    {
        $anonymeUser = $this->taskRepository->getAnonymeUser();

        $this->assertEquals(UserService::ANONYME_USER_EMAIL, $anonymeUser->getEmail());
    }

    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->taskRepository = $this->entityManager->getRepository(Task::class);
        $this->user = ($this->entityManager->getRepository(User::class)->findAll())[0];
    }
}
