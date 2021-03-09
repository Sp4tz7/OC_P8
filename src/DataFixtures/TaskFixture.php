<?php

namespace App\DataFixtures;

use App\Entity\Task;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class TaskFixture extends Fixture implements DependentFixtureInterface
{
    protected $faker;

    public function load(ObjectManager $manager)
    {
        $this->faker = Factory::create();
        for ($i = 0; $i < 10; $i++) {
            $isAnonymous = $this->faker->boolean(10);
            $task = new Task();
            $task->toggle($this->faker->boolean(20));
            $task->setTitle($this->faker->text(40));
            $task->setCreatedAt($this->faker->dateTimeBetween("-2 years", "now"));
            $task->setContent($this->faker->text(200));
            if (!$isAnonymous) {
                $task->setCreatedBy($this->getReference(User::class.'_'.$this->faker->numberBetween(1, 4)));
            }
            $manager->persist($task);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            UserFixture::class
        ];
    }

}
