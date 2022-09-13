<?php

namespace App\Tests\Voter;

use App\Entity\Task;
use App\Entity\User;
use App\Exception\AccessException;
use App\Service\UserService;
use App\Tests\Model\FakeUser;
use App\Voter\TaskVoter;
use http\Env\Response;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class TaskVoterTest extends TestCase
{
    protected function setUp(): void
    {

    }

    private function createUser(int $id, array $roles, string $email): User
    {
        $user = $this->createMock(User::class);
        $user->method('getId')->willReturn($id);
        $user->method('getRoles')->willReturn($roles);
        $user->method('getEmail')->willReturn($email);

        return $user;
    }

    public function provideCases()
    {
        yield 'non-owner cannot edit' => [
            TaskVoter::MANAGE_TASK,
            (new Task())->setUser($this->createUser(1, [], "normal.user@gmail.com")),
            $this->createUser(2, [], "normal.user@gmail.com"),
            AccessException::class
        ];

        yield 'owner can edit' => [
            TaskVoter::MANAGE_TASK,
            (new Task())->setUser($this->createUser(1, [], "normal.user@gmail.com")),
            $this->createUser(1, [], "normal.user@gmail.com"),
            Voter::ACCESS_GRANTED
        ];

        yield 'admin can edit anonymous task' => [
            TaskVoter::MANAGE_TASK,
            (new Task())->setUser($this->createUser(1, [], UserService::ANONYME_USER_EMAIL)),
            $this->createUser(2, ["ROLE_ADMIN"], "admin.user@gmail.com"),
            Voter::ACCESS_GRANTED
        ];

        yield 'wrong Voter attributes' => [
            'Not an attributes',
            (new Task())->setUser($this->createUser(1, [], UserService::ANONYME_USER_EMAIL)),
            $this->createUser(2, ["ROLE_ADMIN"], "admin.user@gmail.com"),
            false
        ];

        yield 'not an Task Object' => [
            TaskVoter::MANAGE_TASK,
            "",
            $this->createUser(2, ["ROLE_ADMIN"], "admin.user@gmail.com"),
            false
        ];
        yield 'not a normal User' => [
            TaskVoter::MANAGE_TASK,
            (new Task())->setUser($this->createUser(1, [], UserService::ANONYME_USER_EMAIL)),
            new FakeUser(),
            TaskVoter::ACCESS_DENIED
        ];
    }

    /**
     * @dataProvider provideCases
     */
    public function testVote($attribute, $task, $user, $expectedVote) {
        $voter = new TaskVoter();
        $token = new UsernamePasswordToken(
            $user, 'credentials', ['memory']
        );

        switch ($expectedVote) {
            case Voter::ACCESS_GRANTED:
                $this->assertSame(
                    $expectedVote,
                    $voter->vote($token, $task, [$attribute])
                );
                break;
            case Voter::ACCESS_DENIED:
                $this->assertEquals(
                    $expectedVote,
                    $voter->vote($token, $task, [$attribute])
                );
                break;
            case false :
                $this->assertFalse(
                    $expectedVote,
                    $voter->vote($token, $task, [$attribute])
                );
            case AccessException::class :
                try {
                    $voter->vote($token, $task, [$attribute]);
                } catch (\Exception $exception) {
                    $this->assertInstanceOf(AccessException::class, $exception);
                }
        }
    }

}
