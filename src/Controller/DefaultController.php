<?php

namespace App\Controller;

use App\Repository\TaskRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="homepage")
     */
    public function index(TaskRepository $taskRepository): response
    {
        $tasks = $taskRepository->findBy(['assigned_to' => $this->getUser()]);
        return $this->render('default/index.html.twig',
        ['tasks' => $tasks]);
    }
}