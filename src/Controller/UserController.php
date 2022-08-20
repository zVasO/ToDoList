<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Service\FormService\UserFormService;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{


    public function __construct(private readonly UserService $userService, private readonly UserFormService $userFormService)
    {
    }

    #[Route('/users', name: 'user_list')]
    public function showUsersList(): Response
    {
        $users = $this->userService->getAllUsers();
        return $this->render('user/list.html.twig', [
            'users' => $users
        ]);
    }

    #[Route('/users/create', name: 'user_create')]
    public function createUser(Request $request): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($this->userFormService->createUser($form, $user)) {
            $this->addFlash('success', "L'utilisateur a bien été ajouté.");
            return $this->redirectToRoute('user_list');
        }

        return $this->renderForm('user/create.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/users/{id}/edit', name: 'user_edit')]
    public function editUser(User $user, Request $request, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($this->userFormService->editUser($form, $user)) {
            $this->addFlash('success', "L'utilisateur a bien été modifié");
            return $this->redirectToRoute('user_list');
        }

        return $this->renderForm('user/edit.html.twig', [
            'form' => $form,
            'user' => $user
        ]);
    }
}
