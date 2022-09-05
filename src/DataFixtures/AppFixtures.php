<?php

namespace App\DataFixtures;

use App\Entity\Task;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Constraints\Date;

class AppFixtures extends Fixture
{


    public function __construct(private UserPasswordHasherInterface $passwordHasher)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $anonymousUser = new User();
        $anonymousUser->setId(0)
            ->setUsername("ANONYMOUS")
            ->setEmail("anonymous@todolist.fr")
            ->setRoles(["ROLE_USER", "ROLE_ADMIN"])
            ->setPassword($this->passwordHasher->hashPassword(
                $anonymousUser,
                "motdepasse"
            ));
        $manager->persist($anonymousUser);

        $adminUser = new User();
        $adminUser->setId(0)
            ->setUsername("Admin")
            ->setEmail("admin@todolist.fr")
            ->setRoles(["ROLE_USER", "ROLE_ADMIN"])
            ->setPassword($this->passwordHasher->hashPassword(
                $adminUser,
                "motdepasse"
            ));
        $manager->persist($adminUser);


        $user = new User();
        $user->setId(0)
            ->setUsername("user")
            ->setEmail("user@todolist.fr")
            ->setRoles(["ROLE_USER"])
            ->setPassword($this->passwordHasher->hashPassword(
                $user,
                "motdepasse"
            ));
        $manager->persist($user);

        $firstTask = new Task();
        $firstTask->setContent("Je suis une tâche a effectuer !")
            ->setCreatedAt(new \DateTime())
            ->setIsDone(false)
            ->setTitle("Une tache normal")
            ->setUser($user);
        $manager->persist($firstTask);

        $taskWithoutUser = new Task();
        $taskWithoutUser->setContent("Je suis une tâche sans user !")
            ->setCreatedAt(new \DateTime())
            ->setIsDone(false)
            ->setTitle("Une tache sans utilisateur, null quoi :(");
        $manager->persist($taskWithoutUser);


        $manager->flush();
    }
}
