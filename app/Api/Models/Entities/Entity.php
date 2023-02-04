<?php

namespace Api\Models\Entities;

abstract class Entity
{
    abstract public function get(): array;
}