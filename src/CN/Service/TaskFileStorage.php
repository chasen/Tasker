<?php
namespace CN\Service;

use CN\Model\Task;
use CN\Service\TaskStorageInterface;
use Rhumsaa\Uuid\Uuid;

class TaskFileStorage implements TaskStorageInterface
{
    private $taskStorageDir;

    public function __construct($app)
    {
        $this->taskStorageDir = $app['root_dir'].'/../tasks/';
    }

    /**
     * @return array
     */
    public function getAllTasks()
    {
        $taskFiles = scandir($this->taskStorageDir);
        $tasks = [];
        foreach($taskFiles as $file){
            if(strpos($file,'task_') !== false) {
                $task = new Task();
                $task->unserialize(file_get_contents($this->taskStorageDir . $file));
                array_push($tasks, $task);
            }
        }
        return $tasks;
    }

    /**
     * @param int $taskId
     * @throws \Exception
     * @return Task
     */
    public function getTask($taskId)
    {
        $filename = $this->getFilenameFromId($taskId);
        if(file_exists($filename)){
            $task = new Task();
            $task->unserialize(file_get_contents($filename));
            return $task;
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
        if($task->id === null){
            $taskId = Uuid::uuid4()->toString();
            $task->id =$taskId;
        }
        $filename = $this->getFilenameFromId($task->id);

        if(file_put_contents($filename,$task->serialize()) === false){
            throw new \Exception('Unable to save task.');
        }
    }

    /**
     * @param mixed $taskId
     * @return boolean
     */
    public function removeTask($taskId)
    {
        $file = $this->getFilenameFromId($taskId);
        return unlink($file);
    }

    /**
     * @param $id
     * @return string
     */
    private function getFilenameFromId($id)
    {
        return $this->taskStorageDir.'task_'.$id.'.json';
    }
}