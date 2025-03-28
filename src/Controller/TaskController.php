<?php

namespace App\Controller;

use App\Service\TaskService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Task;
use App\Form\TaskType;

final class TaskController extends AbstractController
{
    public function __construct(
        private readonly TaskService $taskService,
    ) {}

    #[Route('/task', name: 'app_task')]
    public function index(): Response
    {
        $tasks = [];
        $type = "";
        $msg = "";
        try {
            $tasks = $this->taskService->getTasks();
        } catch (\Exception $e) {
            $type = 'error';
            $msg = 'Error fetching tasks: ' . $e->getMessage();
        }
        $this->addFlash($type, $msg);
        return $this->render('task/index.html.twig', [
            'tasks' => $tasks,
        ]);
    }

    #[Route('task/add', name: 'app_task_add')]
    public function addTask(Request $request): Response
    {
        $task = new Task();
        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);
        $type = "";
        $msg = "";
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->taskService->save($task);
                $type = 'success';
                $msg = 'Task saved successfully';
            } catch (\Exception $e) {
                $type = 'error';
                $msg = 'Error saving task: ' . $e->getMessage();
            }
            $this->addFlash($type, $msg);
            return $this->redirectToRoute('app_task');
        }
        return $this->render('task/add.html.twig',
            [
                'form' => $form,
            ]
        );
    }
}
