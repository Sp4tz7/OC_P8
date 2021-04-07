<?php

namespace App\Repository;

use App\Entity\Task;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;


class TaskRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Task::class);
    }

    public function findByUser(User $user)
    {

        return $this->createQueryBuilder('u')
            ->where('u.created_by = :created_by')
            ->orWhere('u.assigned_to = :assigned_to')
            ->setParameter('created_by', $user)
            ->setParameter('assigned_to', $user)
            ->orderBy('u.isDone')
            ->getQuery()
            ->getResult();
    }
}
