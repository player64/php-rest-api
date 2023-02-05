<?php

namespace Api\Models\Entities;

abstract class Entity
{
    abstract function __construct(array $params);
    abstract public function get(): array;
}