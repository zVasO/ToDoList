<?php

namespace App\Service;


use App\Entity\Task;
use App\Entity\User;
use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use Monolog\DateTimeImmutable;

class TaskService
{

    public function __construct(private readonly TaskRepository $taskRepository,
                                private readonly UserRepository $userRepository
    )
    {
    }

    /**
     * @param User $user
     * @return array
     */
    public function getAllTasks(User $user): array
    {
        if (in_array("ROLE_ADMIN", $user->getRoles())) {
            return $this->taskRepository->findBy(["User" => [$user, null, self::getAnonymousUser()]]);
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
            $user = $this->userRepository->findOneBy(["email"=> UserService::ANONYME_USER_EMAIL ]);
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
            return $this->taskRepository->findBy(["User" => [$user, null, self::getAnonymousUser()], "isDone" => 0]);
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
            return $this->taskRepository->findBy(["User" => [$user, null, self::getAnonymousUser()], "isDone" => 1]);
        }
        return $this->taskRepository->findBy(["User" => $user, "isDone" => 1]);
    }

    private function getAnonymousUser()
    {
        return $this->userRepository->findOneBy(["email" => UserService::ANONYME_USER_EMAIL]);
    }
}
