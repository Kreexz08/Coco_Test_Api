<?php

namespace App\Exceptions;

use Exception;

class ResourceAlreadyExistsException extends Exception
{
    protected $message = 'A resource with this name already exists.';

    public function __construct($message = null)
    {
        parent::__construct($message ?? $this->message);
    }
}
