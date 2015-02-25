<?php
namespace CN\Service;

use CN\Model\Task;
use CN\Service\TaskStorageInterface;
use Rhumsaa\Uuid\Uuid;

class TaskSessionStorage implements TaskStorageInterface
{
    private $app;

    public function __construct($app)
    {
        $this->app = $app;
    }

    /**
     * @return array
     */
    public function getAllTasks()
    {
        $tasks = $this->app['session']->get('tasks');
        return $tasks;
    }

    /**
     * @param int $taskId
     * @throws \Exception
     * @return Task
     */
    public function getTask($taskId)
    {
        $tasks = $this->app['session']->get('tasks');
        if(array_key_exists($taskId,$tasks)){
            return $tasks[$taskId];
        }
        else{
            throw new \Exception('Unable to get task with ID: '.$taskId);
        }
    }

    /**
     * @param Task $task
     * @throws \Exception
     * @return void
     */
    public function putTask(Task &$task)
    {
        if(!$task->id){
            $taskId = Uuid::uuid4()->toString();
            $task->id =$taskId;
        }
        $tasks = $this->app['session']->get('tasks');
        $tasks[$task->id] = $task;
        $this->app['session']->set('tasks',$tasks);
    }

    /**
     * @param mixed $taskId
     * @return boolean
     */
    public function removeTask($taskId)
    {
        $tasks = $this->app['session']->get('tasks');
        unset($tasks[$taskId]);
        $this->app['session']->set('tasks',$tasks);
        return true;
    }
}