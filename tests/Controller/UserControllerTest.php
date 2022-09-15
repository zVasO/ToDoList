<?php

namespace App\Tests\Controller;

use App\Controller\UserController;
use App\Entity\User;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UserControllerTest extends WebTestCase
{

    public function setUp(): void
    {
        $this->client = static::createClient();
        $this->client->disableReboot();
        $this->em = static::getContainer()->get('doctrine')->getManager();
        $this->urlGenerator = $this->client->getContainer()->get('router.default');
    }

    public function testCreateUserPage()
    {
        $crawler = $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate('user_create'));
        $this->assertSelectorTextContains('h1', "Créer un utilisateur");
    }

    public function testCreateAdminUser()
    {
        $crawler = $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate('user_create'));
        $form = $crawler->selectButton("Ajouter")->form([
            "user[username]" => "Create User Form",
            "user[password][first]" => "motdepasse",
            "user[password][second]" => "motdepasse",
            "user[email]" => "create@user.todo",
            "user[roles]" => ["ROLE_ADMIN"]
        ]);
        $this->client->submit($form);
        $this->assertResponseRedirects('');
        $this->client->followRedirect();
        $crawler = $this->client->followRedirect();


        $this->assertSelectorTextContains('h1', "Se connecter");
        $this->assertTrue($crawler->filter('.alert.alert-success')->count() == 1);

        $user = $this->em->getRepository(User::class)->findOneBy(["email" => "create@user.todo"]);
        $this->em->getRepository(User::class)->remove($user, true);
    }

    public function testCreateUser()
    {
        $crawler = $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate('user_create'));
        $form = $crawler->selectButton("Ajouter")->form([
            "user[username]" => "Create User Form",
            "user[password][first]" => "motdepasse",
            "user[password][second]" => "motdepasse",
            "user[email]" => "create@user.todo",
            "user[roles]" => []
        ]);
        $this->client->submit($form);
        $this->assertResponseRedirects('');
        $this->client->followRedirect();
        $crawler = $this->client->followRedirect();


        $this->assertSelectorTextContains('h1', "Se connecter");
        $this->assertTrue($crawler->filter('.alert.alert-success')->count() == 1);

        $user = $this->em->getRepository(User::class)->findOneBy(["email" => "create@user.todo"]);
        $this->em->getRepository(User::class)->remove($user, true);
    }

    public function testEditUser()
    {
        $userRepository = $this->em->getRepository(User::class);
        $testUser = $userRepository->findOneBy(["email" => "admin@todolist.fr"]);
        $this->client->loginUser($testUser);
        $crawler = $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate('user_edit', ["id" => $testUser->getId()]));

        $form = $crawler->selectButton("Modifier")->form([
            "user[username]" => $testUser->getUsername(),
            "user[password][first]" => "motdepasse",
            "user[password][second]" => "motdepasse",
            "user[email]" => "admin@todolist.fr",
            "user[roles]" => ["ROLE_ADMIN"]
        ]);
        $this->client->submit($form);
        $this->assertResponseRedirects('');
        $crawler = $this->client->followRedirect();

        $this->assertTrue($crawler->filter('.alert.alert-success')->count() == 1);
    }

    public function testShowUsersListWithoutConnexion()
    {
        $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate('user_list'));
        $crawler = $this->client->followRedirect();

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorTextContains('h1', "Se connecter");
    }

    public function testShowUsersListWithUser()
    {
        $userRepository = $this->em->getRepository(User::class);
        $testUser = $userRepository->getAllNormalUser()[0];
        $this->client->loginUser($testUser);

        $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate('user_list'));
        $crawler = $this->client->followRedirect();


        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertTrue($crawler->filter('.alert.alert-danger')->count() == 1);
    }

    public function testShowUsersListWithAdmin()
    {
        $userRepository = $this->em->getRepository(User::class);
        $testUser = $userRepository->getAllAdminUser()[0];
        $this->client->loginUser($testUser);

        $crawler = $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate('user_list'));

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorTextContains('h1', "Liste des utilisateurs");
    }
}
