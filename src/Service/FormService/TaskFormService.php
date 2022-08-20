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
     * @param FormInterface $form
     * @param Task $task
     * @return bool
     */
    public function editTask(FormInterface $form, Task $task): bool
    {
        if ($form->isSubmitted() && $form->isValid()) {
            $this->taskService->editTask($task);
            return true;
        }
        return false;
    }

    /**
     * @param FormInterface $form
     * @param Task $task
     * @param User|null $user
     * @return bool
     */
    public function createTask(FormInterface $form, Task $task, ?User $user): bool
    {
        if ($form->isSubmitted() && $form->isValid()) {
            $this->taskService->addTask($task, $user);
            return true;
        }
        return false;
    }
}
