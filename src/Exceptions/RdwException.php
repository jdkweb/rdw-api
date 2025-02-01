<?php

namespace Jdkweb\Rdw\Exceptions;

use Exception;

class RdwException extends Exception
{
    protected $message = '';

    public function __construct($message = "")
    {
        parent::__construct($message);
    }
}
