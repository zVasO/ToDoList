<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;

class UserService
{

    public function __construct(private readonly UserRepository $userRepository)
    {
    }

    public function getAllUsers()
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
