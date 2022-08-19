<?php

namespace App\Service;


use App\Entity\Task;
use App\Repository\TaskRepository;
use Monolog\DateTimeImmutable;

class TaskService
{

    public function __construct(private readonly TaskRepository $taskRepository)
    {
    }

    /**
     * @return array
     */
    public function getAllTasks(): array
    {
        return $this->taskRepository->findAll();
    }

    /**
     * @param Task $task
     * @return void
     */
    public function toggleTask(Task $task): void
    {
        $task->setIsDone(!$task->isIsDone());
        $this->taskRepository->add($task, true);
    }

    public function removeTask(Task $task)
    {
        $this->taskRepository->remove($task, true);
    }

    /**
     * @param Task $task
     * @return void
     */
    public function addTask(Task $task): void
    {
        $this->taskRepository->add($task, true);
    }

    /**
     * @param Task $task
     * @return void
     */
    public function editTask(Task $task): void
    {
        $this->taskRepository->add($task, true);
    }
}
