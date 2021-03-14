<?php

namespace App\Tests\Controller;

use App\DataFixtures\UserFixture;
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

    public function setUp(): void
    {
        $this->client = static::createClient();
        $this->loadFixtures([UserFixture::class]);
        $this->tokenManager = $this->client->getContainer()->get('security.csrf.token_manager');
    }

    public function testDisplayLoginFormIfNotLoggedIn()
    {
        $this->client->request('GET', '/login');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorExists('form');
    }

    public function testDisplayHomepageIfLoggedIn()
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

}
