<?php

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskType;
use App\Manager\UserManager;
use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class TaskController extends AbstractController
{
    /**
     * @Route("/tasks/{status}", name="task_list", requirements={"status": "done|todo|mine|all"})
     */
    public function listAction(TaskRepository $taskRepository, UserRepository $userRepository, $status)
    {
        switch ($status) {
            case 'done':
                $tasks = $taskRepository->findBy(['isDone' => true]);
                break;
            case 'todo':
                $tasks = $taskRepository->findBy(['isDone' => false]);
                break;
            case 'mine':
                $tasks = $taskRepository->findBy(['created_by' => $this->getUser()]);
                break;
            default:
                $tasks = $taskRepository->findAll();
        }

        // set non attributed tasks to anonymous
        foreach ($tasks as $task) {
            if (null === $task->getCreatedBy()) {
                $entityManager = $this->getDoctrine()->getManager();
                $userAnonnymous = $userRepository->findByRoles('anonymous');
                $task->setCreatedBy($userAnonnymous[0]);
                $entityManager->persist($task);
                $entityManager->flush();
            }
        }

        return $this->render('task/list.html.twig', ['tasks' => $tasks]);
    }

    /**
     * @Route("/tasks/create", name="task_create")
     */
    public function createAction(Request $request)
    {
        $task = new Task();
        $form = $this->createForm(TaskType::class, $task);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $task->setCreatedBy($this->getUser());
            $entityManager->persist($task);
            $entityManager->flush();

            $this->addFlash('success', 'La tâche a été bien été ajoutée.');

            return $this->redirectToRoute('task_list', ['status' => 'all']);
        }

        return $this->render('task/create.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/tasks/{id}/edit", name="task_edit")
     */
    public function editAction(Task $task, Request $request)
    {
        $form = $this->createForm(TaskType::class, $task);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'La tâche a bien été modifiée.');

            return $this->redirectToRoute('task_list', ['status' => 'all']);
        }

        return $this->render('task/edit.html.twig', [
            'form' => $form->createView(),
            'task' => $task,
        ]);
    }

    /**
     * @Route("/tasks/{id}/toggle", name="task_toggle")
     */
    public function toggleTaskAction(Task $task)
    {
        $task->toggle(!$task->isDone());
        $this->getDoctrine()->getManager()->flush();

        $this->addFlash('success', sprintf('La tâche %s a bien été marquée comme faite.', $task->getTitle()));

        return $this->redirectToRoute('task_list', ['status' => 'all']);
    }

    /**
     * @Route("/tasks/{id}/delete", name="task_delete")
     */
    public function deleteTaskAction(Task $task, UserManager $userManager)
    {
        if (!$userManager->hasRightToDeleteTask($this->getUser(), $task)) {
            $this->addFlash('danger', 'Vous ne pouvez supprimer que vos propres tâches');

            return $this->redirectToRoute('task_list', ['status' => 'all']);
        }
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($task);
        $entityManager->flush();

        $this->addFlash('success', 'La tâche a bien été supprimée.');

        return $this->redirectToRoute('task_list', ['status' => 'all']);
    }
}
