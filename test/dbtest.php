<?php
require_once '../controller/db.php';
require_once '../model/Response.php';

$response = new Response();

try {
    $writeDB = DB::connectWriteDB();
    $response->setSuccess(true);
    $response->setHttpStatusCode(200);
    $response->addMessage("Write connection success..!!");
} catch (PDOException $e) {
    $response->setSuccess(false);
    $response->setHttpStatusCode(500);
    $response->addMessage("Write connection error..!!");
    $response->send();
    exit;
}

try {
    $readDB = DB::connectReadDB();
    $response->setSuccess(true);
    $response->setHttpStatusCode(200);
    $response->addMessage("Read connection success..!!");
} catch (Exception $e) {
    $response->setSuccess(false);
    $response->setHttpStatusCode(500);
    $response->addMessage("Read connection error..!!");
    $response->send();
}
$response->send();
exit;
