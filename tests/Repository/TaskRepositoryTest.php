<?php

namespace App\Tests\Controller;

use App\DataFixtures\TaskFixture;
use App\DataFixtures\UserFixture;
use App\Entity\User;
use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;


class TaskRepositoryTest extends KernelTestCase
{
    use FixturesTrait;


    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $entityManager;

    /**
     * @covers \App\Repository\UserRepository::findByRoles
     */
    public function testFindByUser()
    {
        self::bootKernel();
        $this->loadFixtures([UserFixture::class, TaskFixture::class]);

        $user = self::$container->get(UserRepository::class)->findOneBy(['username' => 'Admin']);
        $this->assertInstanceOf(User::class, $user);
        $query = self::$container->get(TaskRepository::class)->findByUser($user);
        $this->assertIsArray($query);
    }

    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        // doing this is recommended to avoid memory leaks
        $this->entityManager->close();
        $this->entityManager = null;
    }


}
