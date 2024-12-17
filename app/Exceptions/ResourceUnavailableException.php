<?php

namespace App\Exceptions;

use Exception;

class ResourceUnavailableException extends Exception
{
    protected $message = 'The resource is not available at the selected time.';

    public function __construct($message = null)
    {
        parent::__construct($message ?? $this->message);
    }
}
