<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;

class UserService
{
    public const ANONYME_USER_EMAIL = "anonymous@todolist.fr";


    public function __construct(private readonly UserRepository $userRepository)
    {
    }

    public function getAllUsers(): array
    {
        return $this->userRepository->findAll();
    }

    /**
     * @param User $user
     * @return void
     */
    public function createUser(User $user): void
    {
        $this->userRepository->add($user, true);
    }

    /**
     * @param User $user
     * @return void
     */
    public function editUser(User $user): void
    {
        $this->userRepository->add($user, true);
    }
}
