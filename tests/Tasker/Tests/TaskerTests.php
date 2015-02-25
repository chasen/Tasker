<?php
namespace Tasker\Tests;

use Silex\WebTestCase;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use CN\Service\TaskFileStorage;

class TaskerTests extends WebTestCase
{
    protected $tasks = [];

    public function removeTestTasks()
    {
        foreach($this->tasks as $task){
            $this->app['task_storage']->removeTask($task->id);
        }
    }

    public function createApplication()
    {
        $app_env = 'test';
        $app = require __DIR__ . '/../../../web/index.php';
        $app['debug'] = true;
//        $app['session.storage'] = $app->share(function() {
//            return new MockArraySessionStorage();
//        });
        $app['session.test'] = true;
//        $app['session.use_cookies'] = FALSE;
        $app['exception_handler']->disable();
        $app['task_storage'] = new TaskFileStorage($app);
        return $app;
    }

    public function testIndexPage()
    {
        $client = $this->createClient();
        $crawler = $client->request('GET', '/');
        $this->assertTrue($client->getResponse()->isOk());
        $this->assertCount(1, $crawler->filter('div.panel-heading:contains("Task List")'));
        $this->assertCount(1, $crawler->filter('form'));
    }

    public function testCreateTask($deleteAfter=true)
    {
        $client = $this->createClient();
        $crawler = $client->request('POST', '/api/task/create/', array('task'=>'The is a test task'));
        $this->assertTrue($client->getResponse()->isOk());
        $response = json_decode($client->getResponse()->getContent());
        $this->assertTrue(property_exists($response,'status'));
        $this->assertTrue($response->status === 'success');
        $this->assertTrue(property_exists($response,'task'));
        $this->assertTrue(property_exists($response->task,'id'));
        $this->assertNotNull($response->task->id);
        $this->assertTrue(property_exists($response->task,'task'));
        $this->assertEquals('The is a test task',$response->task->task);

        $this->tasks[] = $response->task;
        if($deleteAfter){
            $this->removeTestTasks();
        }
        return $response->task;
    }


    public function testGetAllTasks()
    {
        $createdTask = $this->testCreateTask(false);
        $client = $this->createClient();
        $crawler = $client->request('GET', '/api/tasks/');
        $this->assertTrue($client->getResponse()->isOk());
        $response = json_decode($client->getResponse()->getContent());
        $this->assertNotEmpty($response->tasks);
        $this->removeTestTasks();
    }

    public function testUpdateTask()
    {
        $createdTask = $this->testCreateTask(false);
        $client = $this->createClient();
        $crawler = $client->request('PUT', '/api/task/update/'.$createdTask->id.'/',array('task'=>'New task text for update test'));
        $this->assertTrue($client->getResponse()->isOk());
        $response = json_decode($client->getResponse()->getContent());
        $this->assertNotEmpty($response->task);
        $this->assertEquals('New task text for update test', $response->task->task);
        $this->assertEquals($createdTask->id, $response->task->id);
        $this->removeTestTasks();
    }

    public function testDeleteTask()
    {
        $createdTask = $this->testCreateTask(false);
        $client = $this->createClient();
        $crawler = $client->request('DELETE', '/api/task/delete/'.$createdTask->id.'/');
        $this->assertTrue($client->getResponse()->isOk());
        $response = json_decode($client->getResponse()->getContent());
        $this->assertEquals('success',$response->status);
    }
}