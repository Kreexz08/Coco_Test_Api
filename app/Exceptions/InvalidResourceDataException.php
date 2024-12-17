<?php

namespace App\Exceptions;

use Exception;

class InvalidResourceDataException extends Exception
{
    protected $message = 'Invalid datetime or duration format.';

    public function __construct($message = null)
    {
        parent::__construct($message ?? $this->message);
    }
}
