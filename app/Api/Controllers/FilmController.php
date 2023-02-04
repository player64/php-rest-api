<?php

namespace Api\Controllers;

use Api\Models\FilmModel;

class FilmController extends Controller
{

    function __construct(\PDO $db)
    {
        $this->model = new FilmModel($db);
    }

    public function list(): array
    {
        return [
            'somethinf' => 'true'
        ];
    }
}