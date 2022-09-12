<?php

namespace App\Tests\Controller;

use App\Controller\DefaultController;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;

class DefaultControllerTest extends WebTestCase
{

    public function setUp(): void
    {
        $this->client = static::createClient();
        $this->urlGenerator = $this->client->getContainer()->get('router.default');

    }

    public function testIndexAction()
    {

        $crawler = $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate('homepage'));
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', "Bienvenue sur Todo List, l'application vous permettant de gérer l'ensemble de vos tâches sans effort !");
    }
}
