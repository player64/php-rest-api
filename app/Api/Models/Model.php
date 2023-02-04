<?php

namespace Api\Models;

use Api\Models\Entities\Entity;

abstract class Model
{
    protected string $table;
    protected Entity $entity;
/*    abstract public function list();

    abstract public function get(int $id): Entity;

    abstract public function modify(Entity $entity);

    abstract public function delete(int $id);*/
    public function get(int $id): array
    {
        return $this->entity->get();
    }
}