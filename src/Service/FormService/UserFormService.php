<?php

namespace App\Service\FormService;

use App\Entity\User;
use App\Service\UserService;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFormService
{

    public function __construct(private readonly UserService                 $userService,
                                private readonly UserPasswordHasherInterface $userPasswordHasher
    )
    {
    }

    /**
     * @param User $user
     * @return void
     */
    public function createUser(User $user): void
    {
        $user->setPassword(
            $this->userPasswordHasher->hashPassword(
                $user,
                $user->getPassword()
            )
        );
        $this->userService->createUser($user);
    }

    /**
     * @param User $user
     * @return void
     */
    public function editUser(User $user): void
    {
        $user->setPassword(
            $this->userPasswordHasher->hashPassword(
                $user,
                $user->getPassword()
            )
        );
        $this->userService->editUser($user);
    }
}
