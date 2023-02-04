<?php

namespace Api;

use Api\Controllers\ControllerException;

class Router
{
    private \PDO $db;
    private string $controller;
    private int|null $id;

    /**
     * @throws ControllerException
     */
    function __construct(\PDO $db)
    {
        $this->db = $db;
        $this->assign_controller_and_method();
    }

    /**
     * @throws ControllerException
     */
    private function assign_controller_and_method(): void
    {
        if (isset($_GET['controller']) && $_GET['controller']) {
            $this->controller = $_GET['controller'];
            $this->id = (isset($_GET['id'])) ? (int)$_GET['id'] : null;
        } else if ($_SERVER['REQUEST_URI'] !== '/' && $_SERVER['REQUEST_URI'] !== '') {
            $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
            $uri = explode('/', $uri);
            $this->controller = $uri[1];
            $this->id = (isset($uri[2])) ? (int)$uri[2] : null;
        }

        // if controller is set and not empty finish here otherwise let to throw exception
        if (isset($this->controller) && $this->controller !== '') {
            return;
        }

        throw new ControllerException('Controller is not set.');
    }

    /**
     * @throws ControllerException
     */
    public function render(): bool|string
    {

        return $this->render_json(
            [
                'controller' => $this->controller,
                'id' => $this->id
            ], 200
        );
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
        $base = 'HTTP/1.1 ';
        $code = match ($code) {
            200 => 'OK',
            201 => 'Created',
            202 => 'Accepted',
            204 => 'No Content',
            400 => 'Bad request',
            404 => 'Not found',
            501 => 'Not implemented',
            default => throw new ControllerException('The status code is not implemented.')
        };

        return $base . $code . ' '. $code;
    }
}