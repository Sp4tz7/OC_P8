<?php

namespace App\Tests\Controller;

use App\DataFixtures\UserFixture;
use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class UserControllerTest extends WebTestCase
{
    use FixturesTrait;



    public function setUp(): void
    {
        $this->client = static::createClient();
        $this->loadFixtures([UserFixture::class]);
        $userRepository           = self::$container->get(UserRepository::class);
        $this->anonymous          = $userRepository->findOneBy(['email' => 'anonymous@todoandco.com']);
        $this->user               = $userRepository->findOneBy(['email' => 'userone@todoandco.com']);
        $this->userTwo            = $userRepository->findOneBy(['email' => 'usertwo@todoandco.com']);
        $this->userNoTask         = $userRepository->findOneBy(['email' => 'userfour@todoandco.com']);
        $this->admin              = $userRepository->findOneBy(['email' => 'admin@todoandco.com']);

    }

    public function testCreateUser()
    {
        $this->client->loginUser($this->admin);
        $crawler = $this->client->request('GET', '/users/create');
        $form = $crawler->filter('[name="user"]')->form([
            'user[username]' => 'testUsernameMod',
            'user[email]' => 'test@test.com',
            'user[password][first]' => 'testpassword',
            'user[password][second]' => 'testpassword',
            'user[roles]' => 'ROLE_USER'
        ]);
        $this->client->submit($form);
        $this->assertResponseRedirects('/users');
        $this->client->followRedirect();
        $this->assertSelectorExists('.alert.alert-success');
    }

}
