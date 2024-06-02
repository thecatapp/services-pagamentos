<?php

namespace App\Exceptions;

use Exception;

class TransferenciaException extends Exception
{
    protected $message;
    protected $status;

    public function __construct($message, $status)
    {
        $this->message = $message;
        $this->status = $status;
        parent::__construct($message, $status);
    }
}
