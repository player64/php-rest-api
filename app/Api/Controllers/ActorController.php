<?php

namespace Api\Controllers;

use Api\Models\ActorModel;

class ActorController extends Controller
{
    public function __construct(\PDO $db)
    {
        $this->model = new ActorModel($db);
    }
}