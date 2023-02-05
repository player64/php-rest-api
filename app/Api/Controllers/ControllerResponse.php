<?php

namespace Api\Controllers;

class ControllerResponse
{
    public int $status;
    public array $data;

    function __construct(array $data, int $status) {
        $this->status = $status;
        $this->data = $data;
    }
}