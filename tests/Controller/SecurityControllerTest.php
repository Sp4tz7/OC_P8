<?php

namespace App\Tests\Controller;

use App\DataFixtures\UserFixture;
use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class SecurityControllerTest extends WebTestCase
{
    use FixturesTrait;

    private $client;
    /**
     * @var object|null
     */
    private $tokenManager;
    private $user;

    public function setUp(): void
    {
        $this->client = static::createClient();
        $this->loadFixtures([UserFixture::class]);
        $this->tokenManager = $this->client->getContainer()->get('security.csrf.token_manager');
        $userRepository           = self::$container->get(UserRepository::class);
        $this->user               = $userRepository->findOneBy(['email' => 'userone@todoandco.com']);
    }

    public function testDisplayHomepageIfLoggedIn()
    {
        $this->client->loginUser($this->user);
        $this->client->request('GET', '/login');
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $this->assertResponseRedirects('/');
    }
    
    public function testDisplayLoginFormIfNotLoggedIn()
    {
        $this->client->request('GET', '/login');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorExists('form');
    }

    public function testLoginWithWrongCredentials()
    {
        $this->client->request('POST', '/login_check', [
            '_username'   => 'Admin',
            '_password'   => 'admin',
            '_csrf_token' => $this->tokenManager->getToken('authenticate'),
        ]);
        $this->assertResponseRedirects('http://localhost/login', 302);
        $this->client->followRedirect();
        $this->assertSelectorExists('.alert.alert-danger');
    }

    public function testLoginWithCorrectCredentials()
    {
        $this->client->request('POST', '/login_check', [
            '_username'   => 'UserOne',
            '_password'   => 'user1_todoandco',
            '_csrf_token' => $this->tokenManager->getToken('authenticate'),
        ]);
        $this->assertResponseRedirects('http://localhost/', 302);
    }

    public function testLoginCheck()
    {
        $this->client->request('GET', '/login_check');
        $this->assertResponseStatusCodeSame(Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * @covers \App\Controller\SecurityController::logoutCheck
     */
    public function testLogoutCheck()
    {
        $this->client->request('GET', '/logout');
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $this->client->followRedirect();
        $this->assertSelectorExists('form');
    }

}
