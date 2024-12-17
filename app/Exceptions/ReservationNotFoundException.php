<?php

namespace App\Exceptions;

use Exception;

class ReservationNotFoundException extends Exception
{
    protected $message = 'Reservation not found.';

    public function __construct($message = null)
    {
        parent::__construct($message ?? $this->message);
    }
}
