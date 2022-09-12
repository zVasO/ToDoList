<?php

namespace App\Tests\Controller;

use App\Controller\SecurityController;
use App\Entity\User;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityControllerTest extends WebTestCase
{

    public function setUp(): void
    {
        $this->client = static::createClient();
        $this->urlGenerator = $this->client->getContainer()->get('router.default');
    }

    public function testDisplayLogin()
    {
        $crawler = $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate('login'));
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorTextContains('h1', 'Se connecter');
        $this->assertSelectorNotExists('.alert.alert-danger');
    }

    public function testLoginWithBadCredentials()
    {
        $crawler = $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate('login'));
        $this->client->loginUser(new User());
        $form = $crawler->selectButton("Se connecter")->form([
            "_username" => "ImWrongOne",
            "_password" => "notaPassword"
        ]);
        $this->client->submit($form);
        $this->assertResponseRedirects('');
        $crawler = $this->client->followRedirect();

        $this->assertTrue($crawler->filter('.alert.alert-danger')->count() == 1);
    }

    public function testLoginWithGoodCredentials()
    {
        $crawler = $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate('login'));
        $form = $crawler->selectButton("Se connecter")->form([
            "_username" => "Admin",
            "_password" => "motdepasse"
        ]);
        $this->client->submit($form);
        $this->assertResponseRedirects();
        $crawler = $this->client->followRedirect();
        $this->assertSelectorTextContains('h1', "Bienvenue sur Todo List, l'application vous permettant de gérer l'ensemble de vos tâches sans effort !");
    }

    public function testLoginWhenAlreadyLoggedIn()
    {
        $user = static::getContainer()->get('doctrine')->getManager()->getRepository(User::class)->findOneByUsername("Admin");
        $this->client->loginUser($user);
        $crawler = $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate('login'));

        $this->assertSelectorTextContains('h1', "Bienvenue sur Todo List, l'application vous permettant de gérer l'ensemble de vos tâches sans effort !");
    }

    public function testLogout()
    {
        $crawler = $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate('logout'));

        $this->assertResponseRedirects();
        $crawler = $this->client->followRedirect();
        $this->assertSelectorTextContains('h1', "Bienvenue sur Todo List, l'application vous permettant de gérer l'ensemble de vos tâches sans effort !");
    }
}
