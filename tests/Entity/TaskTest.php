<?php

namespace App\Tests\Entity;

use App\Entity\Task;
use App\Entity\User;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TaskTest extends KernelTestCase
{

    public function testGetCreatedAt()
    {
        $expectedDate = new \DateTime();
        $newTask = (new Task())->setUser($this->user)
            ->setCreatedAt($expectedDate);

        $date = $newTask->getCreatedAt();

        $this->assertEquals($expectedDate, $date);
    }

    public function testGetId()
    {
        $task = $this->task;

        $id = $task->getId();

        $this->assertNotNull($id);
        $this->assertIsInt($id);
    }

    public function testGetTitle()
    {
        $expectedTitle = "What a title";
        $task = (new Task())
        ->setTitle($expectedTitle);

        $title = $task->getTitle();

        $this->assertEquals($expectedTitle, $title);
    }

    public function testGetContent()
    {
        $expectedContent = "I'm the content";
        $task = (new Task())->setContent($expectedContent);

        $content = $task->getContent();

        $this->assertEquals($expectedContent, $content);
    }

    public function testAddCreatedAt()
    {
        $task = (new Task());
        $nullDate = $task->getCreatedAt();

        $task->addCreatedAt();
        $date = $task->getCreatedAt();

        $this->assertNull($nullDate);
        $this->assertNotNull($date);
    }

    public function testAddIsDone()
    {
        $task = (new Task());
        $nullDone = $task->isIsDone();

        $task->addIsDone();
        $done = $task->isIsDone();

        $this->assertNull($nullDone);
        $this->assertNotNull($done);
        $this->assertIsBool($done);
    }

    public function testGetUser()
    {
        $newTask = new Task();

        $userWithNullData = $newTask->getUser();
        $userWithData = $this->task->getUser();

        $this->assertNotNull($userWithNullData);
        $this->assertNotNull($userWithData);
        $this->assertInstanceOf(User::class, $userWithNullData);
        $this->assertInstanceOf(User::class, $userWithData);
    }

    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
        $this->taskRepository = $this->entityManager->getRepository(Task::class);

        $this->user = $this->entityManager->getRepository(User::class)->findAll()[0];
        $this->task = $this->taskRepository->findAll()[0];
    }
}
