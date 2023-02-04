<?php

namespace Api\Models;

use ReturnTypeWillChange;

class ModelException extends \Exception
{
    public function __construct( $message, $code = 0, $previous = null ) {
        parent::__construct( $message, $code, $previous );
    }

    #[ReturnTypeWillChange]
    public function __toString() {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
}