<?php

namespace App\Voter;

use App\Entity\Task;
use App\Entity\User;
use App\Exception\AccessException;
use App\Service\UserService;
use LogicException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class TaskVoter extends Voter
{
    const MANAGE_TASK = 'MANAGE_TASK';

    /**
     * @param string $attribute
     * @param mixed $subject
     * @return bool
     */
    protected function supports(string $attribute, mixed $subject): bool
    {
        // if the attribute isn't one we support, return false
        if ($attribute != self::MANAGE_TASK) {
            return false;
        }

        // only vote on `Post` objects
        if (!$subject instanceof Task) {
            return false;
        }

        return true;
    }

    /**
     * @param string $attribute
     * @param mixed $subject
     * @param TokenInterface $token
     * @return bool
     * @throws AccessException
     */
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            // the user must be logged in; if not, deny access
            return false;
        }

        // you know $subject is a Post object, thanks to `supports()`
        /** @var Task $task */
        $task = $subject;

        if ($attribute == self::MANAGE_TASK) {
            return $this->canManage($task, $user);
        }

        throw new LogicException('This code should not be reached!');
    }

    /**
     * @param Task $task
     * @param User $user
     * @return bool
     * @throws AccessException
     */
    private function canManage(Task $task, User $user): bool
    {
        if ($task->getUser()->getId() === $user->getId()) {
            return true;
        } elseif (($task->getUser()->getId() === 0 || $task->getUser()->getEmail() === UserService::ANONYME_USER_EMAIL) && in_array('ROLE_ADMIN', $user->getRoles())) {
            return true;
        }
        throw new AccessException("Vous ne pouvez pas accéder a cette page !", Response::HTTP_UNAUTHORIZED);
    }

}
