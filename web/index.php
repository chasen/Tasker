<?php
require_once __DIR__.'/../vendor/autoload.php';

use Symfony\Component\Validator\Constraints as Assert;
use CN\Service\TaskFileService;
use CN\Service\TaskSessionStorage;

//Init App
$app = new Silex\Application();

if (isset($app_env) && in_array($app_env, array('prod','dev','test'))){
    $app['env'] = $app_env;
}
else{
    $app['env'] = 'prod';
}

//Register Services
$app->register(new Silex\Provider\ValidatorServiceProvider());
$app->register(new Silex\Provider\SessionServiceProvider());
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/../views',
));
$app['root_dir'] = __DIR__;
//Use either TaskSessionStorage or TaskFileStorage, or build another storage handler implementing TaskStorageInterface
$app['task_storage'] = new TaskSessionStorage($app);


//Define routes and how they are handled

//Single page that will show the task list
$app->get('/', function() use ($app) {
    return $app['twig']->render('index.html.twig');
});

//Api routes to handle the task CRUD
$app->get('/api/tasks/', function() use ($app) {
    $taskModels = $app['task_storage']->getAllTasks();
    $tasks = array();
    foreach($taskModels as $task){
        array_push($tasks, $task->toArray());
    }
    return json_encode(array(
        'status'=>'success',
        'tasks'=>$tasks
    ));
});
$app->post('/api/task/create/',function() use($app){
    $task = new \CN\Model\Task();
    $task->task = $app['request']->get('task');
    try {
        $app['task_storage']->putTask($task);
        return json_encode(array(
            'status' => 'success',
            'task'=>$task->toArray()
        ));
    } catch(\Exception $e){
        return json_encode(array(
            'status'=>'failed',
            'message'=>$e->getMessage()
        ));
    }

});
$app->put('/api/task/update/{taskId}/',function($taskId) use($app){
    if(!$app['validator']->validateValue($taskId, new Assert\Uuid(array('versions'=>array(4))))){
        return json_encode(array(
            'status'=>'failed',
            'message'=>'Invalid task ID'
        ));
    }
    $task = $app['task_storage']->getTask($taskId);
    $task->task = $app['request']->get('task');
    try {
        $app['task_storage']->putTask($task);
        return json_encode(array(
            'status' => 'success',
            'task'=>$task->toArray()
        ));
    } catch(\Exception $e){
        return json_encode(array(
            'status'=>'failed',
            'message'=>$e->getMessage()
        ));
    }
});
$app->delete('/api/task/delete/{taskId}/', function($taskId) use($app){
    if(!$app['validator']->validateValue($taskId, new Assert\Uuid(array('versions'=>array(4))))){
        return json_encode(array(
            'status'=>'failed',
            'message'=>'Invalid task ID'
        ));
    }
    if($app['task_storage']->removeTask($taskId)){
        return json_encode(array(
            'status'=>'success'
        ));
    }
    return json_decode(array(
        'status'=>'failed',
        'message'=>'failed to delete task'
    ));
});

//Run the app

if ('test' == $app['env']){
    return $app;
}
else {
    $app->run();
}