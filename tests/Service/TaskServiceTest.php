<?php

namespace App\Tests\Service;

use App\Entity\Task;
use App\Entity\User;
use App\Service\TaskService;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TaskServiceTest extends KernelTestCase
{

    public function testGetAllTasks()
    {
        $taskService = new TaskService($this->taskRepository, $this->userRepository);
        $users = $this->userRepository->findAll();
        $tasks = $taskService->getAllTasks($users[0]);

        $this->assertNotNull($tasks);
        foreach ($tasks as $task) {
            $this->assertInstanceOf(Task::class, $task);
        }

    }

    public function testGetAllTasksWithNonAdminUser()
    {
        $taskService = new TaskService($this->taskRepository, $this->userRepository);
        $user = $this->userRepository->findOneBy(["email"=> "user@todolist.fr"]);
        $tasks = $taskService->getAllTasks($user);

        $this->assertNotNull($tasks);
        foreach ($tasks as $task) {
            $this->assertInstanceOf(Task::class, $task);
        }
    }

    public function testToggleTask()
    {
        $taskService = new TaskService($this->taskRepository, $this->userRepository);
        $task = ($this->taskRepository->findAll())[0];

        $oldStatus = $task->isIsDone();
        $taskService->toggleTask($task);
        $taskAfterToggle = ($this->taskRepository->findAll())[0];
        $newStatus = $taskAfterToggle->isIsDone();

        $this->assertIsBool($oldStatus);
        $this->assertIsBool($newStatus);
        $this->assertNotEquals($oldStatus, $newStatus);

    }

    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->userRepository = $this->entityManager->getRepository(User::class);
        $this->taskRepository = $this->entityManager->getRepository(Task::class);

    }
}
