<?php

namespace App\DataFixtures;

use App\Manager\UserManager;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserFixture extends Fixture
{
    /**
     * @var UserManager
     */
    private $userManager;

    /**
     * UserFixtures constructor.
     *
     * @param UserManager $userManager
     */
    public function __construct(UserManager $userManager)
    {
        $this->userManager = $userManager;
    }

    public function load(ObjectManager $manager)
    {
        $user = $this->userManager->createUser('Anonymous', 'anonymous@todoandco.com', 'anonymous_todoandco', ['ROLE_ANONYMOUS']);
        $this->addReference(User::class.'_1', $user);

        $user = $this->userManager->createUser('Admin', 'admin@todoandco.com', 'admin_todoandco', ['ROLE_ADMIN']);
        $this->addReference(User::class.'_2', $user);

        $user = $this->userManager->createUser('UserOne', 'userone@todoandco.com', 'user1_todoandco', ['ROLE_USER']);
        $this->addReference(User::class.'_3', $user);

        $user = $this->userManager->createUser('UserTwo', 'usertwo@todoandco.com', 'user2_todoandco', ['ROLE_USER']);
        $this->addReference(User::class.'_4', $user);

        $user = $this->userManager->createUser('UserThree', 'userthree@todoandco.com', 'user3_todoandco', ['ROLE_USER']);
        $this->addReference(User::class.'_5', $user);

        $user = $this->userManager->createUser('UserFour', 'userfour@todoandco.com', 'user4_todoandco', ['ROLE_USER']);
        $this->addReference(User::class.'_6', $user);


        $manager->flush();
    }
}
