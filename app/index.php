<?php
try {
    require 'bootstrap.php';
    $db = \Api\Models\DbConnection::db();
    $route = new \Api\Router($db);
    echo $route->render();
} catch (RuntimeException | \Api\Models\ModelException  | \Api\Controllers\ControllerException $e) {
    echo $e->getMessage();
}
