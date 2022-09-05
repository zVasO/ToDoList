<?php

namespace App\Service\FormService;

use App\Entity\User;
use App\Service\UserService;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFormService
{

    public function __construct(private readonly UserService $userService,
                                private readonly UserPasswordHasherInterface $userPasswordHasher
    )
    {
    }

    /**
     * @param FormInterface $form
     * @param User $user
     * @return bool
     */
    public function createUser(FormInterface $form, User $user): bool
    {
        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(
                $this->userPasswordHasher->hashPassword(
                    $user,
                    $user->getPassword()
                )
            );
            $this->userService->createUser($user);
            return true;
        }
        return false;
    }

    /**
     * @param FormInterface $form
     * @param User $user
     * @return bool
     */
    public function editUser(FormInterface $form, User $user): bool
    {
        //TODO: retirer form valid and submit
        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(
                $this->userPasswordHasher->hashPassword(
                    $user,
                    $user->getPassword()
                )
            );
            $this->userService->editUser($user);
            return true;
        }
        return false;
    }
}
