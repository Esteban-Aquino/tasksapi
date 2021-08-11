<?php
require_once('../model/Task.php');
require_once('../model/Response.php');

$response = new Response();
try {
    $tasks = array();
    $task = new Task(1, "Title here","Description hera","21/01/2019 21:00","N");
    $tasks[] = $task->returnTaskAsArray();
    $task = new Task(2, "Title 2 here","Description2  here","02/01/2019 22:00","N");
    $tasks[] = $task->returnTaskAsArray();
    $response->setSuccess(true);
    $response->setHttpStatusCode(200);
    $response->addMessage("Task created successfully!!");
    $response->setData($tasks);
    $response->send();
} catch (TaskException $ex) {
    $response->setSuccess(false);
    $response->setHttpStatusCode(500);
    $response->addMessage("Task error:".$ex->getMessage());
    $response->send();
}