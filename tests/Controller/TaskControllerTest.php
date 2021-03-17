<?php

namespace App\Tests\Controller;

use App\DataFixtures\TaskFixture;
use App\DataFixtures\UserFixture;
use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class TaskControllerTest extends WebTestCase
{
    use FixturesTrait;

    private $client;

    private $tokenManager;
    private $anonymous;
    private $user;
    private $userTwo;
    private $userNoTask;
    private $admin;

    private $tasksFromUserTwo;

    public function setUp(): void
    {
        $this->client = static::createClient();
        $this->loadFixtures([TaskFixture::class, UserFixture::class]);
        $userRepository           = self::$container->get(UserRepository::class);
        $taskRepository           = self::$container->get(TaskRepository::class);
        $this->anonymous          = $userRepository->findOneBy(['email' => 'anonymous@todoandco.com']);
        $this->user               = $userRepository->findOneBy(['email' => 'userone@todoandco.com']);
        $this->userTwo            = $userRepository->findOneBy(['email' => 'usertwo@todoandco.com']);
        $this->userNoTask         = $userRepository->findOneBy(['email' => 'userfour@todoandco.com']);
        $this->admin              = $userRepository->findOneBy(['email' => 'admin@todoandco.com']);
        $this->tasksFromUserOne   = $taskRepository->findBy(['created_by' => $this->user])[0];
        $this->tasksFromUserTwo   = $taskRepository->findBy(['created_by' => $this->userTwo])[0];
        $this->tasksFromAnonymous = $taskRepository->findBy(['created_by' => $this->anonymous])[0];
    }

    public function testRedirectToLoginIfNotLoggedIn()
    {
        $this->client->request('GET', '/tasks');
        $this->assertResponseRedirects('http://localhost/login');
    }

    public function testUserTaskListActionWithTasks()
    {
        $this->client->loginUser($this->user);
        $this->client->request('GET', '/tasks');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorExists('tr');
    }

    public function testUserTaskListActionWithNoTasks()
    {
        $this->client->loginUser($this->userNoTask);
        $this->client->request('GET', '/tasks?status=mine');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorExists('.alert.alert-warning');
    }

    public function testNoDoneTasksInTodo()
    {
        $this->client->loginUser($this->user);
        $this->client->request('GET', '/tasks?status=todo');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorNotExists('.fa-thumbs-down');
    }

    public function testNoTodoTasksInDone()
    {
        $this->client->loginUser($this->user);
        $this->client->request('GET', '/tasks?status=done');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorNotExists('.fa-thumbs-up');
    }

    public function testUserCantDeleteOtherTasks()
    {
        $this->client->loginUser($this->user);
        $task = $this->tasksFromUserTwo;
        $this->client->request('GET', '/tasks/'.$task->getId().'/delete');
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $this->assertResponseRedirects('/tasks');
        $this->client->followRedirect();
        $this->assertSelectorExists('.alert.alert-danger');
    }

    public function testAdminCanDeleteAnonymousTasks()
    {
        $this->client->loginUser($this->admin);
        $task = $this->tasksFromAnonymous;
        $this->client->request('GET', '/tasks/'.$task->getId().'/delete');
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $this->assertResponseRedirects('/tasks');
        $this->client->followRedirect();
        $this->assertSelectorExists('.alert.alert-success');
    }

    public function testTaskDelete()
    {
        $this->client->loginUser($this->user);
        $task = $this->tasksFromUserOne;
        $this->client->request('GET', '/tasks/'.$task->getId().'/delete');
        $this->assertNull($task->getId());
    }

    public function testTaskToggleStatus()
    {
        $this->client->loginUser($this->user);
        $task = $this->tasksFromUserOne;
        $done = $task->isDone();
        $this->client->request('POST', '/tasks/'.$task->getId().'/toggle');
        $this->assertNotEquals($task->isDone(), $done);
    }

    public function testTaskCreate()
    {
        $this->client->loginUser($this->user);
        $crawler = $this->client->request('GET', '/tasks/create');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $form = $crawler->filter('[name="task"]')->form([
            'task[title]'   => 'Dolores cumque aut cumque harum quam.',
            'task[content]' => 'Itaque officia sint voluptatibus perferendis officiis. Sit rerum deserunt quia eum et. Fugit animi nesciunt ad.',
        ]);
        $this->client->submit($form);
        $this->assertResponseRedirects('/tasks');
        $this->client->followRedirect();
        $this->assertSelectorExists('.alert.alert-success');
    }

    public function testTaskEdit()
    {
        $this->client->loginUser($this->user);
        $task    = $this->tasksFromUserOne;
        $crawler = $this->client->request('GET', '/tasks/'.$task->getId().'/edit');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $form = $crawler->filter('[name="task"]')->form([
            'task[title]'   => 'Dolores cumque aut cumque harum quam.',
            'task[content]' => 'Itaque officia sint voluptatibus perferendis officiis. Sit rerum deserunt quia eum et. Fugit animi nesciunt ad.',
        ]);
        $this->client->submit($form);
        $this->assertResponseRedirects('/tasks');
        $this->client->followRedirect();
        $this->assertSelectorExists('.alert.alert-success');
    }

}
