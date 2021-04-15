<?php

namespace App\Tests\Entity;

use App\Entity\Task;
use App\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testEraseCredentials()
    {
        $user = new User;
        $this->assertNull($user->eraseCredentials());
    }

    public function testAssignTask()
    {
        $user = new User;
        $task = new Task;

        $user->addAssignedTask($task);
        $this->assertInstanceOf(ArrayCollection::class, $user->getAssignedTasks());
    }

    public function testRemovedAssignedTask()
    {
        $user = new User;
        $task = new Task;

        $user->addAssignedTask($task);
        $user->removeAssignedTask($task);
        $this->assertCount(0, $user->getAssignedTasks());
    }
}
