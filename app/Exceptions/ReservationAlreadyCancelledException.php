<?php

namespace App\Exceptions;

use Exception;

class ReservationAlreadyCancelledException extends Exception
{
    protected $message = 'The reservation is already cancelled.';

    public function __construct($message = null)
    {
        parent::__construct($message ?? $this->message);
    }
}
