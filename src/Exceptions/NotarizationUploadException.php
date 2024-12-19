<?php

namespace Beliven\Notarify\Exceptions;

use Exception;

class NotarizationUploadException extends Exception
{
    protected static $defaultMessage = 'Upload error';

    public function __construct($message = null)
    {
        $message = $message ?? self::$defaultMessage;

        parent::__construct($message);
    }
}
