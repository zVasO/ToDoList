<?php

namespace App\Controller;

use App\Entity\Task;
use App\Entity\User;
use App\Form\TaskType;
use App\Service\FormService\TaskFormService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\TaskService;

class TaskController extends AbstractController
{


    public function __construct(private readonly TaskService $taskService,
                                private readonly TaskFormService $taskFormService
    )
    {
    }

    #[Route('/tasks', name: 'task_list')]
    public function listTasks(): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $tasks = $this->taskService->getAllTasks($user);
        return $this->render('task/list.html.twig', ['tasks' => $tasks]);
    }

    #[Route('/tasks/todo', name: 'task_list_todo')]
    public function listTasksTodo(): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $tasks = $this->taskService->getAllTasksTodo($user);
        return $this->render('task/list.html.twig', ['tasks' => $tasks, 'status'=>'todo']);
    }

    #[Route('/tasks/done', name: 'task_list_done')]
    public function listTasksDone(): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $tasks = $this->taskService->getAllTasksDone($user);
        return $this->render('task/list.html.twig', ['tasks' => $tasks, 'status'=>'done']);
    }

    #[Route('/tasks/create', name: 'task_create')]
    public function createTask(Request $request): Response
    {
        $task = new Task();
        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);
        /** @var User $user */
        $user = $this->getUser();


        if ($form->isSubmitted() && $form->isValid()) {
            $this->taskFormService->createTask($form, $task, $user);
            $this->addFlash('success', 'La tâche a été bien été ajoutée.');
            return $this->redirectToRoute('task_list');
        }

        return $this->renderForm('task/create.html.twig', [
            'form' => $form
        ]);
    }


    #[Route('/tasks/{id}/edit', name: "task_edit")]
    public function editTask(Task $task, Request $request): Response
    {
        $this->denyAccessUnlessGranted('MANAGE_TASK', $task);

        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->taskFormService->editTask($task);
            $this->addFlash('success', 'La tâche a bien été modifiée.');
            return $this->redirectToRoute('task_list');
        }

        return $this->renderForm('task/edit.html.twig', [
            'form' => $form,
            'task' => $task,
        ]);
    }

    #[Route('/tasks/{id}/toggle', name: "task_toggle")]
    public function toggleTaskAction(Task $task): Response
    {
        $this->denyAccessUnlessGranted('MANAGE_TASK', $task);

        $this->taskService->toggleTask($task);
        $this->addFlash('success', sprintf('La tâche %s a bien été marquée comme faite.', $task->getTitle()));
        return $this->redirectToRoute('task_list');
    }


    #[Route('/tasks/{id}/delete', name: "task_delete")]
    public function deleteTaskAction(Task $task): Response
    {
        $this->denyAccessUnlessGranted('MANAGE_TASK', $task);
        $this->taskService->removeTask($task);
        $this->addFlash('success', 'La tâche a bien été supprimée.');
        return $this->redirectToRoute('task_list');
    }
}
