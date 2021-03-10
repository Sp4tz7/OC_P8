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
        $user = $this->userManager->createUser('Admin', 'admin@todoandco.com', 'admin_todoandco', ['ROLE_ADMIN']);
        $this->addReference(User::class.'_1', $user);

        $user = $this->userManager->createUser('UserOne', 'userone@todoandco.com', 'user1_todoandco');
        $this->addReference(User::class.'_2', $user);

        $user = $this->userManager->createUser('UserTwo', 'usertwo@todoandco.com', 'user2_todoandco');
        $this->addReference(User::class.'_3', $user);

        $user = $this->userManager->createUser('UserThree', 'userThree@todoandco.com', 'user3_todoandco');
        $this->addReference(User::class.'_4', $user);


        $manager->flush();
    }
}
