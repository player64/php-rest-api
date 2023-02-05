<?php

namespace Api\Models\Entities;


class ActorEntity extends Entity
{

    private string $name;

    private string $surname;

    private string $gender;


    /**
     * @throws EntityException
     */
    function __construct(array $params)
    {
        $this->set_name($params['name']);
        $this->set_surname($params['surname']);
        $this->set_gender($params['gender']);
    }

    /**
     * @throws EntityException
     */
    public function set_name(string $name): void
    {
        Validator::required_string($name);
        $this->name = trim($name);
    }


    /**
     * @throws EntityException
     */
    public function set_surname(string $surname): void
    {
        Validator::required_string($surname);
        $this->surname = trim($surname);
    }

    /**
     * @throws EntityException
     */
    public function set_gender(string $gender): void
    {
        Validator::is_valid_gender($gender);
        $this->gender = trim($gender);
    }

    public function get(): array
    {
        return [
            'name' => $this->name,
            'surname' => $this->surname,
            'gender' => $this->gender
        ];
    }
}