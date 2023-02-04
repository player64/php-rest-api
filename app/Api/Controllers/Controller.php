<?php

namespace Api\Controllers;

use Api\Models\Entities\Entity;
use Api\Models\FilmModel;
use Api\Models\Model;

abstract class Controller
{
    protected Model $model;

    abstract function __construct(\PDO $db);


    public function list(): array {
        return [];
        // return $this->model->list();
    }

    public function get(int $id): array
    {
        return [];
        // return $this->model->get($id);
    }

    public function create(): array {
        return [];
    }

    public function parse_json_request(): array
    {
        return (array) json_decode(file_get_contents('php://input'), true);
    }

/*    abstract public function modify(Entity $entity);

    abstract public function delete(int $id);*/
}