<?php

namespace App\Tests\Manager;

use App\Entity\Task;
use App\Entity\User;
use App\Manager\TaskManager;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class TaskManagerTest extends TestCase
{

    /**
     * @var TaskManager
     */
    private $taskManager;

    public function setUp(): void
    {
        $manager = $this->getMockBuilder(EntityManagerInterface::class)->disableOriginalConstructor()->getMock();
        $userRepository = $this->getMockBuilder(UserRepository::class)->disableOriginalConstructor()->getMock();

        $this->taskManager = new TaskManager($manager, $userRepository);
    }
    /**
     * @covers \App\Manager\TaskManager::checkTaskAuthors
     */
    public function testCheckTaskAuthors()
    {
        $userAnonymous = new User();
        $taskOne = new Task();
        $taskTwo = new Task();
        $this->assertNull($taskOne->getCreatedBy());
        $this->assertNull($taskTwo->getCreatedBy());
        $tasks[0] = $taskOne;
        $tasks[1] = $taskTwo;
        $tasks = $this->taskManager->checkTaskAuthors($tasks);
        $this->assertIsArray($tasks);
    }
}
