<?php

namespace App\Service;


use App\Entity\Task;
use App\Entity\User;
use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use Monolog\DateTimeImmutable;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class TaskService
{

    public function __construct(private readonly TaskRepository $taskRepository,
                                private readonly TokenStorageInterface $tokenStorage,
                                private readonly UserRepository $userRepository
    )
    {
    }

    /**
     * @return array
     */
    public function getAllTasks(User $user): array
    {
        if (in_array("ROLE_ADMIN", $user->getRoles())) {
            return $this->taskRepository->findBy(["User" => [$user, null, 0]]);
        }
        return $this->taskRepository->findBy(["User" => $user]);
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
     * @param User|null $user
     * @return void
     */
    public function addTask(Task $task, ?User $user): void
    {
        if (null === $user) {
            //we get the anonymous user
            $user = $this->userRepository->find(0);
        }
        $task->setUser($user);
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


    /**
     * @param User $user
     * @return array
     */
    public function getAllTasksTodo(User $user): array
    {
        if (in_array("ROLE_ADMIN", $user->getRoles())) {
            return $this->taskRepository->findBy(["User" => [$user, null, 0], "isDone" => 0]);
        }
        return $this->taskRepository->findBy(["User" => $user, "isDone" => 0]);
    }

    /**
     * @param User $user
     * @return array
     */
    public function getAllTasksDone(User $user): array
    {
        if (in_array("ROLE_ADMIN", $user->getRoles())) {
            return $this->taskRepository->findBy(["User" => [$user, null, 0], "isDone" => 1]);
        }
        return $this->taskRepository->findBy(["User" => $user, "isDone" => 1]);
    }
}
