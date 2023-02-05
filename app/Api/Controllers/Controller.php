<?php

namespace Api\Controllers;

use Api\Models\Model;
use Api\Models\ModelException;
use Api\Models\RecordNotFoundException;

abstract class Controller
{
    protected Model $model;

    abstract function __construct(\PDO $db);

    public function list(): ControllerResponse
    {
        try {
            $data = $this->model->list();
            return new ControllerResponse($data, 200);

        } catch (ModelException $e) {
            return new ControllerResponse([
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    public function get(int $id): ControllerResponse
    {
        try {
            $data = $this->model->get($id);
            return new ControllerResponse( $data, 200);
        } catch (RecordNotFoundException $e) {
            return new ControllerResponse([
                'msg' => $e->getMessage(),
            ], 404);
        }
    }

    public function delete(int $id): ControllerResponse
    {
        try {
            $this->model->delete($id);
            return new ControllerResponse([
                'message' => 'Successfully deleted',
            ], 202);
        } catch (ModelException $e) {
            return new ControllerResponse([
                'error' => $e->getMessage(),
            ], 400);
        } catch (RecordNotFoundException $e) {
            return new ControllerResponse([
                'msg' => $e->getMessage(),
            ], 404);
        }
    }

    public function update(int $id): ControllerResponse
    {
        $request = $this->parse_json_request();
        try {
            $data = $this->model->update($id, $request);
            return new ControllerResponse([
                'message' => 'Successfully updated',
                'data' => $data
            ], 202);

        } catch (ModelException $e) {
            return new ControllerResponse([
                'error' => $e->getMessage(),
            ], 400);
        } catch (RecordNotFoundException $e) {
            return new ControllerResponse([
                'msg' => $e->getMessage(),
            ], 404);
        }
    }

    public function create(): ControllerResponse
    {
        $request = $this->parse_json_request();

        try {
            $data = $this->model->create($request);

            return new ControllerResponse([
                'message' => 'Successfully created',
                'data' => $data
            ], 202);

        } catch (ModelException $e) {
            return new ControllerResponse([
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    protected function parse_json_request(): array
    {
        return (array)json_decode(file_get_contents('php://input'), true);
    }
}