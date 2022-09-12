<?php

namespace App\Tests\Voter;

use App\Entity\Task;
use App\Entity\User;
use App\Voter\TaskVoter;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class TaskVoterTest extends KernelTestCase
{
    protected function setUp(): void
    {

    }

    public function testSupports()
    {
        $authorizationChecker = static::getContainer()->get(AbstractController::class);
        $authorizationChecker->

    }

}
