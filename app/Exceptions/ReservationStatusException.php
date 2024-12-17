<?php

namespace App\Exceptions;

use Exception;

class ReservationStatusException extends Exception
{
    protected $message = 'Invalid reservation status.';

    public function __construct($message = null)
    {
        parent::__construct($message ?? $this->message);
    }
}
