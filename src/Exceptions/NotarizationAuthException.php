<?php

namespace Beliven\Notarify\Exceptions;

use Exception;

class NotarizationAuthException extends Exception
{
    protected static $defaultMessage = "Authentication error";

    public function __construct($message = null)
    {
        $message = $message ?? self::$defaultMessage;

        parent::__construct($message);
    }
}
