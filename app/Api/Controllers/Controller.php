<?php

namespace Api\Controllers;

use Api\Models\Entities\Entity;
use Api\Models\Entities\EntityException;
use Api\Models\FilmModel;
use Api\Models\Model;
use Api\Models\ModelException;

abstract class Controller
{
    protected Model $model;

    abstract function __construct(\PDO $db);


    abstract public function create(): ControllerResponse;

    public function list(): array {
        return [];
    }

    public function get(int $id): array
    {
        return [];
    }

    /**
     * @throws ControllerException
     */
    protected function get_entity_from_request(array $request, string $entity): Entity
    {
        foreach ($this->model->columns as $key) {
            if(!isset($request[$key])) {
                throw new ControllerException('Wrong parameters given. '.strtoupper($key). ' is required');
            }
        }

        try {
            return new $entity($request);
        } catch (EntityException|\TypeError $e) {
            throw new ControllerException($e->getMessage());
        }
    }

    protected function parse_json_request(): array
    {
        return (array) json_decode(file_get_contents('php://input'), true);
    }

/*    abstract public function modify(Entity $entity);

    abstract public function delete(int $id);*/
}