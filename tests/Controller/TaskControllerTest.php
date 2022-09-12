<?php

namespace App\Tests\Controller;

use App\Controller\TaskController;
use App\Entity\Task;
use App\Entity\User;
use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Persistence\ObjectRepository;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @property TaskRepository|ObjectRepository $taskRepository
 * @property UserRepository|ObjectRepository $userRepository
 * @property ObjectManager $entityManager
 * @property KernelBrowser $client
 * @property object|Router|null $urlGenerator
 */
class TaskControllerTest extends WebTestCase
{

    public function setUp(): void
    {
        $this->client = static::createClient();
        $this->client->disableReboot();
        $this->em = static::getContainer()->get('doctrine')->getManager();
        $this->urlGenerator = $this->client->getContainer()->get('router.default');
    }

    /**
     * @throws \Exception
     */
    public function testToggleTaskAction()
    {
        $user = self::getUserWithTask();
        $this->client->loginUser($user);
        $task = $user->getTasks()[0];

        $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate('task_toggle', ['id' => $task->getId()]));
        $crawler = $this->client->followRedirect();

        $this->assertTrue($crawler->filter('.alert.alert-success')->count() == 1);
        if ($task->isIsDone()) {
            $this->assertTrue($crawler->filter("#toggle-" . $task->getId())->text() == "Marquer non terminée");
        } else {
            $this->assertTrue($crawler->filter("#toggle-" . $task->getId())->text() == "Marquer comme faite");
        }
    }

    /**
     * public function testEditTask()
     * {
     *
     * }
     */
    private function getUserWithTask()
    {
        $userRepository = $this->em->getRepository(User::class);
        $users = $userRepository->findAll();

        foreach ($users as $user) {
            if (!$user->getTasks()->isEmpty()) {
                return $user;
            }
        }
        throw new \Exception("All user dont have task");
    }

    public function testListTasksTodo()
    {
        //we connect with a user
        $userRepository = $this->em->getRepository(User::class);
        $testUser = $userRepository->getAllNormalUser()[0];
        $this->client->loginUser($testUser);

        $crawler = $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate('task_list_todo'));
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $taskToDo = $testUser->getTasks()->filter(function (Task $task) {
            return $task->isIsDone() == false;
        });

        if ($taskToDo->isEmpty()) {
            $this->assertTrue($crawler->filter('.btn.btn-warning.pull-right')->count() == 1);
        } else {
            //we make sure, at least all user task are showed, not equal because admin can see anonyme task
            $this->assertTrue($crawler->filter('.card.mt-3.col-sm-4.col-lg-4.col-md-4')->count() >= $this->count($taskToDo));
        }
    }

    public function testListTasksDone()
    {
        //we connect with a user
        $userRepository = $this->em->getRepository(User::class);
        $testUser = $userRepository->getAllNormalUser()[0];
        $this->client->loginUser($testUser);

        $crawler = $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate('task_list_done'));
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $taskDone = $testUser->getTasks()->filter(function (Task $task) {
            return $task->isIsDone() == true;
        });


        if ($taskDone->isEmpty()) {
            $this->assertTrue($crawler->filter('.btn.btn-warning.pull-right')->count() == 1);
        } else {
            //we make sure, at least all user task are showed, not equal because admin can see anonyme task
            $this->assertTrue($crawler->filter('.card.mt-3.col-sm-4.col-lg-4.col-md-4')->count() >= $this->count($taskDone));
        }
    }

    public function testListTasks()
    {
        //we connect with a user
        $userRepository = $this->em->getRepository(User::class);
        $testUser = $userRepository->getAllNormalUser()[0];
        $this->client->loginUser($testUser);

        $crawler = $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate('task_list'));
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        //we make sure, at least all user task are showed, not equal because admin can see anonyme task
        $this->assertTrue($crawler->filter('.card.mt-3.col-sm-4.col-lg-4.col-md-4')->count() >= $testUser->getTasks()->count());
    }

    /**
     * @throws \Exception
     */
    public function testCreateTask()
    {
        //we connect with a user
        $user = self::getUserWithTask();
        $this->client->loginUser($user);

        $crawler = $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate('task_create'));
        $form = $crawler->selectButton("Ajouter")->form([
            "task[title]" => "Im the creation",
            "task[content]" => "Test create task in controller"
        ]);
        $this->client->submit($form);
        $this->assertResponseRedirects('');
        $crawler = $this->client->followRedirect();

        $this->assertTrue($crawler->filter('.alert.alert-success')->count() == 1);
    }

    public function testDeleteTaskAction()
    {
        $taskRepository = $this->em->getRepository(Task::class);
        $task = $taskRepository->findOneByTitle("Im the creation");
        $user = $task->getUser();
        $this->client->loginUser($user);
        $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate('task_delete', ['id' => $task->getId()]));
        $crawler = $this->client->followRedirect();

        //we make sure alert is there
        $this->assertTrue($crawler->filter('.alert.alert-success')->count() == 1);
        $this->assertTrue($crawler->filter("#toggle-" . $task->getId())->count() == 0);

    }
}
