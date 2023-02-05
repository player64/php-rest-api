<?php

namespace Api\Models\Entities;

class GenreEntity extends Entity
{
    private string $name;

    /**
     * @throws EntityException
     */
    function __construct(array $params)
    {
        $this->set_name($params['name']);
    }

    /**
     * @throws EntityException
     */
    public function set_name(string $name): void
    {
        Validator::required_string($name);
        $this->name = trim($name);
    }

    public function get(): array
    {
        return [
            'name' => $this->name,
        ];
    }
}