<?php

namespace Api;

use Api\Controllers\Controller;
use Api\Controllers\ControllerException;
use Api\Controllers\FilmController;

class Router
{
    private \PDO $db;
    private string $action;

    private string $method;

    private Controller $controller;

    private int|null $id;

    /**
     * @throws ControllerException
     */
    function __construct(\PDO $db)
    {
        $this->db = $db;
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->_obtain_action_and_id_from_url();
        $this->_obtain_controller();
    }

    /**
     * @throws ControllerException
     */
    public function render(): bool|string
    {
        switch ($this->method) {
            case 'GET':
                if($this->id) {
                    return $this->render_json($this->controller->get($this->id), 200);
                }
                return $this->render_json($this->controller->list(), 200);
                break;
            case 'POST':

                $response = $this->controller->create();

                return $this->render_json($response->data, $response->status);
            case 'DELETE':
            case 'PUT':
                if (!$this->id) {
                    throw new ControllerException('You cannot modify or delete without id parameter.');
                }
                break;
        }

        return false;
    }

    /**
     * @throws ControllerException
     */
    public function render_json(array $response, int $code): bool|string
    {
        header("Access-Control-Allow-Origin: *");
        header("Content-Type: application/json; charset=UTF-8");
        header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
        header("Access-Control-Max-Age: 3600");
        header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers");
        header($this->resolve_header($code));

        return json_encode($response);
    }

    /**
     * @throws ControllerException
     */
    private function resolve_header(int $code): string
    {
        $status = match ($code) {
            200 => 'OK',
            201 => 'Created',
            202 => 'Accepted',
            204 => 'No Content',
            400 => 'Bad request',
            404 => 'Not found',
            501 => 'Not implemented',
            default => throw new ControllerException('The status code is not implemented.')
        };

        return 'HTTP/1.1 ' . $code . ' ' . $status;
    }

    /**
     * @throws ControllerException
     */
    private function _obtain_action_and_id_from_url(): void
    {
        if (isset($_GET['controller']) && $_GET['controller']) {
            $this->action = strtolower($_GET['controller']);
            $this->id = (isset($_GET['id'])) ? (int)$_GET['id'] : null;
        } else if ($_SERVER['REQUEST_URI'] !== '/' && $_SERVER['REQUEST_URI'] !== '') {
            $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
            $uri = explode('/', $uri);
            $this->action = strtolower($uri[1]);
            $this->id = (isset($uri[2])) ? (int)$uri[2] : null;
        }

        // if controller is set and not empty finish here otherwise let to throw exception
        if (isset($this->action) && $this->action !== '') {
            return;
        }

        throw new ControllerException('Controller is not set.');
    }

    /**
     * @throws ControllerException
     */
    private function _obtain_controller(): void
    {
        $this->controller = match ($this->action) {
            'films' => new FilmController($this->db),
            default => throw new ControllerException('The controller ' . $this->action . ' is not implemented.'),
        };
    }
}