<?php

namespace App\Service\FormService;

use App\Entity\Task;
use App\Entity\User;
use App\Service\TaskService;
use Symfony\Component\Form\FormInterface;

class TaskFormService
{

    public function __construct(private readonly TaskService $taskService)
    {
    }

    /**
     * @param Task $task
     * @return void
     */
    public function editTask(Task $task): void
    {
        $this->taskService->editTask($task);
    }

    /**
     * @param Task $task
     * @param User|null $user
     * @return void
     */
    public function createTask(Task $task, ?User $user): void
    {
        $this->taskService->addTask($task, $user);
    }
}
