<?php

namespace Api\Controllers;

use Api\Models\GenreModel;

class GenreController extends Controller
{

    public function __construct(\PDO $db)
    {
        $this->model = new GenreModel($db);
    }
}