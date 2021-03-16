<?php

namespace App\Tests\Manager;

use App\Entity\Task;
use App\Entity\User;
use App\Manager\UserManager;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserManagerTest extends TestCase
{
    public function testHasRightToDelete()
    {
        $manager = $this->getMockBuilder(EntityManagerInterface::class)->disableOriginalConstructor()->getMock();
        $encoder = $this->getMockBuilder(UserPasswordEncoderInterface::class)->disableOriginalConstructor()->getMock();

        $userManager = new UserManager($manager, $encoder);
        $user        = $userManager->createUser(
            'UserOne',
            'userone@todoandco.com',
            'user1_todoandco',
            ['ROLE_USER']
        );
        $admin       = $userManager->createUser(
            'Admin',
            'admin@todoandco.com',
            'admin_todoandco',
            ['ROLE_ADMIN']
        );
        $anonymous   = $userManager->createUser(
            'Anonymous',
            'anonymous@todoandco.com',
            'anonymous_todoandco',
            ['ROLE_ANONYMOUS']
        );

        $task = new Task();
        $task->setCreatedBy($user);
        $this->assertSame(
            true,
            $userManager->hasRightToDelete($user, $task),
            "User can delete user task"
        );
        $this->assertSame(
            false,
            $userManager->hasRightToDelete($admin, $task),
            "Admin can't delete user task"
        );
        $this->assertSame(
            false,
            $userManager->hasRightToDelete($anonymous, $task),
            "Anonymous can't delete user task"
        );

        $task->setCreatedBy($admin);
        $this->assertSame(
            false,
            $userManager->hasRightToDelete($user, $task),
            "User can't delete admin task"
        );
        $this->assertSame(
            true,
            $userManager->hasRightToDelete($admin, $task),
            "Admin can delete admin task"
        );
        $this->assertSame(
            false,
            $userManager->hasRightToDelete($anonymous, $task),
            "Anonymous can't delete admin task"
        );

        $task->setCreatedBy($anonymous);
        $this->assertSame(
            false,
            $userManager->hasRightToDelete($user, $task),
            "User can't delete Anonymous task"
        );
        $this->assertSame(
            true,
            $userManager->hasRightToDelete($admin, $task),
            "Admin can delete Anonymous task"
        );
        $this->assertSame(
            false,
            $userManager->hasRightToDelete($anonymous, $task),
            "Anonymous can't delete Anonymous task"
        );
    }
}
