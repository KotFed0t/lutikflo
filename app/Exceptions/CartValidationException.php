<?php

namespace App\Exceptions;

use Exception;
use Throwable;

class CartValidationException extends Exception
{
    private array $data;

    public function __construct($message, $code = 0, Throwable $previous = null, array $data = array()) {
        $this->data = $data;
        parent::__construct($message, $code, $previous);
    }

    public function getOptions(): array
    {
        return $this->data;
    }
}
