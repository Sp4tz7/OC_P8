<?php

namespace App\Manager;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;

class TaskManager
{
    /**
     * @var EntityManagerInterface
     */
    private $manager;
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * TaskManager constructor.
     *
     * @param EntityManagerInterface $manager
     * @param UserRepository         $userRepository
     */
    public function __construct(EntityManagerInterface $manager, UserRepository $userRepository)
    {
        $this->manager        = $manager;
        $this->userRepository = $userRepository;
    }

    public function checkTaskAuthors($tasks)
    {
        foreach ($tasks as $task) {
            if (null === $task->getCreatedBy()) {
                $userAnonymous = $this->userRepository->findByRoles('anonymous');
                $task->setCreatedBy($userAnonymous[0]);
                $this->manager->persist($task);
                $this->manager->flush();
            }
        }

        return $tasks;
    }
}
