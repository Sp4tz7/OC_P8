<?php

namespace App\DataFixtures;

use App\Manager\UserManager;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\DependencyInjection\ContainerInterface;

class UserFixture extends Fixture
{
    protected $faker;
    /**
     * @var UserManager
     */
    private $userManager;
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * UserFixtures constructor.
     *
     * @param UserManager $userManager
     */
    public function __construct(UserManager $userManager, ContainerInterface $container)
    {
        $this->userManager = $userManager;
        $this->container   = $container;
    }

    public function load(ObjectManager $manager)
    {
        $this->faker = Factory::create();
        $user        = $this->userManager->createUser(
            'Anonymous',
            'anonymous@todoandco.com',
            'anonymous_todoandco',
            ['ROLE_ANONYMOUS'],
            $this->faker->name,
            $this->faker->lastName,
            $this->faker->date(),
            $this->faker->phoneNumber,
            $this->faker->text(50),
            null,
            $this->faker->image($this->container->getParameter('app.user.avatar_dir'))
        );
        $this->addReference(User::class.'_1', $user);

        $user = $this->userManager->createUser(
            'Admin',
            'admin@todoandco.com',
            'admin_todoandco',
            ['ROLE_ADMIN'],
            $this->faker->name,
            $this->faker->lastName,
            $this->faker->date(),
            $this->faker->phoneNumber,
            $this->faker->text(50),
            null,
            $this->faker->image($this->container->getParameter('app.user.avatar_dir'))
        );
        $this->addReference(User::class.'_2', $user);

        $user = $this->userManager->createUser(
            'UserOne',
            'userone@todoandco.com',
            'user1_todoandco',
            ['ROLE_USER'],
            $this->faker->name,
            $this->faker->lastName,
            $this->faker->date(),
            $this->faker->phoneNumber,
            $this->faker->text(50),
            null,
            $this->faker->image($this->container->getParameter('app.user.avatar_dir'))
        );
        $this->addReference(User::class.'_3', $user);

        $user = $this->userManager->createUser(
            'UserTwo',
            'usertwo@todoandco.com',
            'user2_todoandco',
            ['ROLE_USER'],
            $this->faker->name,
            $this->faker->lastName,
            $this->faker->date(),
            $this->faker->phoneNumber,
            $this->faker->text(50),
            null,
            $this->faker->image($this->container->getParameter('app.user.avatar_dir'))
        );
        $this->addReference(User::class.'_4', $user);

        $user = $this->userManager->createUser(
            'UserThree',
            'userthree@todoandco.com',
            'user3_todoandco',
            ['ROLE_USER'],
            $this->faker->name,
            $this->faker->lastName,
            $this->faker->date(),
            $this->faker->phoneNumber,
            $this->faker->text(50),
            null,
            $this->faker->image($this->container->getParameter('app.user.avatar_dir'))
        );
        $this->addReference(User::class.'_5', $user);

        $user = $this->userManager->createUser(
            'UserFour',
            'userfour@todoandco.com',
            'user4_todoandco',
            ['ROLE_USER'],
            $this->faker->firstName,
            $this->faker->lastName,
            $this->faker->date(),
            $this->faker->phoneNumber,
            $this->faker->text(50),
            null,
            $this->faker->image($this->container->getParameter('app.user.avatar_dir'))
        );
        $this->addReference(User::class.'_6', $user);


        $manager->flush();
    }
}
