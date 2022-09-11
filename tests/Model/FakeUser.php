<?php

namespace App\Tests\Model;

class FakeUser implements \Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface
{

    /**
     * @inheritDoc
     */
    public function getPassword(): ?string
    {
        return "test";
    }
}
