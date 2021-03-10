<?php

namespace App\Tests\Controller;

use App\DataFixtures\UserFixture;
use App\Repository\UserRepository;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class DefaultControllerTest extends WebTestCase
{
    use FixturesTrait;

    private $client;

    public function setUp():void
    {
        $this->client = static::createClient();
        $this->loadFixtures([UserFixture::class]);
        $userRepository = self::$container->get(UserRepository::class);
        $this->user     = $userRepository->findOneBy(['email' => 'userone@todoandco.com']);
        $this->admin    = $userRepository->findOneBy(['email' => 'admin@todoandco.com']);
    }

    public function testRedirectToLogin()
    {
        $this->client->request('GET', '/');
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

    }

    public function testAuthenticatedUserAccessHome()
    {
        $this->client->loginUser($this->user);
        $this->client->request('GET', '/');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

}
