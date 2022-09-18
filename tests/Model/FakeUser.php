<?php

namespace App\Tests\Model;

use Symfony\Component\Security\Core\User\UserInterface;

class FakeUser implements \Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface, UserInterface
{

    /**
     * @inheritDoc
     */
    public function getPassword(): ?string
    {
        return "test";
    }

    public function getRoles(): array
    {
        return [];
    }

    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }

    public function getUserIdentifier(): string
    {
        return "";
    }
}
