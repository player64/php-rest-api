<?php

namespace Api\Controllers;

use Api\Models\FilmModel;
use Api\Models\ModelException;

class FilmController extends Controller
{

    function __construct(\PDO $db)
    {
        $this->model = new FilmModel($db);
    }
}