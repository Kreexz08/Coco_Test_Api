<?php

namespace App\Exceptions;

use Exception;

class ReservationAlreadyConfirmedException extends Exception
{
    protected $message = 'The reservation is already confirmed.';

    public function __construct($message = null)
    {
        parent::__construct($message ?? $this->message);
    }
}
