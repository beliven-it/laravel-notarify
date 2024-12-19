<?php

namespace Beliven\Notarify\Exceptions;

use Exception;

class NotarizationVerificationException extends Exception
{
    protected static $defaultMessage = 'Verification error';

    public function __construct($message = null)
    {
        $message = $message ?? self::$defaultMessage;

        parent::__construct($message);
    }
}
