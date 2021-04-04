<?php

namespace App\Tests\Controller;

use App\DataFixtures\UserFixture;
use App\Repository\UserRepository;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;


class UserRepositoryTest extends KernelTestCase
{
    use FixturesTrait;


    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $entityManager;

    /**
     * @covers \App\Repository\UserRepository::findByRoles
     */
    public function testFindByRoles()
    {
        self::bootKernel();
        $this->loadFixtures([UserFixture::class]);

        $query = self::$container->get(UserRepository::class)->findByRoles('anonymous');
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
