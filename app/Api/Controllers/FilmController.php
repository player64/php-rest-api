<?php

namespace Api\Controllers;

use Api\Models\FilmModel;
use Api\Models\Model;

class FilmController extends Controller
{

    public function __construct()
    {
        $this->model = new FilmModel();
    }
}