<?php
require_once 'db.php';
require_once '../model/Response.php';
require_once '../model/Task.php';

// Create response
$response = new Response();
// Connect Data Base
try {
    $writeDB = DB::connectWriteDB();
    $readDB = DB::connectReadDB();
} catch (PDOException $e) {
    error_log("Connection error: " . $e->getMessage(), 0);
    $response->setSuccess(false);
    $response->setHttpStatusCode(500);
    $response->addMessage("Database connection error");
    $response->send();
    exit();
}

// Exists taskid
if (array_key_exists('taskid', $_GET)) {
    $taskid = $_GET['taskid'];

    if ($taskid == '' || !is_numeric($taskid)) {
        $response->setSuccess(false);
        $response->setHttpStatusCode(400);
        $response->addMessage("Task id is incorrect!");
        $response->send();
        exit();
    }

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        try {
            $query = $readDB->prepare('select id,
                                             title,
                                             description,
                                             DATE_FORMAT(deadline,"%d/%m/%Y %H:%i") as deadline,
                                             compleated
                                             from tbltasks
                                             where id = :taskid');
            $query->bindParam(':taskid', $taskid, PDO::PARAM_INT);
            $query->execute();
            $rowCount = $query->rowCount();
            if ($rowCount === 0) {
                $response->setSuccess(false);
                $response->setHttpStatusCode(404);
                $response->addMessage("Task " . $taskid . " not found");
                $response->send();
                exit();
            }
            $tasks = array();
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                $task = new Task($row['id'], $row['title'], $row['description'], $row['deadline'], $row['compleated']);
                $tasks[] = $task->returnTaskAsArray();
            }
            $returnData = array();
            $returnData['rows_returned'] = $rowCount;
            $returnData['tasks'] = $tasks;
            $response->setData($returnData);

            $response->setSuccess(true);
            $response->setHttpStatusCode(200);
            $response->toCache(true);
            $response->send();
            exit();

        } catch (PDOException $e) {
            error_log("Get error: " . $e->getMessage(), 0);
            $response->setSuccess(false);
            $response->setHttpStatusCode(500);
            $response->addMessage("Error getting the task from de database");
            $response->send();
            exit();

        } catch (TaskException $e) {
            error_log("Task exception: " . $e->getMessage(), 0);
            $response->setSuccess(false);
            $response->setHttpStatusCode(500);
            $response->addMessage("Error creating the task");
            $response->send();
            exit();
        }

    } elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
        try {
            $query = $writeDB->prepare('delete from tbltasks where id = :taskid');
            $query->bindParam('taskid', $taskid, PDO::PARAM_INT);
            $query->execute();
            $rowCount = $query->rowCount();
            if ($rowCount === 0) {
                $response->setSuccess(false);
                $response->setHttpStatusCode(404);
                $response->addMessage("Task " . $taskid . " not found");
                $response->send();
                exit();
            }

            $response->setSuccess(true);
            $response->setHttpStatusCode(200);
            $response->addMessage("Task " . $taskid . " deleted");
            $response->send();
            exit();
        } catch (PDOException $e) {
            error_log("Delete error: " . $e->getMessage(), 0);
            $response->setSuccess(false);
            $response->setHttpStatusCode(500);
            $response->addMessage("Error deleting the task from de database");
            $response->send();
            exit();

        }

    } elseif ($_SERVER['REQUEST_METHOD'] === 'PATCH') {
    } else {
        $response->setSuccess(false);
        $response->setHttpStatusCode(405);
        $response->addMessage("Request method not allowed!");
        $response->send();
        exit();
    }
} elseif (array_key_exists('compleated', $_GET)) {
    $compleated = $_GET['compleated'];
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        try {
            $query = $readDB->prepare('select id,
                                        title,
                                        description,
                                        DATE_FORMAT(deadline,"%d/%m/%Y %H:%i") as deadline,
                                        compleated
                                        from tbltasks
                                        where compleated = :compleated');
            $query->bindParam(':compleated', $compleated, PDO::PARAM_STR);
            $query->execute();
            $rowCount = $query->rowCount();
            if ($rowCount === 0) {
                $response->setSuccess(false);
                $response->setHttpStatusCode(404);
                $response->addMessage("No tasks found");
                $response->send();
                exit();
            }

            $tasks = array();
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                $task = new Task($row['id'], $row['title'], $row['description'], $row['deadline'], $row['compleated']);
                $tasks[] = $task->returnTaskAsArray();
            }
            $returnData = array();
            $returnData['rows_returned'] = $rowCount;
            $returnData['tasks'] = $tasks;
            $response->setData($returnData);

            $response->setSuccess(true);
            $response->setHttpStatusCode(200);
            $response->toCache(true);
            $response->send();
            exit();

        } catch (TaskException $e) {
            $response->setSuccess(false);
            $response->setHttpStatusCode(500);
            $response->addMessage($e->getMessage());
            $response->send();
            exit();
        } catch (PDOException $e) {
            error_log("Get for compleated error: " . $e->getMessage(), 0);
            $response->setSuccess(false);
            $response->setHttpStatusCode(500);
            $response->addMessage("Error getting the tasks from de database");
            $response->send();
            exit();

        }
    } else {
        $response->setSuccess(false);
        $response->setHttpStatusCode(405);
        $response->addMessage("Request method not allowed!");
        $response->send();
        exit();
    }
} elseif (array_key_exists('page', $_GET)) {
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $page = $_GET['page'];
        if ($page == '' || !is_numeric($page)) {
            $response->setSuccess(false);
            $response->setHttpStatusCode(404);
            $response->addMessage("Not valid page");
            $response->send();
            exit();
        }
        $limitPerPage = 20;
        try {
            $query = $readDB->prepare('select count(id) as totalNoOfTasks
                                             from tbltasks');
            $query->execute();                               
            $row = $query->fetch(PDO::FETCH_ASSOC);

            $tasksCount = intval($row['totalNoOfTasks']);
            $numOpPages = ceil($tasksCount/$limitPerPage);

            if($numOpPages == 0) {
                $numOpPages = 1;
            }

            if ($page > $numOpPages || $page == 0){
                $response->setSuccess(false);
                $response->setHttpStatusCode(404);
                $response->addMessage("Page not found");
                $response->send();
                exit();
            }

            $offset = ($page == 1 ? 0: ($limitPerPage * ($page-1)));
            $query = $readDB->prepare('select id,
                                             title,
                                             description,
                                             DATE_FORMAT(deadline,"%d/%m/%Y %H:%i") as deadline,
                                             compleated
                                             from tbltasks 
                                             limit :pglimit offset :offset');
            $query->bindParam(':pglimit', $limitPerPage, PDO::PARAM_INT);     
            $query->bindParam(':offset', $offset, PDO::PARAM_INT);                              

            $query->execute();
            $rowCount = $query->rowCount();
            if ($rowCount === 0) {
                $response->setSuccess(false);
                $response->setHttpStatusCode(404);
                $response->addMessage("No tasks found");
                $response->send();
                exit();
            }
            $tasks = array();
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                $task = new Task($row['id'], $row['title'], $row['description'], $row['deadline'], $row['compleated']);
                $tasks[] = $task->returnTaskAsArray();
            }
            $returnData = array();
            $returnData['rows_returned'] = $rowCount;
            $returnData['total_rows'] = $tasksCount;
            $returnData['total_pages'] = $numOpPages;
            ($page < $numOpPages ? $returnData['has_next_page'] = true: $returnData['has_next_page'] = false);
            ($page > 1 ? $returnData['has_previous_page'] = true: $returnData['has_previous_page'] = false);
            $returnData['tasks'] = $tasks;
            $response->setData($returnData);

            $response->setSuccess(true);
            $response->setHttpStatusCode(200);
            $response->toCache(true);
            $response->send();
            exit();

        } catch (PDOException $e) {
            error_log("Get error: " . $e->getMessage(), 0);
            $response->setSuccess(false);
            $response->setHttpStatusCode(500);
            $response->addMessage("Error getting the task from de database");
            $response->send();
            exit();

        } catch (TaskException $e) {
            error_log("Task exception: " . $e->getMessage(), 0);
            $response->setSuccess(false);
            $response->setHttpStatusCode(500);
            $response->addMessage("Error creating the task");
            $response->send();
            exit();
        }

    } else {
        $response->setSuccess(false);
        $response->setHttpStatusCode(405);
        $response->addMessage("Request method not allowed!");
        $response->send();
        exit();
    }
} elseif (empty($_GET)) {
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        try {
            $query = $readDB->prepare('select id,
                                             title,
                                             description,
                                             DATE_FORMAT(deadline,"%d/%m/%Y %H:%i") as deadline,
                                             compleated
                                             from tbltasks');

            $query->execute();
            $rowCount = $query->rowCount();
            if ($rowCount === 0) {
                $response->setSuccess(false);
                $response->setHttpStatusCode(404);
                $response->addMessage("No tasks found");
                $response->send();
                exit();
            }
            $tasks = array();
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                $task = new Task($row['id'], $row['title'], $row['description'], $row['deadline'], $row['compleated']);
                $tasks[] = $task->returnTaskAsArray();
            }
            $returnData = array();
            $returnData['rows_returned'] = $rowCount;
            $returnData['tasks'] = $tasks;
            $response->setData($returnData);

            $response->setSuccess(true);
            $response->setHttpStatusCode(200);
            $response->toCache(true);
            $response->send();
            exit();

        } catch (PDOException $e) {
            error_log("Get error: " . $e->getMessage(), 0);
            $response->setSuccess(false);
            $response->setHttpStatusCode(500);
            $response->addMessage("Error getting the task from de database");
            $response->send();
            exit();

        } catch (TaskException $e) {
            error_log("Task exception: " . $e->getMessage(), 0);
            $response->setSuccess(false);
            $response->setHttpStatusCode(500);
            $response->addMessage("Error creating the task");
            $response->send();
            exit();
        }

    } else {
        $response->setSuccess(false);
        $response->setHttpStatusCode(405);
        $response->addMessage("Request method not allowed!");
        $response->send();
        exit();
    }
}
