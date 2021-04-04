<?php

namespace App\Tests\Manager;

use App\Entity\Task;
use App\Entity\User;
use App\Manager\UserManager;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Liip\TestFixturesBundle\Test\FixturesTrait;

class UserManagerTest extends TestCase
{
    use FixturesTrait;

    /**
     * @var UserManager
     */
    private $anonymous;
    private $user;
    private $admin;
    private $userManager;

    public function setUp(): void
    {
        $manager = $this->getMockBuilder(EntityManagerInterface::class)->disableOriginalConstructor()->getMock();
        $encoder = $this->getMockBuilder(UserPasswordEncoderInterface::class)->disableOriginalConstructor()->getMock();

        $this->userManager = new UserManager($manager, $encoder);
    }

    public function testhasRightToDeleteTask()
    {
        $admin = new User();
        $admin->setRoles(['ROLE_ADMIN']);

        $anonymous = new User();
        $anonymous->setRoles(['ROLE_ANONYMOUS']);

        $user = new User();
        $user->setRoles(['ROLE_USER']);

        $task = new Task();
        $task->setCreatedBy($user);

        $this->assertSame(
            true,
            $this->userManager->hasRightToDeleteTask($user, $task),
            "User can delete user task"
        );
        $this->assertSame(
            false,
            $this->userManager->hasRightToDeleteTask($admin, $task),
            "Admin can't delete user task"
        );
        $this->assertSame(
            false,
            $this->userManager->hasRightToDeleteTask($anonymous, $task),
            "Anonymous can't delete user task"
        );

        $task->setCreatedBy($admin);
        $this->assertSame(
            false,
            $this->userManager->hasRightToDeleteTask($user, $task),
            "User can't delete admin task"
        );
        $this->assertSame(
            true,
            $this->userManager->hasRightToDeleteTask($admin, $task),
            "Admin can delete admin task"
        );
        $this->assertSame(
            false,
            $this->userManager->hasRightToDeleteTask($anonymous, $task),
            "Anonymous can't delete admin task"
        );

        $task->setCreatedBy($anonymous);
        $this->assertSame(
            false,
            $this->userManager->hasRightToDeleteTask($user, $task),
            "User can't delete Anonymous task"
        );
        $this->assertSame(
            true,
            $this->userManager->hasRightToDeleteTask($admin, $task),
            "Admin can delete Anonymous task"
        );
        $this->assertSame(
            false,
            $this->userManager->hasRightToDeleteTask($anonymous, $task),
            "Anonymous can't delete Anonymous task"
        );
    }
    public function testHasRightToDeleteUser()
    {
        $admin = new User();
        $admin->setRoles(['ROLE_ADMIN']);

        $anonymous = new User();
        $anonymous->setRoles(['ROLE_ANONYMOUS']);

        $user = new User();
        $user->setRoles(['ROLE_USER']);

        $this->assertSame(
            false,
            $this->userManager->hasRightToDeleteUser($admin, $admin),
            "Admin can't delete himself"
        );
        $this->assertSame(
            false,
            $this->userManager->hasRightToDeleteUser($admin, $anonymous),
            "Admin can't delete user anonymous"
        );
        $this->assertSame(
            true,
            $this->userManager->hasRightToDeleteUser($admin, $user),
            "Admin can delete user"
        );


    }

    public function testFormatMobileNumberReturnOnlyDigits()
    {
        $this->assertIsNumeric($this->userManager->formatMobileNumber('0041 79 654 87'));
        $this->assertIsNumeric($this->userManager->formatMobileNumber('tel: 0041 79 654 87'));
        $this->assertNull($this->userManager->formatMobileNumber('tel:'), 'Non numeric value should return null');
    }

    public function testFormatBirthDate()
    {
        $date = '2021-12-12';
        $this->assertEquals(new \DateTime($date), $this->userManager->formatBirthDate($date));
    }

    public function testCreateUser()
    {
        $user = $this->userManager->createUser(
            'Anonymous',
            'anonymous@todoandco.com',
            'anonymous_todoandco',
            ['ROLE_ANONYMOUS'],
            'Anon',
            'Imous',
            '25-12-2552',
            '098765213251',
            'Director',
            null
        );

        $this->assertInstanceOf(User::class, $user);
    }
}
