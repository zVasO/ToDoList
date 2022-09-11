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
        $adminUser = $this->userRepository->getAllAdminUser();
        $tasks = $taskService->getAllTasks($adminUser[0]);

        $this->assertNotNull($tasks);
        foreach ($tasks as $task) {
            $this->assertInstanceOf(Task::class, $task);
        }

    }

    public function testGetAllTasksWithNonAdminUser()
    {
        $taskService = new TaskService($this->taskRepository, $this->userRepository);
        $user = $this->userRepository->findOneBy(["email" => "user@todolist.fr"]);
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

    public function testRemoveTask()
    {
        $taskService = new TaskService($this->taskRepository, $this->userRepository);
        $uniqueTitle = "Im no longer here" . random_int(0, 1000);
        $task = (new Task())->setUser($this->userRepository->findOneBy(["email" => "user@todolist.fr"]))
            ->setContent("Im the content of remove Task !")
            ->setTitle($uniqueTitle);
        $this->taskRepository->add($task, true);

        $task = $this->taskRepository->findOneBy(["title" => $uniqueTitle]);
        $taskService->removeTask($task);
        $nullAtTask = $this->taskRepository->findOneBy(["title" => $uniqueTitle]);

        $this->assertNotNull($task);
        $this->assertNull($nullAtTask);
    }

    public function testAddTaskWithUser()
    {
        $taskService = new TaskService($this->taskRepository, $this->userRepository);
        $uniqueTitle = "Im the test for add Task" . random_int(0, 1000);
        $task = (new Task())->setContent("Im the content of remove Task !")
            ->setTitle($uniqueTitle);

        $taskService->addTask($task, $this->userRepository->findOneBy(["email" => "user@todolist.fr"]));
        $taskInRepo = $this->taskRepository->findOneBy(["title" => $uniqueTitle]);
        $this->taskRepository->remove($taskInRepo, true);

        $this->assertNotNull($taskInRepo);
        $this->assertInstanceOf(Task::class, $taskInRepo);
    }

    public function testAddTaskWithoutUser()
    {
        $taskService = new TaskService($this->taskRepository, $this->userRepository);
        $uniqueTitle = "Im the test for add Task" . random_int(0, 1000);
        $task = (new Task())->setContent("Im the content of remove Task !")
            ->setTitle($uniqueTitle);

        $taskService->addTask($task, null);
        $taskInRepo = $this->taskRepository->findOneBy(["title" => $uniqueTitle]);
        $this->taskRepository->remove($taskInRepo, true);

        $this->assertNotNull($taskInRepo);
        $this->assertInstanceOf(Task::class, $taskInRepo);
    }

    public function testEditTask()
    {
        $expectedTitle = "Im the new title for editTest";
        $expectedContent = "Im the new content for editTest";
        $expectedStatus = false;
        $taskService = new TaskService($this->taskRepository, $this->userRepository);
        $task = ($this->taskRepository->findAll())[0];

        $task->setTitle($expectedTitle)
            ->setContent($expectedContent)
            ->setIsDone($expectedStatus);
        $taskService->editTask($task);
        $taskAfterEdit = $this->taskRepository->find($task->getId());

        $this->assertNotNull($taskAfterEdit);
        $this->assertEquals($expectedTitle, $taskAfterEdit->getTitle());
        $this->assertEquals($expectedStatus, $taskAfterEdit->isIsDone());
    }

    public function testAllTasksTodoWithAdminUser()
    {
        $taskService = new TaskService($this->taskRepository, $this->userRepository);
        $adminUser = $this->userRepository->getAllAdminUser("ROLE_ADMIN")[0];

        $tasksForAdmin = $taskService->getAllTasksTodo($adminUser);

        $this->assertIsArray($tasksForAdmin);
        foreach ($tasksForAdmin as $taskTodo) {
            $this->assertEquals(false, $taskTodo->isIsDone());
        }
    }

    public function testAllTasksTodoWithoutAdminUser()
    {
        $taskService = new TaskService($this->taskRepository, $this->userRepository);
        $normalUser = $this->userRepository->getAllNormalUser()[0];

        $tasksForAdmin = $taskService->getAllTasksTodo($normalUser);

        $this->assertIsArray($tasksForAdmin);
        foreach ($tasksForAdmin as $taskTodo) {
            $this->assertEquals(false, $taskTodo->isIsDone());
        }
    }

    public function testAllTasksDoneWithAdminUser()
    {
        $taskService = new TaskService($this->taskRepository, $this->userRepository);
        $adminUser = $this->userRepository->getAllAdminUser("ROLE_ADMIN")[0];

        $tasksForAdmin = $taskService->getAllTasksDone($adminUser);

        $this->assertIsArray($tasksForAdmin);
        foreach ($tasksForAdmin as $taskTodo) {
            $this->assertEquals(true, $taskTodo->isIsDone());
        }
    }

    public function testAllTasksDoneWithoutAdminUser()
    {
        $taskService = new TaskService($this->taskRepository, $this->userRepository);
        $normalUser = $this->userRepository->getAllNormalUser()[0];

        $tasksForAdmin = $taskService->getAllTasksDone($normalUser);

        $this->assertIsArray($tasksForAdmin);
        foreach ($tasksForAdmin as $taskTodo) {
            $this->assertEquals(true, $taskTodo->isIsDone());
        }
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
