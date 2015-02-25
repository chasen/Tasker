<?php
namespace CN\Service;

use CN\Model\Task;

interface TaskStorageInterface
{
    /**
     * @return array
     */
    public function getAllTasks();

    /**
     * @param $taskId integer
     * @return Task
     */
    public function getTask($taskId);

    /**
     * @param Task $task
     * @return void
     */
    public function putTask(Task &$task);

    /**
     * @param mixed $taskId
     * @return boolean
     */
    public function removeTask($taskId);
}