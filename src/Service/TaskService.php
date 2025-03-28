<?php

namespace App\Service;
use App\Entity\Task;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;

class TaskService
{

    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly TaskRepository $taskRepository){}

    public function save(Task $task): Task
    {
        if($this->taskRepository->findOneBy(['title' => $task->getTitle()])) {
            throw new \Exception('Task with this title already exists.');
        }
        $newTask = new Task();
        $newTask->setTitle($task->getTitle());
        $newTask->setContent($task->getContent());
        $newTask->setExpiredAt($task->getExpiredAt());
        $newTask->setCreatedAt(new \DateTimeImmutable());
        $newTask->setStatus(false);

        $this->em->persist($newTask);
        $this->em->flush();

        return $newTask;
    }

    public function getTasks(): array
    {
        $tasks = $this->taskRepository->findAll();
        if (empty($tasks)) {
            throw new \Exception('No tasks found.');
        }
        return $tasks;
    }
}