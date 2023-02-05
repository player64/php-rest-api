<?php

namespace Api\Models;

use Api\Models\Entities\Entity;

abstract class Model
{
    protected string $table;

    public array $columns;

    protected \PDO $db;

    function __construct(\PDO $db) {
        $this->db = $db;
    }

    abstract public function create(Entity $entity);




    /**
     * @throws ModelException
     */
    protected function validate_params(array $request): void {
        foreach ($this->columns as $key) {
            if(!isset($request[$key])) {
                throw new ModelException('Wrong parameters given. '.strtoupper($key). ' is required');
            }
        }
    }


/*    abstract public function list();

    abstract public function get(int $id): Entity;

    abstract public function modify(Entity $entity);

    abstract public function delete(int $id);*/
    public function get(int $id): array
    {
        return $this->entity->get();
    }
}