<?php

namespace App\Voter;

use App\Entity\Task;
use App\Entity\User;
use LogicException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class TaskVoter extends Voter
{
    const DELETE_TASK = 'DELETE_TASK';

    /**
     * @param string $attribute
     * @param mixed $subject
     * @return bool
     */
    protected function supports(string $attribute, mixed $subject): bool
    {
        // if the attribute isn't one we support, return false
        if ($attribute != self::DELETE_TASK) {
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

        if ($attribute == self::DELETE_TASK) {
            return $this->canDelete($task, $user);
        }

        throw new LogicException('This code should not be reached!');
    }

    /**
     * @param Task $task
     * @param User $user
     * @return bool
     */
    private function canDelete(Task $task, User $user): bool
    {
        if ($task->getUser()->getId() === $user->getId()) {
            return true;
        } elseif ($task->getUser()->getId() === 0 && in_array('ROLE_ADMIN', $user->getRoles())) {
            return true;
        }
        return false;
    }

}
