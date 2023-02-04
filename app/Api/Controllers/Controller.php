<?php

namespace Api\Controllers;

use Api\Models\Entities\Entity;
use Api\Models\Model;

abstract class Controller
{
    protected Model $model;
    public function list(): array {
        return $this->model->list();
    }

    public function get(int $id): array
    {
        return $this->model->get($id);
    }

/*    abstract public function modify(Entity $entity);

    abstract public function delete(int $id);*/
}