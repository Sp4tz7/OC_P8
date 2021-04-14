<?php

namespace App\Tests\Controller;

use App\DataFixtures\UserFixture;
use App\Repository\UserRepository;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{
    use FixturesTrait;


    private $userOne;

    public function setUp(): void
    {
        $this->client = static::createClient();
        $this->loadFixtures([UserFixture::class]);
        $userRepository  = self::$container->get(UserRepository::class);
        $this->anonymous = $userRepository->findOneBy(['email' => 'anonymous@todoandco.com']);
        $this->userOne   = $userRepository->findOneBy(['email' => 'userone@todoandco.com']);
        $this->admin     = $userRepository->findOneBy(['email' => 'admin@todoandco.com']);
    }

    public function testCreateUser()
    {
        $this->client->loginUser($this->admin);
        $crawler = $this->client->request('GET', '/users/create');
        $form    = $crawler->filter('[name="user"]')->form([
            'user[username]'         => 'testUsernameMod',
            'user[email]'            => 'test@test.com',
            'user[password][first]'  => 'testpassword',
            'user[password][second]' => 'testpassword',
            'user[roles]'            => 'ROLE_USER',
        ]);
        $this->client->submit($form);
        $this->assertResponseRedirects('/users');
        $this->client->followRedirect();
        $this->assertSelectorExists('.alert.alert-success');
    }

    public function testUpdateUser()
    {
        $this->client->loginUser($this->admin);
        $crawler = $this->client->request('GET', '/users/'.$this->userOne->getid().'/edit');
        $form    = $crawler->filter('[name="user"]')->form([
            'user[username]'         => $this->userOne->getUsername(),
            'user[email]'            => $this->userOne->getEmail(),
            'user[password][first]'  => 'newpassword',
            'user[password][second]' => 'newpassword',
            'user[roles]'            => 'ROLE_USER',
        ]);
        $this->client->submit($form);
        $this->assertResponseRedirects('/users');
        $this->client->followRedirect();
        $this->assertSelectorExists('.alert.alert-success');
    }

    public function testAdminCanDeleteUser()
    {
        $this->client->loginUser($this->admin);
        $this->client->request('GET', '/users/'.$this->userOne->getid().'/delete');
        $this->assertResponseRedirects('/users');
        $this->client->followRedirect();
        $this->assertSelectorExists('.alert.alert-success');
    }

    public function testAdminCantDeleteAdmin()
    {
        $this->client->loginUser($this->admin);
        $this->client->request('GET', '/users/'.$this->admin->getid().'/delete');
        $this->assertResponseRedirects('/users');
        $this->client->followRedirect();
        $this->assertSelectorExists('.alert.alert-danger');
    }

    public function testAdminCantDeleteAnonymous()
    {
        $this->client->loginUser($this->admin);
        $this->client->request('GET', '/users/'.$this->anonymous->getid().'/delete');
        $this->assertResponseRedirects('/users');
        $this->client->followRedirect();
        $this->assertSelectorExists('.alert.alert-danger');
    }

}
